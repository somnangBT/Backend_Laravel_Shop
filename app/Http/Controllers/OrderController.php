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
                if (is_numeric($search)) {
                    $q->where('id', (int)$search);
                } else {
                    $q->where('customer_name', 'like', "%{$search}%");
                }
                $q->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('bakong_ref', 'like', "%{$search}%")
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

        // Filter by payment status (including quick queue: paid/awaiting orders still pending delivery approval)
        if ($request->filled('payment_status') && $request->input('payment_status') !== 'all') {
            if ($request->input('payment_status') === 'paid_pending_approval') {
                $query->where('status', 'pending')
                      ->whereIn('payment_status', ['paid', 'awaiting_approval']);
            } else {
                $query->where('payment_status', $request->input('payment_status'));
            }
        }

        $orders = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Order::count(),
            'paid_pending_approval' => Order::where('status', 'pending')
                ->whereIn('payment_status', ['paid', 'awaiting_approval'])
                ->count(),
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
            'payment_status' => 'nullable|string|in:pending,awaiting_approval,paid,refunded',
        ]);

        $previousStatus = $order->status;

        if ($request->filled('status')) {
            $order->status = $validated['status'];
        }

        if ($request->filled('payment_status')) {
            $order->payment_status = $validated['payment_status'];
        }

        $order->save();

        if ($request->filled('status') && $order->status !== $previousStatus && in_array($order->status, ['preparing', 'delivering', 'completed'])) {
            $this->sendTelegramApprovalNotification($order, 'Admin Panel');
        }

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
     * One-click Admin Approve Payment & Start Delivery
     */
    public function approveForDelivery(Request $request, Order $order)
    {
        $order->payment_status = 'paid';
        $order->status = 'delivering';
        $order->save();

        $this->sendTelegramApprovalNotification($order, 'Admin Approval');

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Order #{$order->id} approved for delivery!",
                'order' => $order,
            ]);
        }

        return back()->with('success', "✅ Order #{$order->id} ត្រូវបាន Admin អនុម័តការបង់ប្រាក់ និងបញ្ជូនទៅដឹកជញ្ជូនហើយ!");
    }

    /**
     * API: Real-time Bakong KHQR MD5 Verification for React Checkout
     */
    public function checkBakongPayment(Request $request)
    {
        $request->validate([
            'md5' => 'required|string|size:32',
        ]);

        $result = $this->checkBakongMd5($request->input('md5'));

        return response()->json([
            'status' => 'success',
            'paid' => $result['paid'],
            'bakong_ref' => $result['ref'],
            'sender' => $result['sender'],
            'amount' => $result['amount'],
            'currency' => $result['currency'],
            'message' => $result['paid']
                ? '✅ បានផ្ទៀងផ្ទាត់ការបង់ប្រាក់តាម Bakong ជោគជ័យ!'
                : ($result['message'] ?? 'កំពុងរង់ចាំការបង់ប្រាក់...'),
        ]);
    }

    /**
     * API: Optional Telegram Bot Inline Button Webhook (Approve directly in Telegram)
     */
    public function telegramWebhook(Request $request)
    {
        $callback = $request->input('callback_query');
        if (!$callback) {
            return response()->json(['ok' => true]);
        }

        $data = $callback['data'] ?? '';
        $callbackId = $callback['id'] ?? null;
        $chatId = $callback['message']['chat']['id'] ?? null;
        $messageId = $callback['message']['message_id'] ?? null;
        $token = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');

        if (str_starts_with($data, 'approve_delivery_')) {
            $orderId = (int) str_replace('approve_delivery_', '', $data);
            $order = Order::find($orderId);

            if ($order) {
                $order->payment_status = 'paid';
                $order->status = 'delivering';
                $order->save();

                if ($token && $callbackId) {
                    Http::post("https://api.telegram.org/bot{$token}/answerCallbackQuery", [
                        'callback_query_id' => $callbackId,
                        'text' => "✅ បានអនុម័ត Order #{$order->id} សម្រាប់ដឹកជញ្ជូន!",
                        'show_alert' => true,
                    ]);
                }

                if ($token && $chatId && $messageId) {
                    $adminName = $callback['from']['first_name'] ?? 'Admin';
                    Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'reply_to_message_id' => $messageId,
                        'text' => "✅ Order #{$order->id} ត្រូវបាន {$adminName} អនុម័តរួចរាល់!\n💳 Payment: PAID\n🛵 Delivery: កំពុងដឹកជញ្ជូន (Delivering)",
                    ]);
                }
            }
        }

        return response()->json(['ok' => true]);
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
                'payment_proof' => $order->payment_proof ?? null,
                'khqr_md5' => $order->khqr_md5 ?? null,
                'bakong_ref' => $order->bakong_ref ?? null,
                'shipping_address' => $order->shipping_address ?? 'Phnom Penh',
                'phone' => $order->phone ?? '',
                'customer_name' => $order->customer_name ?: (optional($order->user)->name ?? 'Customer'),
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
        // Normalize fields sent from React frontend
        if ($request->has('items') && is_array($request->items)) {
            $items = $request->items;
            foreach ($items as &$it) {
                if (!isset($it['product_id']) && isset($it['pro_id'])) {
                    $it['product_id'] = $it['pro_id'];
                }
                if (!isset($it['product_id']) && isset($it['id'])) {
                    $it['product_id'] = $it['id'];
                }
            }
            $request->merge(['items' => $items]);
        }

        if (!$request->filled('subtotal')) {
            $subtotal = $request->input('total') ?? $request->input('total_amount') ?? 0;
            $request->merge(['subtotal' => $subtotal]);
        }
        if (!$request->filled('total')) {
            $total = $request->input('total_amount') ?? $request->input('subtotal') ?? 0;
            $request->merge(['total' => $total]);
        }
        if (!$request->filled('shipping_address') && $request->filled('address')) {
            $request->merge(['shipping_address' => $request->address]);
        }
        if (!$request->filled('customer_name') && $request->filled('name')) {
            $request->merge(['customer_name' => $request->name]);
        }

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

        // ពិនិត្យការទូទាត់ ABA KHQR / Bakong ពី Server
        $paymentMethod = $request->input('payment_method', 'cash');
        $khqrMd5 = $request->input('khqr_md5');
        $paymentProof = $request->input('payment_proof');
        $paymentStatus = 'pending';
        $bakongRef = $request->input('bakong_ref');
        $bakongSender = null;

        if ($paymentMethod === 'khqr' || $paymentMethod === 'bakong_aba') {
            $bakongId = config('services.bakong.account_id');
            if ($khqrMd5 && $bakongId && $bakongId !== 'abaakhppxxx@abaa') {
                $bakongCheck = $this->checkBakongMd5($khqrMd5);
                if ($bakongCheck['paid']) {
                    $paymentStatus = 'paid';
                    $bakongRef = $bakongCheck['ref'];
                    $bakongSender = $bakongCheck['sender'];
                }
            }

            if ($paymentStatus !== 'paid' && ($paymentProof || $request->boolean('paid_claimed') || $bakongRef)) {
                $paymentStatus = 'awaiting_approval';
            }
        }

        try {
            $order = DB::transaction(function () use ($request, $user, $paymentMethod, $paymentStatus, $paymentProof, $khqrMd5, $bakongRef) {
                $candidateId = $user ? $user->id : $request->input('user_id');
                $resolvedUserId = ($candidateId && \App\Models\User::where('id', $candidateId)->exists())
                    ? $candidateId
                    : null;

                $customerName = $request->input('customer_name')
                    ?? $request->input('name')
                    ?? ($user ? $user->name : null);

                // ២. បង្កើត Order (Status = pending រង់ចាំ Admin Approve ដើម្បីដឹកជញ្ជូន)
                $order = Order::create([
                    'user_id' => $resolvedUserId,
                    'customer_name' => $customerName,
                    'subtotal' => $request->subtotal,
                    'tax' => $request->tax ?? 0,
                    'shipping' => $request->shipping ?? 0,
                    'discount' => $request->discount ?? 0,
                    'total' => $request->total ?? $request->subtotal,
                    'status' => 'pending',
                    'email' => $request->email ?? ($user ? $user->email : null),
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'payment_proof' => $paymentProof,
                    'khqr_md5' => $khqrMd5,
                    'bakong_ref' => $bakongRef,
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

            // ៤. ផ្ញើសារទៅកាន់ Telegram Bot
            $this->sendTelegramOrderNotification($order, $request->items, $bakongSender);

            // ៥. បញ្ជូនលទ្ធផលជោគជ័យត្រឡប់ទៅ React វិញ
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
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

    /**
     * Query NBC Bakong Open API to check whether a dynamic KHQR MD5 has been paid.
     */
    private function checkBakongMd5(string $md5): array
    {
        $token = config('services.bakong.token') ?: env('BAKONG_TOKEN');
        if (empty($token)) {
            return ['paid' => false, 'ref' => null, 'sender' => null, 'amount' => null, 'currency' => null, 'message' => 'BAKONG_TOKEN not configured'];
        }

        try {
            $res = Http::withToken($token)
                ->timeout(8)
                ->post('https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5', ['md5' => $md5]);

            if ($res->successful()) {
                $body = $res->json();
                if (isset($body['responseCode']) && (int)$body['responseCode'] === 0 && !empty($body['data'])) {
                    $tx = $body['data'];
                    return [
                        'paid' => true,
                        'ref' => $tx['externalRef'] ?? $tx['hash'] ?? $md5,
                        'sender' => $tx['fromAccountId'] ?? null,
                        'amount' => isset($tx['amount']) ? (float)$tx['amount'] : null,
                        'currency' => $tx['currency'] ?? 'USD',
                        'message' => 'Success',
                    ];
                }
                return [
                    'paid' => false,
                    'ref' => null,
                    'sender' => null,
                    'amount' => null,
                    'currency' => null,
                    'message' => $body['responseMessage'] ?? 'Transaction pending',
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Bakong check_transaction_by_md5 error: ' . $e->getMessage());
        }

        return ['paid' => false, 'ref' => null, 'sender' => null, 'amount' => null, 'currency' => null, 'message' => 'Unable to reach Bakong API'];
    }

    /**
     * Send rich Telegram Bot notification for new orders (supports KHQR proof & inline admin link).
     */
    private function sendTelegramOrderNotification(Order $order, array $items = [], ?string $bakongSender = null): void
    {
        try {
            $token = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
            $chatId = config('services.telegram.chat_id') ?: env('TELEGRAM_CHAT_ID');

            if (empty($token) || empty($chatId)) {
                Log::error("Telegram Skipped: Token or Chat ID missing.");
                return;
            }

            $productList = "";
            foreach ($items as $index => $item) {
                $name = $item['name'] ?? ('Product #' . ($item['product_id'] ?? ''));
                $qty = (int)($item['quantity'] ?? 1);
                $itemTotal = ((float)($item['price'] ?? 0)) * $qty;
                $productList .= ($index + 1) . ". {$name} (x{$qty}) - $" . number_format($itemTotal, 2) . "\n";
            }

            $customerName = $order->customer_name ?: (optional($order->user)->name ?? 'អតិថិជនទូទៅ (Guest)');
            $isKhqr = in_array($order->payment_method, ['khqr', 'bakong_aba']);
            $abaAccount = config('services.bakong.aba_account', '007842694');
            $merchantName = config('services.bakong.merchant_name', 'YORN SOMNANG');
            $paymentMethodLabel = $isKhqr
                ? "🏦 ABA KHQR ({$merchantName} • {$abaAccount})"
                : '💵 សាច់ប្រាក់សុទ្ធ (Cash on Delivery)';

            if ($order->payment_status === 'paid' && $order->bakong_ref) {
                $paymentStatusText = "✅ បង់លុយពិតរួចរាល់ (Verified)\n🧾 Ref: {$order->bakong_ref}" . ($bakongSender ? "\n🏦 ផ្ញើពី: {$bakongSender}" : "");
            } elseif ($order->payment_status === 'awaiting_approval') {
                $paymentStatusText = "🧾 អតិថិជនបានបង់លុយចូល ABA {$abaAccount} (រង់ចាំ Admin ពិនិត្យ Approve)"
                    . ($order->bakong_ref ? "\n🔖 យោង/អ្នកផ្ញើ: {$order->bakong_ref}" : "");
            } elseif ($order->payment_status === 'paid') {
                $paymentStatusText = "✅ បានទូទាត់ប្រាក់រួចរាល់";
            } else {
                $paymentStatusText = $isKhqr ? "⏳ រង់ចាំ Admin ពិនិត្យលុយចូល ABA ({$abaAccount})" : "💵 ទូទាត់ពេលដឹក";
            }

            $phone = $order->phone ?: 'មិនទាន់បញ្ជាក់';
            $address = $order->shipping_address ?: 'ភ្នំពេញ';
            $totalUSD = number_format((float)$order->total, 2);
            $totalKHR = number_format((float)$order->total * 4100);
            $adminBase = rtrim(config('app.url') ?: env('APP_URL', 'https://backend-laravel-shop.vercel.app'), '/');
            $adminUrl = "{$adminBase}/orders?payment_status=paid_pending_approval";

            $message = "🛒 មានការបញ្ជាទិញថ្មី (Order #{$order->id})!\n"
                     . "⏳ ស្ថានភាព: PENDING — រង់ចាំ Admin Approve ដើម្បីដឹកជញ្ជូន\n\n"
                     . "👤 អតិថិជន: {$customerName}\n"
                     . "📞 ទូរស័ព្ទ: {$phone}\n"
                     . "📍 ទីតាំងដឹក: {$address}\n"
                     . "💳 វិធីទូទាត់: {$paymentMethodLabel}\n"
                     . "📌 ស្ថានភាពបង់ប្រាក់: {$paymentStatusText}\n\n"
                     . "📦 ទំនិញដែលបានទិញ:\n"
                     . $productList
                     . "\n💰 សរុបទឹកប្រាក់: \${$totalUSD} = ({$totalKHR} ៛)\n";

            if ($order->payment_proof) {
                $message .= "🖼 រូបវិក្កយបត្រ: {$order->payment_proof}\n";
            }

            $message .= "\n👉 ចូលពិនិត្យ & Approve ដឹកជញ្ជូន: {$adminUrl}";

            $payload = [
                'chat_id' => $chatId,
                'text' => $message,
            ];

            // Inline button (Works seamlessly with HTTPS admin URL; Approve callback works if webhook is set)
            if (str_starts_with($adminUrl, 'https://')) {
                $payload['reply_markup'] = json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => '✅ Approve & ដឹកជញ្ជូន', 'callback_data' => "approve_delivery_{$order->id}"],
                            ['text' => '🔗 បើក Admin Panel', 'url' => $adminUrl],
                        ],
                    ],
                ]);
            }

            $response = Http::timeout(8)->post("https://api.telegram.org/bot{$token}/sendMessage", $payload);
            if (!$response->successful()) {
                Log::error('Telegram API sendMessage Failed: ' . $response->body());
            }

            // Send receipt slip photo directly into Telegram chat if attached
            if ($order->payment_proof) {
                Http::timeout(8)->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                    'chat_id' => $chatId,
                    'photo' => $order->payment_proof,
                    'caption' => "🧾 វិក្កយបត្របង់ប្រាក់សម្រាប់ Order #{$order->id} (\${$totalUSD}) — {$customerName}",
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Telegram notification error: ' . $e->getMessage());
        }
    }

    /**
     * Notify Telegram Bot when Admin approves or updates an order status for delivery.
     */
    private function sendTelegramApprovalNotification(Order $order, string $source = 'Admin Panel'): void
    {
        try {
            $token = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
            $chatId = config('services.telegram.chat_id') ?: env('TELEGRAM_CHAT_ID');
            if (empty($token) || empty($chatId)) {
                return;
            }

            $customerName = $order->customer_name ?: (optional($order->user)->name ?? 'Customer');
            $statusEmoji = match ($order->status) {
                'preparing' => '🍳 កំពុងរៀបចំ (Preparing)',
                'delivering' => '🛵 កំពុងដឹកជញ្ជូន (Delivering)',
                'completed' => '✅ ដឹកជញ្ជូនជោគជ័យ (Completed)',
                default => strtoupper($order->status),
            };

            $text = "📢 បច្ចុប្បន្នភាព Order #{$order->id} ({$source})\n"
                  . "👤 អតិថិជន: {$customerName} ({$order->phone})\n"
                  . "💳 ការបង់ប្រាក់: " . strtoupper($order->payment_status) . "\n"
                  . "🚚 ស្ថានភាពថ្មី: {$statusEmoji}\n"
                  . "💰 តម្លៃសរុប: \$" . number_format((float)$order->total, 2);

            Http::timeout(8)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            Log::warning('Telegram approval notify error: ' . $e->getMessage());
        }
    }
}