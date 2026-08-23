@extends('layouts.dashboard')

@section('title', 'Kitchen Display System (KDS) - Vyora')
@section('page_title', 'Kitchen KDS Station')

@section('dashboard_content')
<div class="space-y-6">
  <!-- KDS Top Bar -->
  <div class="flex items-center justify-between p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold">
        <i data-lucide="chef-hat" class="w-5 h-5"></i>
      </div>
      <div>
        <h2 class="font-serif font-bold text-base text-[var(--text-primary)]">Chef Station Tickets</h2>
        <p class="text-xs text-[var(--text-secondary)]">Showing orders currently in preparation (FIFO Queue).</p>
      </div>
    </div>
    <div class="flex items-center gap-3">
      <button onclick="loadKDS()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Refresh">
        <i data-lucide="refresh-cw" id="kds-refresh-icon" class="w-4 h-4"></i>
      </button>
      <span id="kds-active-count" class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 font-bold text-xs">
        0 Active Tickets
      </span>
    </div>
  </div>

  <!-- KDS Ticket Grid -->
  <div id="kds-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-start">
    <div class="col-span-full p-12 text-center rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] text-[var(--text-muted)]">
      <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-500 mx-auto mb-3"></i>
      <h3 class="font-bold text-sm text-[var(--text-primary)] mb-1">Kitchen All Clear!</h3>
      <p class="text-xs">No pending meal tickets in the cooking queue right now.</p>
    </div>
  </div>
</div>

<script>
  let kdsOrders = [];

  document.addEventListener('DOMContentLoaded', () => {
    loadKDS();
    setInterval(updateAgeTimers, 1000);

    window.addEventListener('qrestro:order:new', () => loadKDS());
    window.addEventListener('qrestro:order:updated', () => loadKDS());
  });

  async function loadKDS() {
    const icon = document.getElementById('kds-refresh-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/orders?status=preparing&limit=100', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        // Sort oldest first (FIFO)
        kdsOrders = data.data.sort((a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime());
        renderKDS(kdsOrders);
      }
    } catch (e) {
      console.error(e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function renderKDS(orders) {
    const grid = document.getElementById('kds-grid');
    const badge = document.getElementById('kds-active-count');
    badge.textContent = `${orders.length} Active Tickets`;

    if (orders.length === 0) {
      grid.innerHTML = `
        <div class="col-span-full p-12 text-center rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] text-[var(--text-muted)]">
          <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-500 mx-auto mb-3"></i>
          <h3 class="font-bold text-sm text-[var(--text-primary)] mb-1">Kitchen All Clear!</h3>
          <p class="text-xs">No pending meal tickets in the cooking queue right now.</p>
        </div>
      `;
      lucide.createIcons();
      return;
    }

    grid.innerHTML = '';
    orders.forEach((order, idx) => {
      const card = document.createElement('div');
      card.className = 'p-5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border-2 border-[var(--border)] shadow-md space-y-4 flex flex-col justify-between transition hover:border-[var(--accent)]';

      const itemsList = order.items.map(i => `
        <li class="flex items-center justify-between text-xs py-1 border-b border-[var(--border)] last:border-0">
          <span class="font-bold text-[var(--text-primary)] text-sm"><strong class="text-amber-600 font-extrabold mr-1">${i.quantity}x</strong> ${i.menuItemName}</span>
        </li>
      `).join('');

      const notes = order.notes ? `
        <div class="p-2.5 rounded-[var(--radius-sm)] bg-amber-500/10 border border-amber-500/20 text-xs text-amber-800 dark:text-amber-200">
          <strong>Special Request:</strong> ${order.notes}
        </div>
      ` : '';

      const locBadge = order.orderType === 'room_service'
        ? `<span class="px-2.5 py-1 rounded-[var(--radius-sm)] bg-purple-600 text-white font-bold text-xs flex items-center gap-1">
             <i data-lucide="hotel" class="w-3.5 h-3.5 shrink-0"></i>
             <span>${order.hotelName || 'Partner Hotel'} • Room #${order.roomNumber}</span>
           </span>`
        : `<span class="font-serif font-bold text-base text-[var(--text-primary)]">Table #${order.tableNumber}</span>`;

      card.innerHTML = `
        <div>
          <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
            <div class="flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-[var(--accent)] text-white font-bold text-xs flex items-center justify-center font-mono">#${idx + 1}</span>
              ${locBadge}
            </div>
            <div class="flex items-center gap-1 font-mono text-xs font-bold text-amber-600 bg-amber-500/10 px-2.5 py-1 rounded-full kds-timer" data-created="${order.createdAt}">
              <i data-lucide="clock" class="w-3.5 h-3.5"></i>
              <span class="timer-text">0m 00s</span>
            </div>
          </div>
          <ul class="space-y-1 my-3">
            ${itemsList}
          </ul>
          ${notes}
        </div>
        <button onclick="markKdsReady('${order.id}')" class="w-full py-3 rounded-[var(--radius-md)] bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
          <i data-lucide="check" class="w-4 h-4"></i>
          <span>Order Ready / Plate Up</span>
        </button>
      `;
      grid.appendChild(card);
    });

    lucide.createIcons();
    updateAgeTimers();
  }

  function updateAgeTimers() {
    const timers = document.querySelectorAll('.kds-timer');
    timers.forEach(t => {
      const created = new Date(t.getAttribute('data-created')).getTime();
      const elapsedSec = Math.floor((Date.now() - created) / 1000);
      const mins = Math.floor(elapsedSec / 60);
      const secs = elapsedSec % 60;
      const text = `${mins}m ${secs < 10 ? '0' : ''}${secs}s`;
      const span = t.querySelector('.timer-text');
      if (span) span.textContent = text;

      // Color coding based on ticket wait time
      if (mins >= 15) {
        t.className = 'flex items-center gap-1 font-mono text-xs font-bold text-red-600 bg-red-500/10 px-2.5 py-1 rounded-full kds-timer animate-pulse';
      } else if (mins >= 10) {
        t.className = 'flex items-center gap-1 font-mono text-xs font-bold text-amber-600 bg-amber-500/10 px-2.5 py-1 rounded-full kds-timer';
      }
    });
  }

  async function markKdsReady(orderId) {
    try {
      const res = await fetch(`/api/orders/${orderId}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ status: 'ready' }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Ticket marked Ready for serving!');
        loadKDS();
      }
    } catch(e) {
      console.error(e);
    }
  }
</script>
@endsection
