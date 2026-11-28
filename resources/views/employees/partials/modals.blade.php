<!-- Employee Detail Modal -->
<div id="employeeDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="employee-detail-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeEmployeeDetailModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-lg">
            <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="detail-emp-avatar" class="w-10 h-10 rounded-xl theme-bg-gradient flex items-center justify-center text-white font-bold shadow-sm">
                        E
                    </div>
                    <div>
                        <h3 id="employee-detail-title" class="text-lg font-bold text-white">
                            <span id="detail-emp-name">Employee Details</span>
                        </h3>
                        <p class="text-xs text-gray-400">ID: #<span id="detail-emp-id" class="theme-text-color font-mono font-medium"></span></p>
                    </div>
                </div>
                <button type="button" onclick="closeEmployeeDetailModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-xl bg-gray-950/70 border border-gray-800">
                        <span class="text-[11px] text-gray-400 uppercase tracking-wider block mb-1">Position</span>
                        <span id="detail-emp-position" class="text-sm font-semibold text-white">-</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-gray-950/70 border border-gray-800">
                        <span class="text-[11px] text-gray-400 uppercase tracking-wider block mb-1">Department</span>
                        <span id="detail-emp-department" class="text-sm font-semibold text-white">-</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-gray-950/60 border border-gray-800">
                    <span class="text-xs text-gray-400 font-medium">Employment Status</span>
                    <span id="detail-emp-status" class="px-3 py-1 rounded-full text-xs font-bold">Active</span>
                </div>

                <div class="pt-2 border-t border-gray-800/80 flex justify-between text-xs text-gray-400">
                    <span>Created: <strong id="detail-emp-created-at" class="text-gray-300 font-normal"></strong></span>
                    <span>Updated: <strong id="detail-emp-updated-at" class="text-gray-300 font-normal"></strong></span>
                </div>
            </div>

            <div class="bg-gray-850 px-6 py-3.5 border-t border-gray-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEmployeeDetailModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
                    Close
                </button>
                <button type="button" id="detail-emp-to-edit-btn" class="px-4 py-2 rounded-xl theme-btn-gradient text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit Employee</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Employee Edit Modal -->
<div id="employeeEditModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="employee-edit-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeEmployeeEditModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-gray-900 border border-gray-700/80 text-left shadow-2xl transition-all sm:my-8 w-full max-w-lg">
            <form id="employeeEditForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="bg-gray-850 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-user-edit text-sm"></i>
                        </div>
                        <div>
                            <h3 id="employee-edit-title" class="text-lg font-bold text-white">Edit Employee</h3>
                            <p class="text-xs text-gray-400">Update employee details &amp; status</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeEmployeeEditModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="modal_edit_emp_name" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                            Name <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" id="modal_edit_emp_name" name="name" required
                               class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="modal_edit_emp_position" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                                Position <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" id="modal_edit_emp_position" name="position" required
                                   class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                        </div>

                        <div>
                            <label for="modal_edit_emp_department" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                                Department <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" id="modal_edit_emp_department" name="department" required
                                   class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label for="modal_edit_emp_status" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                            Status <span class="text-rose-400">*</span>
                        </label>
                        <select id="modal_edit_emp_status" name="status" required
                                class="w-full px-3.5 py-2.5 bg-gray-950 border border-gray-700/80 rounded-xl text-white text-sm focus:outline-none focus:border-cyan-500">
                            <option value="Active">Active</option>
                            <option value="On Leave">On Leave</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="bg-gray-850 px-6 py-4 border-t border-gray-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeEmployeeEditModal()" class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-750 text-gray-300 text-sm font-medium transition-colors">
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
let currentEmployeeModalData = null;

function openEmployeeDetailModal(data) {
    currentEmployeeModalData = data;
    document.getElementById('detail-emp-avatar').textContent = (data.name || 'E').charAt(0).toUpperCase();
    document.getElementById('detail-emp-name').textContent = data.name || 'Employee Details';
    document.getElementById('detail-emp-id').textContent = data.id || 'N/A';
    document.getElementById('detail-emp-position').textContent = data.position || '-';
    document.getElementById('detail-emp-department').textContent = data.department || '-';
    document.getElementById('detail-emp-created-at').textContent = data.created_at || 'N/A';
    document.getElementById('detail-emp-updated-at').textContent = data.updated_at || 'N/A';

    const statusBadge = document.getElementById('detail-emp-status');
    statusBadge.textContent = data.status || 'Active';
    statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold ' + (
        data.status === 'Active'
            ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
            : data.status === 'On Leave'
            ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
            : 'bg-rose-500/20 text-rose-400 border border-rose-500/30'
    );

    document.getElementById('employeeDetailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEmployeeDetailModal() {
    document.getElementById('employeeDetailModal').classList.add('hidden');
    if (document.getElementById('employeeEditModal').classList.contains('hidden')) {
        document.body.style.overflow = '';
    }
}

function openEmployeeEditModal(data) {
    currentEmployeeModalData = data;
    closeEmployeeDetailModal();

    document.getElementById('employeeEditForm').action = data.update_url;
    document.getElementById('modal_edit_emp_name').value = data.name || '';
    document.getElementById('modal_edit_emp_position').value = data.position || '';
    document.getElementById('modal_edit_emp_department').value = data.department || '';
    document.getElementById('modal_edit_emp_status').value = data.status || 'Active';

    document.getElementById('employeeEditModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEmployeeEditModal() {
    document.getElementById('employeeEditModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    const toEditBtn = document.getElementById('detail-emp-to-edit-btn');
    if (toEditBtn) {
        toEditBtn.addEventListener('click', function() {
            if (currentEmployeeModalData) openEmployeeEditModal(currentEmployeeModalData);
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEmployeeDetailModal();
            closeEmployeeEditModal();
        }
    });
});
</script>
