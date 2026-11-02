@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Staff Roles & Permissions</h1>
                    <p class="text-gray-400 text-sm mt-0.5">គ្រប់គ្រង និងកំណត់សិទ្ធិតួនាទីបុគ្គលិក (Admin, Cashier, Kitchen, Customer)</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs px-3 py-1.5 rounded-xl bg-gray-900 border border-gray-800 text-gray-300">
                <i class="fas fa-users text-cyan-400 mr-1.5"></i>
                Total Accounts: <strong class="text-white">{{ $roleCounts['all'] }}</strong>
            </span>
        </div>
    </div>

    <!-- Role Summary Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <!-- Admin -->
        <div class="bg-gray-900/70 border border-gray-800 rounded-2xl p-4 flex items-center justify-between">
            <div>
                <span class="text-xs uppercase font-semibold text-gray-400">Super Admins</span>
                <h3 class="text-2xl font-extrabold text-purple-400 mt-0.5">{{ $roleCounts['admin'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center text-lg">
                <i class="fas fa-crown"></i>
            </div>
        </div>

        <!-- Cashier -->
        <div class="bg-gray-900/70 border border-gray-800 rounded-2xl p-4 flex items-center justify-between">
            <div>
                <span class="text-xs uppercase font-semibold text-gray-400">Cashiers</span>
                <h3 class="text-2xl font-extrabold text-cyan-400 mt-0.5">{{ $roleCounts['cashier'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center text-lg">
                <i class="fas fa-cash-register"></i>
            </div>
        </div>

        <!-- Kitchen -->
        <div class="bg-gray-900/70 border border-gray-800 rounded-2xl p-4 flex items-center justify-between">
            <div>
                <span class="text-xs uppercase font-semibold text-gray-400">Kitchen Staff</span>
                <h3 class="text-2xl font-extrabold text-amber-400 mt-0.5">{{ $roleCounts['kitchen'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center text-lg">
                <i class="fas fa-utensils"></i>
            </div>
        </div>

        <!-- Customers -->
        <div class="bg-gray-900/70 border border-gray-800 rounded-2xl p-4 flex items-center justify-between">
            <div>
                <span class="text-xs uppercase font-semibold text-gray-400">Customers</span>
                <h3 class="text-2xl font-extrabold text-emerald-400 mt-0.5">{{ $roleCounts['user'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-lg">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800 p-4 mb-6 shadow-xl">
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." 
                       class="w-full bg-gray-950 border border-gray-800 rounded-xl py-2 pl-9 pr-4 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
            </div>

            <div class="w-full sm:w-48">
                <select name="role" class="w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="all">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="kitchen" {{ request('role') === 'kitchen' ? 'selected' : '' }}>Kitchen Staff</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Customer (User)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none theme-bg-gradient text-white font-semibold py-2 px-5 rounded-xl text-sm transition-all shadow-md">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'role']) && request('role') !== 'all')
                    <a href="{{ route('users.index') }}" class="p-2 bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white rounded-xl border border-gray-700 transition-colors" title="Clear">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-gray-900/80 rounded-2xl border border-gray-800 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-950/80 text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-800">
                        <th class="py-3.5 px-4 w-12 text-center whitespace-nowrap">#</th>
                        <th class="py-3.5 px-4 min-w-[200px] whitespace-nowrap">User Account</th>
                        <th class="py-3.5 px-4 min-w-[180px] whitespace-nowrap">Email Address</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[130px]">Registered Date</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[160px]">Assigned Role</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap min-w-[150px]">Change Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            
                            <!-- Index -->
                            <td class="py-4 px-4 text-center text-xs font-mono text-gray-500 whitespace-nowrap">
                                {{ $index + 1 }}
                            </td>

                            <!-- User Avatar & Name -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white font-bold text-xs flex-shrink-0 shadow-sm">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-white flex items-center gap-2">
                                            <span>{{ $u->name }}</span>
                                            @if(auth()->id() === $u->id)
                                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                                                    You
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-gray-400 font-mono">ID: #{{ $u->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-4 px-4 text-xs font-mono text-gray-300 whitespace-nowrap">
                                {{ $u->email }}
                            </td>

                            <!-- Date -->
                            <td class="py-4 px-4 text-center text-xs font-mono text-gray-400 whitespace-nowrap">
                                {{ $u->created_at ? $u->created_at->format('M d, Y') : 'N/A' }}
                            </td>

                            <!-- Current Role Badge -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @if($u->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-950 text-purple-400 border border-purple-800">
                                        <i class="fas fa-crown text-[10px]"></i>
                                        Administrator
                                    </span>
                                @elseif($u->role === 'cashier')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-950 text-cyan-400 border border-cyan-800">
                                        <i class="fas fa-cash-register text-[10px]"></i>
                                        Cashier
                                    </span>
                                @elseif($u->role === 'kitchen')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-950 text-amber-400 border border-amber-800">
                                        <i class="fas fa-fire-burner text-[10px]"></i>
                                        Kitchen Staff
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-300 border border-gray-700">
                                        <i class="far fa-user text-[10px]"></i>
                                        Customer
                                    </span>
                                @endif
                            </td>

                            <!-- Role Changer Dropdown -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @if(auth()->id() !== $u->id)
                                    <form action="{{ route('users.update-role', $u->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" onchange="this.form.submit()" 
                                                class="text-xs font-medium bg-gray-950 border border-gray-700 rounded-xl px-2.5 py-1.5 text-gray-200 cursor-pointer focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Set as Admin</option>
                                            <option value="cashier" {{ $u->role === 'cashier' ? 'selected' : '' }}>Set as Cashier</option>
                                            <option value="kitchen" {{ $u->role === 'kitchen' ? 'selected' : '' }}>Set as Kitchen</option>
                                            <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>Set as Customer</option>
                                        </select>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-500 italic">Current Session</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <p class="text-base font-semibold text-gray-300">No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-gray-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
