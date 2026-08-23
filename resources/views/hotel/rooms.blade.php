@extends('layouts.dashboard')

@section('title', 'Hotel Rooms & PMS - Vyora')
@section('page_title', 'Rooms & Live Occupancy Grid')

@section('dashboard_content')
<div class="space-y-6">
  <!-- Top Bar: Status Filters & Quick Actions -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <!-- Status Filter Buttons -->
    <div class="flex flex-wrap items-center gap-2">
      <button onclick="filterRooms('all')" id="btn-filter-all" class="px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm">
        All (<span id="count-all">0</span>)
      </button>
      <button onclick="filterRooms('occupied')" id="btn-filter-occupied" class="px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        Occupied (<span id="count-occupied">0</span>)
      </button>
      <button onclick="filterRooms('vacant_clean')" id="btn-filter-clean" class="px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        Vacant Clean (<span id="count-clean">0</span>)
      </button>
      <button onclick="filterRooms('vacant_dirty')" id="btn-filter-dirty" class="px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        Vacant Dirty (<span id="count-dirty">0</span>)
      </button>
      <button onclick="filterRooms('maintenance')" id="btn-filter-maint" class="px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        Maintenance (<span id="count-maint">0</span>)
      </button>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-2 w-full sm:w-auto">
      <button onclick="openAddRoomModal()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm transition flex items-center gap-1.5">
        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
        <span>Add Room</span>
      </button>
      <button onclick="openCategoryModal()" class="px-3 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] transition flex items-center gap-1.5">
        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
        <span>Room Types</span>
      </button>
      <button onclick="loadRooms()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Refresh">
        <i data-lucide="refresh-cw" id="room-refresh-icon" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <!-- Room Grid (Minimum 2 in a row on mobile) -->
  <div id="rooms-grid" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
    <div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">Loading hotel rooms...</div>
  </div>
</div>

<!-- Check-In Guest Modal -->
<div id="checkin-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
      <div>
        <h3 id="checkin-modal-title" class="font-serif font-bold text-lg text-[var(--text-primary)]">Guest Check-In</h3>
        <p class="text-xs text-[var(--text-secondary)]">Assign guest to room and generate fresh session QR.</p>
      </div>
      <button onclick="closeCheckinModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form onsubmit="submitCheckin(event)" class="space-y-4">
      <input type="hidden" id="checkin-room-id">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Guest Full Name *</label>
          <input type="text" id="checkin-name" required placeholder="e.g. John Doe" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Phone Number *</label>
          <input type="text" id="checkin-phone" required placeholder="+1 555-0199" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Email Address</label>
          <input type="email" id="checkin-email" placeholder="john@example.com" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">ID Document Type</label>
          <select id="checkin-id-type" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
            <option value="Passport">Passport</option>
            <option value="Driver License">Driver's License</option>
            <option value="National ID">National ID Card</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">ID Document Number</label>
        <input type="text" id="checkin-id-num" placeholder="e.g. X18293847" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Nightly Rate (₹) *</label>
          <input type="number" step="0.01" min="0" id="checkin-rate" required class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Advance Deposit (₹)</label>
          <input type="number" step="0.01" min="0" id="checkin-advance" value="0.00" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Expected Check-out</label>
          <input type="date" id="checkin-checkout-date" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Special Preferences / Notes</label>
        <textarea id="checkin-notes" rows="2" placeholder="e.g. High floor, extra pillows, early check-in" class="w-full px-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]"></textarea>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-[var(--border)]">
        <button type="button" onclick="closeCheckinModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Cancel</button>
        <button type="submit" id="checkin-submit-btn" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">Complete Check-In</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Room Modal -->
<div id="add-room-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-sm p-6">
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
      <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Add Room</h3>
      <button onclick="closeAddRoomModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <form onsubmit="submitAddRoom(event)" class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Room Number / Name *</label>
        <input type="text" id="new-room-number" required placeholder="e.g. 101, 204, Villa 1" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
      </div>
      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Room Category</label>
        <select id="new-room-category" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          <option value="">Standard Room</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Floor</label>
        <input type="number" min="1" id="new-room-floor" value="1" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
      </div>
      <div id="add-room-error" class="text-xs text-red-500 hidden"></div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeAddRoomModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Cancel</button>
        <button type="submit" id="add-room-submit-btn" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm">Save Room</button>
      </div>
    </form>
  </div>
</div>

<!-- Room QR Code Modal -->
<div id="room-qr-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-sm p-6 text-center">
    <div class="flex justify-between items-center pb-3 border-b border-[var(--border)] mb-4">
      <h3 id="room-qr-modal-title" class="font-serif font-bold text-lg text-[var(--text-primary)]">Room # QR Code</h3>
      <button onclick="closeRoomQrModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- QR Code Display -->
    <div class="p-6 rounded-[var(--radius-lg)] bg-white border border-[var(--border)] shadow-inner inline-block my-2">
      <img id="room-qr-img" src="" alt="Room QR" class="w-52 h-52 object-contain mx-auto">
    </div>

    <p class="text-[11px] text-[var(--text-muted)] my-3 max-w-xs mx-auto truncate" id="room-qr-url">http://...</p>

    <!-- Download & Copy Buttons -->
    <div class="space-y-2 pt-2">
      <div class="grid grid-cols-2 gap-2">
        <button onclick="downloadRoomQr()" class="py-2.5 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
          <i data-lucide="download" class="w-3.5 h-3.5"></i>
          <span>Save SVG</span>
        </button>
        <button onclick="copyRoomPortalLink()" class="py-2.5 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] font-semibold text-xs transition flex items-center justify-center gap-1.5">
          <i data-lucide="copy" class="w-3.5 h-3.5"></i>
          <span>Copy URL</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Room Categories Modal -->
<div id="category-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-md p-6 relative max-h-[85vh] flex flex-col justify-between">
    <div>
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Room Categories</h3>
        <button onclick="closeCategoryModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form onsubmit="submitCategory(event)" class="space-y-3 mb-4">
        <input type="text" id="new-cat-name" required placeholder="Category Name (e.g. Deluxe Suite)" class="w-full px-3 py-1.5 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        <div class="grid grid-cols-2 gap-2">
          <input type="number" step="0.01" id="new-cat-price" placeholder="Base Price ($)" class="px-3 py-1.5 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          <input type="number" min="1" id="new-cat-cap" placeholder="Max Guests (2)" class="px-3 py-1.5 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <button type="submit" class="w-full py-1.5 bg-[var(--accent)] text-white font-bold text-xs rounded-[var(--radius-sm)] hover:bg-[var(--accent-hover)]">+ Add Category</button>
      </form>

      <div id="category-items-list" class="space-y-2 max-h-48 overflow-y-auto divide-y divide-[var(--border)]">
        <!-- Populated by JS -->
      </div>
    </div>

    <div class="pt-4 border-t border-[var(--border)] text-right">
      <button onclick="closeCategoryModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Done</button>
    </div>
  </div>
</div>

<script>
  let allRooms = [];
  let allCategories = [];
  let activeFilter = 'all';
  let selectedQrRoom = null;

  document.addEventListener('DOMContentLoaded', () => {
    loadRooms();
    loadCategories();

    window.addEventListener('qrestro:room:status', () => loadRooms());
  });

  async function loadCategories() {
    try {
      const res = await fetch('/api/hotel/categories', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        allCategories = data.data;
        renderCategoryOptions();
      }
    } catch(e) {
      console.error(e);
    }
  }

  function renderCategoryOptions() {
    const select = document.getElementById('new-room-category');
    const catList = document.getElementById('category-items-list');

    select.innerHTML = '<option value="">Standard Room</option>';
    catList.innerHTML = '';

    allCategories.forEach(cat => {
      const opt = document.createElement('option');
      opt.value = cat.id;
      opt.textContent = `${cat.name} ($${parseFloat(cat.base_price).toFixed(2)}/night)`;
      select.appendChild(opt);

      const div = document.createElement('div');
      div.className = 'flex items-center justify-between py-2 text-xs';
      div.innerHTML = `
        <div>
          <p class="font-bold text-[var(--text-primary)]">${cat.name}</p>
          <span class="text-[10px] text-[var(--text-muted)]">$${parseFloat(cat.base_price).toFixed(2)}/night • Max ${cat.max_occupancy} guests</span>
        </div>
        <button onclick="deleteCategory('${cat.id}')" class="p-1 text-red-500 hover:text-red-700" title="Delete">
          <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
        </button>
      `;
      catList.appendChild(div);
    });

    lucide.createIcons();
  }

  async function loadRooms() {
    const icon = document.getElementById('room-refresh-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/hotel/rooms', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        allRooms = data.data;
        updateCounts();
        renderRoomGrid();
      }
    } catch(e) {
      console.error(e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function updateCounts() {
    document.getElementById('count-all').textContent = allRooms.length;
    document.getElementById('count-occupied').textContent = allRooms.filter(r => r.status === 'occupied').length;
    document.getElementById('count-clean').textContent = allRooms.filter(r => r.status === 'vacant_clean').length;
    document.getElementById('count-dirty').textContent = allRooms.filter(r => r.status === 'vacant_dirty').length;
    document.getElementById('count-maint').textContent = allRooms.filter(r => r.status === 'maintenance').length;
  }

  function filterRooms(f) {
    activeFilter = f;
    ['all', 'occupied', 'clean', 'dirty', 'maint'].forEach(k => {
      const btn = document.getElementById(`btn-filter-${k}`);
      if (!btn) return;
      if ((f === 'all' && k === 'all') || (f === 'vacant_clean' && k === 'clean') || (f === 'vacant_dirty' && k === 'dirty') || (f === 'maintenance' && k === 'maint') || (f === 'occupied' && k === 'occupied')) {
        btn.className = 'px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm';
      } else {
        btn.className = 'px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]';
      }
    });
    renderRoomGrid();
  }

  function renderRoomGrid() {
    const grid = document.getElementById('rooms-grid');
    let filtered = allRooms;

    if (activeFilter !== 'all') {
      filtered = filtered.filter(r => r.status === activeFilter);
    }

    if (filtered.length === 0) {
      grid.innerHTML = '<div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">No rooms found in this category. Click "+ Add Room" to create a room.</div>';
      return;
    }

    grid.innerHTML = '';
    filtered.forEach(room => {
      const card = document.createElement('div');
      card.className = 'p-5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between space-y-4 transition hover:shadow-md';

      let statusBadge = '';
      let statusColor = '';
      let mainAction = '';

      if (room.status === 'vacant_clean') {
        statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 font-bold text-[11px]">Vacant Clean 🟢</span>';
        statusColor = 'border-l-4 border-l-emerald-500';
        mainAction = `
          <button onclick="openCheckinModal('${room.id}')" class="w-full py-2.5 rounded-[var(--radius-sm)] bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
            <span>Check-In Guest</span>
          </button>
        `;
      } else if (room.status === 'occupied') {
        const guestName = room.activeBooking && room.activeBooking.guest ? room.activeBooking.guest.full_name : 'Guest';
        statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-600 font-bold text-[11px] animate-pulse">Occupied 🔵</span>';
        statusColor = 'border-l-4 border-l-blue-500';
        mainAction = `
          <div class="space-y-1.5">
            <a href="/dashboard/hotel/folio/${room.activeBooking ? room.activeBooking.id : ''}" class="w-full py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
              <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
              <span>View Folio & Checkout</span>
            </a>
          </div>
        `;
      } else if (room.status === 'vacant_dirty') {
        statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 font-bold text-[11px]">Needs Cleaning 🟠</span>';
        statusColor = 'border-l-4 border-l-amber-500';
        mainAction = `
          <button onclick="updateRoomStatus('${room.id}', 'vacant_clean')" class="w-full py-2 rounded-[var(--radius-sm)] bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
            <span>Mark Cleaned & Ready</span>
          </button>
        `;
      } else {
        statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-red-500/10 text-red-600 font-bold text-[11px]">Maintenance 🔴</span>';
        statusColor = 'border-l-4 border-l-red-500';
        mainAction = `
          <button onclick="updateRoomStatus('${room.id}', 'vacant_dirty')" class="w-full py-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-xs font-semibold text-[var(--text-primary)] transition">
            Release to Housekeeping
          </button>
        `;
      }

      card.className += ` ${statusColor}`;

      const catName = room.category ? room.category.name : 'Standard Room';
      const guestInfo = room.activeBooking && room.activeBooking.guest ? `
        <div class="p-2.5 rounded bg-[var(--bg-base)] border border-[var(--border)] text-xs">
          <p class="font-bold text-[var(--text-primary)]">👤 ${room.activeBooking.guest.full_name}</p>
          <p class="text-[10px] text-[var(--text-muted)]">📞 ${room.activeBooking.guest.phone || 'No phone'}</p>
        </div>
      ` : '';

      card.innerHTML = `
        <div>
          <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
            <div>
              <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Room ${room.room_number}</h3>
              <p class="text-[11px] text-[var(--text-muted)]">${catName} • Floor ${room.floor}</p>
            </div>
            ${statusBadge}
          </div>
          <div class="py-3 space-y-2">
            ${guestInfo}
          </div>
        </div>

        <div class="space-y-2 pt-2 border-t border-[var(--border)]">
          ${mainAction}
          <div class="flex items-center justify-between pt-1">
            <button onclick="openRoomQrModal('${room.id}')" class="text-xs text-[var(--accent)] font-semibold flex items-center gap-1 hover:underline">
              <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
              <span>In-Room QR</span>
            </button>
            <select onchange="updateRoomStatus('${room.id}', this.value)" class="text-[11px] font-semibold bg-[var(--bg-base)] border border-[var(--border)] rounded px-2 py-1">
              <option value="">Change Status...</option>
              <option value="vacant_clean">Set Clean 🟢</option>
              <option value="vacant_dirty">Set Dirty 🟠</option>
              <option value="maintenance">Set Maintenance 🔴</option>
            </select>
          </div>
        </div>
      `;

      grid.appendChild(card);
    });

    lucide.createIcons();
  }

  function openCheckinModal(roomId) {
    const room = allRooms.find(r => r.id === roomId);
    if (!room) return;

    document.getElementById('checkin-room-id').value = room.id;
    document.getElementById('checkin-modal-title').textContent = `Check-In to Room ${room.room_number}`;
    document.getElementById('checkin-rate').value = room.category ? room.category.base_price : '99.00';
    document.getElementById('checkin-advance').value = '0.00';
    document.getElementById('checkin-name').value = '';
    document.getElementById('checkin-phone').value = '';
    document.getElementById('checkin-email').value = '';
    document.getElementById('checkin-id-num').value = '';
    document.getElementById('checkin-notes').value = '';

    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('checkin-checkout-date').value = tomorrow.toISOString().split('T')[0];

    document.getElementById('checkin-modal').classList.remove('hidden');
  }

  function closeCheckinModal() {
    document.getElementById('checkin-modal').classList.add('hidden');
  }

  async function submitCheckin(e) {
    e.preventDefault();
    const btn = document.getElementById('checkin-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Checking In...';

    const payload = {
      room_id: document.getElementById('checkin-room-id').value,
      full_name: document.getElementById('checkin-name').value.trim(),
      phone: document.getElementById('checkin-phone').value.trim(),
      email: document.getElementById('checkin-email').value.trim() || null,
      id_type: document.getElementById('checkin-id-type').value,
      id_number: document.getElementById('checkin-id-num').value.trim() || null,
      room_rate: parseFloat(document.getElementById('checkin-rate').value),
      advance_deposit: parseFloat(document.getElementById('checkin-advance').value) || 0.00,
      expected_check_out: document.getElementById('checkin-checkout-date').value || null,
      notes: document.getElementById('checkin-notes').value.trim() || null,
    };

    try {
      const res = await fetch('/api/hotel/check-in', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Guest checked in successfully!');
        closeCheckinModal();
        loadRooms();
      } else {
        alert(data.error?.message || 'Check-in failed');
      }
    } catch(err) {
      console.error(err);
    } finally {
      btn.disabled = false;
      btn.textContent = 'Complete Check-In';
    }
  }

  async function updateRoomStatus(roomId, newStatus) {
    if (!newStatus) return;
    try {
      const res = await fetch(`/api/hotel/rooms/${roomId}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ status: newStatus }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Room status updated');
        loadRooms();
      }
    } catch(err) {
      console.error(err);
    }
  }

  function openAddRoomModal() {
    document.getElementById('add-room-modal').classList.remove('hidden');
    document.getElementById('new-room-number').value = '';
    document.getElementById('add-room-error').classList.add('hidden');
  }

  function closeAddRoomModal() {
    document.getElementById('add-room-modal').classList.add('hidden');
  }

  async function submitAddRoom(e) {
    e.preventDefault();
    const btn = document.getElementById('add-room-submit-btn');
    const err = document.getElementById('add-room-error');
    btn.disabled = true;
    err.classList.add('hidden');

    const payload = {
      room_number: document.getElementById('new-room-number').value.trim(),
      category_id: document.getElementById('new-room-category').value || null,
      floor: parseInt(document.getElementById('new-room-floor').value) || 1,
    };

    try {
      const res = await fetch('/api/hotel/rooms', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast(`Room ${payload.room_number} added!`);
        closeAddRoomModal();
        loadRooms();
      } else {
        err.textContent = data.error?.message || 'Failed to add room';
        err.classList.remove('hidden');
      }
    } catch(e) {
      err.textContent = 'Network error. Please try again.';
      err.classList.remove('hidden');
    } finally {
      btn.disabled = false;
    }
  }

  function openRoomQrModal(roomId) {
    const room = allRooms.find(r => r.id === roomId);
    if (!room) return;
    selectedQrRoom = room;

    document.getElementById('room-qr-modal-title').textContent = `Room ${room.room_number} Guest Portal QR`;
    document.getElementById('room-qr-img').src = room.qr_code_image_url;
    document.getElementById('room-qr-url').textContent = room.qr_code_data;
    document.getElementById('room-qr-modal').classList.remove('hidden');
    lucide.createIcons();
  }

  function closeRoomQrModal() {
    selectedQrRoom = null;
    document.getElementById('room-qr-modal').classList.add('hidden');
  }

  function downloadRoomQr() {
    if (!selectedQrRoom) return;
    const a = document.createElement('a');
    a.href = selectedQrRoom.qr_code_image_url;
    a.download = `room-${selectedQrRoom.room_number}-qr.svg`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  function copyRoomPortalLink() {
    if (!selectedQrRoom) return;
    navigator.clipboard.writeText(selectedQrRoom.qr_code_data);
    window.showToast('Room Portal URL copied!');
  }

  function openCategoryModal() {
    document.getElementById('category-modal').classList.remove('hidden');
  }

  function closeCategoryModal() {
    document.getElementById('category-modal').classList.add('hidden');
  }

  async function submitCategory(e) {
    e.preventDefault();
    const name = document.getElementById('new-cat-name').value.trim();
    const price = parseFloat(document.getElementById('new-cat-price').value) || 0.00;
    const cap = parseInt(document.getElementById('new-cat-cap').value) || 2;

    try {
      const res = await fetch('/api/hotel/categories', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ name, base_price: price, max_occupancy: cap })
      });
      const data = await res.json();
      if (data.success) {
        document.getElementById('new-cat-name').value = '';
        window.showToast('Category created!');
        loadCategories();
      }
    } catch(e) {
      console.error(e);
    }
  }

  async function deleteCategory(id) {
    if (!confirm('Delete this room category?')) return;
    try {
      await fetch(`/api/hotel/categories/${id}`, {
        method: 'DELETE',
        headers: window.getAuthHeader(),
      });
      window.showToast('Category deleted');
      loadCategories();
    } catch(e) {
      console.error(e);
    }
  }
</script>
@endsection
