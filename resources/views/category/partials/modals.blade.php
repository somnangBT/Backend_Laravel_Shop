<!-- Category Detail Modal -->
<div id="categoryDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeCategoryDetailModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-lg">
            <!-- Modal Header -->
            <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                        <i class="fas fa-tags text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span id="detail-cat-name">Category Details</span>
                        </h3>
                        <p class="text-xs text-gray-400">ID: #<span id="detail-cat-id" class="theme-text-color font-mono font-medium"></span></p>
                    </div>
                </div>
                <button type="button" onclick="closeCategoryDetailModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <div class="p-4 rounded-xl bg-gray-950/70 border border-gray-800 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-400 uppercase tracking-wider block mb-1">Total Assigned Products</span>
                        <span id="detail-cat-products-count" class="text-2xl font-extrabold text-white font-mono">0</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gray-850 border border-gray-750 flex items-center justify-center theme-text-color text-xl">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-800/80 flex justify-between text-xs text-gray-400">
                    <span>Created: <strong id="detail-cat-created-at" class="text-gray-300 font-normal"></strong></span>
                    <span>Updated: <strong id="detail-cat-updated-at" class="text-gray-300 font-normal"></strong></span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-850 px-6 py-3.5 border-t border-gray-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeCategoryDetailModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
                    Close
                </button>
                <button type="button" id="detail-cat-to-edit-btn" class="px-4 py-2 rounded-xl theme-btn-gradient text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit Category</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Category Edit Modal -->
<div id="categoryEditModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeCategoryEditModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-lg">
            
            <form id="categoryEditForm" method="POST" action="">
                @csrf
                @method('PUT')

                <!-- Modal Header -->
                <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Edit Category</h3>
                            <p class="text-xs text-gray-400">Modify category classification name</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeCategoryEditModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label for="modal_edit_cat_name" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Category Name <span class="text-rose-400">*</span></label>
                        <input 
                            type="text" 
                            id="modal_edit_cat_name" 
                            name="cat_name" 
                            required
                            class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all"
                            placeholder="Enter category name"
                        >
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-850 px-6 py-4 border-t border-gray-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeCategoryEditModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl theme-btn-gradient text-sm font-semibold flex items-center gap-2">
                        <i class="fas fa-check text-xs"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
let currentCategoryData = null;

function openCategoryDetailModal(data) {
    currentCategoryData = data;
    document.getElementById('detail-cat-name').textContent = data.cat_name || 'Category Details';
    document.getElementById('detail-cat-id').textContent = data.cat_id || 'N/A';
    document.getElementById('detail-cat-products-count').textContent = data.products_count || 0;
    document.getElementById('detail-cat-created-at').textContent = data.created_at || 'N/A';
    document.getElementById('detail-cat-updated-at').textContent = data.updated_at || 'N/A';

    document.getElementById('categoryDetailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCategoryDetailModal() {
    document.getElementById('categoryDetailModal').classList.add('hidden');
    if (document.getElementById('categoryEditModal').classList.contains('hidden')) {
        document.body.style.overflow = '';
    }
}

function openCategoryEditModal(data) {
    currentCategoryData = data;
    closeCategoryDetailModal();

    document.getElementById('categoryEditForm').action = data.update_url;
    document.getElementById('modal_edit_cat_name').value = data.cat_name || '';

    document.getElementById('categoryEditModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCategoryEditModal() {
    document.getElementById('categoryEditModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    const toEditBtn = document.getElementById('detail-cat-to-edit-btn');
    if (toEditBtn) {
        toEditBtn.addEventListener('click', function() {
            if (currentCategoryData) {
                openCategoryEditModal(currentCategoryData);
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCategoryDetailModal();
            closeCategoryEditModal();
        }
    });
});
</script>
