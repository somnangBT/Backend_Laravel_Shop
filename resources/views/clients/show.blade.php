@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Client Details</h1>
                <p class="text-gray-400">View client information</p>
            </div>
            <a href="{{ route('clients.index') }}" class="mt-4 md:mt-0 bg-gray-700 hover:bg-gray-600 text-white font-medium py-2.5 px-5 rounded-lg flex items-center gap-2 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Clients
            </a>
        </div>

        <!-- Client Details Card -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700/50 shadow-xl overflow-hidden">
            <div class="p-6">
                @if(isset($client))
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Client Image -->
                        <div class="flex-shrink-0">
                            @if($client->image)
                                <img src="{{ asset('storage/' . $client->image) }}" alt="{{ $client->name }}" class="h-32 w-32 object-cover rounded-xl border border-gray-700/50">
                            @else
                                <div class="h-32 w-32 bg-gray-700 rounded-xl flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Client Info -->
                        <div class="flex-grow">
                            <h2 class="text-2xl font-bold text-white mb-4">{{ $client->name }}</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Email</label>
                                    <p class="text-gray-200">{{ $client->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Phone</label>
                                    <p class="text-gray-200">{{ $client->phone }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Address</label>
                                    <p class="text-gray-200">{{ $client->address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <p>Client not found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection