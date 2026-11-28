<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">
    <!-- Sleek Toast Alert -->
    <div id="cart-flash-toasts" class="fixed top-5 right-5 z-50 space-y-2.5 max-w-sm w-full pointer-events-none">
        @if (session('success'))
            <div class="pointer-events-auto flex items-start gap-3 rounded-2xl bg-white border border-emerald-200 p-4 shadow-xl" role="status">
                <div class="h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 font-bold">✓</div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-slate-900">ជោគជ័យ (Success)</div>
                    <div class="text-xs text-slate-600 mt-0.5">{{ session('success') }}</div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm px-1">✕</button>
            </div>
        @endif
        @if (session('error'))
            <div class="pointer-events-auto flex items-start gap-3 rounded-2xl bg-white border border-rose-200 p-4 shadow-xl" role="alert">
                <div class="h-8 w-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 font-bold">!</div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-slate-900">មានបញ្ហា (Error)</div>
                    <div class="text-xs text-slate-600 mt-0.5">{{ session('error') }}</div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm px-1">✕</button>
            </div>
        @endif
    </div>

    <div class="container mx-auto max-w-5xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Your Orders</h1>
            <a href="{{ route('cart.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-xl transition duration-200 text-sm shadow-sm">
                + Create New Order
            </a>
        </div>

        @if ($purchases->isEmpty())
            <div class="bg-white border border-slate-200 rounded-2xl p-10 text-center text-slate-500">
                No orders found.
            </div>
        @else
            <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach ($purchases as $purchase)
                            @php
                                $cartData = [
                                    'id' => $purchase->id,
                                    'status' => $purchase->status,
                                    'total' => number_format((float) $purchase->total, 2),
                                    'update_url' => route('cart.update', $purchase->id),
                                    'items' => $purchase->orderItems->map(function ($item) {
                                        $name = optional($item->product)->pro_name ?? optional($item->product)->name ?? ('Product #' . $item->product_id);
                                        return [
                                            'product_id' => $item->product_id,
                                            'name' => $name,
                                            'quantity' => (int) $item->quantity,
                                            'price' => number_format((float) $item->price, 2),
                                            'subtotal' => number_format((float) $item->price * (int) $item->quantity, 2),
                                        ];
                                    })->values()->all(),
                                ];
                                $cartJson = htmlspecialchars(json_encode($cartData), ENT_QUOTES, 'UTF-8');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-semibold text-slate-800">#{{ $purchase->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-semibold text-slate-900">${{ number_format($purchase->total, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $purchase->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($purchase->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        <button type="button" onclick="openCartDetailModal({{ $cartJson }})" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-1.5 px-3 rounded-lg text-xs transition duration-200">
                                            View
                                        </button>
                                        <button type="button" onclick="openCartEditModal({{ $cartJson }})" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-3 rounded-lg text-xs transition duration-200">
                                            Edit
                                        </button>
                                        <form action="{{ route('cart.destroy', $purchase->id) }}" method="POST" onsubmit="event.preventDefault(); openCartConfirmDelete(this, '#{{ $purchase->id }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold py-1.5 px-3 rounded-lg text-xs transition duration-200">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- View Order Detail Modal -->
    <div id="cartDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" onclick="closeCartDetailModal()">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full overflow-hidden" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Order #<span id="cart-detail-id"></span></h3>
                    <p class="text-xs text-slate-500">Status: <strong id="cart-detail-status"></strong> · Total: <strong id="cart-detail-total"></strong></p>
                </div>
                <button type="button" onclick="closeCartDetailModal()" class="text-slate-400 hover:text-slate-700 text-lg px-2">✕</button>
            </div>
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500 uppercase border-b border-slate-200">
                        <tr>
                            <th class="py-2 text-left">Product</th>
                            <th class="py-2 text-center">Qty</th>
                            <th class="py-2 text-right">Price</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="cart-detail-items" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeCartDetailModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-100">Close</button>
                <button type="button" id="cart-detail-to-edit" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Edit Order</button>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div id="cartEditModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" onclick="closeCartEditModal()">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden" onclick="event.stopPropagation()">
            <form id="cartEditForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Edit Order #<span id="cart-edit-id"></span></h3>
                    <button type="button" onclick="closeCartEditModal()" class="text-slate-400 hover:text-slate-700 text-lg px-2">✕</button>
                </div>
                <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                    <div>
                        <label for="cart-edit-status" class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Status</label>
                        <select id="cart-edit-status" name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-blue-500">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-600 uppercase mb-2">Order Items</span>
                        <div id="cart-edit-items" class="space-y-2.5"></div>
                    </div>
                </div>
                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeCartEditModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="cartConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" onclick="closeCartConfirm()">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full overflow-hidden text-center" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-3 text-lg font-bold">!</div>
                <h3 class="text-base font-bold text-slate-900">បញ្ជាក់ការលុបការបញ្ជាទិញ</h3>
                <p id="cartConfirmText" class="text-sm text-slate-600 mt-1.5">តើអ្នកពិតជាចង់លុបការបញ្ជាទិញនេះមែនទេ?</p>
            </div>
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex gap-2.5">
                <button type="button" onclick="closeCartConfirm()" class="flex-1 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-100">Cancel</button>
                <button type="button" id="cartConfirmOk" class="flex-1 px-4 py-2 rounded-xl bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">Delete</button>
            </div>
        </div>
    </div>

    <script>
        let activeCartData = null;
        let pendingCartDeleteForm = null;

        function escCartHtml(str) {
            const div = document.createElement('div');
            div.textContent = String(str || '');
            return div.innerHTML;
        }

        function openCartDetailModal(data) {
            activeCartData = data;
            document.getElementById('cart-detail-id').textContent = data.id;
            document.getElementById('cart-detail-status').textContent = data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : '-';
            document.getElementById('cart-detail-total').textContent = '$' + (data.total || '0.00');

            const tbody = document.getElementById('cart-detail-items');
            if (!data.items || data.items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-slate-400">No items in this order.</td></tr>';
            } else {
                tbody.innerHTML = data.items.map(it => `
                    <tr>
                        <td class="py-2.5 text-slate-800 font-medium">${escCartHtml(it.name)}</td>
                        <td class="py-2.5 text-center">${it.quantity}</td>
                        <td class="py-2.5 text-right font-mono">$${escCartHtml(it.price)}</td>
                        <td class="py-2.5 text-right font-mono font-semibold">$${escCartHtml(it.subtotal)}</td>
                    </tr>
                `).join('');
            }
            const modal = document.getElementById('cartDetailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCartDetailModal() {
            const modal = document.getElementById('cartDetailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openCartEditModal(data) {
            activeCartData = data;
            closeCartDetailModal();
            document.getElementById('cart-edit-id').textContent = data.id;
            document.getElementById('cartEditForm').action = data.update_url;
            document.getElementById('cart-edit-status').value = data.status || 'pending';

            const wrap = document.getElementById('cart-edit-items');
            if (!data.items || data.items.length === 0) {
                wrap.innerHTML = '<p class="text-xs text-slate-400">No items found.</p>';
            } else {
                wrap.innerHTML = data.items.map((it, idx) => `
                    <div class="flex items-center justify-between gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-sm font-medium text-slate-700 truncate">${escCartHtml(it.name)}</span>
                        <input type="hidden" name="items[${idx}][product_id]" value="${it.product_id}">
                        <input type="number" min="1" name="items[${idx}][quantity]" value="${it.quantity}" class="w-20 rounded-lg border border-slate-300 px-2.5 py-1 text-sm text-center">
                    </div>
                `).join('');
            }
            const modal = document.getElementById('cartEditModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCartEditModal() {
            const modal = document.getElementById('cartEditModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openCartConfirmDelete(form, label) {
            pendingCartDeleteForm = form;
            document.getElementById('cartConfirmText').textContent = `តើអ្នកពិតជាចង់លុបការបញ្ជាទិញ ${label} មែនទេ?`;
            const modal = document.getElementById('cartConfirmModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCartConfirm() {
            const modal = document.getElementById('cartConfirmModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingCartDeleteForm = null;
        }

        document.getElementById('cart-detail-to-edit').addEventListener('click', () => {
            if (activeCartData) openCartEditModal(activeCartData);
        });
        document.getElementById('cartConfirmOk').addEventListener('click', () => {
            if (pendingCartDeleteForm) pendingCartDeleteForm.submit();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeCartDetailModal();
                closeCartEditModal();
                closeCartConfirm();
            }
        });
        setTimeout(() => {
            document.querySelectorAll('#cart-flash-toasts > div').forEach(el => el.remove());
        }, 4500);
    </script>
</body>
</html>
