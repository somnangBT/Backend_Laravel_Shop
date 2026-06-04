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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        @forelse($clients as $client)
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">
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
@endsection
