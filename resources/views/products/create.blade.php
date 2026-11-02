@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Add New Product</h1>
                <p class="text-gray-400">Create a new product in your inventory</p>
            </div>
            <a href="{{ route('products.index') }}" class="mt-4 md:mt-0 bg-gray-700 hover:bg-gray-600 text-white font-medium py-2.5 px-5 rounded-lg flex items-center gap-2 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Products
            </a>
        </div>

        <!-- Notifications -->
        @if (session('success'))
            <div class="bg-emerald-900/50 border-l-4 border-emerald-500 text-emerald-200 p-4 mb-6 rounded-r-lg flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-900/50 border-l-4 border-rose-500 text-rose-200 p-4 mb-6 rounded-r-lg">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-rose-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">Please correct the following errors:</span>
                </div>
                <ul class="mt-2 ml-9 list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('error'))
            <div class="bg-rose-900/50 border-l-4 border-rose-500 text-rose-200 p-4 mb-6 rounded-r-lg flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-rose-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700/50 shadow-xl overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-700/50">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Product Information
                </h2>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Product Code -->
                        <div>
                            <label for="pro_code" class="block text-sm font-medium text-gray-300 mb-1">Product Code <span class="text-rose-400">*</span></label>
                            <input 
                                type="text" 
                                id="pro_code" 
                                name="pro_code" 
                                value="{{ old('pro_code') }}" 
                                required
                                class="w-full px-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200"
                                placeholder="Enter product code"
                            >
                            @error('pro_code')
                                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Product Name -->
                        <div>
                            <label for="pro_name" class="block text-sm font-medium text-gray-300 mb-1">Product Name <span class="text-rose-400">*</span></label>
                            <input 
                                type="text" 
                                id="pro_name" 
                                name="pro_name" 
                                value="{{ old('pro_name') }}" 
                                required
                                class="w-full px-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200"
                                placeholder="Enter product name"
                            >
                            @error('pro_name')
                                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-300 mb-1">Category</label>
                            <select 
                                name="category_id" 
                                id="category_id"
                                class="w-full px-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200"
                            >
                                <option value="" {{ old('category_id') === null ? 'selected' : '' }}>Select a Category (Optional)</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->cat_id }}" {{ old('category_id') == $category->cat_id ? 'selected' : '' }}>
                                        {{ $category->cat_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price and Discount Row -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-300 mb-1">Price <span class="text-rose-400">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400">$</span>
                                    </div>
                                    <input 
                                        type="number" 
                                        id="price" 
                                        name="price" 
                                        value="{{ old('price') }}" 
                                        required 
                                        min="0" 
                                        step="0.01"
                                        class="w-full pl-8 pr-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200"
                                        placeholder="0.00"
                                    >
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Discount -->
                            <div>
                                <label for="discount" class="block text-sm font-medium text-gray-300 mb-1">Discount</label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        id="discount" 
                                        name="discount" 
                                        value="{{ old('discount') }}" 
                                        min="0" 
                                        max="100"
                                        class="w-full pr-8 pl-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200"
                                        placeholder="0"
                                    >
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400">%</span>
                                    </div>
                                </div>
                                @error('discount')
                                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label for="qty" class="block text-sm font-medium text-gray-300 mb-1">Quantity <span class="text-rose-400">*</span></label>
                            <input 
                                type="number" 
                                id="qty" 
                                name="qty" 
                                value="{{ old('qty') }}" 
                                required 
                                min="0"
                                class="w-full px-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200"
                                placeholder="Enter quantity"
                            >
                            @error('qty')
                                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                            <textarea 
                                id="description" 
                                name="description"
                                rows="4"
                                class="w-full px-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200"
                                placeholder="Enter product description"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Live Preview -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Image Preview</label>
                            <div id="preview-container" class="bg-gray-900/70 border border-gray-700/50 rounded-lg p-4 flex flex-col items-center justify-center min-h-[170px] relative">
                                <div id="preview-placeholder" class="text-center text-gray-500 py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-400">No image selected</p>
                                    <p class="text-xs text-gray-600 mt-0.5">Upload a file or enter a URL below to preview</p>
                                </div>
                                <div id="preview-image-wrapper" class="hidden w-full flex flex-col items-center">
                                    <div class="relative group">
                                        <img id="preview-image" src="#" alt="Product Preview" class="max-h-48 object-contain rounded-lg border border-gray-700/50 shadow-md">
                                    </div>
                                    <div id="preview-filename" class="text-xs text-cyan-400 font-medium mt-2 bg-gray-800/80 px-2.5 py-1 rounded border border-gray-700/50 truncate max-w-xs"></div>
                                    <button type="button" onclick="clearImagePreview()" class="mt-2 text-xs text-rose-400 hover:text-rose-300 flex items-center gap-1 bg-rose-500/10 hover:bg-rose-500/20 px-2.5 py-1 rounded border border-rose-500/30 transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Remove Image
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-300 mb-1">Product Image (Upload)</label>
                            <div id="drop-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-700 border-dashed rounded-lg transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-400 justify-center">
                                        <label for="image" class="relative cursor-pointer bg-gray-800 rounded-md font-medium text-cyan-400 hover:text-cyan-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-cyan-500">
                                            <span class="px-2">Upload a file</span>
                                            <input id="image" name="image" type="file" accept="image/*" class="sr-only" onchange="previewImageFile(this)">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            @error('image')
                                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image URL -->
                        <div>
                            <label for="image_url" class="block text-sm font-medium text-gray-300 mb-1">Product Image (URL)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input 
                                    type="url" 
                                    id="image_url" 
                                    name="image_url" 
                                    value="{{ old('image_url') }}"
                                    placeholder="https://example.com/image.jpg"
                                    oninput="previewImageUrl(this.value)"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200"
                                >
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter an external image URL or upload an image above. URL takes precedence if both are provided.</p>
                            @error('image_url')
                                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 pt-6 border-t border-gray-700/50 flex flex-wrap gap-3">
                    <button 
                        type="submit"
                        class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2.5 px-6 rounded-lg transition-all duration-200 shadow-lg shadow-cyan-900/20 flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add Product
                    </button>
                    <a 
                        href="{{ route('products.index') }}"
                        class="bg-gray-700 hover:bg-gray-600 text-white font-medium py-2.5 px-6 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar for webkit browsers */
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
    
    /* File input styling */
    input[type="file"]::file-selector-button {
        border: none;
        background: #374151;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        color: white;
        cursor: pointer;
        transition: background 0.2s ease-in-out;
    }
    
    input[type="file"]::file-selector-button:hover {
        background: #4B5563;
    }
    
    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
</style>

<script>
    function previewImageFile(input) {
        const previewWrapper = document.getElementById('preview-image-wrapper');
        const placeholder = document.getElementById('preview-placeholder');
        const previewImg = document.getElementById('preview-image');
        const filenameLabel = document.getElementById('preview-filename');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewWrapper.classList.remove('hidden');
                placeholder.classList.add('hidden');
                if (filenameLabel) {
                    filenameLabel.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                }
            };

            reader.readAsDataURL(file);
        }
    }

    function previewImageUrl(url) {
        const previewWrapper = document.getElementById('preview-image-wrapper');
        const placeholder = document.getElementById('preview-placeholder');
        const previewImg = document.getElementById('preview-image');
        const filenameLabel = document.getElementById('preview-filename');
        const fileInput = document.getElementById('image');

        if (url && url.trim() !== '') {
            previewImg.src = url.trim();
            previewImg.onerror = function() {
                previewImg.src = '/images/fallback.jpg';
            };
            previewWrapper.classList.remove('hidden');
            placeholder.classList.add('hidden');
            if (filenameLabel) {
                filenameLabel.textContent = 'URL: ' + url.trim();
            }
        } else if (fileInput && fileInput.files && fileInput.files[0]) {
            previewImageFile(fileInput);
        } else {
            clearImagePreview();
        }
    }

    function clearImagePreview() {
        const previewWrapper = document.getElementById('preview-image-wrapper');
        const placeholder = document.getElementById('preview-placeholder');
        const previewImg = document.getElementById('preview-image');
        const fileInput = document.getElementById('image');
        const urlInput = document.getElementById('image_url');
        const filenameLabel = document.getElementById('preview-filename');

        if (fileInput) fileInput.value = '';
        if (urlInput) urlInput.value = '';
        previewImg.src = '#';
        previewWrapper.classList.add('hidden');
        placeholder.classList.remove('hidden');
        if (filenameLabel) filenameLabel.textContent = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dropArea = document.getElementById('drop-area');
        if (dropArea) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropArea.classList.add('border-cyan-500', 'bg-cyan-500/10');
                }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropArea.classList.remove('border-cyan-500', 'bg-cyan-500/10');
                }, false);
            });
            dropArea.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files[0]) {
                    const fileInput = document.getElementById('image');
                    fileInput.files = files;
                    previewImageFile(fileInput);
                }
            }, false);
        }

        // Check if there was an old image URL or old input
        const urlInput = document.getElementById('image_url');
        if (urlInput && urlInput.value) {
            previewImageUrl(urlInput.value);
        }
    });
</script>
@endsection