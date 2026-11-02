@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <!-- Kitchen Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 border-b border-gray-800 pb-5">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-lg">
                <i class="fas fa-fire-burner text-2xl animate-pulse"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    Kitchen Display System (KDS)
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                </h1>
                <p class="text-gray-400 text-xs sm:text-sm mt-0.5">
                    ផ្ទាំងបញ្ជាចុងភៅ — រៀបចំម្ហូប និងកែប្រែស្ថានភាពភ្លាមៗតាមលំដាប់លំដោយ
                </p>
            </div>
        </div>

        <!-- Controls & Counters -->
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                <span class="text-gray-400">Queue:</span>
                <strong class="text-white">{{ $stats['pending'] }}</strong>
            </div>

            <div class="flex items-center gap-2 bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span class="text-gray-400">Cooking:</span>
                <strong class="text-white">{{ $stats['preparing'] }}</strong>
            </div>

            <div class="flex items-center gap-2 bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-gray-400">Today Done:</span>
                <strong class="text-white">{{ $stats['today_completed'] }}</strong>
            </div>

            <button onclick="location.reload()" class="p-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white border border-gray-700 transition-colors" title="Refresh Kitchen">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <!-- Active Kitchen Orders Grid -->
    @if(count($orders) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($orders as $order)
                @php
                    $isCooking = $order->status === 'preparing';
                    $minutesAgo = $order->created_at ? $order->created_at->diffInMinutes(now()) : 0;
                    $isLate = $minutesAgo > 20;
                @endphp
                <div class="rounded-2xl border transition-all duration-200 overflow-hidden flex flex-col justify-between shadow-xl {{ $isCooking ? 'bg-amber-950/20 border-amber-500/40' : 'bg-gray-900/90 border-gray-800' }} {{ $isLate ? 'ring-2 ring-red-500/50' : '' }}">
                    
                    <div>
                        <!-- Card Header -->
                        <div class="p-4 border-b {{ $isCooking ? 'border-amber-500/30 bg-amber-500/10' : 'border-gray-800 bg-gray-950/60' }} flex items-center justify-between">
                            <div>
                                <span class="font-mono text-lg font-extrabold {{ $isCooking ? 'text-amber-400' : 'text-cyan-400' }}">
                                    #{{ $order->id }}
                                </span>
                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    {{ optional($order->user)->name ?? ($order->email ?: 'Customer') }}
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1 text-xs font-bold font-mono px-2 py-0.5 rounded-full {{ $isLate ? 'bg-red-500/20 text-red-400 border border-red-500/30 animate-pulse' : 'bg-gray-800 text-gray-300 border border-gray-700' }}">
                                    <i class="far fa-clock text-[10px]"></i>
                                    {{ $minutesAgo }}m ago
                                </span>
                                <div class="text-[10px] uppercase font-bold tracking-wider mt-1 {{ $isCooking ? 'text-amber-400' : 'text-indigo-400' }}">
                                    {{ $order->status }}
                                </div>
                            </div>
                        </div>

                        <!-- Order Items List -->
                        <div class="p-4 space-y-3">
                            @foreach($order->orderItems as $item)
                                <div class="flex items-start justify-between gap-2 p-2 rounded-xl bg-gray-950/70 border border-gray-800">
                                    <div class="flex items-start gap-2.5">
                                        <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-400 font-extrabold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                            {{ $item->quantity }}x
                                        </span>
                                        <div>
                                            <h4 class="text-sm font-bold text-white leading-snug">
                                                {{ optional($item->product)->name ?? 'Food Item' }}
                                            </h4>
                                            @if($item->product && $item->product->category)
                                                <span class="text-[10px] text-gray-400">{{ $item->product->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($order->notes)
                                <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300 flex items-start gap-2">
                                    <i class="fas fa-sticky-note mt-0.5 flex-shrink-0"></i>
                                    <span>Note: {{ $order->notes }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="p-4 pt-0">
                        @if(!$isCooking)
                            <!-- Start Cooking Button -->
                            <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="preparing">
                                <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-black bg-amber-400 hover:bg-amber-300 shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-fire"></i>
                                    <span>Start Cooking (ចាប់ផ្ដើមធ្វើ)</span>
                                </button>
                            </form>
                        @else
                            <!-- Ready / Deliver Button -->
                            <div class="grid grid-cols-2 gap-2">
                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="delivering">
                                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 transition-all flex items-center justify-center gap-1.5 shadow-md">
                                        <i class="fas fa-motorcycle"></i>
                                        <span>Delivery</span>
                                    </button>
                                </form>

                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-all flex items-center justify-center gap-1.5 shadow-md">
                                        <i class="fas fa-check"></i>
                                        <span>Ready (រួចរាល់)</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <!-- Empty Kitchen State -->
        <div class="bg-gray-900/50 rounded-3xl border border-gray-800 p-16 text-center max-w-xl mx-auto my-12 shadow-2xl">
            <div class="w-20 h-20 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto mb-4 text-3xl">
                <i class="fas fa-check-double"></i>
            </div>
            <h3 class="text-xl font-bold text-white">All Orders Completed!</h3>
            <p class="text-sm text-gray-400 mt-2">
                មិនមានការបញ្ជាទិញដែលត្រូវចម្អិននៅពេលនេះទេ។ រាល់ការកុម្ម៉ង់ថ្មីពីអតិថិជននឹងបង្ហាញលើអេក្រង់នេះដោយស្វ័យប្រវត្តិ។
            </p>
            <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-500">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Auto-refreshing every 20 seconds</span>
            </div>
        </div>
    @endif

</div>

<!-- Auto-refresh Kitchen screen every 20 seconds -->
<script>
    setTimeout(function() {
        location.reload();
    }, 20000);
</script>
@endsection
