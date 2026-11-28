<!-- Slide Detail Modal -->
<div id="slideDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="slide-detail-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeSlideDetailModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-2xl">
            <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                        <i class="fas fa-images text-sm"></i>
                    </div>
                    <div>
                        <h3 id="slide-detail-title" class="text-lg font-bold text-white">
                            <span id="detail-slide-title">Slide Details</span>
                        </h3>
                        <p class="text-xs text-gray-400">Slide ID: #<span id="detail-slide-id" class="theme-text-color font-mono font-medium"></span></p>
                    </div>
                </div>
                <button type="button" onclick="closeSlideDetailModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" id="detail-slide-gallery">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block mb-1.5">Image 1</span>
                        <div class="h-36 rounded-xl bg-gray-950 border border-gray-800 overflow-hidden flex items-center justify-center">
                            <img id="detail-slide-img1" src="/images/fallback.jpg" alt="Slide Image 1" class="w-full h-full object-cover" onerror="this.src='/images/fallback.jpg'">
                        </div>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block mb-1.5">Image 2</span>
                        <div class="h-36 rounded-xl bg-gray-950 border border-gray-800 overflow-hidden flex items-center justify-center">
                            <img id="detail-slide-img2" src="/images/fallback.jpg" alt="Slide Image 2" class="w-full h-full object-cover" onerror="this.src='/images/fallback.jpg'">
                        </div>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block mb-1.5">Image 3</span>
                        <div class="h-36 rounded-xl bg-gray-950 border border-gray-800 overflow-hidden flex items-center justify-center">
                            <img id="detail-slide-img3" src="/images/fallback.jpg" alt="Slide Image 3" class="w-full h-full object-cover" onerror="this.src='/images/fallback.jpg'">
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1.5">Description</span>
                    <div id="detail-slide-description" class="text-sm text-gray-300 bg-gray-950/60 p-3.5 rounded-xl border border-gray-800 max-h-32 overflow-y-auto leading-relaxed">
                        No description provided.
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-800/80 flex justify-between text-xs text-gray-400">
                    <span>Created: <strong id="detail-slide-created-at" class="text-gray-300 font-normal"></strong></span>
                    <span>Updated: <strong id="detail-slide-updated-at" class="text-gray-300 font-normal"></strong></span>
                </div>
            </div>

            <div class="bg-gray-850 px-6 py-3.5 border-t border-gray-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeSlideDetailModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
                    Close
                </button>
                <button type="button" id="detail-slide-to-edit-btn" class="px-4 py-2 rounded-xl theme-btn-gradient text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit Slide</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Slide Edit Modal -->
<div id="slideEditModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="slide-edit-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeSlideEditModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-3xl">
            <form id="slideEditForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        <div>
                            <h3 id="slide-edit-title" class="text-lg font-bold text-white">Edit Slide Hero</h3>
                            <p class="text-xs text-gray-400">Update title, description, and banner images</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeSlideEditModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="modal_edit_slide_title" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                                Main Title <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" id="modal_edit_slide_title" name="main_title" required
                                   class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label for="modal_edit_slide_desc" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                                Description <span class="text-rose-400">*</span>
                            </label>
                            <textarea id="modal_edit_slide_desc" name="description" rows="2" required
                                      class="w-full px-3.5 py-2 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-gray-800/80">
                        @foreach ([1, 2, 3] as $idx)
                            @php $field = "image{$idx}"; @endphp
                            <div class="p-3.5 rounded-xl bg-gray-950/60 border border-gray-800 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-300 uppercase tracking-wider">Image {{ $idx }}</span>
                                    <div class="flex items-center gap-3 text-xs text-gray-400">
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="radio" name="{{ $field }}_type" value="file" onchange="toggleSlideImageInput('{{ $field }}', 'file')" class="text-cyan-500">
                                            <span>File</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="radio" name="{{ $field }}_type" value="url" onchange="toggleSlideImageInput('{{ $field }}', 'url')" class="text-cyan-500">
                                            <span>URL</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="h-28 rounded-lg bg-gray-900 border border-gray-800 overflow-hidden flex items-center justify-center">
                                    <img id="modal_edit_{{ $field }}_preview" src="/images/fallback.jpg" alt="Preview {{ $idx }}" class="w-full h-full object-cover" onerror="this.src='/images/fallback.jpg'">
                                </div>

                                <input type="file" name="{{ $field }}" id="modal_edit_{{ $field }}_file" accept="image/*"
                                       onchange="previewSlideFile(this, 'modal_edit_{{ $field }}_preview')"
                                       class="w-full text-xs text-gray-400 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-gray-200 hover:file:bg-gray-700 cursor-pointer">

                                <input type="url" name="{{ $field }}_url" id="modal_edit_{{ $field }}_url" placeholder="https://..."
                                       oninput="if(this.value) document.getElementById('modal_edit_{{ $field }}_preview').src = this.value"
                                       class="hidden w-full px-3 py-1.5 bg-gray-900 border border-gray-700 rounded-lg text-xs text-white focus:outline-none focus:border-cyan-500">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-850 px-6 py-4 border-t border-gray-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeSlideEditModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
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
let currentSlideModalData = null;

function toggleSlideImageInput(field, mode) {
    const fileInput = document.getElementById(`modal_edit_${field}_file`);
    const urlInput = document.getElementById(`modal_edit_${field}_url`);
    if (mode === 'url') {
        urlInput.classList.remove('hidden');
        fileInput.classList.add('hidden');
    } else {
        fileInput.classList.remove('hidden');
        urlInput.classList.add('hidden');
    }
}

function previewSlideFile(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function openSlideDetailModal(data) {
    currentSlideModalData = data;
    document.getElementById('detail-slide-title').textContent = data.main_title || 'Slide Details';
    document.getElementById('detail-slide-id').textContent = data.id || 'N/A';
    document.getElementById('detail-slide-description').textContent = data.description || 'No description provided.';
    document.getElementById('detail-slide-img1').src = data.image1_url || '/images/fallback.jpg';
    document.getElementById('detail-slide-img2').src = data.image2_url || '/images/fallback.jpg';
    document.getElementById('detail-slide-img3').src = data.image3_url || '/images/fallback.jpg';
    document.getElementById('detail-slide-created-at').textContent = data.created_at || 'N/A';
    document.getElementById('detail-slide-updated-at').textContent = data.updated_at || 'N/A';

    document.getElementById('slideDetailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSlideDetailModal() {
    document.getElementById('slideDetailModal').classList.add('hidden');
    if (document.getElementById('slideEditModal').classList.contains('hidden')) {
        document.body.style.overflow = '';
    }
}

function openSlideEditModal(data) {
    currentSlideModalData = data;
    closeSlideDetailModal();

    document.getElementById('slideEditForm').action = data.update_url;
    document.getElementById('modal_edit_slide_title').value = data.main_title || '';
    document.getElementById('modal_edit_slide_desc').value = data.description || '';

    [1, 2, 3].forEach((idx) => {
        const field = `image${idx}`;
        const isUrl = Boolean(data[`${field}_is_url`]);
        const mode = isUrl ? 'url' : 'file';
        const radio = document.querySelector(`#slideEditModal input[name="${field}_type"][value="${mode}"]`);
        if (radio) radio.checked = true;

        const fileEl = document.getElementById(`modal_edit_${field}_file`);
        const urlEl = document.getElementById(`modal_edit_${field}_url`);
        if (fileEl) fileEl.value = '';
        if (urlEl) urlEl.value = isUrl ? (data[field] || '') : '';

        document.getElementById(`modal_edit_${field}_preview`).src = data[`${field}_url`] || '/images/fallback.jpg';
        toggleSlideImageInput(field, mode);
    });

    document.getElementById('slideEditModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSlideEditModal() {
    document.getElementById('slideEditModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    const toEditBtn = document.getElementById('detail-slide-to-edit-btn');
    if (toEditBtn) {
        toEditBtn.addEventListener('click', function() {
            if (currentSlideModalData) openSlideEditModal(currentSlideModalData);
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSlideDetailModal();
            closeSlideEditModal();
        }
    });
});
</script>
