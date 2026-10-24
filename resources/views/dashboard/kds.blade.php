@extends('layouts.dashboard')

@section('title', 'Kitchen Display System (KDS) - Vyora')
@section('page_title', 'Kitchen KDS Station')

@section('dashboard_content')
<div class="space-y-6">
  <!-- KDS Top Bar -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold shrink-0">
        <i data-lucide="chef-hat" class="w-5 h-5"></i>
      </div>
      <div>
        <h2 class="font-serif font-bold text-base text-[var(--text-primary)]">Chef Station Tickets</h2>
        <p class="text-xs text-[var(--text-secondary)]">Real-time incoming orders & cooking queue (FIFO).</p>
      </div>
    </div>

    <!-- Filter Tabs & Refresh -->
    <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
      <div class="flex items-center bg-[var(--bg-base)] p-1 rounded-[var(--radius-sm)] border border-[var(--border)] text-xs">
        <button onclick="setKdsFilter('all')" id="kds-filter-all" class="px-2.5 py-1 rounded-[var(--radius-sm)] font-bold bg-[var(--accent)] text-white shadow-xs transition">
          Accepted Queue (<span id="kds-count-all">0</span>)
        </button>
        <button onclick="setKdsFilter('preparing')" id="kds-filter-preparing" class="px-2.5 py-1 rounded-[var(--radius-sm)] font-semibold text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition">
          Cooking (<span id="kds-count-preparing">0</span>)
        </button>
        <button onclick="setKdsFilter('ready')" id="kds-filter-ready" class="px-2.5 py-1 rounded-[var(--radius-sm)] font-semibold text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition">
          Pass Ready (<span id="kds-count-ready">0</span>)
        </button>
      </div>

      <button onclick="loadKDS()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition shrink-0" title="Refresh">
        <i data-lucide="refresh-cw" id="kds-refresh-icon" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <!-- KDS Ticket Grid -->
  <div id="kds-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-start">
    <div class="col-span-full p-12 text-center rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] text-[var(--text-muted)]">
      <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-500 mx-auto mb-3"></i>
      <h3 class="font-bold text-sm text-[var(--text-primary)] mb-1">Kitchen All Clear!</h3>
      <p class="text-xs">No active cooking tickets in the kitchen right now.</p>
    </div>
  </div>
</div>

<script>
  let kdsOrders = [];
  let kdsActiveFilter = 'all';

  // Filter out non-kitchen items like water bottles, mineral water, and packaged drinks
  function isKitchenDish(item) {
    const name = (item.menuItemName || item.name || '').toLowerCase().trim();
    const nonKitchenKeywords = [
      'water bottle', 'bottle water', 'mineral water', 'packaged water',
      'bisleri', 'aquafina', 'kinley', 'packaged drink', 'packaged beverage'
    ];
    for (const kw of nonKitchenKeywords) {
      if (name.includes(kw)) return false;
    }
    if (name === 'water') return false;
    return true;
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadKDS();
    setInterval(updateAgeTimers, 1000);
    // Auto-poll every 5 seconds for resilience
    setInterval(loadKDS, 5000);

    window.addEventListener('qrestro:order:new', (e) => {
      loadKDS();
    });
    window.addEventListener('qrestro:order:updated', () => {
      loadKDS();
      if (window.playNotificationSound) window.playNotificationSound();
    });
  });

  function setKdsFilter(f) {
    kdsActiveFilter = f;
    ['all', 'preparing', 'ready'].forEach(k => {
      const btn = document.getElementById(`kds-filter-${k}`);
      if (!btn) return;
      if (k === f) {
        btn.className = 'px-2.5 py-1 rounded-[var(--radius-sm)] font-bold bg-[var(--accent)] text-white shadow-xs transition';
      } else {
        btn.className = 'px-2.5 py-1 rounded-[var(--radius-sm)] font-semibold text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition';
      }
    });
    renderKDS(kdsOrders);
  }

  async function loadKDS() {
    const icon = document.getElementById('kds-refresh-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/orders?limit=100', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && Array.isArray(data.data)) {
        // As requested: KDS shows ONLY accepted orders (preparing or ready), NEVER pending!
        const acceptedOrders = data.data.filter(o => o.status === 'preparing' || o.status === 'ready');

        // As requested: exclude water bottles / non-kitchen items. If an order ONLY has water bottles, hide ticket entirely from chef.
        const kitchenTickets = acceptedOrders.filter(o => {
          const kitchenItems = (o.items || []).filter(isKitchenDish);
          return kitchenItems.length > 0;
        });

        // FIFO queue: oldest tickets first
        kdsOrders = kitchenTickets.sort((a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime());
        renderKDS(kdsOrders);
      }
    } catch (e) {
      console.error('Error in loadKDS:', e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function renderKDS(orders) {
    const grid = document.getElementById('kds-grid');
    const prepCount = orders.filter(o => o.status === 'preparing').length;
    const readyCount = orders.filter(o => o.status === 'ready').length;

    const cntAll = document.getElementById('kds-count-all');
    const cntPrep = document.getElementById('kds-count-preparing');
    const cntReady = document.getElementById('kds-count-ready');
    if (cntAll) cntAll.textContent = orders.length;
    if (cntPrep) cntPrep.textContent = prepCount;
    if (cntReady) cntReady.textContent = readyCount;

    let filtered = orders;
    if (kdsActiveFilter === 'preparing') filtered = orders.filter(o => o.status === 'preparing');
    else if (kdsActiveFilter === 'ready') filtered = orders.filter(o => o.status === 'ready');

    if (filtered.length === 0) {
      grid.innerHTML = `
        <div class="col-span-full p-12 text-center rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] text-[var(--text-muted)] animate-in fade-in">
          <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-500 mx-auto mb-3"></i>
          <h3 class="font-bold text-sm text-[var(--text-primary)] mb-1">Kitchen All Clear!</h3>
          <p class="text-xs">No active cooking tickets in the queue right now.</p>
        </div>
      `;
      if (window.lucide) lucide.createIcons();
      return;
    }

    grid.innerHTML = '';
    filtered.forEach((order, idx) => {
      const card = document.createElement('div');
      const isReady = order.status === 'ready';
      const borderClass = isReady ? 'border-emerald-500/40 bg-emerald-50/[0.03]' : 'border-amber-500/40 bg-[var(--bg-surface)]';
      card.className = `p-5 rounded-[var(--radius-xl)] ${borderClass} border-2 shadow-md space-y-4 flex flex-col justify-between transition hover:border-[var(--accent)] animate-in fade-in duration-200`;

      // Filter out water bottles from ticket line items
      const kitchenItems = (order.items || []).filter(isKitchenDish);
      const itemsList = kitchenItems.map(i => `
        <li class="flex items-center justify-between text-xs py-1.5 border-b border-[var(--border)] last:border-0">
          <span class="font-bold text-[var(--text-primary)] text-sm"><strong class="text-amber-600 font-extrabold mr-1.5">${i.quantity}x</strong> ${i.menuItemName}</span>
        </li>
      `).join('');

      const notes = order.notes ? `
        <div class="p-2.5 rounded-[var(--radius-sm)] bg-amber-500/10 border border-amber-500/20 text-xs text-amber-900 font-semibold">
          <strong>Special Request:</strong> ${order.notes}
        </div>
      ` : '';

      const locBadge = order.orderType === 'room_service'
        ? `<span class="px-2.5 py-1 rounded-[var(--radius-sm)] bg-purple-600 text-white font-bold text-xs flex items-center gap-1 font-sans">
             <i data-lucide="hotel" class="w-3.5 h-3.5 shrink-0"></i>
             <span>${order.hotelName || 'Partner Hotel'} • Room #${order.roomNumber}</span>
           </span>`
        : `<span class="font-serif font-bold text-base text-[var(--text-primary)]">Table #${order.tableNumber}</span>`;

      const isReadyState = order.status === 'ready';

      const statusBadge = isReadyState
        ? `<span class="px-2.5 py-1 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-800 font-black text-[11px] uppercase tracking-wider flex items-center gap-1">
             <i data-lucide="check-check" class="w-3.5 h-3.5 text-emerald-700"></i> Pass Ready
           </span>`
        : `<span class="px-2.5 py-1 rounded-full bg-amber-100 border border-amber-300 text-amber-900 font-black text-[11px] uppercase tracking-wider flex items-center gap-1">
             <i data-lucide="flame" class="w-3.5 h-3.5 text-amber-700"></i> Cooking
           </span>`;

      const actionBtn = isReadyState
        ? `<button onclick="updateKdsStatus('${order.id}', 'completed')" class="w-full py-3 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[#023d2b] text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
             <i data-lucide="bell" class="w-4 h-4"></i>
             <span>Served to Guest</span>
           </button>`
        : `<button onclick="updateKdsStatus('${order.id}', 'ready')" class="w-full py-3 rounded-[var(--radius-md)] bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
             <i data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
             <span>Order Ready / Plate Up</span>
           </button>`;

      card.innerHTML = `
        <div>
          <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
            <div class="flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-[var(--accent)] text-white font-bold text-xs flex items-center justify-center font-mono">#${idx + 1}</span>
              ${locBadge}
            </div>
            <div class="flex items-center gap-2">
              ${statusBadge}
              <div class="flex items-center gap-1 font-mono text-xs font-bold text-amber-600 bg-amber-500/10 px-2.5 py-1 rounded-full kds-timer" data-created="${order.createdAt}">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                <span class="timer-text">0m 00s</span>
              </div>
            </div>
          </div>
          <ul class="space-y-1 my-3">
            ${itemsList}
          </ul>
          ${notes}
        </div>
        <div>
          ${actionBtn}
        </div>
      `;
      grid.appendChild(card);
    });

    if (window.lucide) lucide.createIcons();
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

  async function updateKdsStatus(orderId, nextStatus) {
    try {
      const res = await fetch(`/api/orders/${orderId}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ status: nextStatus }),
      });
      const data = await res.json();
      if (data.success) {
        const msg = nextStatus === 'preparing' ? 'Ticket moved to Cooking queue' : 'Ticket marked Ready for serving!';
        window.showToast(msg, nextStatus === 'preparing' ? 'order' : 'success');
        loadKDS();
      }
    } catch(e) {
      console.error('Error updating order:', e);
    }
  }
</script>
@endsection
