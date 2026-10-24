@extends('layouts.dashboard')

@section('title', 'Housekeeping & Services - Vyora')
@section('page_title', 'Housekeeping & Service Requests')

@section('dashboard_content')
<div class="space-y-6">
  <!-- Controls Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <div class="flex items-center gap-2">
      <button onclick="setTab('pending')" id="tab-pending" class="px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm">
        Pending (<span id="count-pending">0</span>)
      </button>
      <button onclick="setTab('in_progress')" id="tab-in-prog" class="px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        In Progress (<span id="count-in-prog">0</span>)
      </button>
      <button onclick="setTab('completed')" id="tab-completed" class="px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        Completed (<span id="count-completed">0</span>)
      </button>
    </div>

    <div class="flex items-center gap-3">
      <span class="flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
        <span class="font-semibold text-emerald-600">Live</span>
      </span>
      <button onclick="loadServices()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Refresh">
        <i data-lucide="refresh-cw" id="serv-ref-icon" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <!-- Service Requests List (Minimum 2 per row) -->
  <div id="services-grid" class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
    <div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">Loading service requests...</div>
  </div>
</div>

<script>
  let allRequests = [];
  let currentTab = 'pending';

  document.addEventListener('DOMContentLoaded', () => {
    loadServices();

    window.addEventListener('qrestro:service:request', () => loadServices());
    window.addEventListener('qrestro:service:updated', () => loadServices());
  });

  async function loadServices() {
    const icon = document.getElementById('serv-ref-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/hotel/services', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        allRequests = data.data;
        updateCounts();
        renderServices();
      }
    } catch(e) {
      console.error(e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function updateCounts() {
    document.getElementById('count-pending').textContent = allRequests.filter(r => r.status === 'pending').length;
    document.getElementById('count-in-prog').textContent = allRequests.filter(r => r.status === 'in_progress').length;
    document.getElementById('count-completed').textContent = allRequests.filter(r => r.status === 'completed').length;
  }

  function setTab(t) {
    currentTab = t;
    ['pending', 'in_progress', 'completed'].forEach(k => {
      const btn = document.getElementById(k === 'in_progress' ? 'tab-in-prog' : `tab-${k}`);
      if (!btn) return;
      if (k === t) {
        btn.className = 'px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm';
      } else {
        btn.className = 'px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]';
      }
    });
    renderServices();
  }

  function renderServices() {
    const grid = document.getElementById('services-grid');
    const filtered = allRequests.filter(r => r.status === currentTab);

    if (filtered.length === 0) {
      grid.innerHTML = `<div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">No ${currentTab.replace('_', ' ')} requests right now.</div>`;
      return;
    }

    grid.innerHTML = '';
    filtered.forEach(req => {
      const card = document.createElement('div');
      card.className = 'p-5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between space-y-4 transition hover:shadow-md';

      let actionBtn = '';
      if (req.status === 'pending') {
        actionBtn = `
          <button onclick="updateRequestStatus('${req.id}', 'in_progress')" class="w-full py-2 rounded-[var(--radius-sm)] bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
            <i data-lucide="play" class="w-3.5 h-3.5"></i>
            <span>Accept & Dispatch</span>
          </button>
        `;
      } else if (req.status === 'in_progress') {
        actionBtn = `
          <button onclick="updateRequestStatus('${req.id}', 'completed')" class="w-full py-2 rounded-[var(--radius-sm)] bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
            <i data-lucide="check" class="w-3.5 h-3.5"></i>
            <span>Mark Completed</span>
          </button>
        `;
      }

      const roomNum = req.room ? req.room.room_number : '-';
      const guestName = req.booking && req.booking.guest ? req.booking.guest.full_name : 'Guest';
      const timeStr = new Date(req.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

      let catIcon = 'bell';
      if (req.category === 'housekeeping') catIcon = 'sparkles';
      else if (req.category === 'maintenance') catIcon = 'wrench';

      card.innerHTML = `
        <div>
          <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
            <div class="flex items-center gap-2">
              <span class="font-serif font-bold text-base text-[var(--text-primary)]">Room ${roomNum}</span>
              <span class="px-2 py-0.5 rounded bg-[var(--bg-base)] text-[10px] font-bold text-[var(--accent)] uppercase">${req.category}</span>
            </div>
            <span class="font-mono text-xs text-[var(--text-muted)]">${timeStr}</span>
          </div>

          <div class="py-3 space-y-2">
            <div class="flex items-start gap-2">
              <i data-lucide="${catIcon}" class="w-4 h-4 text-[var(--accent)] shrink-0 mt-0.5"></i>
              <div>
                <h4 class="font-bold text-sm text-[var(--text-primary)]">${req.service_name}</h4>
                <p class="text-xs text-[var(--text-secondary)]">Requested by: <strong>${guestName}</strong></p>
              </div>
            </div>
            ${req.notes ? `
              <div class="p-2.5 rounded bg-[var(--bg-base)] border border-[var(--border)] text-xs text-[var(--text-secondary)]">
                <strong>Note:</strong> ${req.notes}
              </div>
            ` : ''}
          </div>
        </div>

        <div class="pt-2 border-t border-[var(--border)]">
          ${actionBtn}
        </div>
      `;

      grid.appendChild(card);
    });

    lucide.createIcons();
  }

  async function updateRequestStatus(id, status) {
    try {
      const res = await fetch(`/api/hotel/services/${id}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ status }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast(`Request marked as ${status.replace('_', ' ')}!`);
        loadServices();
      }
    } catch(err) {
      console.error(err);
    }
  }
</script>
@endsection
