<!-- Project Detail Modal -->
<div id="projectDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="project-detail-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeProjectDetailModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-lg">
            <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                        <i class="fas fa-project-diagram text-sm"></i>
                    </div>
                    <div>
                        <h3 id="project-detail-title" class="text-lg font-bold text-white">
                            <span id="detail-project-name">Project Details</span>
                        </h3>
                        <p class="text-xs text-gray-400">ID: #<span id="detail-project-id" class="theme-text-color font-mono font-medium"></span></p>
                    </div>
                </div>
                <button type="button" onclick="closeProjectDetailModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-gray-950/60 border border-gray-800">
                    <span class="text-xs text-gray-400 font-medium">Status</span>
                    <span id="detail-project-status" class="px-3 py-1 rounded-full text-xs font-bold">-</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-xl bg-gray-950/70 border border-gray-800">
                        <span class="text-[11px] text-gray-400 uppercase tracking-wider block mb-1">Start Date</span>
                        <span id="detail-project-start" class="text-sm font-mono text-white">-</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-gray-950/70 border border-gray-800">
                        <span class="text-[11px] text-gray-400 uppercase tracking-wider block mb-1">End Date</span>
                        <span id="detail-project-end" class="text-sm font-mono text-white">-</span>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1.5">Description</span>
                    <div id="detail-project-description" class="text-sm text-gray-300 bg-gray-950/60 p-3.5 rounded-xl border border-gray-800 max-h-28 overflow-y-auto">
                        No description provided.
                    </div>
                </div>
            </div>

            <div class="bg-gray-850 px-6 py-3.5 border-t border-gray-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeProjectDetailModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
                    Close
                </button>
                <button type="button" id="detail-project-to-edit-btn" class="px-4 py-2 rounded-xl theme-btn-gradient text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit Project</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Project Edit Modal -->
<div id="projectEditModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="project-edit-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeProjectEditModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-lg">
            <form id="projectEditForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        <div>
                            <h3 id="project-edit-title" class="text-lg font-bold text-white">Edit Project</h3>
                            <p class="text-xs text-gray-400">Update project timeline and status</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeProjectEditModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="modal_edit_project_name" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Project Name <span class="text-rose-400">*</span></label>
                        <input type="text" id="modal_edit_project_name" name="name" required
                               class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                    </div>

                    <div>
                        <label for="modal_edit_project_description" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Description <span class="text-rose-400">*</span></label>
                        <textarea id="modal_edit_project_description" name="description" rows="3" required
                                  class="w-full px-3.5 py-2 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                    </div>

                    <div>
                        <label for="modal_edit_project_status" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Status <span class="text-rose-400">*</span></label>
                        <select id="modal_edit_project_status" name="status" required
                                class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                            <option value="Completed">Completed</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Not Started">Not Started</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="modal_edit_project_start" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">Start Date <span class="text-rose-400">*</span></label>
                            <input type="date" id="modal_edit_project_start" name="start_date" required
                                   class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label for="modal_edit_project_end" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">End Date <span class="text-rose-400">*</span></label>
                            <input type="date" id="modal_edit_project_end" name="end_date" required
                                   class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-850 px-6 py-4 border-t border-gray-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeProjectEditModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
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
let currentProjectModalData = null;

function openProjectDetailModal(data) {
    currentProjectModalData = data;
    document.getElementById('detail-project-name').textContent = data.name || 'Project Details';
    document.getElementById('detail-project-id').textContent = data.id || 'N/A';
    document.getElementById('detail-project-start').textContent = data.start_date || '-';
    document.getElementById('detail-project-end').textContent = data.end_date || '-';
    document.getElementById('detail-project-description').textContent = data.description || 'No description provided.';

    const badge = document.getElementById('detail-project-status');
    badge.textContent = data.status || 'Not Started';
    badge.className = 'px-3 py-1 rounded-full text-xs font-bold ' + (
        data.status === 'Completed'
            ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
            : data.status === 'In Progress'
            ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
            : 'bg-rose-500/20 text-rose-400 border border-rose-500/30'
    );

    document.getElementById('projectDetailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeProjectDetailModal() {
    document.getElementById('projectDetailModal').classList.add('hidden');
    if (document.getElementById('projectEditModal').classList.contains('hidden')) {
        document.body.style.overflow = '';
    }
}

function openProjectEditModal(data) {
    currentProjectModalData = data;
    closeProjectDetailModal();

    document.getElementById('projectEditForm').action = data.update_url;
    document.getElementById('modal_edit_project_name').value = data.name || '';
    document.getElementById('modal_edit_project_description').value = data.description || '';
    document.getElementById('modal_edit_project_status').value = data.status || 'Not Started';
    document.getElementById('modal_edit_project_start').value = data.start_date || '';
    document.getElementById('modal_edit_project_end').value = data.end_date || '';

    document.getElementById('projectEditModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeProjectEditModal() {
    document.getElementById('projectEditModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    const toEditBtn = document.getElementById('detail-project-to-edit-btn');
    if (toEditBtn) {
        toEditBtn.addEventListener('click', function() {
            if (currentProjectModalData) openProjectEditModal(currentProjectModalData);
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProjectDetailModal();
            closeProjectEditModal();
        }
    });
});
</script>
