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
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Orders Management</h1>
                    <p class="text-gray-400 text-sm mt-0.5">គ្រប់គ្រងការបញ្ជាទិញ ស្ថានភាពម្ហូប និងការទូទាត់ប្រាក់</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('orders.kitchen') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm text-white bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-900/30 transition-all">
                <i class="fas fa-fire-burner"></i>
                <span>Kitchen Display (KDS)</span>
                @if(($counts['pending'] + $counts['preparing']) > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs bg-white text-amber-900 font-extrabold ml-1">
                        {{ $counts['pending'] + $counts['preparing'] }}
                    </span>
                @endif
            </a>

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
            All Orders ({{ $counts['all'] }})
        </a>
        <a href="{{ route('orders.index', ['payment_status' => 'paid_pending_approval']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('payment_status') === 'paid_pending_approval' ? 'bg-emerald-500 text-gray-950 shadow-md font-bold' : 'bg-emerald-950/60 text-emerald-300 hover:bg-emerald-900/60 border border-emerald-700/50' }}">
            💳 បង់លុយរួច (រង់ចាំ Approve) ({{ $counts['paid_pending_approval'] ?? 0 }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'pending' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Pending ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'preparing']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'preparing' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Preparing ({{ $counts['preparing'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'delivering']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'delivering' ? 'bg-cyan-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Delivering ({{ $counts['delivering'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'completed' ? 'bg-emerald-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Completed ({{ $counts['completed'] }})
        </a>
        <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('status') === 'cancelled' ? 'bg-red-600 text-white shadow-md' : 'bg-gray-900 text-gray-300 hover:bg-gray-800 border border-gray-800' }}">
            Cancelled ({{ $counts['cancelled'] }})
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800 p-4 mb-6 shadow-xl">
        <form action="{{ route('orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">

            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Order ID, customer name, email, phone, Bakong ref..." 
                           class="w-full bg-gray-950 border border-gray-800 rounded-xl py-2 pl-9 pr-4 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
            </div>

            <div>
                <select name="payment_status" class="w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="all">All Payment Statuses</option>
                    <option value="paid_pending_approval" {{ request('payment_status') === 'paid_pending_approval' ? 'selected' : '' }}>💳 Paid & Waiting Approve</option>
                    <option value="awaiting_approval" {{ request('payment_status') === 'awaiting_approval' ? 'selected' : '' }}>🧾 Slip Submitted (Verify)</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>⏳ Payment Pending</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>✅ Paid</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>↩️ Refunded</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 theme-bg-gradient hover:opacity-90 text-white font-semibold py-2 px-4 rounded-xl text-sm transition-all shadow-md">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'payment_status']) || request('status') !== 'all')
                    <a href="{{ route('orders.index') }}" class="p-2 bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white rounded-xl border border-gray-700 transition-colors" title="Clear">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-950/80 text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-800">
                        <th class="py-3.5 px-4 w-16 text-center whitespace-nowrap">Order #</th>
                        <th class="py-3.5 px-4 min-w-[150px] whitespace-nowrap">Customer</th>
                        <th class="py-3.5 px-4 min-w-[200px] whitespace-nowrap">Items Ordered</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[110px]">Payment</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[150px]">Order Status</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap min-w-[120px]">Total Amount</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[140px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            
                            <!-- Order ID & Time -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="font-mono font-bold text-sm text-cyan-400 block">#{{ $order->id }}</span>
                                <span class="text-[11px] text-gray-500 font-mono block mt-0.5">
                                    {{ $order->created_at ? $order->created_at->format('M d, H:i') : '' }}
                                </span>
                            </td>

                            <!-- Customer -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="font-semibold text-white">
                                    {{ $order->customer_name ?: (optional($order->user)->name ?? ($order->email ?: 'Guest Customer')) }}
                                </div>
                                <div class="text-xs text-gray-400 flex items-center gap-1.5 mt-0.5">
                                    <i class="fas fa-phone-alt text-[10px] text-gray-500"></i>
                                    <span>{{ $order->phone ?: 'No phone' }}</span>
                                </div>
                                @if($order->shipping_address)
                                    <div class="text-[11px] text-gray-500 truncate max-w-[180px] mt-0.5" title="{{ $order->shipping_address }}">
                                        <i class="fas fa-map-marker-alt text-[10px]"></i> {{ $order->shipping_address }}
                                    </div>
                                @endif
                            </td>

                            <!-- Items List -->
                            <td class="py-4 px-4">
                                <div class="space-y-1">
                                    @foreach($order->orderItems->take(3) as $item)
                                        <div class="text-xs text-gray-300 flex items-center justify-between gap-3">
                                            <span class="truncate max-w-[160px]">
                                                <span class="font-bold text-cyan-400">{{ $item->quantity }}x</span> 
                                                {{ optional($item->product)->pro_name ?? optional($item->product)->name ?? 'Item' }}
                                            </span>
                                            <span class="font-mono text-gray-400 text-[11px]">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </div>
                                    @endforeach
                                    @if($order->orderItems->count() > 3)
                                        <span class="text-[11px] text-gray-500 font-medium">
                                            +{{ $order->orderItems->count() - 3 }} more items
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Payment Method & Status -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <div class="text-xs font-semibold uppercase text-gray-300 flex items-center justify-center gap-1">
                                    @if(in_array($order->payment_method, ['khqr', 'bakong_aba']))
                                        <span class="px-1.5 py-0.5 rounded bg-red-950/80 text-red-300 border border-red-700/60 text-[10px]">ABA KHQR</span>
                                    @else
                                        <span>{{ $order->payment_method ?: 'Cash' }}</span>
                                    @endif
                                </div>
                                @if($order->bakong_ref)
                                    <div class="text-[10px] {{ $order->payment_status === 'paid' ? 'text-emerald-400' : 'text-cyan-300' }} font-mono mt-1" title="Ref / Sender: {{ $order->bakong_ref }}">
                                        {{ $order->payment_status === 'paid' ? '✅ Paid Ref:' : '🔖 Ref:' }} {{ \Illuminate\Support\Str::limit($order->bakong_ref, 22) }}
                                    </div>
                                @endif
                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="inline-block mt-1">
                                    @csrf
                                    <select name="payment_status" onchange="this.form.submit()" 
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full cursor-pointer focus:outline-none {{ $order->payment_status === 'paid' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : ($order->payment_status === 'awaiting_approval' ? 'bg-cyan-950 text-cyan-300 border border-cyan-700' : 'bg-amber-950 text-amber-400 border border-amber-800') }}">
                                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="awaiting_approval" {{ $order->payment_status === 'awaiting_approval' ? 'selected' : '' }}>Awaiting Approve</option>
                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                </form>
                                @if($order->payment_proof)
                                    <div class="mt-1.5">
                                        <button type="button" onclick="openSlipModal('{{ $order->payment_proof }}', {{ $order->id }})"
                                                class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded bg-indigo-950 hover:bg-indigo-900 text-indigo-300 border border-indigo-700 transition-colors">
                                            <i class="fas fa-receipt"></i> View Slip
                                        </button>
                                    </div>
                                @endif
                            </td>

                            <!-- Order Status with Quick Change -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
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
                                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>🍳 Preparing</option>
                                        <option value="delivering" {{ $order->status === 'delivering' ? 'selected' : '' }}>🛵 Delivering</option>
                                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Total -->
                            <td class="py-4 px-4 text-right font-mono font-bold text-white whitespace-nowrap">
                                ${{ number_format($order->total, 2) }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($order->status === 'pending')
                                        <form action="{{ route('orders.approve', $order->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('អនុម័តការបង់ប្រាក់ និងបញ្ជូន Order #{{ $order->id }} ទៅដឹកជញ្ជូន?')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold shadow-md transition-all"
                                                    title="Approve Payment & Start Delivery">
                                                <i class="fas fa-check"></i>
                                                <span>Approve & ដឹក</span>
                                            </button>
                                        </form>
                                    @endif
                                    <!-- Print Receipt Button -->
                                    <button onclick="printOrderReceipt({{ json_encode($order) }})" 
                                            class="p-2 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white border border-gray-700 transition-colors" title="Print Receipt">
                                        <i class="fas fa-print text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gray-800/50 flex items-center justify-center text-gray-600">
                                    <i class="fas fa-box-open text-2xl"></i>
                                </div>
                                <p class="text-base font-semibold text-gray-300">No orders found</p>
                                <p class="text-xs text-gray-500 mt-1">New incoming orders from React will display here.</p>
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
    </div>

</div>

<!-- Payment Slip Modal -->
<div id="slipModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 p-4" onclick="closeSlipModal()">
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

function printOrderReceipt(order) {
    const itemsHtml = (order.order_items || []).map(item => `
        <tr style="border-bottom: 1px dashed #ccc;">
            <td style="padding: 4px 0;">${item.quantity}x ${item.product ? (item.product.pro_name || item.product.name || 'Item') : 'Item'}</td>
            <td style="text-align: right; padding: 4px 0;">$${(item.price * item.quantity).toFixed(2)}</td>
        </tr>
    `).join('');

    const receiptHtml = `
        <div style="text-align: center; border-bottom: 1px solid #000; padding-bottom: 8px; margin-bottom: 8px;">
            <h2 style="margin: 0; font-size: 18px; font-weight: bold;">DeliciousEats</h2>
            <p style="margin: 2px 0; font-size: 11px;">Phnom Penh, Cambodia</p>
            <p style="margin: 2px 0; font-size: 11px;">Tel: 012 345 678</p>
            <h3 style="margin: 6px 0 0 0; font-size: 14px;">ORDER RECEIPT #${order.id}</h3>
        </div>
        <div style="font-size: 11px; margin-bottom: 8px;">
            <div>Date: ${new Date(order.created_at).toLocaleString()}</div>
            <div>Customer: ${order.user ? order.user.name : (order.email || 'Guest')}</div>
            <div>Phone: ${order.phone || 'N/A'}</div>
            <div>Payment: ${(order.payment_method || 'CASH').toUpperCase()} (${order.payment_status || 'pending'})</div>
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
            <p style="margin: 0;">Thank you for dining with us!</p>
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
