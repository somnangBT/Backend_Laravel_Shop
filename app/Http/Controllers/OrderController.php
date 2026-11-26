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

        // Web Admin Orders Management Listing (lean columns for fast remote Postgres query)
        $query = Order::with([
            'user:id,name,email',
            'orderItems:id,order_id,product_id,quantity,price,discount',
            'orderItems.product:pro_id,pro_name',
        ])->orderBy('created_at', 'desc');

        // Search by order id, customer name, email, phone, or Bakong ref
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('id', (int)$search)
                      ->orWhere('customer_name', 'like', "%{$search}%");
                } else {
                    $q->where('customer_name', 'like', "%{$search}%");
                }
                $q->orWhere('phone', 'like', "%{$search}%")
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

        // Skip tab-count aggregation during typing search; fetch counts on full load or live-refresh
        if ($request->ajax() && !$request->boolean('with_counts')) {
            $counts = [
                'all' => 0,
                'paid_pending_approval' => 0,
                'pending' => 0,
                'preparing' => 0,
                'delivering' => 0,
                'completed' => 0,
                'cancelled' => 0,
            ];
        } else {
            $agg = DB::table('orders')->selectRaw("
                COUNT(*) as all_count,
                SUM(CASE WHEN status = 'pending' AND payment_status IN ('paid', 'awaiting_approval') THEN 1 ELSE 0 END) as paid_pending_approval,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'preparing' THEN 1 ELSE 0 END) as preparing_count,
                SUM(CASE WHEN status = 'delivering' THEN 1 ELSE 0 END) as delivering_count,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count
            ")->first();

            $counts = [
                'all' => (int) ($agg->all_count ?? 0),
                'paid_pending_approval' => (int) ($agg->paid_pending_approval ?? 0),
                'pending' => (int) ($agg->pending_count ?? 0),
                'preparing' => (int) ($agg->preparing_count ?? 0),
                'delivering' => (int) ($agg->delivering_count ?? 0),
                'completed' => (int) ($agg->completed_count ?? 0),
                'cancelled' => (int) ($agg->cancelled_count ?? 0),
            ];
        }

        return view('orders.index', compact('orders', 'counts'));
    }

    /**
     * Ultra-lightweight JSON endpoint for real-time Admin order notifications & auto-refresh
     */
    public function livePulse()
    {
        $latest = Order::select('id', 'customer_name', 'total', 'payment_method', 'payment_status', 'status', 'created_at')
            ->orderBy('id', 'desc')
            ->first();

        $summary = DB::table('orders')->selectRaw("
            COUNT(*) as total_count,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'pending' AND payment_status IN ('paid', 'awaiting_approval') THEN 1 ELSE 0 END) as paid_waiting_count,
            MAX(updated_at) as last_updated_at
        ")->first();

        return response()->json([
            'latest_id' => $latest ? (int) $latest->id : 0,
            'total_count' => (int) ($summary->total_count ?? 0),
            'pending_count' => (int) ($summary->pending_count ?? 0),
            'paid_waiting_count' => (int) ($summary->paid_waiting_count ?? 0),
            'last_updated_at' => (string) ($summary->last_updated_at ?? ''),
            'latest_order' => $latest ? [
                'id' => $latest->id,
                'customer_name' => $latest->customer_name ?: 'Guest Customer',
                'total' => number_format((float) $latest->total, 2),
                'payment_method' => $latest->payment_method ?: 'cash',
                'payment_status' => $latest->payment_status ?: 'pending',
                'status' => $latest->status ?: 'pending',
                'time' => $latest->created_at ? $latest->created_at->timezone('Asia/Phnom_Penh')->format('h:i A') : '',
            ] : null,
        ]);
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
            'md5' => ['required', 'string', 'regex:/^[a-fA-F0-9]{32}$/'],
        ]);

        $md5 = strtolower(trim($request->input('md5')));

        // Replay protection: block reusing an MD5 hash already recorded on a paid order
        $alreadyUsed = Order::where('khqr_md5', $md5)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->exists();
        if ($alreadyUsed) {
            return response()->json([
                'status' => 'error',
                'paid' => false,
                'already_used' => true,
                'message' => '⚠️ ប្រតិបត្តិការ Bakong KHQR នេះត្រូវបានប្រើរួចរាល់ហើយ សូមបង្កើត QR ថ្មី!',
            ]);
        }

        $result = $this->checkBakongMd5($md5);

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
        $secret = config('services.telegram.webhook_secret');
        if (!empty($secret)) {
            $headerSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token');
            if (!hash_equals($secret, $headerSecret)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized webhook'], 403);
            }
        }

        $callback = $request->input('callback_query');
        if (!$callback) {
            return response()->json(['ok' => true]);
        }

        $data = $callback['data'] ?? '';
        $callbackId = $callback['id'] ?? null;
        $chatId = $callback['message']['chat']['id'] ?? null;
        $messageId = $callback['message']['message_id'] ?? null;
        $token = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
        $configuredChatId = (string) (config('services.telegram.chat_id') ?: env('TELEGRAM_CHAT_ID'));

        // Verify callback originates from our shop's Telegram admin chat
        if (empty($configuredChatId) || (string) $chatId !== $configuredChatId) {
            return response()->json(['ok' => false, 'message' => 'Forbidden chat'], 403);
        }

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

        // Prevent unauthenticated dumping of all customer orders when no identifier is supplied
        if (!$userId && !$request->filled('email')) {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ]);
        }

        $query = Order::with(['items.product', 'orderItems.product'])
                      ->orderBy('created_at', 'desc');

        $query->where(function($q) use ($userId, $request) {
            if ($userId) {
                $q->where('user_id', $userId);
            }
            if ($request->filled('email')) {
                $userId ? $q->orWhere('email', $request->input('email')) : $q->where('email', $request->input('email'));
            }
        });

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

        if (!$request->filled('shipping_address') && $request->filled('address')) {
            $request->merge(['shipping_address' => $request->address]);
        }
        if (!$request->filled('customer_name') && $request->filled('name')) {
            $request->merge(['customer_name' => $request->name]);
        }

        // ១. ធ្វើ Validation ទិន្នន័យ (តម្លៃពិតនឹងត្រូវគណនាពី Database នៅ Backend)
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,pro_id',
            'items.*.quantity' => 'required|integer|min:1|max:500',
            'items.*.price' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0|max:500',
            'shipping' => 'nullable|numeric|min:0|max:500',
            'discount' => 'nullable|numeric|min:0',
            'promo_code' => 'nullable|string|max:50',
            'payment_method' => 'nullable|in:cash,khqr,aba_khqr,bakong_aba',
            'khqr_md5' => ['nullable', 'string', 'regex:/^[a-fA-F0-9]{32}$/'],
            'payment_proof' => 'nullable|string|max:1000',
            'bakong_ref' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
        ]);

        $user = Auth::guard('sanctum')->user() ?? Auth::user();

        // គណនាតម្លៃទំនិញ និង Total ពិតប្រាកដពី Database (ការពារករណីក្លែងបន្លំតម្លៃពី Client)
        $pricing = $this->computeVerifiedOrderTotals(
            $request->items,
            (float) ($request->input('tax', 0)),
            (float) ($request->input('shipping', 0)),
            $request->input('promo_code')
        );

        if (isset($pricing['error'])) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $pricing['error'],
            ], 422);
        }

        $verifiedItems = $pricing['items'];
        $computedSubtotal = $pricing['subtotal'];
        $computedTax = $pricing['tax'];
        $computedShipping = $pricing['shipping'];
        $computedDiscount = $pricing['discount'];
        $computedTotal = $pricing['total'];
        $normalizedPromo = $pricing['promo_code'];

        // ពិនិត្យការទូទាត់ Bakong KHQR (Auto MD5 Verification + Replay/Amount/Account Guard) ឬ ABA Direct KHQR
        $paymentMethod = $request->input('payment_method', 'cash');
        $khqrMd5 = $request->filled('khqr_md5') ? strtolower(trim((string) $request->input('khqr_md5'))) : null;
        $paymentProof = $request->input('payment_proof');
        if ($paymentProof && !preg_match('#^(https?://|/)#i', trim((string) $paymentProof))) {
            $paymentProof = null;
        }
        $paymentStatus = 'pending';
        $bakongRef = $request->input('bakong_ref');
        $bakongSender = null;

        if (in_array($paymentMethod, ['khqr', 'aba_khqr', 'bakong_aba'])) {
            if ($khqrMd5) {
                // Fast-path replay guard before calling Bakong Open API
                $existingMd5Order = Order::where('khqr_md5', $khqrMd5)
                    ->where('status', '!=', 'cancelled')
                    ->first();
                if ($existingMd5Order) {
                    return response()->json([
                        'success' => false,
                        'status' => 'error',
                        'message' => "កូដ Bakong KHQR នេះត្រូវបានប្រើសម្រាប់ Order #{$existingMd5Order->id} រួចហើយ! សូមបង្កើត QR ថ្មី។",
                    ], 409);
                }

                $bakongCheck = $this->checkBakongMd5($khqrMd5);
                if ($bakongCheck['paid']) {
                    if (!$this->bakongPaymentMatchesOrder($bakongCheck, $computedTotal)) {
                        $paidDisp = ($bakongCheck['currency'] === 'KHR')
                            ? number_format((float) ($bakongCheck['amount'] ?? 0)) . ' KHR'
                            : '$' . number_format((float) ($bakongCheck['amount'] ?? 0), 2);
                        Log::warning('Bakong underpayment or currency mismatch rejected', [
                            'md5' => $khqrMd5,
                            'paid_amount' => $bakongCheck['amount'],
                            'paid_currency' => $bakongCheck['currency'],
                            'expected_usd' => $computedTotal,
                        ]);

                        return response()->json([
                            'success' => false,
                            'status' => 'error',
                            'message' => "ចំនួនទឹកប្រាក់ដែលបានបង់ ({$paidDisp}) មិនគ្រប់តម្លៃសរុប (\${$computedTotal}) នៃការបញ្ជាទិញនេះទេ! សូមផ្ទៀងផ្ទាត់ និងស្កេន QR ថ្មី។",
                        ], 422);
                    }

                    $paymentStatus = 'paid';
                    $bakongRef = $bakongCheck['ref'];
                    $bakongSender = $bakongCheck['sender'];
                }
            }

            if ($paymentStatus !== 'paid' && ($paymentProof || $request->boolean('paid_claimed') || $bakongRef)) {
                $paymentStatus = 'awaiting_approval';
            }
        }

        // Strict guard: never allow placing unpaid Bakong KHQR or proofless ABA KHQR orders
        if (in_array($paymentMethod, ['khqr', 'bakong_aba']) && $paymentStatus !== 'paid') {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'ការទូទាត់តាម Bakong KHQR មិនទាន់បានបង់ប្រាក់ទេ! សូមស្កេនបង់ប្រាក់ជាមុនសិន។',
            ], 422);
        }

        if ($paymentMethod === 'aba_khqr' && $paymentStatus !== 'paid' && empty($paymentProof)) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'សម្រាប់ ABA KHQR សូមភ្ជាប់រូបវិក្កយបត្របង់ប្រាក់ (Slip) ជាមុនសិន!',
            ], 422);
        }

        try {
            $txResult = DB::transaction(function () use (
                $request, $user, $paymentMethod, $paymentStatus, $paymentProof,
                $khqrMd5, $bakongRef, $verifiedItems, $computedSubtotal,
                $computedTax, $computedShipping, $computedDiscount, $computedTotal, $normalizedPromo
            ) {
                // Atomic lock-based replay check against concurrent duplicate submissions
                if ($khqrMd5) {
                    $dupMd5 = Order::where('khqr_md5', $khqrMd5)
                        ->where('status', '!=', 'cancelled')
                        ->lockForUpdate()
                        ->first();
                    if ($dupMd5) {
                        return [
                            'error' => "ប្រតិបត្តិការ Bakong KHQR នេះត្រូវបានកត់ត្រាសម្រាប់ Order #{$dupMd5->id} រួចហើយ។",
                            'code' => 409,
                        ];
                    }
                }

                if ($paymentStatus === 'paid' && $bakongRef) {
                    $dupRef = Order::where('bakong_ref', $bakongRef)
                        ->where('payment_status', 'paid')
                        ->where('status', '!=', 'cancelled')
                        ->lockForUpdate()
                        ->first();
                    if ($dupRef) {
                        return [
                            'error' => "លេខយោង Bakong Ref ({$bakongRef}) នេះត្រូវបានប្រើរួចហើយ! សូមបង្កើត QR ថ្មី។",
                            'code' => 409,
                        ];
                    }
                }

                $candidateId = $user ? $user->id : $request->input('user_id');
                $resolvedUserId = ($candidateId && \App\Models\User::where('id', $candidateId)->exists())
                    ? $candidateId
                    : null;

                $customerName = $request->input('customer_name')
                    ?? $request->input('name')
                    ?? ($user ? $user->name : null);

                // ២. បង្កើត Order ដោយប្រើតម្លៃដែលបានគណនាផ្ទៀងផ្ទាត់ពី Server
                $order = Order::create([
                    'user_id' => $resolvedUserId,
                    'customer_name' => $customerName,
                    'subtotal' => $computedSubtotal,
                    'tax' => $computedTax,
                    'shipping' => $computedShipping,
                    'discount' => $computedDiscount,
                    'promo_code' => $normalizedPromo ?: null,
                    'total' => $computedTotal,
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

                // ៣. បញ្ចូលផលិតផលទៅក្នុងតារាង Order Items តាមតម្លៃពិតពី Database
                foreach ($verifiedItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount' => $item['discount'],
                    ]);
                }

                // លុប Cart ចោលក្រោយទិញរួច
                if ($user) {
                    Cart::where('user_id', $user->id)->delete();
                }

                return ['order' => $order];
            });

            if (isset($txResult['error'])) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => $txResult['error'],
                ], $txResult['code'] ?? 409);
            }

            $order = $txResult['order'];

            // ៤. ផ្ញើសារទៅកាន់ Telegram Bot
            $this->sendTelegramOrderNotification($order, $verifiedItems, $bakongSender);

            // ៥. បញ្ជូនលទ្ធផលជោគជ័យត្រឡប់ទៅ React វិញ
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'payment_status' => $order->payment_status,
                'bakong_ref' => $order->bakong_ref,
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
     * Compute authoritative order subtotal, discount, total, and items directly from DB product prices.
     */
    private function computeVerifiedOrderTotals(array $items, float $tax, float $shipping, ?string $promoCode = null): array
    {
        $productIds = collect($items)->pluck('product_id')->unique()->values();
        $products = Product::whereIn('pro_id', $productIds)->get()->keyBy('pro_id');

        $computedSubtotal = 0.0;
        $verifiedItems = [];

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            if (!$product) {
                return ['error' => 'រកមិនឃើញទំនិញ #' . ($item['product_id'] ?? '?') . ' នៅក្នុងប្រព័ន្ធទេ។'];
            }

            $qty = max(1, (int) ($item['quantity'] ?? 1));
            $dbPrice = max(0.0, (float) $product->price);
            $dbDiscountPct = max(0.0, min(100.0, (float) ($product->discount ?? 0)));
            $rawDiscounted = $product->getRawOriginal('discounted_price');
            $effectiveUnit = ($rawDiscounted !== null && is_numeric($rawDiscounted) && (float) $rawDiscounted > 0)
                ? (float) $rawDiscounted
                : ($dbPrice * (1 - $dbDiscountPct / 100));

            $computedSubtotal += ($effectiveUnit * $qty);
            $verifiedItems[] = [
                'product_id' => $product->pro_id,
                'name' => $product->pro_name,
                'quantity' => $qty,
                'price' => round($dbPrice, 2),
                'discount' => round($dbDiscountPct, 2),
            ];
        }

        $computedSubtotal = round($computedSubtotal, 2);
        $tax = round(max(0.0, $tax), 2);
        $shipping = round(max(0.0, $shipping), 2);

        $normalizedPromo = strtoupper(trim((string) ($promoCode ?? '')));
        $allowedPromos = [
            'KHMER10' => 10,
            'WELCOME15' => 15,
            'DELICIOUS20' => 20,
        ];
        $promoRate = $allowedPromos[$normalizedPromo] ?? 0;
        $computedDiscount = $promoRate > 0 ? round($computedSubtotal * ($promoRate / 100), 2) : 0.0;
        $computedTotal = round(max(0.01, $computedSubtotal + $tax + $shipping - $computedDiscount), 2);

        return [
            'items' => $verifiedItems,
            'subtotal' => $computedSubtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'discount' => $computedDiscount,
            'promo_code' => $promoRate > 0 ? $normalizedPromo : null,
            'total' => $computedTotal,
        ];
    }

    /**
     * Verify paid amount and currency from NBC Bakong API cover the server-computed USD total.
     */
    private function bakongPaymentMatchesOrder(array $bakongCheck, float $orderTotalUsd): bool
    {
        $paidAmount = (float) ($bakongCheck['amount'] ?? 0);
        $paidCurrency = strtoupper((string) ($bakongCheck['currency'] ?? 'USD'));

        if ($paidCurrency === 'USD') {
            return ($paidAmount + 0.01) >= $orderTotalUsd;
        }

        if ($paidCurrency === 'KHR') {
            $expectedKhr = $orderTotalUsd * 4000;
            return $paidAmount >= ($expectedKhr * 0.98);
        }

        return false;
    }

    /**
     * Query NBC Bakong Open API to check whether a dynamic KHQR MD5 has been paid to this shop.
     */
    private function checkBakongMd5(string $md5): array
    {
        $md5 = strtolower(trim($md5));
        if (!preg_match('/^[a-f0-9]{32}$/', $md5)) {
            return ['paid' => false, 'ref' => null, 'sender' => null, 'amount' => null, 'currency' => null, 'message' => 'Invalid MD5'];
        }

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
                    $expectedAccount = strtolower(trim((string) (config('services.bakong.account_id') ?: 'somnang_yorn@bkrt')));
                    $toAccount = strtolower(trim((string) ($tx['toAccountId'] ?? '')));

                    if ($expectedAccount !== '' && $toAccount !== $expectedAccount) {
                        Log::warning('Bakong receiver account mismatch rejected', [
                            'md5' => $md5,
                            'expected' => $expectedAccount,
                            'actual' => $toAccount,
                        ]);
                        return [
                            'paid' => false,
                            'ref' => null,
                            'sender' => null,
                            'amount' => null,
                            'currency' => null,
                            'message' => 'គណនីទទួលប្រាក់ KHQR មិនត្រូវគ្នាជាមួយគណនីហាងឡើយ',
                        ];
                    }

                    return [
                        'paid' => true,
                        'ref' => $tx['externalRef'] ?? $tx['hash'] ?? $md5,
                        'sender' => $tx['fromAccountId'] ?? null,
                        'to_account' => $toAccount,
                        'amount' => isset($tx['amount']) ? (float)$tx['amount'] : null,
                        'currency' => strtoupper((string) ($tx['currency'] ?? 'USD')),
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
            $isAbaDirect = $order->payment_method === 'aba_khqr';
            $isKhqr = in_array($order->payment_method, ['khqr', 'aba_khqr', 'bakong_aba']);
            $abaAccount = config('services.bakong.aba_account', '007842694');
            $bakongId = config('services.bakong.account_id', 'somnang_yorn@bkrt');

            if ($isAbaDirect) {
                $paymentMethodLabel = "🏦 ABA Direct KHQR (YORN SOMNANG • {$abaAccount})";
            } elseif ($isKhqr) {
                $paymentMethodLabel = "⚡ Bakong KHQR ({$bakongId})";
            } else {
                $paymentMethodLabel = '💵 សាច់ប្រាក់សុទ្ធ (Cash on Delivery)';
            }

            if ($order->payment_status === 'paid' && $order->bakong_ref) {
                $paymentStatusText = "✅ បង់លុយពិតរួចរាល់ (Bakong Verified)\n🧾 Ref: {$order->bakong_ref}" . ($bakongSender ? "\n🏦 ផ្ញើពី: {$bakongSender}" : "");
            } elseif ($order->payment_status === 'awaiting_approval') {
                $dest = $isAbaDirect ? "ABA {$abaAccount}" : "Bakong {$bakongId}";
                $paymentStatusText = "🧾 អតិថិជនបានបង់លុយចូល {$dest} (រង់ចាំ Admin ពិនិត្យ Approve)"
                    . ($order->bakong_ref ? "\n🔖 យោង/អ្នកផ្ញើ: {$order->bakong_ref}" : "");
            } elseif ($order->payment_status === 'paid') {
                $paymentStatusText = "✅ Already Paid (បានបង់ប្រាក់រួចរាល់)"
                    . ($order->bakong_ref ? "\n🔖 យោង: {$order->bakong_ref}" : "");
            } else {
                $paymentStatusText = $isKhqr
                    ? "⏳ Not Paid Yet (មិនទាន់បង់ប្រាក់ — រង់ចាំផ្ទៀងផ្ទាត់ KHQR)"
                    : "⏳ Not Paid Yet (💵 ទូទាត់ពេលដឹក)";
            }

            $requestedAt = ($order->created_at ?? now())->timezone('Asia/Phnom_Penh')->format('d M Y, h:i A');
            $paymentWhen = ($order->updated_at ?? $order->created_at ?? now())->timezone('Asia/Phnom_Penh')->format('d M Y, h:i A');
            $phone = $order->phone ?: 'មិនទាន់បញ្ជាក់';
            $address = $order->shipping_address ?: 'ភ្នំពេញ';
            $totalUSD = number_format((float)$order->total, 2);
            $totalKHR = number_format((float)$order->total * 4100);
            $adminBase = rtrim(config('app.url') ?: env('APP_URL', 'https://backend-laravel-shop.vercel.app'), '/');
            $adminUrl = "{$adminBase}/orders?payment_status=paid_pending_approval";

            $headerLine = ($order->payment_status === 'paid')
                ? "🎉 ភ្ញៀវបានបង់លុយរួចរាល់ (Request #{$order->id})!\n✅ Already Paid — រង់ចាំ Admin Approve ដើម្បីដឹកជញ្ជូន\n\n"
                : "🛒 មានសំណើបញ្ជាទិញថ្មី (Request #{$order->id})!\n⏳ ស្ថានភាព: PENDING — រង់ចាំការពិនិត្យ\n\n";

            $message = $headerLine
                     . "👤 អតិថិជន: {$customerName}\n"
                     . "📞 ទូរស័ព្ទ: {$phone}\n"
                     . "📍 ទីតាំងដឹក: {$address}\n"
                     . "🕒 ពេលវេលាស្នើទិញ: {$requestedAt}\n"
                     . "💳 តាមរយៈ (Via): {$paymentMethodLabel}\n"
                     . "📌 ស្ថានភាពបង់ប្រាក់: {$paymentStatusText}\n"
                     . (in_array($order->payment_status, ['paid', 'awaiting_approval']) ? "⏱ ពេលវេលាបង់/ផ្ញើសំណើ: {$paymentWhen}\n" : "")
                     . "\n"
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

            $response = Http::timeout(3)->post("https://api.telegram.org/bot{$token}/sendMessage", $payload);
            if (!$response->successful()) {
                Log::error('Telegram API sendMessage Failed: ' . $response->body());
            }

            // Send receipt slip photo directly into Telegram chat if attached
            if ($order->payment_proof) {
                Http::timeout(3)->post("https://api.telegram.org/bot{$token}/sendPhoto", [
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
                'preparing' => '📦 កំពុងវេចខ្ចប់ទំនិញ (Packing)',
                'delivering' => '🛵 កំពុងដឹកជញ្ជូន (Delivering)',
                'completed' => '✅ ដឹកជញ្ជូនជោគជ័យ (Completed)',
                default => strtoupper($order->status),
            };
            $viaChannel = match ($order->payment_method) {
                'aba_khqr' => 'ABA KHQR',
                'khqr', 'bakong_aba' => 'Bakong KHQR',
                default => strtoupper($order->payment_method ?: 'Cash (COD)'),
            };
            $paidLabel = match ($order->payment_status) {
                'paid' => '✅ Already Paid (បង់រួច)',
                'awaiting_approval' => '🧾 Paid Claimed (Verify)',
                'refunded' => '↩️ Refunded',
                default => '⏳ Not Paid Yet',
            };
            $when = ($order->updated_at ?? $order->created_at ?? now())->timezone('Asia/Phnom_Penh')->format('d M Y, h:i A');

            $text = "📢 បច្ចុប្បន្នភាព Request #{$order->id} ({$source})\n"
                  . "👤 អតិថិជន: {$customerName} ({$order->phone})\n"
                  . "💳 ការបង់ប្រាក់: {$paidLabel}\n"
                  . "🏦 តាមរយៈ (Via): {$viaChannel}\n"
                  . "🕒 ពេលវេលា: {$when}\n"
                  . "🚚 ស្ថានភាពថ្មី: {$statusEmoji}\n"
                  . "💰 តម្លៃសរុប: \$" . number_format((float)$order->total, 2);

            Http::timeout(3)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            Log::warning('Telegram approval notify error: ' . $e->getMessage());
        }
    }
}