@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Clients</h1>
                <p class="text-gray-400">Manage your client relationships</p>
            </div>
            <a href="{{ route('reports.customers') }}" class="mt-4 md:mt-0 bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-2.5 px-4 rounded-xl flex items-center gap-2 transition-all duration-200 shadow-lg shadow-emerald-900/20">
                <i class="fas fa-file-excel"></i>
                <span>Customer Report & Excel</span>
            </a>
        </div>

        <!-- Clients Table -->
        <div class="bg-gray-800/50 rounded-xl border border-gray-700/50 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-900/70 text-gray-400 text-sm">
                            <th class="py-3 px-4 text-left font-medium">Image</th>
                            <th class="py-3 px-4 text-left font-medium">Name</th>
                            <th class="py-3 px-4 text-left font-medium">Email</th>
                            <th class="py-3 px-4 text-left font-medium">Phone</th>
                            <th class="py-3 px-4 text-left font-medium">Address</th>
                            <th class="py-3 px-4 text-left font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        @forelse($clients as $client)
                            @php
                                $clientImgUrl = $client->image ? asset('storage/' . $client->image) : '/images/fallback.jpg';
                                $clientData = [
                                    'id' => $client->id,
                                    'name' => $client->name,
                                    'email' => $client->email,
                                    'phone' => $client->phone,
                                    'address' => $client->address,
                                    'image_url' => $clientImgUrl,
                                    'update_url' => route('clients.update', $client->id),
                                    'created_at' => $client->created_at ? $client->created_at->format('M d, Y') : 'N/A',
                                    'updated_at' => $client->updated_at ? $client->updated_at->format('M d, Y') : 'N/A',
                                ];
                                $clientJson = htmlspecialchars(json_encode($clientData), ENT_QUOTES, 'UTF-8');
                            @endphp
                            <tr class="hover:bg-gray-700/30 transition-colors duration-150">
                                <td class="py-3 px-4">
                                    @if($client->image)
                                        <img src="{{ asset('storage/' . $client->image) }}" alt="{{ $client->name }}" class="h-12 w-12 object-cover rounded-lg border border-gray-700/50">
                                    @else
                                        <div class="h-12 w-12 bg-gray-700 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-200 font-medium">{{ $client->name }}</td>
                                <td class="py-3 px-4 text-gray-300">{{ $client->email }}</td>
                                <td class="py-3 px-4 text-gray-300">{{ $client->phone }}</td>
                                <td class="py-3 px-4 text-gray-300">{{ $client->address }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openClientDetailModal({{ $clientJson }})" class="p-2 bg-gray-700/60 hover:bg-gray-700 text-gray-300 hover:text-white rounded-lg transition-all" title="View Details">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                        <button type="button" onclick="openClientEditModal({{ $clientJson }})" class="p-2 bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-400 hover:text-cyan-300 rounded-lg transition-all" title="Edit Client">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" data-confirm="តើអ្នកពិតជាចង់លុបអតិថិជន «{{ $client->name }}» មែនទេ?" data-confirm-title="បញ្ជាក់ការលុបអតិថិជន" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-600/20 hover:bg-rose-600/40 text-rose-400 hover:text-rose-300 rounded-lg transition-colors" title="Delete Client">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p>No clients found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if(isset($clients) && $clients instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-6">
                {{ $clients->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
</div>

@include('clients.partials.modals')
@endsection
