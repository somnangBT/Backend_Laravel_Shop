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
            <a href="{{ route('reports.sales') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl font-medium text-sm text-gray-300 bg-gray-800 hover:bg-gray-700 border border-gray-700 transition-colors">
                <i class="fas fa-chart-line"></i>
                <span>Sales Reports</span>
            </a>
        </div>
    </div>

    <!-- Pending Delivery Approval Alert -->
    @if(($counts['paid_pending_approval'] ?? 0) > 0)
        <div class="mb-5 rounded-xl bg-gray-900 border border-gray-700 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <div class="text-sm font-semibold text-gray-100">
                    មានការបញ្ជាទិញចំនួន {{ $counts['paid_pending_approval'] }} បានបង់ប្រាក់តាម KHQR / ភ្ជាប់វិក្កយបត្រ និងកំពុងរង់ចាំ Approve
                </div>
                <div class="text-xs text-gray-400 mt-0.5">
                    សូមពិនិត្យការទូទាត់ រួចចុច Approve ដើម្បីដឹកជញ្ជូន។
                </div>
            </div>
            <a href="{{ route('orders.index', ['payment_status' => 'paid_pending_approval']) }}"
               class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs whitespace-nowrap transition-colors">
                មើលបញ្ជីរង់ចាំ ({{ $counts['paid_pending_approval'] }})
            </a>
        </div>
    @endif

    <!-- Status Tabs -->
    <div id="orders-status-tabs" class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-none">
        <a href="{{ route('orders.index') }}" class="px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ !request('status') && !request('payment_status') ? 'bg-gray-700 text-white border border-gray-600' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            All Orders ({{ $counts['all'] }})
        </a>
        <a href="{{ route('orders.index', ['payment_status' => 'paid_pending_approval']) }}" class="px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request('payment_status') === 'paid_pending_approval' ? 'bg-emerald-700 text-white border border-emerald-600' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Waiting Approval ({{ $counts['paid_pending_approval'] ?? 0 }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request('status') === 'pending' ? 'bg-gray-700 text-white border border-gray-600' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Pending ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'preparing']) }}" class="px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request('status') === 'preparing' ? 'bg-gray-700 text-white border border-gray-600' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Packing ({{ $counts['preparing'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'delivering']) }}" class="px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request('status') === 'delivering' ? 'bg-gray-700 text-white border border-gray-600' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Delivering ({{ $counts['delivering'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request('status') === 'completed' ? 'bg-gray-700 text-white border border-gray-600' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Completed ({{ $counts['completed'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" class="px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ request('status') === 'cancelled' ? 'bg-gray-700 text-white border border-gray-600' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Cancelled ({{ $counts['cancelled'] }})
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="bg-gray-900/80 rounded-xl border border-gray-800 p-4 mb-6">
        <form action="{{ route('orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">

            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" autocomplete="off"
                           placeholder="Search Order #, customer name, phone, ref..." 
                           class="w-full bg-gray-950 border border-gray-800 rounded-lg py-2 pl-9 pr-4 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-600">
                </div>
            </div>

            <div>
                <select name="payment_status" class="w-full bg-gray-950 border border-gray-800 rounded-lg py-2 px-3 text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-gray-600 cursor-pointer">
                    <option value="all">All Payment Statuses</option>
                    <option value="paid_pending_approval" {{ request('payment_status') === 'paid_pending_approval' ? 'selected' : '' }}>Paid & Waiting Approve</option>
                    <option value="awaiting_approval" {{ request('payment_status') === 'awaiting_approval' ? 'selected' : '' }}>Slip Submitted (Verify)</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Payment Pending</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Already Paid</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('orders.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white rounded-lg border border-gray-700 text-sm font-medium transition-colors" title="Reset Filters">
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

                            <!-- Essential Payment: Paid Status, Method, Time -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div>
                                    @if($order->payment_status === 'paid')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-950 text-emerald-400 border border-emerald-800">
                                            Already Paid
                                        </span>
                                    @elseif($order->payment_status === 'awaiting_approval')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-cyan-950 text-cyan-400 border border-cyan-800">
                                            Verify Claim
                                        </span>
                                    @elseif($order->payment_status === 'refunded')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-950 text-red-400 border border-red-800">
                                            Refunded
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-amber-950 text-amber-400 border border-amber-800">
                                            Not Paid
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    @if($order->payment_method === 'aba_khqr')
                                        ABA KHQR
                                    @elseif(in_array($order->payment_method, ['khqr', 'bakong_aba']))
                                        Bakong KHQR
                                    @else
                                        Cash
                                    @endif
                                    @if($displayWhen)
                                        · {{ $displayWhen->format('d M, h:i A') }}
                                    @endif
                                </div>
                            </td>

                            <!-- Request Status Quick Change -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" 
                                            class="text-xs font-medium px-2.5 py-1 rounded-lg cursor-pointer focus:outline-none transition-colors border {{ 
                                                $order->status === 'completed' ? 'bg-emerald-950 text-emerald-400 border-emerald-800' : 
                                                ($order->status === 'delivering' ? 'bg-cyan-950 text-cyan-400 border-cyan-800' : 
                                                ($order->status === 'preparing' ? 'bg-amber-950 text-amber-400 border-amber-800' : 
                                                ($order->status === 'cancelled' ? 'bg-red-950 text-red-400 border-red-800' : 
                                                'bg-gray-800 text-gray-300 border-gray-700'))) 
                                            }}">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Packing</option>
                                        <option value="delivering" {{ $order->status === 'delivering' ? 'selected' : '' }}>Delivering</option>
                                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Total & Item Count -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="font-mono font-semibold text-white">${{ number_format($order->total, 2) }}</div>
                                <div class="text-xs text-gray-400">{{ $itemCount }} {{ $itemCount === 1 ? 'item' : 'items' }}</div>
                            </td>

                            <!-- Actions: Detail Button + Quick Approve -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                            onclick="openOrderDetail({{ $order->id }})"
                                            class="px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 text-xs font-medium transition-colors cursor-pointer">
                                        Detail
                                    </button>
                                    @if($order->status === 'pending')
                                        <form action="{{ route('orders.approve', $order->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    data-confirm="អនុម័តការបង់ប្រាក់ និងបញ្ជូន Order #{{ $order->id }} ទៅដឹកជញ្ជូន?"
                                                    data-confirm-title="បញ្ជាក់ការអនុម័តការបញ្ជាទិញ"
                                                    data-confirm-ok="អនុម័ត & ដឹកជញ្ជូន"
                                                    data-confirm-variant="success"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium transition-colors cursor-pointer"
                                                    title="Approve & Deliver">
                                                Approve
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
<div id="orderDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 p-4 overflow-y-auto" onclick="closeOrderDetail()">
    <div id="orderDetailCard"
         style="background-color:#111827; color:#f3f4f6; border:1px solid #374151;"
         class="rounded-xl max-w-2xl w-full p-5 sm:p-6 shadow-xl my-auto max-h-[92vh] overflow-y-auto"
         onclick="event.stopPropagation()">
        <div class="flex items-start justify-between gap-4 pb-4" style="border-bottom:1px solid #1f2937;">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h3 id="detailOrderTitle" class="text-lg font-semibold" style="color:#ffffff;">Order #</h3>
                    <span id="detailPaidBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium"></span>
                </div>
                <p id="detailOrderTime" class="text-xs mt-1" style="color:#9ca3af;"></p>
            </div>
            <button type="button" onclick="closeOrderDetail()" class="text-xl px-2 py-0.5 rounded-lg" style="color:#9ca3af;">&times;</button>
        </div>

        <!-- Customer & Payment Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-4">
            <div class="rounded-lg p-3.5" style="background-color:#1f2937; border:1px solid #374151;">
                <div class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#9ca3af;">Customer & Delivery</div>
                <div id="detailCustomerBody" class="space-y-1.5 text-xs" style="color:#e5e7eb;"></div>
            </div>

            <div class="rounded-lg p-3.5" style="background-color:#1f2937; border:1px solid #374151;">
                <div class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#9ca3af;">Payment Information</div>
                <div id="detailPaymentBody" class="space-y-1.5 text-xs" style="color:#e5e7eb;"></div>

                <form id="detailPaymentForm" method="POST" class="mt-3 flex items-center gap-2">
                    @csrf
                    <select id="detailPaymentSelect" name="payment_status"
                            style="background-color:#111827; color:#f3f4f6; border:1px solid #4b5563;"
                            class="text-xs font-medium px-2.5 py-1.5 rounded-lg flex-1">
                        <option value="pending">Not Paid</option>
                        <option value="awaiting_approval">Verify Claim</option>
                        <option value="paid">Already Paid</option>
                        <option value="refunded">Refunded</option>
                    </select>
                    <button type="submit" style="background:#374151; color:#ffffff; border:1px solid #4b5563;" class="px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer">
                        Save
                    </button>
                </form>

                <div id="detailSlipBox" class="hidden mt-3">
                    <button type="button" id="detailSlipBtn" style="background:#374151; color:#ffffff; border:1px solid #4b5563;"
                            class="w-full px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer">
                        View Payment Slip
                    </button>
                </div>
            </div>
        </div>

        <!-- Items Ordered Table -->
        <div class="rounded-lg p-3.5 mb-4" style="background-color:#1f2937; border:1px solid #374151;">
            <div class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#9ca3af;">Items Ordered</div>
            <div id="detailItemsList" class="divide-y divide-gray-700 text-xs"></div>
            <div id="detailTotalsBox" class="mt-3 pt-2.5 space-y-1 text-xs" style="border-top:1px solid #374151;"></div>
        </div>

        <!-- Footer Controls -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3" style="border-top:1px solid #1f2937;">
            <form id="detailStatusForm" method="POST" class="flex items-center gap-2">
                @csrf
                <select id="detailStatusSelect" name="status"
                        style="background-color:#1f2937; color:#f3f4f6; border:1px solid #4b5563;"
                        class="text-xs font-medium px-2.5 py-1.5 rounded-lg">
                    <option value="pending">Pending</option>
                    <option value="preparing">Packing</option>
                    <option value="delivering">Delivering</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <button type="submit" style="background:#374151; color:#ffffff;" class="px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer">
                    Update Status
                </button>
            </form>

            <div class="flex items-center gap-2 flex-wrap">
                <form id="detailApproveForm" method="POST" class="hidden">
                    @csrf
                    <button type="submit"
                            data-confirm="អនុម័តការបង់ប្រាក់ និងបញ្ជូន Order នេះទៅដឹកជញ្ជូន?"
                            data-confirm-title="បញ្ជាក់ការអនុម័តការបញ្ជាទិញ"
                            data-confirm-ok="អនុម័ត & ដឹកជញ្ជូន"
                            data-confirm-variant="success"
                            style="background:#059669; color:#ffffff;"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer">
                        Approve
                    </button>
                </form>
                <button type="button" id="detailPrintBtn"
                        style="background:#374151; color:#ffffff;"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer">
                    Print Receipt
                </button>
                <button type="button" onclick="closeOrderDetail()"
                        style="background:#1f2937; color:#d1d5db; border:1px solid #4b5563;"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Slip Modal -->
<div id="slipModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/80 p-4" onclick="closeSlipModal()">
    <div class="bg-gray-900 border border-gray-700 rounded-xl max-w-md w-full p-4 shadow-xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-3">
            <h3 id="slipModalTitle" class="text-white font-semibold text-base">Payment Slip</h3>
            <button type="button" onclick="closeSlipModal()" class="text-gray-400 hover:text-white text-lg px-2">&times;</button>
        </div>
        <div class="bg-black rounded-lg overflow-hidden flex items-center justify-center max-h-[70vh]">
            <img id="slipModalImg" src="" alt="Payment Slip" class="max-h-[70vh] w-auto object-contain">
        </div>
        <div class="mt-3 flex justify-end gap-2">
            <a id="slipModalLink" href="#" target="_blank" class="px-3 py-1.5 rounded-lg bg-gray-700 hover:bg-gray-600 text-white text-xs font-medium">
                Open Full Image
            </a>
            <button type="button" onclick="closeSlipModal()" class="px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-medium">
                Close
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
        paidBadge.textContent = 'Already Paid';
        paidBadge.style.background = '#064e3b';
        paidBadge.style.color = '#6ee7b7';
        paidBadge.style.border = '1px solid #047857';
    } else if (order.payment_status === 'awaiting_approval') {
        paidBadge.textContent = 'Verify Claim';
        paidBadge.style.background = '#0c4a6e';
        paidBadge.style.color = '#7dd3fc';
        paidBadge.style.border = '1px solid #0369a1';
    } else if (order.payment_status === 'refunded') {
        paidBadge.textContent = 'Refunded';
        paidBadge.style.background = '#450a0a';
        paidBadge.style.color = '#fca5a5';
        paidBadge.style.border = '1px solid #991b1b';
    } else {
        paidBadge.textContent = 'Not Paid';
        paidBadge.style.background = '#451a03';
        paidBadge.style.color = '#fcd34d';
        paidBadge.style.border = '1px solid #92400e';
    }

    document.getElementById('detailCustomerBody').innerHTML =
        '<div><strong>Name:</strong> ' + escapeHtml(order.customer_name) + '</div>' +
        '<div><strong>Phone:</strong> ' + escapeHtml(order.phone) + '</div>' +
        (order.email ? '<div><strong>Email:</strong> ' + escapeHtml(order.email) + '</div>' : '') +
        '<div><strong>Address:</strong> ' + escapeHtml(order.shipping_address) + '</div>' +
        (order.notes ? '<div><strong>Notes:</strong> ' + escapeHtml(order.notes) + '</div>' : '');

    let viaLabel = 'Cash (COD)';
    if (order.payment_method === 'aba_khqr') viaLabel = 'ABA KHQR';
    else if (order.payment_method === 'khqr' || order.payment_method === 'bakong_aba') viaLabel = 'Bakong KHQR';

    document.getElementById('detailPaymentBody').innerHTML =
        '<div><strong>Via Channel:</strong> ' + escapeHtml(viaLabel) + '</div>' +
        '<div><strong>Time:</strong> ' + escapeHtml(order.payment_kh) + '</div>' +
        (order.bakong_ref ? '<div><strong>Ref / Sender:</strong> <span class="font-mono" style="color:#6ee7b7;">' + escapeHtml(order.bakong_ref) + '</span></div>' : '') +
        (order.khqr_md5 ? '<div><strong>KHQR MD5:</strong> <span class="font-mono text-[11px]" style="color:#9ca3af;">' + escapeHtml(order.khqr_md5) + '</span></div>' : '');

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
    document.getElementById('slipModalTitle').textContent = 'Payment Slip - Order #' + orderId;
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
