<!-- Product Detail Modal -->
<div id="productDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop blur overlay -->
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeProductDetailModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-2xl">
            <!-- Modal Header -->
            <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                        <i class="fas fa-box-open text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span id="detail-pro-name">Product Details</span>
                        </h3>
                        <p class="text-xs text-gray-400">Code: <span id="detail-pro-code" class="theme-text-color font-mono font-medium"></span></p>
                    </div>
                </div>
                <button type="button" onclick="closeProductDetailModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-6">
                    <!-- Left: Product Image -->
                    <div class="sm:col-span-5 flex flex-col items-center">
                        <div class="w-full h-56 rounded-xl bg-gray-950 border border-gray-800 overflow-hidden flex items-center justify-center p-2 relative group shadow-inner">
                            <img id="detail-pro-image" src="/images/fallback.jpg" alt="Product" class="max-h-full max-w-full object-contain rounded-lg transition-transform duration-300 group-hover:scale-105" onerror="this.src='/images/fallback.jpg'">
                            <div id="detail-discount-pill" class="absolute top-2 right-2 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500 text-white shadow-md hidden">
                                0% OFF
                            </div>
                        </div>
                        <span id="detail-category-badge" class="mt-3 px-3 py-1 rounded-full text-xs font-semibold bg-gray-800 text-gray-300 border border-gray-700">
                            Category
                        </span>
                    </div>

                    <!-- Right: Info Details -->
                    <div class="sm:col-span-7 space-y-4">
                        <!-- Pricing Grid -->
                        <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-gray-950/70 border border-gray-800">
                            <div>
                                <span class="text-[11px] text-gray-400 uppercase tracking-wider block">Regular Price</span>
                                <span id="detail-original-price" class="text-base font-bold text-gray-400 line-through font-mono">$0.00</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-gray-400 uppercase tracking-wider block">Discounted Price</span>
                                <span id="detail-discounted-price" class="text-xl font-extrabold text-emerald-400 font-mono">$0.00</span>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-950/40 border border-gray-800">
                            <span class="text-xs text-gray-400 font-medium">Inventory Stock:</span>
                            <span id="detail-stock-badge" class="px-2.5 py-1 rounded-full text-xs font-bold">
                                0 in stock
                            </span>
                        </div>

                        <!-- Description -->
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1.5">Description</span>
                            <div id="detail-description" class="text-sm text-gray-300 bg-gray-950/60 p-3 rounded-xl border border-gray-800 max-h-24 overflow-y-auto leading-relaxed">
                                No description provided.
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="pt-2 border-t border-gray-800/80 flex justify-between text-[11px] text-gray-500">
                            <span>Added: <strong id="detail-created-at" class="text-gray-400 font-normal"></strong></span>
                            <span>Updated: <strong id="detail-updated-at" class="text-gray-400 font-normal"></strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-850 px-6 py-3.5 border-t border-gray-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeProductDetailModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
                    Close
                </button>
                <button type="button" id="detail-to-edit-btn" class="px-4 py-2 rounded-xl theme-btn-gradient text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit Product</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Product Edit Modal -->
<div id="productEditModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop blur overlay -->
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeProductEditModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-3xl">
            
            <form id="productEditForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Modal Header -->
                <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Edit Product</h3>
                            <p class="text-xs text-gray-400">Update product specifications, inventory, and pricing</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeProductEditModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 max-h-[75vh] overflow-y-auto space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <!-- Product Code -->
                        <div>
                            <label for="modal_edit_pro_code" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Product Code <span class="text-rose-400">*</span></label>
                            <input 
                                type="text" 
                                id="modal_edit_pro_code" 
                                name="pro_code" 
                                required
                                class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all font-mono"
                                placeholder="e.g. PRO-001"
                            >
                        </div>

                        <!-- Product Name -->
                        <div>
                            <label for="modal_edit_pro_name" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Product Name <span class="text-rose-400">*</span></label>
                            <input 
                                type="text" 
                                id="modal_edit_pro_name" 
                                name="pro_name" 
                                required
                                class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all"
                                placeholder="Enter product name"
                            >
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="modal_edit_category_id" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Category <span class="text-rose-400">*</span></label>
                            <select 
                                name="category_id" 
                                id="modal_edit_category_id"
                                required
                                class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all"
                            >
                                <option value="">Select Category</option>
                                @foreach($categories ?? \App\Models\Category::all() as $cat)
                                    <option value="{{ $cat->cat_id }}">{{ $cat->cat_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label for="modal_edit_qty" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Stock Quantity <span class="text-rose-400">*</span></label>
                            <input 
                                type="number" 
                                id="modal_edit_qty" 
                                name="qty" 
                                required 
                                min="0"
                                class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all font-mono"
                                placeholder="0"
                            >
                        </div>

                        <!-- Price & Discount Container -->
                        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 p-3.5 rounded-xl bg-gray-950/70 border border-gray-800">
                            <div>
                                <label for="modal_edit_price" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Price ($) <span class="text-rose-400">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">$</span>
                                    <input 
                                        type="number" 
                                        id="modal_edit_price" 
                                        name="price" 
                                        required 
                                        min="0" 
                                        step="0.01"
                                        class="w-full pl-7 pr-3 py-2 bg-gray-900 border border-gray-700/80 rounded-lg text-white text-sm focus:outline-none focus:border-cyan-500 font-mono"
                                        placeholder="0.00"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="modal_edit_discount" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">Discount (%)</label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        id="modal_edit_discount" 
                                        name="discount" 
                                        min="0" 
                                        max="100" 
                                        step="1"
                                        class="w-full pr-7 pl-3 py-2 bg-gray-900 border border-gray-700/80 rounded-lg text-white text-sm focus:outline-none focus:border-cyan-500 font-mono"
                                        placeholder="0"
                                    >
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-sm">%</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Final Calculated Price</label>
                                <div class="py-2 px-3 bg-gray-900/80 border border-gray-800 rounded-lg text-emerald-400 font-mono font-bold text-sm">
                                    <span id="modal_edit_final_price">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="modal_edit_description" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Description</label>
                            <textarea 
                                id="modal_edit_description" 
                                name="description" 
                                rows="3"
                                class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all leading-relaxed"
                                placeholder="Enter product description or specifications..."
                            ></textarea>
                        </div>

                        <!-- Image Section -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Product Image</label>
                            <div class="flex flex-col sm:flex-row gap-4 items-start bg-gray-950/60 p-3.5 rounded-xl border border-gray-800">
                                <!-- Image Preview -->
                                <div class="w-24 h-24 rounded-xl bg-gray-900 border border-gray-700/80 overflow-hidden flex items-center justify-center flex-shrink-0 p-1 shadow-inner">
                                    <img id="modal_edit_image_preview" src="/images/fallback.jpg" alt="Preview" class="max-h-full max-w-full object-contain rounded-lg" onerror="this.src='/images/fallback.jpg'">
                                </div>

                                <!-- Upload options -->
                                <div class="flex-1 space-y-2.5 w-full">
                                    <div>
                                        <label class="block text-[11px] text-gray-400 mb-1">Upload New File:</label>
                                        <input 
                                            type="file" 
                                            id="modal_edit_image_file" 
                                            name="image" 
                                            accept="image/*"
                                            class="block w-full text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-cyan-400 hover:file:bg-gray-750 cursor-pointer"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-400 mb-1">Or Image URL:</label>
                                        <input 
                                            type="url" 
                                            id="modal_edit_image_url" 
                                            name="image_url" 
                                            placeholder="https://example.com/image.jpg"
                                            class="w-full px-3 py-1.5 bg-gray-900 border border-gray-700/80 rounded-lg text-white text-xs focus:outline-none focus:border-cyan-500"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-850 px-6 py-4 border-t border-gray-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeProductEditModal()" class="px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl theme-btn-gradient text-sm font-semibold flex items-center gap-2">
                        <i class="fas fa-check"></i>
                        <span>Update Product</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
let currentModalProductData = null;

function openProductDetailModal(data) {
    currentModalProductData = data;
    document.getElementById('detail-pro-name').textContent = data.pro_name || 'Product Details';
    document.getElementById('detail-pro-code').textContent = data.pro_code || 'N/A';
    document.getElementById('detail-pro-image').src = data.image_url || '/images/fallback.jpg';
    document.getElementById('detail-category-badge').textContent = data.category_name || 'No Category';
    
    const price = Number(data.price || 0);
    const discPrice = Number(data.discounted_price || price);
    const discount = Number(data.discount || 0);

    document.getElementById('detail-original-price').textContent = '$' + price.toFixed(2);
    document.getElementById('detail-discounted-price').textContent = '$' + discPrice.toFixed(2);

    const discountPill = document.getElementById('detail-discount-pill');
    if (discount > 0) {
        discountPill.textContent = discount + '% OFF';
        discountPill.classList.remove('hidden');
    } else {
        discountPill.classList.add('hidden');
    }

    const stockBadge = document.getElementById('detail-stock-badge');
    const qty = parseInt(data.qty) || 0;
    if (qty > 10) {
        stockBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30';
        stockBadge.textContent = 'In Stock (' + qty + ')';
    } else if (qty > 0) {
        stockBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30';
        stockBadge.textContent = 'Low Stock (' + qty + ')';
    } else {
        stockBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30';
        stockBadge.textContent = 'Out of Stock (0)';
    }

    document.getElementById('detail-description').textContent = data.description || 'No description provided.';
    document.getElementById('detail-created-at').textContent = data.created_at || 'N/A';
    document.getElementById('detail-updated-at').textContent = data.updated_at || 'N/A';

    document.getElementById('productDetailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeProductDetailModal() {
    document.getElementById('productDetailModal').classList.add('hidden');
    if (document.getElementById('productEditModal').classList.contains('hidden')) {
        document.body.style.overflow = '';
    }
}

function openProductEditModal(data) {
    currentModalProductData = data;
    closeProductDetailModal();

    document.getElementById('productEditForm').action = data.update_url;
    document.getElementById('modal_edit_pro_code').value = data.pro_code || '';
    document.getElementById('modal_edit_pro_name').value = data.pro_name || '';
    document.getElementById('modal_edit_category_id').value = data.category_id || '';
    document.getElementById('modal_edit_qty').value = data.qty !== undefined ? data.qty : 0;
    document.getElementById('modal_edit_price').value = data.price !== undefined ? data.price : 0;
    document.getElementById('modal_edit_discount').value = data.discount !== undefined ? data.discount : 0;
    document.getElementById('modal_edit_description').value = data.description || '';
    document.getElementById('modal_edit_image_preview').src = data.image_url || '/images/fallback.jpg';
    document.getElementById('modal_edit_image_file').value = '';
    document.getElementById('modal_edit_image_url').value = '';

    recalculateModalFinalPrice();
    document.getElementById('productEditModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeProductEditModal() {
    document.getElementById('productEditModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function recalculateModalFinalPrice() {
    const price = parseFloat(document.getElementById('modal_edit_price').value) || 0;
    const discount = parseFloat(document.getElementById('modal_edit_discount').value) || 0;
    const finalPrice = price * (1 - (discount / 100));
    document.getElementById('modal_edit_final_price').textContent = '$' + finalPrice.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    const editPriceInput = document.getElementById('modal_edit_price');
    const editDiscountInput = document.getElementById('modal_edit_discount');
    const editFileInput = document.getElementById('modal_edit_image_file');
    const editUrlInput = document.getElementById('modal_edit_image_url');
    const toEditBtn = document.getElementById('detail-to-edit-btn');

    if (editPriceInput) editPriceInput.addEventListener('input', recalculateModalFinalPrice);
    if (editDiscountInput) editDiscountInput.addEventListener('input', recalculateModalFinalPrice);

    if (editFileInput) {
        editFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('modal_edit_image_preview').src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    if (editUrlInput) {
        editUrlInput.addEventListener('input', function() {
            if (this.value) {
                document.getElementById('modal_edit_image_preview').src = this.value;
            }
        });
    }

    if (toEditBtn) {
        toEditBtn.addEventListener('click', function() {
            if (currentModalProductData) {
                openProductEditModal(currentModalProductData);
            }
        });
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProductDetailModal();
            closeProductEditModal();
        }
    });
});
</script>
