@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Search Categories</h1>

    <div class="mb-4">
        <form action="{{ route('categories.search') }}" method="GET" class="flex max-w-md gap-2">
            <input type="text" name="search" placeholder="វាយស្វែងរកឈ្មោះប្រភេទទំនិញ... (Auto Search)" value="{{ $search ?? '' }}" autocomplete="off"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <a href="{{ route('categories.search') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Reset
            </a>
        </form>
    </div>

    <div data-live-region="categories" class="bg-white shadow-md rounded my-6">
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">ID</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @forelse($categories as $category)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $category->cat_id }}</td>
                        <td class="py-3 px-6 text-left">{{ $category->cat_name }}</td>
                        <td class="py-3 px-6 text-center">
                            @php
                                $catData = [
                                    'cat_id' => $category->cat_id,
                                    'cat_name' => $category->cat_name,
                                    'products_count' => $category->products()->count(),
                                    'update_url' => route('categories.update', $category->cat_id),
                                    'created_at' => $category->created_at ? $category->created_at->format('M d, Y h:i A') : 'N/A',
                                    'updated_at' => $category->updated_at ? $category->updated_at->format('M d, Y h:i A') : 'N/A',
                                ];
                                $catJson = htmlspecialchars(json_encode($catData), ENT_QUOTES, 'UTF-8');
                            @endphp
                            <div class="flex item-center justify-center space-x-3">
                                <button type="button" onclick="openCategoryDetailModal({{ $catJson }})" class="text-blue-500 hover:text-blue-700 font-medium">View</button>
                                <button type="button" onclick="openCategoryEditModal({{ $catJson }})" class="text-cyan-500 hover:text-cyan-700 font-medium">Edit</button>
                                <form action="{{ route('categories.destroy', $category->cat_id) }}" method="POST" data-confirm="តើអ្នកពិតជាចង់លុបប្រភេទទំនិញ «{{ $category->cat_name }}» មែនទេ?" data-confirm-title="បញ្ជាក់ការលុបប្រភេទទំនិញ" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium bg-transparent border-0">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-3 px-6 text-center text-gray-500">
                            No categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>

    <a href="{{ route('categories.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
        Back to Categories
    </a>
</div>

@include('category.partials.modals')
@endsection