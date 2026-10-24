@extends('layouts.dashboard')

@section('title', 'Guest Directory & Stays - Vyora')
@section('page_title', 'Guests & Booking Directory')

@section('dashboard_content')
<div class="space-y-6">
  <!-- Controls Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <div class="flex items-center gap-2">
      <button onclick="setFilter('active')" id="btn-active" class="px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm">
        In-House Active
      </button>
      <button onclick="setFilter('checked_out')" id="btn-past" class="px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        Past Check-Outs
      </button>
      <button onclick="setFilter('all')" id="btn-all" class="px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        All Records
      </button>
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto">
      <div class="relative flex-1 sm:w-64">
        <input type="text" id="guest-search" oninput="applySearch()" placeholder="Search guest name, phone, room..." class="w-full pl-8 pr-3 py-1.5 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        <i data-lucide="search" class="w-3.5 h-3.5 text-[var(--text-muted)] absolute left-2.5 top-1/2 -translate-y-1/2"></i>
      </div>
      <button onclick="loadBookings()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Refresh">
        <i data-lucide="refresh-cw" id="guest-refresh-icon" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <!-- Bookings & Guests Table -->
  <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-[var(--bg-base)] text-[var(--text-muted)] uppercase tracking-wider text-[10px] border-b border-[var(--border)]">
          <tr>
            <th class="p-3">Guest Profile</th>
            <th class="p-3">Room Assigned</th>
            <th class="p-3">Check-In / Out</th>
            <th class="p-3">Nightly Rate</th>
            <th class="p-3">Deposit</th>
            <th class="p-3">Status</th>
            <th class="p-3">Folio Invoice</th>
          </tr>
        </thead>
        <tbody id="guests-tbody" class="divide-y divide-[var(--border)]">
          <tr><td colspan="7" class="p-8 text-center text-[var(--text-muted)]">Loading bookings directory...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  let allBookings = [];
  let currentFilter = 'active';

  document.addEventListener('DOMContentLoaded', () => {
    loadBookings();
  });

  async function loadBookings() {
    const icon = document.getElementById('guest-refresh-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch(`/api/hotel/bookings?status=${currentFilter}`, { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        allBookings = data.data;
        renderBookings();
      }
    } catch(e) {
      console.error(e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function setFilter(f) {
    currentFilter = f;
    ['active', 'checked_out', 'all'].forEach(k => {
      const btn = document.getElementById(`btn-${k === 'checked_out' ? 'past' : k}`);
      if (!btn) return;
      if (k === f) {
        btn.className = 'px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm';
      } else {
        btn.className = 'px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]';
      }
    });
    loadBookings();
  }

  function applySearch() {
    renderBookings();
  }

  function renderBookings() {
    const tbody = document.getElementById('guests-tbody');
    const q = document.getElementById('guest-search').value.toLowerCase().trim();

    let filtered = allBookings;
    if (q) {
      filtered = filtered.filter(b => {
        const guestName = (b.guest?.full_name || '').toLowerCase();
        const phone = (b.guest?.phone || '').toLowerCase();
        const roomNum = String(b.room?.room_number || '').toLowerCase();
        return guestName.includes(q) || phone.includes(q) || roomNum.includes(q);
      });
    }

    if (filtered.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-[var(--text-muted)]">No guests found.</td></tr>';
      return;
    }

    tbody.innerHTML = '';
    filtered.forEach(b => {
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-[var(--bg-hover)] transition';

      const checkInTime = new Date(b.check_in).toLocaleDateString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
      const checkOutTime = b.check_out ? new Date(b.check_out).toLocaleDateString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : (b.expected_check_out ? 'Exp: ' + new Date(b.expected_check_out).toLocaleDateString([], { month: 'short', day: 'numeric' }) : 'Active Stay');

      tr.innerHTML = `
        <td class="p-3">
          <p class="font-bold text-[var(--text-primary)]">${b.guest ? b.guest.full_name : 'Unnamed Guest'}</p>
          <p class="text-[10px] text-[var(--text-muted)] flex items-center gap-1 mt-0.5">
            <i data-lucide="phone" class="w-3 h-3 text-[var(--text-muted)]"></i>
            <span>${b.guest?.phone || 'No phone'}</span>
            <span>•</span>
            <span>${b.guest?.id_type || 'ID'}: ${b.guest?.id_number || '-'}</span>
          </p>
        </td>
        <td class="p-3">
          <span class="font-serif font-bold text-[var(--text-primary)]">Room ${b.room ? b.room.room_number : '-'}</span>
          <p class="text-[10px] text-[var(--text-muted)]">${b.room?.category?.name || 'Standard'}</p>
        </td>
        <td class="p-3 font-mono text-[11px] text-[var(--text-secondary)]">
          <p>In: ${checkInTime}</p>
          <p>Out: ${checkOutTime}</p>
        </td>
        <td class="p-3 font-mono font-bold text-[var(--text-primary)]">
          $${parseFloat(b.room_rate).toFixed(2)}/nt
        </td>
        <td class="p-3 font-mono text-emerald-600 font-bold">
          $${parseFloat(b.advance_deposit).toFixed(2)}
        </td>
        <td class="p-3">
          <span class="px-2.5 py-0.5 rounded-full ${b.status === 'active' ? 'bg-blue-500/10 text-blue-600 font-bold' : 'bg-gray-500/10 text-gray-500'} text-[11px]">
            ${b.status === 'active' ? 'In-House' : 'Checked Out'}
          </span>
        </td>
        <td class="p-3">
          <a href="/dashboard/hotel/folio/${b.id}" class="px-3 py-1 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition inline-flex items-center gap-1">
            <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
            <span>Folio</span>
          </a>
        </td>
      `;
      tbody.appendChild(tr);
    });

    if (window.lucide) lucide.createIcons();

    lucide.createIcons();
  }
</script>
@endsection
