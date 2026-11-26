@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-shopping-bag text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Customer Order Requests</h1>
                    <p class="text-gray-400 text-sm mt-0.5">ពិនិត្យស្ថានភាពសំណើបញ្ជាទិញ ៖ បង់ប្រាក់ហើយឬនៅ តាមរយៈវិធីអ្វី និងពេលណា</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('reports.sales') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl font-medium text-sm text-gray-300 bg-gray-800 hover:bg-gray-700 border border-gray-700 transition-all">
                <i class="fas fa-chart-line"></i>
                <span>Sales Reports</span>
            </a>
        </div>
    </div>

    <!-- Paid & Pending Admin Delivery Approval Alert -->
    @if(($counts['paid_pending_approval'] ?? 0) > 0)
        <div class="mb-5 rounded-2xl bg-gradient-to-r from-emerald-950/90 via-teal-950/80 to-cyan-950/80 border border-emerald-500/40 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center text-emerald-300 flex-shrink-0">
                    <i class="fas fa-qrcode text-lg"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-emerald-300">
                        💳 មានការបញ្ជាទិញចំនួន {{ $counts['paid_pending_approval'] }} បានបង់ប្រាក់តាម KHQR / ភ្ជាប់វិក្កយបត្រ និងកំពុងរង់ចាំ Admin Approve ដើម្បីដឹកជញ្ជូន!
                    </div>
                    <div class="text-xs text-gray-300 mt-0.5">
                        សូមពិនិត្យលុយចូល Bakong ឬរូបវិក្កយបត្រ រួចចុចប៊ូតុង <strong class="text-white">«Approve & ដឹកជញ្ជូន»</strong>។
                    </div>
                </div>
            </div>
            <a href="{{ route('orders.index', ['payment_status' => 'paid_pending_approval']) }}"
               class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-bold text-xs whitespace-nowrap transition-all shadow-md">
                🔍 មើលបញ្ជីរង់ចាំ Approve ({{ $counts['paid_pending_approval'] }})
            </a>
        </div>
    @endif

    <!-- Status Tabs / Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-none">
        <a href="{{ route('orders.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ !request('status') && !request('payment_status') ? 'theme-bg-gradient text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            All Requests ({{ $counts['all'] }})
        </a>
        <a href="{{ route('orders.index', ['payment_status' => 'paid_pending_approval']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('payment_status') === 'paid_pending_approval' ? 'bg-emerald-500 text-gray-950 shadow-md font-bold' : 'bg-emerald-950/60 text-emerald-300 hover:bg-emerald-900/60 border border-emerald-700/50' }}">
            💳 Already Paid / Verify ({{ $counts['paid_pending_approval'] ?? 0 }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'pending' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            ⏳ Pending ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'preparing']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'preparing' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            📦 Packing ({{ $counts['preparing'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'delivering']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'delivering' ? 'bg-cyan-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            🛵 Delivering ({{ $counts['delivering'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'completed' ? 'bg-emerald-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            ✅ Completed ({{ $counts['completed'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'cancelled' ? 'bg-red-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            ❌ Cancelled ({{ $counts['cancelled'] }})
        </a>
    </div>

    <!-- Search & Auto-Filters -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800 p-4 mb-6 shadow-xl">
        <form action="{{ route('orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">

            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" autocomplete="off"
                           placeholder="វាយស្វែងរក Order #, ឈ្មោះ, ទូរស័ព្ទ, Bakong ref... (Auto)" 
                           class="w-full bg-gray-950 border border-gray-800 rounded-xl py-2 pl-9 pr-4 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
            </div>

            <div>
                <select name="payment_status" class="w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 cursor-pointer">
                    <option value="all">All Payment Statuses</option>
                    <option value="paid_pending_approval" {{ request('payment_status') === 'paid_pending_approval' ? 'selected' : '' }}>💳 Paid & Waiting Approve</option>
                    <option value="awaiting_approval" {{ request('payment_status') === 'awaiting_approval' ? 'selected' : '' }}>🧾 Slip Submitted (Verify)</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>⏳ Payment Pending</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>✅ Paid</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>↩️ Refunded</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('orders.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white rounded-xl border border-gray-700 text-sm font-medium transition-colors" title="Reset Filters">
                    <i class="fas fa-undo-alt text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table (Clean Essential View + Detail Modal) -->
    <div data-live-region="orders-table" class="bg-gray-900/80 rounded-2xl border border-gray-800 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-950/80 text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-800">
                        <th class="py-3 px-4 w-32 whitespace-nowrap">Request #</th>
                        <th class="py-3 px-4 whitespace-nowrap">Customer</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Payment</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Total</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                    @php $ordersMap = []; @endphp
                    @forelse($orders as $order)
                        @php
                            $reqTime = $order->created_at ? $order->created_at->timezone('Asia/Phnom_Penh') : null;
                            $payTime = ($order->updated_at ?? $order->created_at)?->timezone('Asia/Phnom_Penh');
                            $displayWhen = in_array($order->payment_status, ['paid', 'awaiting_approval']) ? $payTime : $reqTime;
                            $itemCount = $order->orderItems->sum('quantity') ?: $order->orderItems->count();
                            $customerDisplay = $order->customer_name ?: (optional($order->user)->name ?? ($order->email ?: 'Guest Customer'));
                            $ordersMap[$order->id] = [
                                'id' => $order->id,
                                'created_at' => $order->created_at ? $order->created_at->toIso8601String() : null,
                                'created_kh' => $reqTime ? $reqTime->format('d M Y, h:i A') : 'N/A',
                                'created_ago' => $reqTime ? $reqTime->diffForHumans() : '',
                                'payment_kh' => $displayWhen ? $displayWhen->format('d M Y, h:i A') : 'N/A',
                                'customer_name' => $customerDisplay,
                                'email' => $order->email ?: (optional($order->user)->email ?? ''),
                                'phone' => $order->phone ?: 'No phone',
                                'shipping_address' => $order->shipping_address ?: 'Phnom Penh, Cambodia',
                                'notes' => $order->notes ?? '',
                                'payment_method' => $order->payment_method ?: 'cash',
                                'payment_status' => $order->payment_status ?: 'pending',
                                'payment_proof' => $order->payment_proof ?? null,
                                'bakong_ref' => $order->bakong_ref ?? null,
                                'khqr_md5' => $order->khqr_md5 ?? null,
                                'status' => $order->status ?: 'pending',
                                'subtotal' => (float) ($order->subtotal ?? 0),
                                'tax' => (float) ($order->tax ?? 0),
                                'shipping' => (float) ($order->shipping ?? 0),
                                'discount' => (float) ($order->discount ?? 0),
                                'promo_code' => $order->promo_code ?? '',
                                'total' => (float) ($order->total ?? 0),
                                'update_url' => route('orders.update-status', $order->id),
                                'approve_url' => route('orders.approve', $order->id),
                                'items' => $order->orderItems->map(fn ($item) => [
                                    'name' => optional($item->product)->pro_name ?? optional($item->product)->name ?? ('Product #' . $item->product_id),
                                    'quantity' => (int) $item->quantity,
                                    'price' => (float) $item->price,
                                    'discount' => (float) ($item->discount ?? 0),
                                ])->values(),
                            ];
                        @endphp
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            <!-- Request ID & Short Timestamp -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-mono font-bold text-sm text-cyan-400 block">#{{ $order->id }}</span>
                                @if($reqTime)
                                    <span class="text-[11px] text-gray-400 block mt-0.5" title="{{ $reqTime->format('d M Y, h:i:s A') }}">
                                        {{ $reqTime->format('d M, h:i A') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Customer Essentials -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-semibold text-white">{{ $customerDisplay }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $order->phone ?: 'No phone' }}</div>
                            </td>

                            <!-- Essential Payment: Paid? • Via • When -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div>
                                    @if($order->payment_status === 'paid')
                                        <span style="background:#059669; color:#ffffff;" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold shadow-sm">
                                            ✅ Already Paid
                                        </span>
                                    @elseif($order->payment_status === 'awaiting_approval')
                                        <span style="background:#0284c7; color:#ffffff;" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold shadow-sm">
                                            🧾 Verify Claim
                                        </span>
                                    @elseif($order->payment_status === 'refunded')
                                        <span style="background:#e11d48; color:#ffffff;" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold shadow-sm">
                                            ↩️ Refunded
                                        </span>
                                    @else
                                        <span style="background:#d97706; color:#ffffff;" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold shadow-sm">
                                            ⏳ Not Paid
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-gray-400 mt-1">
                                    @if($order->payment_method === 'aba_khqr')
                                        🏦 ABA
                                    @elseif(in_array($order->payment_method, ['khqr', 'bakong_aba']))
                                        ⚡ Bakong
                                    @else
                                        💵 Cash
                                    @endif
                                    @if($displayWhen)
                                        • {{ $displayWhen->format('d M, h:i A') }}
                                    @endif
                                </div>
                            </td>

                            <!-- Request Status Quick Change -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" 
                                            class="text-xs font-semibold px-2.5 py-1 rounded-xl cursor-pointer focus:outline-none transition-colors border {{ 
                                                $order->status === 'completed' ? 'bg-emerald-950 text-emerald-400 border-emerald-800' : 
                                                ($order->status === 'delivering' ? 'bg-cyan-950 text-cyan-400 border-cyan-800' : 
                                                ($order->status === 'preparing' ? 'bg-amber-950 text-amber-400 border-amber-800' : 
                                                ($order->status === 'cancelled' ? 'bg-red-950 text-red-400 border-red-800' : 
                                                'bg-indigo-950 text-indigo-400 border-indigo-800'))) 
                                            }}">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>📦 Packing</option>
                                        <option value="delivering" {{ $order->status === 'delivering' ? 'selected' : '' }}>🛵 Delivering</option>
                                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Total & Item Count -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="font-mono font-bold text-white">${{ number_format($order->total, 2) }}</div>
                                <div class="text-[11px] text-gray-400">{{ $itemCount }} {{ $itemCount === 1 ? 'item' : 'items' }}</div>
                            </td>

                            <!-- Actions: Detail Button + Quick Approve -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                            onclick="openOrderDetail({{ $order->id }})"
                                            style="background:#2563eb; color:#ffffff;"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:opacity-90 text-xs font-bold shadow-sm transition-all cursor-pointer">
                                        <i class="fas fa-eye"></i>
                                        <span>Detail</span>
                                    </button>
                                    @if($order->status === 'pending')
                                        <form action="{{ route('orders.approve', $order->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('អនុម័ត និងបញ្ជូន Order #{{ $order->id }} ទៅដឹកជញ្ជូន?')"
                                                    style="background:#059669; color:#ffffff;"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg hover:opacity-90 text-xs font-bold shadow-sm transition-all cursor-pointer"
                                                    title="Approve & Deliver">
                                                <i class="fas fa-check"></i>
                                                <span>Approve</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gray-800/50 flex items-center justify-center text-gray-600">
                                    <i class="fas fa-box-open text-2xl"></i>
                                </div>
                                <p class="text-base font-semibold text-gray-300">No orders found</p>
                                <p class="text-xs text-gray-500 mt-1">New incoming customer requests will display here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-gray-800">
                {{ $orders->links() }}
            </div>
        @endif
        <script type="application/json" id="orders-map-json">@json($ordersMap)</script>
    </div>

</div>

<!-- Order Full Detail Modal -->
<div id="orderDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 p-4 overflow-y-auto" onclick="closeOrderDetail()">
    <div id="orderDetailCard"
         style="background-color:#0f172a; color:#f8fafc; border:1px solid #334155;"
         class="rounded-2xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl my-auto max-h-[92vh] overflow-y-auto"
         onclick="event.stopPropagation()">
        <div class="flex items-start justify-between gap-4 pb-4" style="border-bottom:1px solid #1e293b;">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h3 id="detailOrderTitle" class="text-xl font-bold" style="color:#ffffff;">Order #</h3>
                    <span id="detailPaidBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"></span>
                </div>
                <p id="detailOrderTime" class="text-xs mt-1" style="color:#94a3b8;"></p>
            </div>
            <button type="button" onclick="closeOrderDetail()" class="text-xl px-2 py-0.5 rounded-lg" style="color:#94a3b8;">&times;</button>
        </div>

        <!-- Customer & Payment Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-4">
            <div class="rounded-xl p-3.5" style="background-color:#1e293b; border:1px solid #334155;">
                <div class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#38bdf8;">👤 Customer & Delivery</div>
                <div id="detailCustomerBody" class="space-y-1.5 text-xs" style="color:#e2e8f0;"></div>
            </div>

            <div class="rounded-xl p-3.5" style="background-color:#1e293b; border:1px solid #334155;">
                <div class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#34d399;">💳 Payment Info</div>
                <div id="detailPaymentBody" class="space-y-1.5 text-xs" style="color:#e2e8f0;"></div>

                <form id="detailPaymentForm" method="POST" class="mt-3 flex items-center gap-2">
                    @csrf
                    <select id="detailPaymentSelect" name="payment_status"
                            style="background-color:#0f172a; color:#f8fafc; border:1px solid #475569;"
                            class="text-xs font-semibold px-2.5 py-1.5 rounded-lg flex-1">
                        <option value="pending">⏳ Not Paid Yet</option>
                        <option value="awaiting_approval">🧾 Verify Claim</option>
                        <option value="paid">✅ Already Paid</option>
                        <option value="refunded">↩️ Refunded</option>
                    </select>
                    <button type="submit" style="background:#0284c7; color:#ffffff;" class="px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer">
                        Save
                    </button>
                </form>

                <div id="detailSlipBox" class="hidden mt-3">
                    <button type="button" id="detailSlipBtn" style="background:#4338ca; color:#ffffff;"
                            class="w-full px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer">
                        🧾 View Payment Slip Image
                    </button>
                </div>
            </div>
        </div>

        <!-- Items Ordered Table -->
        <div class="rounded-xl p-3.5 mb-4" style="background-color:#1e293b; border:1px solid #334155;">
            <div class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#fbbf24;">📦 Items Ordered</div>
            <div id="detailItemsList" class="divide-y divide-slate-700/70 text-xs"></div>
            <div id="detailTotalsBox" class="mt-3 pt-2.5 space-y-1 text-xs" style="border-top:1px solid #334155;"></div>
        </div>

        <!-- Footer Controls -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3" style="border-top:1px solid #1e293b;">
            <form id="detailStatusForm" method="POST" class="flex items-center gap-2">
                @csrf
                <select id="detailStatusSelect" name="status"
                        style="background-color:#1e293b; color:#f8fafc; border:1px solid #475569;"
                        class="text-xs font-semibold px-2.5 py-1.5 rounded-lg">
                    <option value="pending">⏳ Pending</option>
                    <option value="preparing">📦 Packing</option>
                    <option value="delivering">🛵 Delivering</option>
                    <option value="completed">✅ Completed</option>
                    <option value="cancelled">❌ Cancelled</option>
                </select>
                <button type="submit" style="background:#334155; color:#ffffff;" class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer">
                    Update Status
                </button>
            </form>

            <div class="flex items-center gap-2 flex-wrap">
                <form id="detailApproveForm" method="POST" class="hidden">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('អនុម័តការបង់ប្រាក់ និងបញ្ជូន Order នេះទៅដឹកជញ្ជូន?')"
                            style="background:#059669; color:#ffffff;"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer">
                        ✅ Approve & ដឹក
                    </button>
                </form>
                <button type="button" id="detailPrintBtn"
                        style="background:#334155; color:#ffffff;"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer">
                    🖨 Print Receipt
                </button>
                <button type="button" onclick="closeOrderDetail()"
                        style="background:#1e293b; color:#cbd5e1; border:1px solid #475569;"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Slip Modal -->
<div id="slipModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/80 p-4" onclick="closeSlipModal()">
    <div class="bg-gray-900 border border-gray-700 rounded-2xl max-w-md w-full p-4 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-3">
            <h3 id="slipModalTitle" class="text-white font-bold text-base">🧾 វិក្កយបត្របង់ប្រាក់</h3>
            <button type="button" onclick="closeSlipModal()" class="text-gray-400 hover:text-white text-lg px-2">&times;</button>
        </div>
        <div class="bg-black rounded-xl overflow-hidden flex items-center justify-center max-h-[70vh]">
            <img id="slipModalImg" src="" alt="Payment Slip" class="max-h-[70vh] w-auto object-contain">
        </div>
        <div class="mt-3 flex justify-end gap-2">
            <a id="slipModalLink" href="#" target="_blank" class="px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold">
                បើករូបភាពពេញ ↗
            </a>
            <button type="button" onclick="closeSlipModal()" class="px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-semibold">
                បិទ
            </button>
        </div>
    </div>
</div>

<!-- Printable Receipt Template (Hidden until printed) -->
<div id="receiptPrintArea" class="hidden">
    <div style="width: 80mm; font-family: monospace; font-size: 12px; padding: 10px; color: #000; background: #fff;" id="receiptContent">
        <!-- Injected via JavaScript -->
    </div>
</div>

<script>
window.__ORDERS_MAP = @json($ordersMap);

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function openOrderDetail(orderId) {
    const order = window.__ORDERS_MAP[orderId];
    if (!order) return;

    document.getElementById('detailOrderTitle').textContent = 'Request #' + order.id;
    document.getElementById('detailOrderTime').textContent = 'Requested: ' + order.created_kh + (order.created_ago ? ' (' + order.created_ago + ')' : '');

    const paidBadge = document.getElementById('detailPaidBadge');
    if (order.payment_status === 'paid') {
        paidBadge.textContent = '✅ Already Paid';
        paidBadge.style.background = '#059669';
        paidBadge.style.color = '#ffffff';
    } else if (order.payment_status === 'awaiting_approval') {
        paidBadge.textContent = '🧾 Verify Claim';
        paidBadge.style.background = '#0284c7';
        paidBadge.style.color = '#ffffff';
    } else if (order.payment_status === 'refunded') {
        paidBadge.textContent = '↩️ Refunded';
        paidBadge.style.background = '#e11d48';
        paidBadge.style.color = '#ffffff';
    } else {
        paidBadge.textContent = '⏳ Not Paid Yet';
        paidBadge.style.background = '#d97706';
        paidBadge.style.color = '#ffffff';
    }

    document.getElementById('detailCustomerBody').innerHTML =
        '<div><strong>Name:</strong> ' + escapeHtml(order.customer_name) + '</div>' +
        '<div><strong>Phone:</strong> ' + escapeHtml(order.phone) + '</div>' +
        (order.email ? '<div><strong>Email:</strong> ' + escapeHtml(order.email) + '</div>' : '') +
        '<div><strong>Address:</strong> ' + escapeHtml(order.shipping_address) + '</div>' +
        (order.notes ? '<div><strong>Notes:</strong> ' + escapeHtml(order.notes) + '</div>' : '');

    let viaLabel = '💵 Cash (COD)';
    if (order.payment_method === 'aba_khqr') viaLabel = '🏦 ABA KHQR';
    else if (order.payment_method === 'khqr' || order.payment_method === 'bakong_aba') viaLabel = '⚡ Bakong KHQR';

    document.getElementById('detailPaymentBody').innerHTML =
        '<div><strong>Via Channel:</strong> ' + escapeHtml(viaLabel) + '</div>' +
        '<div><strong>Time:</strong> ' + escapeHtml(order.payment_kh) + '</div>' +
        (order.bakong_ref ? '<div><strong>Ref / Sender:</strong> <span class="font-mono" style="color:#6ee7b7;">' + escapeHtml(order.bakong_ref) + '</span></div>' : '') +
        (order.khqr_md5 ? '<div><strong>KHQR MD5:</strong> <span class="font-mono text-[11px]" style="color:#94a3b8;">' + escapeHtml(order.khqr_md5) + '</span></div>' : '');

    document.getElementById('detailPaymentForm').action = order.update_url;
    document.getElementById('detailPaymentSelect').value = order.payment_status || 'pending';

    const slipBox = document.getElementById('detailSlipBox');
    if (order.payment_proof) {
        slipBox.classList.remove('hidden');
        document.getElementById('detailSlipBtn').onclick = function() {
            openSlipModal(order.payment_proof, order.id);
        };
    } else {
        slipBox.classList.add('hidden');
    }

    const itemsHtml = (order.items || []).map(function(item) {
        const lineTotal = (Number(item.price || 0) * (1 - Number(item.discount || 0) / 100) * Number(item.quantity || 1));
        return '<div class="py-2 flex items-center justify-between gap-3">' +
            '<div><span class="font-bold" style="color:#38bdf8;">' + item.quantity + 'x</span> ' + escapeHtml(item.name) +
            (item.discount > 0 ? ' <span class="text-[10px]" style="color:#34d399;">(-' + item.discount + '%)</span>' : '') + '</div>' +
            '<div class="font-mono" style="color:#f8fafc;">$' + lineTotal.toFixed(2) + '</div>' +
            '</div>';
    }).join('');
    document.getElementById('detailItemsList').innerHTML = itemsHtml || '<div class="py-2" style="color:#94a3b8;">No items listed</div>';

    document.getElementById('detailTotalsBox').innerHTML =
        '<div class="flex justify-between" style="color:#cbd5e1;"><span>Subtotal</span><span class="font-mono">$' + Number(order.subtotal || 0).toFixed(2) + '</span></div>' +
        (order.shipping > 0 ? '<div class="flex justify-between" style="color:#cbd5e1;"><span>Shipping</span><span class="font-mono">$' + Number(order.shipping).toFixed(2) + '</span></div>' : '') +
        (order.discount > 0 ? '<div class="flex justify-between" style="color:#34d399;"><span>Discount' + (order.promo_code ? ' (' + escapeHtml(order.promo_code) + ')' : '') + '</span><span class="font-mono">-$' + Number(order.discount).toFixed(2) + '</span></div>' : '') +
        '<div class="flex justify-between font-bold text-sm pt-1" style="color:#ffffff;"><span>Total</span><span class="font-mono" style="color:#38bdf8;">$' + Number(order.total || 0).toFixed(2) + '</span></div>';

    document.getElementById('detailStatusForm').action = order.update_url;
    document.getElementById('detailStatusSelect').value = order.status || 'pending';

    const approveForm = document.getElementById('detailApproveForm');
    if (order.status === 'pending') {
        approveForm.action = order.approve_url;
        approveForm.classList.remove('hidden');
    } else {
        approveForm.classList.add('hidden');
    }

    document.getElementById('detailPrintBtn').onclick = function() {
        printOrderReceipt(order.id);
    };

    const modal = document.getElementById('orderDetailModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeOrderDetail() {
    const modal = document.getElementById('orderDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openSlipModal(url, orderId) {
    document.getElementById('slipModalTitle').textContent = '🧾 វិក្កយបត្របង់ប្រាក់ — Order #' + orderId;
    document.getElementById('slipModalImg').src = url;
    document.getElementById('slipModalLink').href = url;
    const modal = document.getElementById('slipModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSlipModal() {
    const modal = document.getElementById('slipModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function printOrderReceipt(orderOrId) {
    const order = typeof orderOrId === 'object' ? orderOrId : window.__ORDERS_MAP[orderOrId];
    if (!order) return;

    const rawItems = order.items || order.order_items || [];
    const itemsHtml = rawItems.map(item => {
        const name = item.name || (item.product ? (item.product.pro_name || item.product.name) : 'Item');
        const total = (Number(item.price || 0) * (1 - Number(item.discount || 0) / 100) * Number(item.quantity || 1));
        return `
        <tr style="border-bottom: 1px dashed #ccc;">
            <td style="padding: 4px 0;">${item.quantity}x ${escapeHtml(name)}</td>
            <td style="text-align: right; padding: 4px 0;">$${total.toFixed(2)}</td>
        </tr>`;
    }).join('');

    const receiptHtml = `
        <div style="text-align: center; border-bottom: 1px solid #000; padding-bottom: 8px; margin-bottom: 8px;">
            <h2 style="margin: 0; font-size: 18px; font-weight: bold;">LuckShop</h2>
            <p style="margin: 2px 0; font-size: 11px;">Phnom Penh, Cambodia</p>
            <p style="margin: 2px 0; font-size: 11px;">https://luckshop-kh.vercel.app</p>
            <h3 style="margin: 6px 0 0 0; font-size: 14px;">ORDER RECEIPT #${order.id}</h3>
        </div>
        <div style="font-size: 11px; margin-bottom: 8px;">
            <div>Date: ${order.created_kh || (order.created_at ? new Date(order.created_at).toLocaleString('en-US', { timeZone: 'Asia/Phnom_Penh' }) : '')}</div>
            <div>Customer: ${escapeHtml(order.customer_name || (order.user ? order.user.name : (order.email || 'Guest')))}</div>
            <div>Phone: ${escapeHtml(order.phone || 'N/A')}</div>
            <div>Payment: ${escapeHtml((order.payment_method || 'CASH').toUpperCase())} (${order.payment_status === 'paid' ? 'ALREADY PAID' : escapeHtml((order.payment_status || 'not paid').toUpperCase())})</div>
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 8px;">
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th style="text-align: left; padding: 4px 0;">Item</th>
                    <th style="text-align: right; padding: 4px 0;">Total</th>
                </tr>
            </thead>
            <tbody>
                ${itemsHtml}
            </tbody>
        </table>
        <div style="border-top: 1px solid #000; padding-top: 6px; font-size: 12px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                <span>Subtotal:</span>
                <span>$${parseFloat(order.subtotal || 0).toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; margin-top: 4px; border-top: 1px dashed #000; padding-top: 4px;">
                <span>GRAND TOTAL:</span>
                <span>$${parseFloat(order.total || 0).toFixed(2)}</span>
            </div>
        </div>
        <div style="text-align: center; margin-top: 15px; font-size: 11px; border-top: 1px dashed #000; padding-top: 8px;">
            <p style="margin: 0;">Thank you for shopping with us!</p>
            <p style="margin: 2px 0;">សូមអរគុណ និងសូមអញ្ជើញមកម្ដងទៀត!</p>
        </div>
    `;

    const printWin = window.open('', '', 'width=400,height=600');
    printWin.document.write('<html><head><title>Receipt #' + order.id + '</title></head><body style="margin:0; padding:10px;">' + receiptHtml + '</body></html>');
    printWin.document.close();
    printWin.focus();
    setTimeout(() => {
        printWin.print();
        printWin.close();
    }, 250);
}
</script>
@endsection
