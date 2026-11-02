@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950 text-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header & Action Buttons -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-800 pb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                            Customer Report
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-cyan-950 text-cyan-400 border border-cyan-800">
                                {{ strtoupper($summary['period']) }}
                            </span>
                        </h1>
                        <p class="text-sm text-gray-400 mt-0.5">
                            របាយការណ៍ស្ថិតិ និងទិន្នន័យអតិថិជនប្រចាំថ្ងៃ ប្រចាំខែ និងប្រចាំឆ្នាំ
                        </p>
                    </div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Excel (.xls) Export Button -->
                <a href="{{ route('reports.customers.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-900/30 transition-all duration-200 active:scale-95">
                    <i class="fas fa-file-excel text-base"></i>
                    <span>Export to Excel</span>
                </a>

                <!-- CSV (.csv) Export Button -->
                <a href="{{ route('reports.customers.export', array_merge(request()->all(), ['format' => 'csv'])) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl font-medium text-sm text-gray-300 bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-gray-600 transition-all duration-200">
                    <i class="fas fa-file-csv text-base text-gray-400"></i>
                    <span>CSV</span>
                </a>

                <!-- Print Report Button -->
                <button onclick="window.print()" type="button" 
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl font-medium text-sm text-gray-400 hover:text-white bg-gray-900 hover:bg-gray-800 border border-gray-800 transition-all duration-200" title="Print Report">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-gray-900/70 backdrop-blur-md rounded-2xl border border-gray-800/80 p-5 shadow-xl">
            <form action="{{ route('reports.customers') }}" method="GET" id="reportFilterForm" class="space-y-4">
                
                <!-- Period Type Tabs -->
                <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-gray-800">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mr-2">Timeframe:</span>
                    
                    <button type="button" onclick="setPeriod('daily')" 
                            class="period-tab px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 {{ $summary['period'] === 'daily' ? 'theme-bg-gradient text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        <i class="fas fa-calendar-day mr-1.5"></i> Daily (ប្រចាំថ្ងៃ)
                    </button>

                    <button type="button" onclick="setPeriod('monthly')" 
                            class="period-tab px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 {{ $summary['period'] === 'monthly' ? 'theme-bg-gradient text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        <i class="fas fa-calendar-alt mr-1.5"></i> Monthly (ប្រចាំខែ)
                    </button>

                    <button type="button" onclick="setPeriod('yearly')" 
                            class="period-tab px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 {{ $summary['period'] === 'yearly' ? 'theme-bg-gradient text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        <i class="fas fa-calendar mr-1.5"></i> Yearly (ប្រចាំឆ្នាំ)
                    </button>

                    <button type="button" onclick="setPeriod('custom')" 
                            class="period-tab px-4 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 {{ $summary['period'] === 'custom' ? 'theme-bg-gradient text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        <i class="fas fa-sliders-h mr-1.5"></i> Custom Range (ចន្លោះកាលបរិច្ឆេទ)
                    </button>

                    <input type="hidden" name="period" id="periodInput" value="{{ $summary['period'] }}">
                </div>

                <!-- Filter Controls Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 pt-2">
                    
                    <!-- Daily Picker -->
                    <div id="dailyPicker" class="lg:col-span-3 {{ $summary['period'] === 'daily' ? '' : 'hidden' }}">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Select Date</label>
                        <div class="relative">
                            <input type="date" name="date" value="{{ $summary['selected_date'] }}" 
                                   class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>

                    <!-- Monthly Picker -->
                    <div id="monthlyPicker" class="lg:col-span-3 {{ $summary['period'] === 'monthly' ? '' : 'hidden' }}">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Select Month</label>
                        <input type="month" name="month" value="{{ $summary['selected_month'] }}" 
                               class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <!-- Yearly Picker -->
                    <div id="yearlyPicker" class="lg:col-span-3 {{ $summary['period'] === 'yearly' ? '' : 'hidden' }}">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Select Year</label>
                        <select name="year" class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            @for($y = (int)date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $summary['selected_year'] == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Custom Range -->
                    <div id="customPicker" class="lg:col-span-4 {{ $summary['period'] === 'custom' ? '' : 'hidden' }} grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">From Date</label>
                            <input type="date" name="start_date" value="{{ $summary['start_date']->toDateString() }}" 
                                   class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-2.5 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">To Date</label>
                            <input type="date" name="end_date" value="{{ $summary['end_date']->toDateString() }}" 
                                   class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-2.5 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="lg:col-span-3">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Search Customer</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-2.5 text-xs text-gray-500"></i>
                            <input type="text" name="search" value="{{ $summary['search'] }}" placeholder="Name, Email, Phone..." 
                                   class="w-full bg-gray-950 border border-gray-700/80 rounded-xl pl-9 pr-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Status</label>
                        <select name="status" class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="all" {{ $summary['status'] === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="active" {{ $summary['status'] === 'active' ? 'selected' : '' }}>Active (With Orders)</option>
                            <option value="new" {{ $summary['status'] === 'new' ? 'selected' : '' }}>New in Period</option>
                            <option value="inactive" {{ $summary['status'] === 'inactive' ? 'selected' : '' }}>Inactive / No Orders</option>
                        </select>
                    </div>

                    <!-- Source Filter -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Source</label>
                        <select name="source" class="w-full bg-gray-950 border border-gray-700/80 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="all" {{ $summary['source'] === 'all' ? 'selected' : '' }}>All Sources</option>
                            <option value="clients" {{ $summary['source'] === 'clients' ? 'selected' : '' }}>Clients Only</option>
                            <option value="users" {{ $summary['source'] === 'users' ? 'selected' : '' }}>Users (Accounts)</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="lg:col-span-2 flex items-end gap-2">
                        <button type="submit" class="flex-1 theme-bg-gradient hover:opacity-90 text-white font-semibold py-2 px-4 rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-1.5 shadow-md">
                            <i class="fas fa-filter text-xs"></i>
                            <span>Filter</span>
                        </button>
                        @if(request()->hasAny(['search', 'status', 'source']) || request('period') !== 'daily')
                            <a href="{{ route('reports.customers') }}" class="p-2 bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white rounded-xl border border-gray-700 transition-colors" title="Reset Filters">
                                <i class="fas fa-undo-alt text-sm"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Total Customers -->
            <div class="bg-gray-900/60 rounded-2xl border border-gray-800 p-5 shadow-lg relative overflow-hidden group hover:border-gray-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Customers</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-white mt-1">
                            {{ number_format($summary['total_customers']) }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                            <span class="text-cyan-400 font-medium">{{ $summary['period_label'] }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-cyan-950/80 border border-cyan-800/50 flex items-center justify-center text-cyan-400 shadow-inner">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-cyan-500 to-blue-500 absolute bottom-0 left-0"></div>
            </div>

            <!-- Card 2: New Customers -->
            <div class="bg-gray-900/60 rounded-2xl border border-gray-800 p-5 shadow-lg relative overflow-hidden group hover:border-gray-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">New Registered</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-400 mt-1">
                            +{{ number_format($summary['new_customers_count']) }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Joined in this timeframe
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-950/80 border border-emerald-800/50 flex items-center justify-center text-emerald-400 shadow-inner">
                        <i class="fas fa-user-plus text-xl"></i>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-emerald-500 to-teal-500 absolute bottom-0 left-0"></div>
            </div>

            <!-- Card 3: Active Orders -->
            <div class="bg-gray-900/60 rounded-2xl border border-gray-800 p-5 shadow-lg relative overflow-hidden group hover:border-gray-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 whitespace-nowrap">Period Orders</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-indigo-400 mt-1 whitespace-nowrap">
                            {{ number_format($summary['period_orders_count']) }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            From <span class="text-indigo-300 font-semibold">{{ $summary['active_customers_count'] }}</span> active customers
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-950/80 border border-indigo-800/50 flex items-center justify-center text-indigo-400 shadow-inner flex-shrink-0 ml-3">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-indigo-500 to-purple-500 absolute bottom-0 left-0"></div>
            </div>

            <!-- Card 4: Period Revenue -->
            <div class="bg-gray-900/60 rounded-2xl border border-gray-800 p-5 shadow-lg relative overflow-hidden group hover:border-gray-700 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 whitespace-nowrap">Period Revenue</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-400 mt-1 font-mono whitespace-nowrap">
                            ${{ number_format($summary['period_total_revenue'], 2) }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            All-time: <span class="text-gray-300 font-medium">${{ number_format($summary['all_time_revenue'], 2) }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-950/80 border border-amber-800/50 flex items-center justify-center text-amber-400 shadow-inner flex-shrink-0 ml-3">
                        <i class="fas fa-dollar-sign text-xl"></i>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-amber-500 to-orange-500 absolute bottom-0 left-0"></div>
            </div>
        </div>

        <!-- Customer Data Table Card -->
        <div class="bg-gray-900/70 rounded-2xl border border-gray-800/80 overflow-hidden shadow-2xl">
            
            <!-- Table Header Bar -->
            <div class="p-5 border-b border-gray-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-900/90">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>Customer Breakdown</span>
                        <span class="text-xs font-normal text-gray-400">({{ $customers->count() }} records found)</span>
                    </h3>
                    <p class="text-xs text-gray-400">
                        Date Range: {{ $summary['start_date']->format('M d, Y') }} — {{ $summary['end_date']->format('M d, Y') }}
                    </p>
                </div>

                <div class="text-xs text-gray-400 flex items-center gap-3">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Active (Purchased)</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-400"></span> New</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-gray-500"></span> Inactive</span>
                </div>
            </div>

            <!-- Table Responsive Container -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-950/80 text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-800">
                            <th class="py-3.5 px-4 w-12 text-center whitespace-nowrap">#</th>
                            <th class="py-3.5 px-4 min-w-[200px] whitespace-nowrap">Customer</th>
                            <th class="py-3.5 px-4 min-w-[190px] whitespace-nowrap">Contact Info</th>
                            <th class="py-3.5 px-4 min-w-[160px] whitespace-nowrap">Address</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[120px]">Joined Date</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap min-w-[150px]">Period Orders</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap min-w-[130px]">Period Spent</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap min-w-[120px]">Total Spent</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[110px]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60 text-sm">
                        @forelse($customers as $index => $c)
                            <tr class="hover:bg-gray-800/40 transition-colors duration-150">
                                
                                <!-- Index -->
                                <td class="py-3.5 px-4 text-center text-xs font-mono text-gray-500 whitespace-nowrap">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Customer Avatar & Name -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gray-800 border border-gray-700/70 flex items-center justify-center font-bold text-xs text-gray-200 flex-shrink-0 shadow-sm">
                                            {{ strtoupper(substr($c->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-white hover:text-cyan-400 transition-colors flex items-center gap-2">
                                                <span class="whitespace-nowrap">{{ $c->name }}</span>
                                                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-gray-800 text-gray-400 border border-gray-700 flex-shrink-0">
                                                    {{ $c->id }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-gray-400 flex items-center gap-1.5 mt-0.5 whitespace-nowrap">
                                                <i class="far fa-user text-[10px]"></i>
                                                <span>{{ $c->source }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact Info -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <div class="text-xs text-gray-300 font-mono flex items-center gap-1.5">
                                            <i class="far fa-envelope text-[10px] text-gray-500"></i>
                                            <span>{{ $c->email }}</span>
                                        </div>
                                        @if($c->phone !== 'N/A')
                                            <div class="text-xs text-gray-400 font-mono flex items-center gap-1.5">
                                                <i class="fas fa-phone-alt text-[10px] text-gray-500"></i>
                                                <span>{{ $c->phone }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Address -->
                                <td class="py-3.5 px-4 text-xs text-gray-400 max-w-xs truncate" title="{{ $c->address }}">
                                    @if($c->address !== 'N/A')
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-map-marker-alt text-[10px] text-gray-500 flex-shrink-0"></i>
                                            <span class="truncate">{{ $c->address }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-600">—</span>
                                    @endif
                                </td>

                                <!-- Joined Date -->
                                <td class="py-3.5 px-4 text-center text-xs text-gray-300 font-mono whitespace-nowrap">
                                    {{ $c->created_at ? $c->created_at->format('M d, Y') : 'N/A' }}
                                </td>

                                <!-- Period Orders -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    @if($c->period_orders_count > 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-950 text-cyan-400 border border-cyan-800 whitespace-nowrap shadow-sm">
                                            <i class="fas fa-shopping-bag text-[10px]"></i>
                                            <span>{{ $c->period_orders_count }} {{ Str::plural('order', $c->period_orders_count) }}</span>
                                        </span>
                                    @else
                                        <span class="text-gray-500 text-xs font-mono font-medium">0 orders</span>
                                    @endif
                                </td>

                                <!-- Period Spent -->
                                <td class="py-3.5 px-4 text-right font-mono font-semibold whitespace-nowrap">
                                    @if($c->period_total_spent > 0)
                                        <span class="text-emerald-400">${{ number_format($c->period_total_spent, 2) }}</span>
                                    @else
                                        <span class="text-gray-600">$0.00</span>
                                    @endif
                                </td>

                                <!-- Total Spent -->
                                <td class="py-3.5 px-4 text-right font-mono text-gray-300 whitespace-nowrap">
                                    ${{ number_format($c->total_spent, 2) }}
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($c->status === 'Active')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-950/80 text-emerald-400 border border-emerald-800/80">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Active
                                        </span>
                                    @elseif($c->status === 'New')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-950/80 text-indigo-400 border border-indigo-800/80">
                                            <i class="fas fa-sparkles text-[10px]"></i>
                                            New
                                        </span>
                                    @elseif($c->status === 'Regular')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-950/80 text-amber-400 border border-amber-800/80">
                                            Regular
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-400 border border-gray-700">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-gray-500">
                                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gray-800/50 flex items-center justify-center text-gray-600">
                                        <i class="fas fa-user-slash text-2xl"></i>
                                    </div>
                                    <p class="text-base font-semibold text-gray-300">No customer records found</p>
                                    <p class="text-xs text-gray-500 mt-1">Try adjusting your date range or filters above.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    
                    @if($customers->count() > 0)
                        <tfoot>
                            <tr class="bg-gray-950 text-xs font-semibold text-gray-300 border-t-2 border-gray-800">
                                <td colspan="5" class="py-4 px-4 text-right uppercase tracking-wider text-gray-400">
                                    Summary Totals:
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-cyan-400 font-mono">
                                    {{ $summary['period_orders_count'] }} Orders
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-emerald-400 font-mono text-sm">
                                    ${{ number_format($summary['period_total_revenue'], 2) }}
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-gray-200 font-mono text-sm">
                                    ${{ number_format($summary['all_time_revenue'], 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    function setPeriod(type) {
        document.getElementById('periodInput').value = type;
        
        // Hide all pickers
        document.getElementById('dailyPicker').classList.add('hidden');
        document.getElementById('monthlyPicker').classList.add('hidden');
        document.getElementById('yearlyPicker').classList.add('hidden');
        document.getElementById('customPicker').classList.add('hidden');

        // Show the active picker
        if (type === 'daily') {
            document.getElementById('dailyPicker').classList.remove('hidden');
        } else if (type === 'monthly') {
            document.getElementById('monthlyPicker').classList.remove('hidden');
        } else if (type === 'yearly') {
            document.getElementById('yearlyPicker').classList.remove('hidden');
        } else if (type === 'custom') {
            document.getElementById('customPicker').classList.remove('hidden');
        }

        // Auto submit form on tab change
        document.getElementById('reportFilterForm').submit();
    }
</script>
@endsection
