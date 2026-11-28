<!-- Client Detail Modal -->
<div id="clientDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="client-detail-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeClientDetailModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-lg">
            <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div>
                        <h3 id="client-detail-title" class="text-lg font-bold text-white">
                            <span id="detail-client-name">Client Details</span>
                        </h3>
                        <p class="text-xs text-gray-400">Client ID: #<span id="detail-client-id" class="theme-text-color font-mono font-medium"></span></p>
                    </div>
                </div>
                <button type="button" onclick="closeClientDetailModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex items-center gap-4">
                    <img id="detail-client-image" src="/images/fallback.jpg" alt="Client" class="h-16 w-16 rounded-xl object-cover border border-gray-700" onerror="this.src='/images/fallback.jpg'">
                    <div class="space-y-1">
                        <div class="text-sm text-gray-300"><span class="text-gray-500">Email:</span> <span id="detail-client-email" class="font-mono">-</span></div>
                        <div class="text-sm text-gray-300"><span class="text-gray-500">Phone:</span> <span id="detail-client-phone" class="font-mono">-</span></div>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1.5">Address</span>
                    <div id="detail-client-address" class="text-sm text-gray-300 bg-gray-950/60 p-3 rounded-xl border border-gray-800">
                        -
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-800/80 flex justify-between text-xs text-gray-400">
                    <span>Created: <strong id="detail-client-created-at" class="text-gray-300 font-normal"></strong></span>
                    <span>Updated: <strong id="detail-client-updated-at" class="text-gray-300 font-normal"></strong></span>
                </div>
            </div>

            <div class="bg-gray-850 px-6 py-3.5 border-t border-gray-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeClientDetailModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
                    Close
                </button>
                <button type="button" id="detail-client-to-edit-btn" class="px-4 py-2 rounded-xl theme-btn-gradient text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit Client</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Client Edit Modal -->
<div id="clientEditModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="client-edit-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeClientEditModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-lg">
            <form id="clientEditForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        <div>
                            <h3 id="client-edit-title" class="text-lg font-bold text-white">Edit Client</h3>
                            <p class="text-xs text-gray-400">Update client contact details &amp; profile</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeClientEditModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="modal_edit_client_name" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Name <span class="text-rose-400">*</span></label>
                            <input type="text" id="modal_edit_client_name" name="name" required
                                   class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label for="modal_edit_client_email" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Email <span class="text-rose-400">*</span></label>
                            <input type="email" id="modal_edit_client_email" name="email" required
                                   class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label for="modal_edit_client_phone" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Phone</label>
                        <input type="text" id="modal_edit_client_phone" name="phone"
                               class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                    </div>

                    <div>
                        <label for="modal_edit_client_address" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Address</label>
                        <textarea id="modal_edit_client_address" name="address" rows="2"
                                  class="w-full px-3.5 py-2 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                    </div>

                    <div>
                        <label for="modal_edit_client_image" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Profile Image</label>
                        <input type="file" id="modal_edit_client_image" name="image" accept="image/*"
                               class="w-full text-xs text-gray-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-gray-200 hover:file:bg-gray-700 cursor-pointer">
                    </div>
                </div>

                <div class="bg-gray-850 px-6 py-4 border-t border-gray-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeClientEditModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
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
let currentClientModalData = null;

function openClientDetailModal(data) {
    currentClientModalData = data;
    document.getElementById('detail-client-name').textContent = data.name || 'Client Details';
    document.getElementById('detail-client-id').textContent = data.id || 'N/A';
    document.getElementById('detail-client-email').textContent = data.email || '-';
    document.getElementById('detail-client-phone').textContent = data.phone || '-';
    document.getElementById('detail-client-address').textContent = data.address || 'No address provided.';
    document.getElementById('detail-client-image').src = data.image_url || '/images/fallback.jpg';
    document.getElementById('detail-client-created-at').textContent = data.created_at || 'N/A';
    document.getElementById('detail-client-updated-at').textContent = data.updated_at || 'N/A';

    document.getElementById('clientDetailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeClientDetailModal() {
    document.getElementById('clientDetailModal').classList.add('hidden');
    if (document.getElementById('clientEditModal').classList.contains('hidden')) {
        document.body.style.overflow = '';
    }
}

function openClientEditModal(data) {
    currentClientModalData = data;
    closeClientDetailModal();

    document.getElementById('clientEditForm').action = data.update_url;
    document.getElementById('modal_edit_client_name').value = data.name || '';
    document.getElementById('modal_edit_client_email').value = data.email || '';
    document.getElementById('modal_edit_client_phone').value = data.phone || '';
    document.getElementById('modal_edit_client_address').value = data.address || '';
    document.getElementById('modal_edit_client_image').value = '';

    document.getElementById('clientEditModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeClientEditModal() {
    document.getElementById('clientEditModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    const toEditBtn = document.getElementById('detail-client-to-edit-btn');
    if (toEditBtn) {
        toEditBtn.addEventListener('click', function() {
            if (currentClientModalData) openClientEditModal(currentClientModalData);
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeClientDetailModal();
            closeClientEditModal();
        }
    });
});
</script>
