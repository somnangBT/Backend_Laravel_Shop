@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Slide Heroes</h1>
                <p class="text-gray-400">Manage your hero slides</p>
            </div>
            <a href="{{ route('slide-heroes.create') }}" class="mt-4 md:mt-0 bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-5 rounded-lg flex items-center gap-2 transition-all duration-200 shadow-lg shadow-emerald-900/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Slide
            </a>
        </div>

        <!-- Search & Filters -->
        <div class="bg-gray-800/50 rounded-xl p-5 border border-gray-700/50 mb-8 shadow-lg">
            <form action="{{ route('slide-heroes.index') }}" method="GET">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            autocomplete="off"
                            placeholder="វាយស្វែងរកចំណងជើង Slide... (Auto Search)"
                            class="bg-gray-900/70 text-gray-100 border border-gray-700/50 rounded-lg w-full py-2.5 pl-10 pr-4 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200"
                        >
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('slide-heroes.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-medium py-2.5 px-5 rounded-lg transition-all duration-200 flex-shrink-0">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div data-live-region="slides">

        <!-- Slides Table -->
        <div class="bg-gray-800/50 rounded-xl border border-gray-700/50 overflow-hidden shadow-xl mb-8">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-900/70 text-gray-400 text-sm">
                            <th class="py-3 px-4 text-left font-medium">Images</th>
                            <th class="py-3 px-4 text-left font-medium">ID</th>
                            <th class="py-3 px-4 text-left font-medium">Main Title</th>
                            <th class="py-3 px-4 text-left font-medium">Description</th>
                            <th class="py-3 px-4 text-left font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        @forelse ($slides as $slide)
                            <tr class="hover:bg-gray-700/30 transition-colors duration-150">
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        @foreach (['image1', 'image2', 'image3'] as $imageField)
                                            @if ($slide->$imageField)
                                                <img 
                                                    src="{{ filter_var($slide->$imageField, FILTER_VALIDATE_URL) ? $slide->$imageField : Storage::url($slide->$imageField) }}" 
                                                    alt="{{ $slide->main_title }} {{ $imageField }}"
                                                    class="h-14 w-14 object-cover rounded-lg border border-gray-700/50 shadow-md" 
                                                    loading="lazy" 
                                                    onerror="this.src='/images/fallback.jpg'"
                                                >
                                            @endif
                                        @endforeach
                                        @if (!$slide->image1 && !$slide->image2 && !$slide->image3)
                                            <div class="h-14 w-14 rounded-lg bg-gray-700/50 flex items-center justify-center text-gray-500 border border-gray-700/50">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-300">{{ $slide->id }}</td>
                                <td class="py-3 px-4 text-gray-200 font-medium">{{ $slide->main_title }}</td>
                                <td class="py-3 px-4 text-gray-300 max-w-xs truncate">{{ $slide->description }}</td>
                                <td class="py-3 px-4">
                                    @php
                                        $resolveSlideImg = function ($val) {
                                            return $val ? (filter_var($val, FILTER_VALIDATE_URL) ? $val : Storage::url($val)) : '/images/fallback.jpg';
                                        };
                                        $slideData = [
                                            'id' => $slide->id,
                                            'main_title' => $slide->main_title,
                                            'description' => $slide->description ?? '',
                                            'image1' => $slide->image1,
                                            'image2' => $slide->image2,
                                            'image3' => $slide->image3,
                                            'image1_url' => $resolveSlideImg($slide->image1),
                                            'image2_url' => $resolveSlideImg($slide->image2),
                                            'image3_url' => $resolveSlideImg($slide->image3),
                                            'image1_is_url' => (bool) filter_var($slide->image1, FILTER_VALIDATE_URL),
                                            'image2_is_url' => (bool) filter_var($slide->image2, FILTER_VALIDATE_URL),
                                            'image3_is_url' => (bool) filter_var($slide->image3, FILTER_VALIDATE_URL),
                                            'update_url' => route('slide-heroes.update', $slide->id),
                                            'created_at' => $slide->created_at ? $slide->created_at->format('M d, Y h:i A') : 'N/A',
                                            'updated_at' => $slide->updated_at ? $slide->updated_at->format('M d, Y h:i A') : 'N/A',
                                        ];
                                        $slideJson = htmlspecialchars(json_encode($slideData), ENT_QUOTES, 'UTF-8');
                                    @endphp
                                    <div class="flex gap-2">
                                        <button type="button" onclick="openSlideDetailModal({{ $slideJson }})" class="p-2 bg-gray-700/50 hover:bg-gray-700 text-gray-300 hover:text-white rounded-lg transition-all hover:scale-105" title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button type="button" onclick="openSlideEditModal({{ $slideJson }})" class="p-2 bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-400 hover:text-cyan-300 rounded-lg transition-all hover:scale-105" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('slide-heroes.destroy', $slide->id) }}" method="POST" data-confirm="តើអ្នកពិតជាចង់លុប Slide «{{ $slide->main_title }}» មែនទេ?" data-confirm-title="បញ្ជាក់ការលុប Slide" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-600/20 hover:bg-rose-600/40 text-rose-400 hover:text-rose-300 rounded-lg transition-colors" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-lg">No slides found</p>
                                        <p class="text-sm text-gray-600 mt-1">Try adjusting your search criteria or add a new slide</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $slides->links('vendor.pagination.tailwind') }}
        </div>
        </div>
    </div>
</div>

@include('slide.partials.modals')

<style>
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: rgba(31, 41, 55, 0.5);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: rgba(75, 85, 99, 0.5);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(107, 114, 128, 0.5);
    }
    
    table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .bg-cyan-600\/20 {
        box-shadow: 0 0 10px rgba(8, 145, 178, 0.2);
    }
    
    .bg-rose-600\/20 {
        box-shadow: 0 0 10px rgba(225, 29, 72, 0.2);
    }
    
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
</style>
@endsection