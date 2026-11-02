<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // If API / JSON request from React
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->apiIndex($request);
        }

        // Web Admin Orders Management Listing
        $query = Order::with(['user', 'orderItems.product'])->orderBy('created_at', 'desc');

        // Search by order id, customer name, email, or phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by order status
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Filter by payment status
        if ($request->filled('payment_status') && $request->input('payment_status') !== 'all') {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $orders = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'delivering' => Order::where('status', 'delivering')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('orders.index', compact('orders', 'counts'));
    }

    /**
     * Kitchen Display System (KDS) View
     */
    public function kitchen(Request $request)
    {
        // Kitchen only displays active cooking orders: pending and preparing
        $orders = Order::with(['orderItems.product', 'user'])
                       ->whereIn('status', ['pending', 'preparing'])
                       ->orderBy('created_at', 'asc') // First-in First-out
                       ->get();

        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'today_completed' => Order::where('status', 'completed')->whereDate('created_at', now()->today())->count(),
        ];

        return view('orders.kitchen', compact('orders', 'stats'));
    }

    /**
     * Update order and payment status quickly
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:pending,preparing,delivering,completed,cancelled',
            'payment_status' => 'nullable|string|in:pending,paid,refunded',
        ]);

        if ($request->filled('status')) {
            $order->status = $validated['status'];
        }

        if ($request->filled('payment_status')) {
            $order->payment_status = $validated['payment_status'];
        }

        $order->save();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Order #{$order->id} status updated to {$order->status}.",
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                ]
            ]);
        }

        return back()->with('success', "Order #{$order->id} updated successfully!");
    }

    /**
     * API: Get Order History for React Frontend
     */
    public function apiIndex(Request $request)
    {
        $userId = null;
        $user = Auth::guard('sanctum')->user() ?? Auth::user();

        if ($user) {
            $userId = $user->id;
        } elseif ($request->filled('user_id')) {
            $userId = $request->input('user_id');
        } elseif ($request->filled('email')) {
            $u = \App\Models\User::where('email', $request->input('email'))->first();
            if ($u) {
                $userId = $u->id;
            }
        }

        $query = Order::with(['items.product', 'orderItems.product'])
                      ->orderBy('created_at', 'desc');

        if ($userId) {
            $query->where(function($q) use ($userId, $request) {
                $q->where('user_id', $userId);
                if ($request->filled('email')) {
                    $q->orWhere('email', $request->input('email'));
                }
            });
        }

        $orders = $query->limit(50)->get();

        $formattedOrders = $orders->map(function ($order) {
            $itemList = $order->items->isNotEmpty() ? $order->items : $order->orderItems;

            return [
                'id' => $order->id,
                'created_at' => $order->created_at ? $order->created_at->toIso8601String() : now()->toIso8601String(),
                'status' => $order->status ?? 'pending',
                'payment_status' => $order->payment_status ?? 'pending',
                'payment_method' => $order->payment_method ?? 'cash',
                'shipping_address' => $order->shipping_address ?? 'Phnom Penh',
                'phone' => $order->phone ?? '',
                'customer_name' => optional($order->user)->name ?? 'Customer',
                'total' => (float)($order->total ?? 0),
                'subtotal' => (float)($order->subtotal ?? 0),
                'shipping' => (float)($order->shipping ?? 0),
                'tax' => (float)($order->tax ?? 0),
                'discount' => (float)($order->discount ?? 0),
                'promo_code' => $order->promo_code ?? null,
                'items' => $itemList->map(function ($item) {
                    $prod = $item->product;
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'quantity' => (int)($item->quantity ?? 1),
                        'price' => (float)($item->price ?? 0),
                        'product_name' => $prod ? ($prod->pro_name ?? $prod->name) : ('Product #' . $item->product_id),
                        'image' => $prod ? $prod->image : null,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedOrders
        ]);
    }

    /**
     * API: Cancel an order from React Frontend
     */
    public function cancelApi(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        if (in_array($order->status, ['completed', 'delivering'])) {
            return response()->json(['status' => 'error', 'message' => 'Cannot cancel an order that is already completed or in delivery'], 400);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order cancelled successfully',
            'data' => $order
        ]);
    }

    public function store(Request $request)
    {
        // ១. ធ្វើ Validation ទិន្នន័យដែលផ្ញើពី React
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,pro_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'total' => 'nullable|numeric',
            'tax' => 'nullable|numeric',       
            'shipping' => 'nullable|numeric',  
            'discount' => 'nullable|numeric',  
            'customer_name' => 'nullable|string',
        ]);

        $user = Auth::guard('sanctum')->user() ?? Auth::user();

        try {
            $order = DB::transaction(function () use ($request, $user) {
                $resolvedUserId = $user ? $user->id : ($request->input('user_id') ?: 1);

                // ២. បង្កើត Order
                $order = Order::create([
                    'user_id' => $resolvedUserId, 
                    'subtotal' => $request->subtotal, 
                    'tax' => $request->tax ?? 0,             
                    'shipping' => $request->shipping ?? 0,   
                    'discount' => $request->discount ?? 0,   
                    'total' => $request->total ?? $request->subtotal, 
                    'status' => 'pending',
                    'email' => $request->email ?? ($user ? $user->email : null),
                    'payment_method' => $request->payment_method ?? 'cash',
                    'shipping_address' => $request->shipping_address ?? 'Phnom Penh',
                    'phone' => $request->phone ?? null,
                ]);

                // ៣. បញ្ចូលផលិតផលទៅក្នុងតារាង Order Items
                foreach ($request->items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'], 
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount' => $item['discount'] ?? 0,
                    ]);
                }

                // លុប Cart ចោលក្រោយទិញរួច
                if ($user) {
                    Cart::where('user_id', $user->id)->delete();
                }

                return $order;
            });

            // ៤. ផ្ញើសារទៅកាន់ Telegram
            try {
                $token = env('TELEGRAM_BOT_TOKEN');
                $chatId = env('TELEGRAM_CHAT_ID');
                
                if (empty($token) || empty($chatId)) {
                    Log::error("Telegram Skipped: Token ឬ Chat ID ទទេស្អាត! Token={$token}, ChatID={$chatId}");
                }

                if ($token && $chatId) {
                    $productList = "";
                    foreach ($request->items as $index => $item) {
                        $name = $item['name'] ?? 'Product ID ' . $item['product_id'];
                        $itemTotal = $item['price'] * $item['quantity'];
                        $productList .= ($index + 1) . ". {$name} (x{$item['quantity']}) - $" . number_format($itemTotal, 2) . "\n";
                    }
                    
                    // អ្នកគ្រូកែត្រង់នេះ៖ យកឈ្មោះពី React មកបង្ហាញ បើអត់មានទើបដាក់ Guest
                    $customerName = $request->customer_name ?? ($user ? $user->name : 'អតិថិជនទូទៅ (Guest)');
                    
                    $message = "🛒 មានការបញ្ជាទិញថ្មី (Order #" . $order->id . ")!\n\n"
                             . "👤 អតិថិជន: {$customerName}\n"
                             . "📦 ទំនិញដែលបានទិញ:\n"
                             . $productList
                             . "\n💰 សរុបទឹកប្រាក់: $" . number_format($request->total ?? $request->subtotal, 2) . "\n\n"
                             . "សូមពិនិត្យមើលក្នុងប្រព័ន្ធដើម្បីរៀបចំឥវ៉ាន់!";

                    $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $message,
                    ]);

                    if (!$response->successful()) {
                        Log::error('Telegram API Failed: ' . $response->body());
                    }
                }
            } catch (\Exception $e) {
                Log::error('Telegram error: ' . $e->getMessage());
            }

            // ៥. បញ្ជូនលទ្ធផលជោគជ័យត្រឡប់ទៅ React វិញ
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $order
            ], 201);

        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['orderItems.product']);
        return view('cart.show', compact('order'));
    }

    public function create()
    {
        $this->authorize('create', Order::class);
        $products = Product::whereHas('category', function ($q) {
            $q->whereNull('deleted_at');
        })->get();
        
        $cartItems = Cart::where('user_id', Auth::id())->with(['product' => function ($query) {
            $query->whereHas('category', function ($q) {
                $q->whereNull('deleted_at');
            });
        }])->get()->filter(function ($item) {
            return $item->product !== null;
        });
        
        return view('cart.create', compact('products', 'cartItems'));
    }

    public function edit(Order $order)
    {
        $this->authorize('update', $order);
        $products = Product::whereHas('category', function ($q) {
            $q->whereNull('deleted_at');
        })->get();
        $order->load('orderItems');
        return view('cart.edit', compact('order', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        // ត្រង់នេះ បើ React ហៅមក Cancel អ្នកគ្រូឱ្យវា Return ជា JSON វិញ ដើម្បីកុំឱ្យ Error ពេល Cancel Order
        if ($request->wantsJson() || $request->is('api/*')) {
            $order->update(['status' => 'cancelled']);
            return response()->json(['status' => 'success', 'message' => 'Order cancelled']);
        }

        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,pro_id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                $total = 0;
                $itemsToKeep = [];

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }
                    $total += $product->discounted_price * $item['quantity'];
                    $itemsToKeep[] = $item['product_id'];
                }

                $order->update([
                    'total' => $total,
                    'status' => $request->status,
                ]);

                $order->orderItems()->whereNotIn('product_id', $itemsToKeep)->delete();

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $order->orderItems()->updateOrCreate(
                        ['product_id' => $item['product_id']],
                        [
                            'quantity' => $item['quantity'],
                            'price' => $product->discounted_price,
                        ]
                    );
                }
            });

            return redirect()->route('cart.index')->with('success', 'Order updated successfully');
        } catch (\Exception $e) {
            Log::error('Order update failed: ' . $e->getMessage());
            return redirect()->route('cart.edit', $order)->with('error', $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);
        $order->delete();
        return redirect()->route('cart.index')->with('success', 'Order deleted successfully');
    }
}