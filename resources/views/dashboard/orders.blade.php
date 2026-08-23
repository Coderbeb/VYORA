@extends('layouts.dashboard')

@section('title', 'Live Orders Kanban - Vyora')
@section('page_title', 'Real-time Orders Board')

@section('dashboard_content')
<div class="space-y-6">
  <!-- Controls & Filters Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <div class="flex items-center gap-3 w-full sm:w-auto">
      <div class="relative flex-1 sm:w-64">
        <input type="text" id="order-search" oninput="filterOrders()" placeholder="Filter by table or item..." class="w-full pl-9 pr-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        <i data-lucide="search" class="w-4 h-4 text-[var(--text-muted)] absolute left-3 top-1/2 -translate-y-1/2"></i>
      </div>
      <button onclick="loadOrders()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Refresh">
        <i data-lucide="refresh-cw" id="refresh-icon" class="w-4 h-4"></i>
      </button>
    </div>
    <div class="flex items-center gap-2 text-xs text-[var(--text-muted)]">
      <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
      <span class="font-semibold text-emerald-600 dark:text-emerald-400">Live</span>
    </div>
  </div>

  <!-- Kanban 4-Column Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-start">
    <!-- 1. Pending Column -->
    <div class="rounded-[var(--radius-lg)] bg-[var(--bg-kanban-col)] border border-[var(--border)] p-4">
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-[var(--status-pending)]"></span>
          <h3 class="font-bold text-xs uppercase tracking-wider text-[var(--text-primary)]">Pending</h3>
        </div>
        <span id="count-pending" class="px-2 py-0.5 rounded-full bg-[var(--status-pending-bg)] text-[var(--status-pending)] font-bold text-xs">0</span>
      </div>
      <div id="col-pending" class="space-y-3 min-h-[200px]">
        <div class="p-6 text-center text-xs text-[var(--text-muted)]">No pending orders</div>
      </div>
    </div>

    <!-- 2. Preparing Column -->
    <div class="rounded-[var(--radius-lg)] bg-[var(--bg-kanban-col)] border border-[var(--border)] p-4">
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-[var(--status-preparing)]"></span>
          <h3 class="font-bold text-xs uppercase tracking-wider text-[var(--text-primary)]">Preparing</h3>
        </div>
        <span id="count-preparing" class="px-2 py-0.5 rounded-full bg-[var(--status-preparing-bg)] text-[var(--status-preparing)] font-bold text-xs">0</span>
      </div>
      <div id="col-preparing" class="space-y-3 min-h-[200px]">
        <div class="p-6 text-center text-xs text-[var(--text-muted)]">No orders in prep</div>
      </div>
    </div>

    <!-- 3. Ready Column -->
    <div class="rounded-[var(--radius-lg)] bg-[var(--bg-kanban-col)] border border-[var(--border)] p-4">
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-[var(--status-ready)]"></span>
          <h3 class="font-bold text-xs uppercase tracking-wider text-[var(--text-primary)]">Ready to Serve</h3>
        </div>
        <span id="count-ready" class="px-2 py-0.5 rounded-full bg-[var(--status-ready-bg)] text-[var(--status-ready)] font-bold text-xs">0</span>
      </div>
      <div id="col-ready" class="space-y-3 min-h-[200px]">
        <div class="p-6 text-center text-xs text-[var(--text-muted)]">No ready tickets</div>
      </div>
    </div>

    <!-- 4. Completed Column -->
    <div class="rounded-[var(--radius-lg)] bg-[var(--bg-kanban-col)] border border-[var(--border)] p-4">
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-[var(--status-completed)]"></span>
          <h3 class="font-bold text-xs uppercase tracking-wider text-[var(--text-primary)]">Completed</h3>
        </div>
        <span id="count-completed" class="px-2 py-0.5 rounded-full bg-[var(--status-completed-bg)] text-[var(--status-completed)] font-bold text-xs">0</span>
      </div>
      <div id="col-completed" class="space-y-3 min-h-[200px] max-h-[600px] overflow-y-auto">
        <div class="p-6 text-center text-xs text-[var(--text-muted)]">No completed orders</div>
      </div>
    </div>
  </div>
</div>

<!-- Cancel Order Reason Modal -->
<div id="cancel-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-md p-6">
    <h3 class="text-base font-bold text-[var(--text-primary)] font-serif mb-2">Cancel Order</h3>
    <p class="text-xs text-[var(--text-secondary)] mb-4">Please provide a reason for cancelling this order (e.g. 86'd out of stock, customer left).</p>
    <textarea id="cancel-reason-input" rows="3" required placeholder="Enter cancellation reason..." class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-red-500 text-[var(--text-primary)] mb-4"></textarea>
    <div class="flex justify-end gap-3">
      <button onclick="closeCancelModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Back</button>
      <button onclick="confirmCancelOrder()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-red-600 hover:bg-red-700 text-white shadow-sm">Confirm Cancel</button>
    </div>
  </div>
</div>

<script>
  let allOrders = [];
  let pendingCancelOrderId = null;

  document.addEventListener('DOMContentLoaded', () => {
    loadOrders();

    window.addEventListener('qrestro:order:new', () => loadOrders());
    window.addEventListener('qrestro:order:updated', () => loadOrders());
  });

  async function loadOrders() {
    const icon = document.getElementById('refresh-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/orders?limit=100', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        allOrders = data.data;
        renderKanban(allOrders);
      }
    } catch(e) {
      console.error(e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function filterOrders() {
    const q = document.getElementById('order-search').value.toLowerCase().trim();
    if (!q) {
      renderKanban(allOrders);
      return;
    }
    const filtered = allOrders.filter(o => {
      const matchTable = `table #${o.tableNumber}`.includes(q) || String(o.tableNumber).includes(q);
      const matchItems = o.items.some(i => i.menuItemName.toLowerCase().includes(q));
      return matchTable || matchItems;
    });
    renderKanban(filtered);
  }

  function renderKanban(orders) {
    const cols = {
      pending: document.getElementById('col-pending'),
      preparing: document.getElementById('col-preparing'),
      ready: document.getElementById('col-ready'),
      completed: document.getElementById('col-completed'),
    };

    const counts = { pending: 0, preparing: 0, ready: 0, completed: 0 };
    Object.values(cols).forEach(col => col.innerHTML = '');

    orders.forEach(order => {
      if (order.status === 'cancelled') return;
      if (counts[order.status] !== undefined) counts[order.status]++;

      const col = cols[order.status];
      if (!col) return;

      const card = createOrderCard(order);
      col.appendChild(card);
    });

    // Update count labels
    Object.keys(counts).forEach(key => {
      const badge = document.getElementById(`count-${key}`);
      if (badge) badge.textContent = counts[key];
      if (counts[key] === 0 && cols[key]) {
        cols[key].innerHTML = `<div class="p-6 text-center text-xs text-[var(--text-muted)]">No ${key} orders</div>`;
      }
    });

    lucide.createIcons();
  }

  function createOrderCard(order) {
    const card = document.createElement('div');
    card.className = 'p-4 rounded-[var(--radius-md)] bg-[var(--bg-order-card)] border border-[var(--border)] shadow-sm space-y-3 transition hover:shadow-[var(--shadow-md)]';

    const itemsHtml = order.items.map(i => `
      <div class="flex justify-between items-center text-xs">
        <span class="text-[var(--text-primary)] font-medium"><strong class="text-[var(--accent)] font-bold mr-1">${i.quantity}x</strong> ${i.menuItemName}</span>
        <span class="text-[var(--text-muted)] font-mono">₹${(i.price * i.quantity).toFixed(2)}</span>
      </div>
    `).join('');

    const notesHtml = order.notes ? `
      <div class="p-2 rounded bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-700 dark:text-amber-300 flex items-start gap-1.5">
        <i data-lucide="message-square" class="w-3.5 h-3.5 shrink-0 mt-0.5"></i>
        <span><strong>Note:</strong> ${order.notes}</span>
      </div>
    ` : '';

    let actionBtn = '';
    if (order.status === 'pending') {
      actionBtn = `
        <button onclick="updateOrderStatus('${order.id}', 'preparing')" class="flex-1 py-1.5 rounded-[var(--radius-sm)] bg-[var(--status-preparing)] hover:opacity-90 text-white font-bold text-xs shadow-sm transition">
          Accept & Cook
        </button>
      `;
    } else if (order.status === 'preparing') {
      actionBtn = `
        <button onclick="updateOrderStatus('${order.id}', 'ready')" class="flex-1 py-1.5 rounded-[var(--radius-sm)] bg-[var(--status-ready)] hover:opacity-90 text-white font-bold text-xs shadow-sm transition">
          Mark Ready
        </button>
      `;
    } else if (order.status === 'ready') {
      actionBtn = `
        <button onclick="updateOrderStatus('${order.id}', 'completed')" class="flex-1 py-1.5 rounded-[var(--radius-sm)] bg-[var(--status-completed)] hover:opacity-90 text-white font-bold text-xs shadow-sm transition">
          Served / Done
        </button>
      `;
    }

    const cancelBtn = order.status !== 'completed' ? `
      <button onclick="openCancelModal('${order.id}')" class="px-2.5 py-1.5 rounded-[var(--radius-sm)] border border-red-500/20 text-red-500 hover:bg-red-500/10 text-xs font-semibold" title="Cancel Order">
        <i data-lucide="x" class="w-3.5 h-3.5"></i>
      </button>
    ` : '';

    const locationHeader = order.orderType === 'room_service' 
      ? `<span class="font-bold text-xs bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 px-2.5 py-1 rounded-full flex items-center gap-1 font-sans">
           <i data-lucide="hotel" class="w-3.5 h-3.5 shrink-0"></i>
           <strong class="truncate max-w-[140px]">${order.hotelName || 'Partner Hotel'}</strong> • Room #${order.roomNumber}
         </span>`
      : `<span class="font-bold text-sm font-serif text-[var(--text-primary)]">Table #${order.tableNumber}</span>`;

    card.innerHTML = `
      <div class="flex items-center justify-between pb-2 border-b border-[var(--border)]">
        ${locationHeader}
        <span class="text-[11px] text-[var(--text-muted)] font-mono">${timeAgo}</span>
      </div>
      <div class="space-y-1.5 py-1">
        ${itemsHtml}
      </div>
      ${notesHtml}
      <div class="flex items-center justify-between pt-2 border-t border-[var(--border)] text-xs font-bold text-[var(--text-primary)]">
        <span>Total</span>
        <span>₹${parseFloat(order.totalAmount).toFixed(2)}</span>
      </div>
      <div class="flex items-center gap-2 pt-1">
        ${actionBtn}
        ${cancelBtn}
      </div>
    `;

    return card;
  }

  async function updateOrderStatus(id, newStatus) {
    try {
      const res = await fetch(`/api/orders/${id}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ status: newStatus }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast(`Order status updated to ${newStatus}`);
        loadOrders();
      }
    } catch(e) {
      console.error(e);
    }
  }

  function openCancelModal(id) {
    pendingCancelOrderId = id;
    document.getElementById('cancel-modal').classList.remove('hidden');
  }

  function closeCancelModal() {
    pendingCancelOrderId = null;
    document.getElementById('cancel-modal').classList.add('hidden');
    document.getElementById('cancel-reason-input').value = '';
  }

  async function confirmCancelOrder() {
    const reason = document.getElementById('cancel-reason-input').value.trim();
    if (!reason) {
      alert('Please enter a cancellation reason.');
      return;
    }
    try {
      const res = await fetch(`/api/orders/${pendingCancelOrderId}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ status: 'cancelled', cancellationReason: reason }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Order cancelled.');
        closeCancelModal();
        loadOrders();
      }
    } catch(e) {
      console.error(e);
    }
  }
</script>
@endsection
