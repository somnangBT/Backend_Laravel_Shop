@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950 text-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header & Action Buttons -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-800 pb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-chart-pie text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                            Sales & Revenue Report
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-950 text-emerald-400 border border-emerald-800">
                                {{ strtoupper($summary['period']) }}
                            </span>
                        </h1>
                        <p class="text-sm text-gray-400 mt-0.5">
                            របាយការណ៍ចំណូល ការបញ្ជាទិញ និងមុខម្ហូបលក់ដាច់ប្រចាំថ្ងៃ ប្រចាំខែ និងប្រចាំឆ្នាំ
                        </p>
                    </div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Excel Export -->
                <a href="{{ route('reports.sales.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition-all duration-200 active:scale-95">
                    <i class="fas fa-file-excel text-base"></i>
                    <span>Export to Excel</span>
                </a>

                <!-- CSV Export -->
                <a href="{{ route('reports.sales.export', array_merge(request()->all(), ['format' => 'csv'])) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl font-medium text-sm text-gray-300 bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-gray-600 transition-all duration-200">
                    <i class="fas fa-file-csv text-base text-gray-400"></i>
                    <span>CSV</span>
                </a>

                <!-- Print -->
                <button onclick="window.print()" type="button" 
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl font-medium text-sm text-gray-400 hover:text-white bg-gray-900 hover:bg-gray-800 border border-gray-800 transition-all duration-200" title="Print Report">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-gray-900/70 backdrop-blur-md rounded-2xl border border-gray-800/80 p-5 shadow-xl">
            <form action="{{ route('reports.sales') }}" method="GET" id="salesFilterForm" class="space-y-4">
                
                <!-- Period Type Tabs -->
                <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-gray-800">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mr-2">Timeframe:</span>
                    
                    <button type="button" onclick="setPeriod('daily')" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ $summary['period'] === 'daily' ? 'theme-bg-gradient text-white shadow-md' : 'bg-gray-800/80 text-gray-300 hover:bg-gray-750 border border-gray-700' }}">
                        <i class="far fa-calendar-day mr-1"></i> Daily
                    </button>

                    <button type="button" onclick="setPeriod('monthly')" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ $summary['period'] === 'monthly' ? 'theme-bg-gradient text-white shadow-md' : 'bg-gray-800/80 text-gray-300 hover:bg-gray-750 border border-gray-700' }}">
                        <i class="far fa-calendar-alt mr-1"></i> Monthly
                    </button>

                    <button type="button" onclick="setPeriod('yearly')" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ $summary['period'] === 'yearly' ? 'theme-bg-gradient text-white shadow-md' : 'bg-gray-800/80 text-gray-300 hover:bg-gray-750 border border-gray-700' }}">
                        <i class="fas fa-calendar mr-1"></i> Yearly
                    </button>

                    <button type="button" onclick="setPeriod('custom')" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ $summary['period'] === 'custom' ? 'theme-bg-gradient text-white shadow-md' : 'bg-gray-800/80 text-gray-300 hover:bg-gray-750 border border-gray-700' }}">
                        <i class="fas fa-sliders-h mr-1"></i> Custom Range
                    </button>

                    <input type="hidden" name="period" id="periodInput" value="{{ $summary['period'] }}">
                </div>

                <!-- Input Controls Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 pt-1">
                    
                    <!-- Daily Picker -->
                    <div id="dailyPicker" class="lg:col-span-2 {{ $summary['period'] === 'daily' ? '' : 'hidden' }}">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Select Day</label>
                        <input type="date" name="date" value="{{ $summary['selected_date'] }}" 
                               class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <!-- Monthly Picker -->
                    <div id="monthlyPicker" class="lg:col-span-2 {{ $summary['period'] === 'monthly' ? '' : 'hidden' }}">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Select Month</label>
                        <input type="month" name="month" value="{{ $summary['selected_month'] }}" 
                               class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <!-- Yearly Picker -->
                    <div id="yearlyPicker" class="lg:col-span-2 {{ $summary['period'] === 'yearly' ? '' : 'hidden' }}">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Select Year</label>
                        <select name="year" class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            @for($y = (int)date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ (string)$y === (string)$summary['selected_year'] ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Custom Range -->
                    <div id="customPicker" class="lg:col-span-2 {{ $summary['period'] === 'custom' ? '' : 'hidden' }}">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Date Range</label>
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="start_date" value="{{ $summary['start_date_input'] }}" 
                                   class="w-1/2 bg-gray-950 border border-gray-700/80 rounded-xl px-2.5 py-2 text-xs text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <span class="text-gray-500 text-xs">to</span>
                            <input type="date" name="end_date" value="{{ $summary['end_date_input'] }}" 
                                   class="w-1/2 bg-gray-950 border border-gray-700/80 rounded-xl px-2.5 py-2 text-xs text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>

                    <!-- Order Status Filter -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Order Status</label>
                        <select name="status" class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="all" {{ $summary['selected_status'] === 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="completed" {{ $summary['selected_status'] === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="delivering" {{ $summary['selected_status'] === 'delivering' ? 'selected' : '' }}>Delivering</option>
                            <option value="preparing" {{ $summary['selected_status'] === 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="pending" {{ $summary['selected_status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ $summary['selected_status'] === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Payment Method Filter -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="all" {{ $summary['selected_payment'] === 'all' ? 'selected' : '' }}>All Methods</option>
                            <option value="cash" {{ $summary['selected_payment'] === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="khqr" {{ $summary['selected_payment'] === 'khqr' ? 'selected' : '' }}>KHQR / Bakong</option>
                            <option value="card" {{ $summary['selected_payment'] === 'card' ? 'selected' : '' }}>Credit / Debit Card</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Search</label>
                        <input type="text" name="search" value="{{ $summary['search'] }}" placeholder="Order #, name..."
                               class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <!-- Filter & Reset Buttons -->
                    <div class="lg:col-span-1 flex items-end gap-2">
                        <button type="submit" class="flex-1 theme-bg-gradient hover:opacity-90 text-white font-semibold py-2 px-3 rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-1.5 shadow-md">
                            <i class="fas fa-filter text-xs"></i>
                            <span>Filter</span>
                        </button>
                        <a href="{{ route('reports.sales') }}" class="p-2 bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white rounded-xl border border-gray-700 transition-colors" title="Reset Filters">
                            <i class="fas fa-undo-alt text-sm"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Total Revenue -->
            <div class="bg-gray-900/60 rounded-2xl border border-gray-800 p-5 shadow-lg relative overflow-hidden group hover:border-gray-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 whitespace-nowrap">Total Sales Revenue</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-400 mt-1 font-mono whitespace-nowrap">
                            ${{ number_format($summary['total_sales'], 2) }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Paid: <span class="text-gray-200 font-semibold font-mono">${{ number_format($summary['paid_sales'], 2) }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-950/80 border border-emerald-800/50 flex items-center justify-center text-emerald-400 shadow-inner flex-shrink-0 ml-3">
                        <i class="fas fa-dollar-sign text-xl"></i>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-emerald-500 to-teal-500 absolute bottom-0 left-0"></div>
            </div>

            <!-- Card 2: Total Orders -->
            <div class="bg-gray-900/60 rounded-2xl border border-gray-800 p-5 shadow-lg relative overflow-hidden group hover:border-gray-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 whitespace-nowrap">Total Orders</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-white mt-1 whitespace-nowrap">
                            {{ number_format($summary['total_orders']) }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Completed: <span class="text-emerald-400 font-semibold">{{ $summary['completed_orders'] }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-cyan-950/80 border border-cyan-800/50 flex items-center justify-center text-cyan-400 shadow-inner flex-shrink-0 ml-3">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-cyan-500 to-blue-500 absolute bottom-0 left-0"></div>
            </div>

            <!-- Card 3: Average Order Value (AOV) -->
            <div class="bg-gray-900/60 rounded-2xl border border-gray-800 p-5 shadow-lg relative overflow-hidden group hover:border-gray-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 whitespace-nowrap">Average Order Value</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-indigo-400 mt-1 font-mono whitespace-nowrap">
                            ${{ number_format($summary['avg_order_value'], 2) }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Per customer order
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-950/80 border border-indigo-800/50 flex items-center justify-center text-indigo-400 shadow-inner flex-shrink-0 ml-3">
                        <i class="fas fa-receipt text-xl"></i>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-indigo-500 to-purple-500 absolute bottom-0 left-0"></div>
            </div>

            <!-- Card 4: Order Fulfillment -->
            <div class="bg-gray-900/60 rounded-2xl border border-gray-800 p-5 shadow-lg relative overflow-hidden group hover:border-gray-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 whitespace-nowrap">Fulfillment Status</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-400 mt-1 whitespace-nowrap">
                            {{ $summary['pending_orders'] }} <span class="text-xs font-normal text-gray-400">active</span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Cancelled: <span class="text-red-400 font-medium">{{ $summary['cancelled_orders'] }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-950/80 border border-amber-800/50 flex items-center justify-center text-amber-400 shadow-inner flex-shrink-0 ml-3">
                        <i class="fas fa-truck-loading text-xl"></i>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-amber-500 to-orange-500 absolute bottom-0 left-0"></div>
            </div>
        </div>

        <!-- Analytical Insights Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Top 5 Best Selling Items -->
            <div class="lg:col-span-2 bg-gray-900/70 rounded-2xl border border-gray-800 p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4 border-b border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-crown text-amber-400"></i>
                        <span>Top 5 Best-Selling Food Items</span>
                    </h3>
                    <span class="text-xs text-gray-400">By quantity sold in period</span>
                </div>

                @if(count($topProducts) > 0)
                    <div class="space-y-3">
                        @foreach($topProducts as $idx => $prod)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-950/60 border border-gray-800/80 hover:border-gray-700 transition-all">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg font-bold text-xs flex items-center justify-center {{ $idx === 0 ? 'bg-amber-500 text-black shadow-md' : ($idx === 1 ? 'bg-slate-300 text-black' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-gray-800 text-gray-300')) }}">
                                        #{{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <h4 class="text-sm font-semibold text-white">{{ $prod->name }}</h4>
                                        <p class="text-xs text-gray-400 font-mono">${{ number_format($prod->price, 2) }} each</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-emerald-400 font-mono">
                                        ${{ number_format($prod->total_revenue, 2) }}
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-950 text-cyan-400 border border-cyan-800 font-semibold">
                                        {{ $prod->total_qty }} sold
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-gray-500 text-sm">
                        <i class="fas fa-utensils text-2xl mb-2 opacity-50"></i>
                        <p>No product sales recorded in this timeframe.</p>
                    </div>
                @endif
            </div>

            <!-- Payment Methods Breakdown -->
            <div class="bg-gray-900/70 rounded-2xl border border-gray-800 p-6 shadow-xl flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4 border-b border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-wallet text-cyan-400"></i>
                            <span>Payment Methods</span>
                        </h3>
                        <span class="text-xs text-gray-400">Share</span>
                    </div>

                    @if(count($paymentBreakdown) > 0)
                        <div class="space-y-3">
                            @foreach($paymentBreakdown as $method => $data)
                                <div class="p-3 rounded-xl bg-gray-950/60 border border-gray-800">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-semibold text-white uppercase flex items-center gap-1.5">
                                            @if(strtolower($data['method']) === 'cash')
                                                <i class="fas fa-money-bill-wave text-emerald-400"></i>
                                            @elseif(strtolower($data['method']) === 'khqr')
                                                <i class="fas fa-qrcode text-red-400"></i>
                                            @else
                                                <i class="fas fa-credit-card text-indigo-400"></i>
                                            @endif
                                            {{ $data['method'] }}
                                        </span>
                                        <span class="text-xs font-bold font-mono text-emerald-400">
                                            ${{ number_format($data['total'], 2) }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $data['count'] }} {{ Str::plural('transaction', $data['count']) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-500 text-sm">
                            <p>No transaction data.</p>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-gray-800 text-xs text-gray-400 flex items-center justify-between">
                    <span>Generated Report</span>
                    <span>{{ $summary['period_label'] }}</span>
                </div>
            </div>
        </div>

        <!-- Orders Breakdown Table -->
        <div class="bg-gray-900/70 rounded-2xl border border-gray-800/80 overflow-hidden shadow-2xl">
            <div class="p-5 border-b border-gray-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-900/90">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>Order Transactions Breakdown</span>
                        <span class="text-xs font-normal text-gray-400">({{ $orders->total() }} records)</span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Date Range: {{ $summary['start_date']->format('M d, Y') }} — {{ $summary['end_date']->format('M d, Y') }}
                    </p>
                </div>
                <div class="text-xs text-gray-400">
                    Showing page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                </div>
            </div>

            <!-- Table Responsive Container -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-950/80 text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-800">
                            <th class="py-3.5 px-4 w-14 text-center whitespace-nowrap">Order #</th>
                            <th class="py-3.5 px-4 min-w-[150px] whitespace-nowrap">Date & Time</th>
                            <th class="py-3.5 px-4 min-w-[180px] whitespace-nowrap">Customer</th>
                            <th class="py-3.5 px-4 min-w-[160px] whitespace-nowrap">Contact</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[100px]">Items</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[120px]">Payment</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[120px]">Order Status</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap min-w-[130px]">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60 text-sm">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-800/40 transition-colors duration-150">
                                
                                <!-- Order ID -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="font-mono font-bold text-xs px-2 py-1 rounded-lg bg-gray-800 text-cyan-400 border border-gray-700">
                                        #{{ $order->id }}
                                    </span>
                                </td>

                                <!-- Date & Time -->
                                <td class="py-3.5 px-4 text-xs font-mono text-gray-300 whitespace-nowrap">
                                    {{ $order->created_at ? $order->created_at->format('M d, Y h:i A') : 'N/A' }}
                                </td>

                                <!-- Customer -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-white">
                                        {{ optional($order->user)->name ?? ($order->email ? explode('@', $order->email)[0] : 'Guest Customer') }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $order->user_id ? 'Registered User' : 'Guest Order' }}
                                    </div>
                                </td>

                                <!-- Contact -->
                                <td class="py-3.5 px-4 text-xs font-mono text-gray-300 whitespace-nowrap">
                                    <div>{{ $order->phone ?: 'No phone' }}</div>
                                    <div class="text-[11px] text-gray-400 truncate max-w-[160px]">{{ $order->email ?: optional($order->user)->email ?: 'N/A' }}</div>
                                </td>

                                <!-- Items Count -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-800 text-gray-300 border border-gray-700">
                                        {{ $order->orderItems ? $order->orderItems->sum('quantity') : 0 }} items
                                    </span>
                                </td>

                                <!-- Payment -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="text-xs font-semibold uppercase text-gray-200">
                                        {{ $order->payment_method ?: 'cash' }}
                                    </div>
                                    <span class="inline-block mt-0.5 text-[10px] px-2 py-0.2 rounded-full font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-amber-950 text-amber-400 border border-amber-800' }}">
                                        {{ strtoupper($order->payment_status ?: 'pending') }}
                                    </span>
                                </td>

                                <!-- Order Status -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($order->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-950/80 text-emerald-400 border border-emerald-800/80">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Completed
                                        </span>
                                    @elseif($order->status === 'delivering')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-950/80 text-cyan-400 border border-cyan-800/80">
                                            <i class="fas fa-motorcycle text-[10px]"></i>
                                            Delivering
                                        </span>
                                    @elseif($order->status === 'preparing')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-950/80 text-amber-400 border border-amber-800/80">
                                            <i class="fas fa-fire-burner text-[10px]"></i>
                                            Preparing
                                        </span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-950/80 text-red-400 border border-red-800/80">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-950/80 text-indigo-400 border border-indigo-800/80">
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                <!-- Total Amount -->
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-white whitespace-nowrap">
                                    ${{ number_format($order->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500">
                                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gray-800/50 flex items-center justify-center text-gray-600">
                                        <i class="fas fa-receipt text-2xl"></i>
                                    </div>
                                    <p class="text-base font-semibold text-gray-300">No orders found</p>
                                    <p class="text-xs text-gray-500 mt-1">Try adjusting your filters or date selection above.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="p-4 border-t border-gray-800 flex items-center justify-between">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    function setPeriod(type) {
        document.getElementById('periodInput').value = type;
        
        ['daily', 'monthly', 'yearly', 'custom'].forEach(p => {
            const el = document.getElementById(p + 'Picker');
            if (el) {
                if (p === type) el.classList.remove('hidden');
                else el.classList.add('hidden');
            }
        });

        document.getElementById('salesFilterForm').submit();
    }
</script>
@endsection
