@extends('layouts.app')

@section('title', 'Guest Room Portal - Vyora')

@section('content')
<div class="min-h-screen pb-32 bg-[var(--bg-base)] text-[var(--text-primary)]">
  <!-- Top Bar -->
  <header class="sticky top-0 z-30 bg-[var(--bg-surface)]/90 backdrop-blur-md border-b border-[var(--border)] px-4 py-3 shadow-sm">
    <div class="max-w-2xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[var(--accent)] text-white flex items-center justify-center font-bold text-base font-serif">
          H
        </div>
        <div>
          <h1 id="hotel-name-header" class="font-serif font-bold text-base tracking-tight truncate max-w-[180px] sm:max-w-xs">Loading Portal...</h1>
          <div class="flex items-center gap-1.5">
            <span class="inline-block px-2 py-0.5 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-[10px] uppercase tracking-wide">
              Room #{{ $roomNumber }}
            </span>
            <span id="guest-greeting-badge" class="text-[11px] text-[var(--text-secondary)] font-medium"></span>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button id="room-tracking-btn" onclick="toggleTrackingSheet()" class="hidden px-2.5 py-1.5 rounded-[var(--radius-sm)] bg-amber-500/10 text-amber-600 border border-amber-500/20 font-bold text-xs flex items-center gap-1 animate-pulse">
          <i data-lucide="clock" class="w-3.5 h-3.5"></i>
          <span>Track Status</span>
        </button>
        <button onclick="toggleTheme()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">
          <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
          <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Main Container -->
  <main class="max-w-2xl mx-auto px-4 pt-4 space-y-4">
    <!-- Portal Navigation Tabs (Dining / Housekeeping / Concierge) -->
    <div class="grid grid-cols-3 gap-2 p-1 rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
      <button onclick="switchPortalTab('dining')" id="btn-tab-dining" class="py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] text-white shadow-sm transition flex items-center justify-center gap-1.5">
        <i data-lucide="utensils" class="w-3.5 h-3.5"></i>
        <span>Dining</span>
      </button>
      <button onclick="switchPortalTab('housekeeping')" id="btn-tab-housekeeping" class="py-2 text-xs font-semibold rounded-[var(--radius-sm)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] transition flex items-center justify-center gap-1.5">
        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
        <span>Housekeeping</span>
      </button>
      <button onclick="switchPortalTab('concierge')" id="btn-tab-concierge" class="py-2 text-xs font-semibold rounded-[var(--radius-sm)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] transition flex items-center justify-center gap-1.5">
        <i data-lucide="bell" class="w-3.5 h-3.5"></i>
        <span>Concierge</span>
      </button>
    </div>

    <!-- 1. TAB: ROOM SERVICE DINING -->
    <div id="tab-content-dining" class="space-y-4">
      <!-- Provider Notice -->
      <div id="food-billing-banner" class="p-3 rounded-[var(--radius-md)] bg-blue-500/10 border border-blue-500/20 text-xs text-blue-800 dark:text-blue-200 flex items-center justify-between">
        <span id="food-billing-text">Room Service Orders are charged to your Room Folio</span>
      </div>

      <!-- Search Input -->
      <div class="relative">
        <input type="text" id="dish-search" oninput="filterDishes()" placeholder="Search room service menu..." class="w-full pl-9 pr-4 py-2.5 text-xs bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)] shadow-sm">
        <i data-lucide="search" class="w-4 h-4 text-[var(--text-muted)] absolute left-3 top-1/2 -translate-y-1/2"></i>
      </div>

      <!-- Category Filter Tabs -->
      <div id="dining-cat-tabs" class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <button onclick="selectCategory('all')" id="cat-tab-all" class="px-4 py-1.5 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm shrink-0">
          All Dishes
        </button>
      </div>

      <!-- Dishes List Container (Minimum 2 per row) -->
      <div id="dining-dishes-list" class="grid grid-cols-2 gap-3 sm:gap-4 pt-2">
        <div class="p-12 text-center text-xs text-[var(--text-muted)]">
          <span class="animate-spin inline-block mr-2">⏳</span> Loading in-room menu...
        </div>
      </div>
    </div>

    <!-- 2. TAB: HOUSEKEEPING & AMENITIES -->
    <div id="tab-content-housekeeping" class="space-y-4 hidden">
      <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-1">
        <h3 class="font-serif font-bold text-base text-[var(--text-primary)]">In-Room Housekeeping Services</h3>
        <p class="text-xs text-[var(--text-secondary)]">Tap any service below to request immediate dispatch to your room.</p>
      </div>

      <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <!-- Service 1: Clean Room -->
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] flex flex-col justify-between space-y-3 hover:border-[var(--accent)] transition">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold shrink-0">
              <i data-lucide="sparkles" class="w-4 h-4"></i>
            </div>
            <div>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">Room Refresh / Clean</h4>
              <p class="text-[11px] text-[var(--text-secondary)]">Bed making, trash disposal, and vacuuming.</p>
            </div>
          </div>
          <button onclick="requestHotelService('Room Refresh / Cleaning', 'housekeeping')" class="w-full py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition">
            Request Clean Room
          </button>
        </div>

        <!-- Service 2: Fresh Towels -->
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] flex flex-col justify-between space-y-3 hover:border-[var(--accent)] transition">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold shrink-0">
              <i data-lucide="bath" class="w-4 h-4"></i>
            </div>
            <div>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">Fresh Soft Towels</h4>
              <p class="text-[11px] text-[var(--text-secondary)]">Set of sanitized bath and face towels.</p>
            </div>
          </div>
          <button onclick="requestHotelService('Fresh Soft Towels', 'housekeeping')" class="w-full py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition">
            Request Towels
          </button>
        </div>

        <!-- Service 3: Packaged Water -->
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] flex flex-col justify-between space-y-3 hover:border-[var(--accent)] transition">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-600 flex items-center justify-center font-bold shrink-0">
              <i data-lucide="droplet" class="w-4 h-4"></i>
            </div>
            <div>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">Packaged Mineral Water</h4>
              <p class="text-[11px] text-[var(--text-secondary)]">2x Chilled 1L drinking water bottles.</p>
            </div>
          </div>
          <button onclick="requestHotelService('Packaged Mineral Water Bottles', 'housekeeping')" class="w-full py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition">
            Request Water
          </button>
        </div>

        <!-- Service 4: Toiletries Kit -->
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] flex flex-col justify-between space-y-3 hover:border-[var(--accent)] transition">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center font-bold shrink-0">
              <i data-lucide="package" class="w-4 h-4"></i>
            </div>
            <div>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">Toiletries & Dental Kit</h4>
              <p class="text-[11px] text-[var(--text-secondary)]">Shampoo, conditioner, soap & toothbrush.</p>
            </div>
          </div>
          <button onclick="requestHotelService('Toiletries & Dental Kit', 'housekeeping')" class="w-full py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition">
            Request Toiletries
          </button>
        </div>
      </div>
    </div>

    <!-- 3. TAB: CONCIERGE & FRONT DESK -->
    <div id="tab-content-concierge" class="space-y-4 hidden">
      <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-1">
        <h3 class="font-serif font-bold text-base text-[var(--text-primary)]">Concierge & Front Desk</h3>
        <p class="text-xs text-[var(--text-secondary)]">Assistance with luggage, travel, and front desk coordination.</p>
      </div>

      <div class="space-y-3">
        <!-- Concierge 1: Luggage Pickup -->
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold shrink-0">
              <i data-lucide="briefcase" class="w-4 h-4"></i>
            </div>
            <div>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">Luggage / Bellhop Assistance</h4>
              <p class="text-[11px] text-[var(--text-secondary)]">Staff to assist with baggage check-out.</p>
            </div>
          </div>
          <button onclick="requestHotelService('Bellhop Luggage Assistance', 'concierge')" class="px-4 py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm shrink-0">
            Request
          </button>
        </div>

        <!-- Concierge 2: Wakeup Call -->
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold shrink-0">
              <i data-lucide="alarm-clock" class="w-4 h-4"></i>
            </div>
            <div>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">Morning Wake-Up Call</h4>
              <p class="text-[11px] text-[var(--text-secondary)]">Front desk phone call at your preferred time.</p>
            </div>
          </div>
          <button onclick="promptWakeupCall()" class="px-4 py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm shrink-0">
            Set Call
          </button>
        </div>

        <!-- Concierge 3: Taxi / Ride -->
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-bold shrink-0">
              <i data-lucide="car" class="w-4 h-4"></i>
            </div>
            <div>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">Taxi & Airport Transfer</h4>
              <p class="text-[11px] text-[var(--text-secondary)]">Cab pickup coordination at hotel porch.</p>
            </div>
          </div>
          <button onclick="requestHotelService('Taxi / Airport Cab Request', 'concierge')" class="px-4 py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm shrink-0">
            Book Cab
          </button>
        </div>
      </div>
    </div>
  </main>

  <!-- Sticky Bottom Cart Bar -->
  <div id="room-cart-bar" class="fixed bottom-0 inset-x-0 p-4 bg-[var(--bg-surface)]/95 backdrop-blur-md border-t border-[var(--border)] z-40 hidden shadow-2xl transition-all">
    <div class="max-w-2xl mx-auto flex items-center justify-between">
      <div>
        <span id="room-cart-count" class="text-xs font-bold text-[var(--text-primary)]">0 items</span>
        <h4 id="room-cart-total" class="font-serif font-bold text-lg text-[var(--accent)]">₹0.00</h4>
      </div>
      <button onclick="openRoomCartModal()" class="px-6 py-3 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-md transition flex items-center gap-2 active:scale-95">
        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
        <span>Review Order</span>
      </button>
    </div>
  </div>

  <!-- Cart Modal / Bottom Sheet -->
  <div id="room-cart-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-t-[var(--radius-xl)] sm:rounded-[var(--radius-xl)] shadow-2xl w-full max-w-lg p-6 max-h-[85vh] flex flex-col justify-between">
      <div class="overflow-y-auto pr-1">
        <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
          <div class="flex items-center gap-2">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-[var(--accent)]"></i>
            <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Room Service Order</h3>
          </div>
          <button onclick="closeRoomCartModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
            <i data-lucide="x" class="w-5 h-5"></i>
          </button>
        </div>

        <div id="room-cart-modal-items" class="divide-y divide-[var(--border)] text-xs mb-4">
          <!-- Populated by JS -->
        </div>

        <div class="mb-4">
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Kitchen & Delivery Notes</label>
          <textarea id="room-order-notes" rows="2" placeholder="e.g. Cutlery for 2, extra napkins, bell ring on delivery" class="w-full px-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]"></textarea>
        </div>

        <div class="pt-3 border-t-2 border-[var(--border)] flex justify-between items-center text-sm font-bold text-[var(--text-primary)]">
          <span>Order Total</span>
          <span id="room-modal-grand-total" class="font-mono text-base text-[var(--accent)]">₹0.00</span>
        </div>
      </div>

      <div class="pt-4 border-t border-[var(--border)]">
        <button onclick="submitRoomOrder()" id="room-place-order-btn" class="w-full py-3.5 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2 active:scale-95">
          <span>Transmit Room Service Order</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Live Tracking Timeline Modal -->
  <div id="tracking-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-t-[var(--radius-xl)] sm:rounded-[var(--radius-xl)] shadow-2xl w-full max-w-lg p-6 max-h-[85vh] overflow-y-auto">
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Active Orders & Requests</h3>
        <button onclick="toggleTrackingSheet()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <div id="room-tracking-list" class="space-y-4">
        <!-- Injected by JS -->
      </div>
    </div>
  </div>
</div>

<script>
  const HOTEL_ID = "{{ $hotelId }}";
  const ROOM_NUMBER = "{{ $roomNumber }}";
  const SESSION_KEY = `roomSessionToken_${HOTEL_ID}_${ROOM_NUMBER}`;

  let hotelPortalData = null;
  let menuItems = [];
  let categories = [];
  let cart = new Map();
  let selectedCategory = 'all';
  let activeLiveOrders = [];
  let activeLiveServices = [];

  document.addEventListener('DOMContentLoaded', () => {
    loadPortalInfo();
    initPusher();
  });

  function initPusher() {
    if (window.PUSHER_KEY && HOTEL_ID) {
      const pusher = new Pusher(window.PUSHER_KEY, { cluster: window.PUSHER_CLUSTER || 'mt1' });
      const channel = pusher.subscribe('restaurant.' + HOTEL_ID);

      channel.bind('order:updated', function(data) {
        if (data.roomNumber === ROOM_NUMBER) loadLiveStatus();
      });

      channel.bind('service:updated', function(data) {
        if (data.roomNumber === ROOM_NUMBER) loadLiveStatus();
      });

      channel.bind('table:reset', function(data) {
        if (data.roomNumber === ROOM_NUMBER) {
          sessionStorage.removeItem(SESSION_KEY);
          window.showToast('Your room stay has concluded. Thank you!');
          location.reload();
        }
      });
    }
  }

  async function loadPortalInfo() {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code') || '';
    const storedSession = sessionStorage.getItem(SESSION_KEY) || '';

    try {
      const res = await fetch(`/api/public/room/info?hotelId=${HOTEL_ID}&roomNumber=${ROOM_NUMBER}&code=${code}&sessionToken=${storedSession}`);
      const data = await res.json();

      if (data.success && data.data) {
        hotelPortalData = data.data;

        document.getElementById('hotel-name-header').textContent = hotelPortalData.hotel.name;
        if (hotelPortalData.room.guestName) {
          document.getElementById('guest-greeting-badge').textContent = `Hello, ${hotelPortalData.room.guestName}!`;
        }

        if (hotelPortalData.sessionToken) {
          sessionStorage.setItem(SESSION_KEY, hotelPortalData.sessionToken);
        }

        document.getElementById('food-billing-text').textContent = hotelPortalData.food.billingNote;

        menuItems = hotelPortalData.food.items || [];
        categories = hotelPortalData.food.categories || [];

        renderCategoryTabs();
        renderDishes();
        loadLiveStatus();
      } else {
        document.getElementById('dining-dishes-list').innerHTML = `
          <div class="p-8 rounded-[var(--radius-xl)] bg-red-500/10 border border-red-500/20 text-center text-xs text-red-600">
            <h4 class="font-bold text-sm mb-1">${data.error?.code === 'SESSION_EXPIRED' ? 'Session Expired' : 'Access Denied'}</h4>
            <p>${data.error?.message || 'Please scan the QR code in your room.'}</p>
          </div>
        `;
      }
    } catch(err) {
      console.error(err);
    }
  }

  function switchPortalTab(tab) {
    ['dining', 'housekeeping', 'concierge'].forEach(t => {
      const btn = document.getElementById(`btn-tab-${t}`);
      const content = document.getElementById(`tab-content-${t}`);
      if (t === tab) {
        btn.className = 'py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] text-white shadow-sm transition flex items-center justify-center gap-1.5';
        content.classList.remove('hidden');
      } else {
        btn.className = 'py-2 text-xs font-semibold rounded-[var(--radius-sm)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] transition flex items-center justify-center gap-1.5';
        content.classList.add('hidden');
      }
    });
  }

  function renderCategoryTabs() {
    const container = document.getElementById('dining-cat-tabs');
    container.innerHTML = `
      <button onclick="selectCategory('all')" id="cat-tab-all" class="px-4 py-1.5 rounded-full text-xs font-bold transition ${selectedCategory === 'all' ? 'bg-[var(--accent)] text-white shadow-sm' : 'border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]'} shrink-0">
        All Dishes
      </button>
    `;

    categories.forEach(cat => {
      const btn = document.createElement('button');
      btn.onclick = () => selectCategory(cat.id);
      btn.className = `px-4 py-1.5 rounded-full text-xs font-bold transition shrink-0 ${selectedCategory === cat.id ? 'bg-[var(--accent)] text-white shadow-sm' : 'border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]'}`;
      btn.textContent = cat.name;
      container.appendChild(btn);
    });
  }

  function selectCategory(catId) {
    selectedCategory = catId;
    renderCategoryTabs();
    renderDishes();
  }

  function filterDishes() {
    renderDishes();
  }

  function renderDishes() {
    const container = document.getElementById('dining-dishes-list');
    const q = document.getElementById('dish-search').value.toLowerCase().trim();

    let filtered = menuItems;
    if (selectedCategory !== 'all') {
      filtered = filtered.filter(i => i.categoryId === selectedCategory);
    }
    if (q) {
      filtered = filtered.filter(i => i.name.toLowerCase().includes(q) || (i.description && i.description.toLowerCase().includes(q)));
    }

    if (filtered.length === 0) {
      container.innerHTML = '<div class="p-8 text-center text-xs text-[var(--text-muted)]">No dishes found in menu.</div>';
      return;
    }

    container.innerHTML = '';
    filtered.forEach(dish => {
      const card = document.createElement('div');
      card.className = 'p-3 sm:p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between space-y-2.5 transition hover:border-[var(--accent)]';

      const qty = cart.get(dish.id)?.quantity || 0;

      let actionControl = '';
      if (qty === 0) {
        actionControl = `
          <button onclick="addToCart('${dish.id}')" class="w-full py-1.5 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition active:scale-95">
            + Add
          </button>
        `;
      } else {
        actionControl = `
          <div class="flex items-center justify-between bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] p-1">
            <button onclick="removeFromCart('${dish.id}')" class="w-6 h-6 rounded bg-[var(--bg-surface)] text-[var(--text-primary)] font-bold text-xs flex items-center justify-center hover:bg-red-500 hover:text-white transition">-</button>
            <span class="font-bold text-xs text-[var(--accent)] font-mono">${qty}</span>
            <button onclick="addToCart('${dish.id}')" class="w-6 h-6 rounded bg-[var(--accent)] text-white font-bold text-xs flex items-center justify-center hover:bg-[var(--accent-hover)] transition">+</button>
          </div>
        `;
      }

      const imgHtml = dish.imageUrl ? `
        <img src="${dish.imageUrl}" alt="${dish.name}" class="w-full h-24 sm:h-28 rounded-[var(--radius-md)] object-cover border border-[var(--border)] mb-1">
      ` : '';

      card.innerHTML = `
        <div class="overflow-hidden">
          ${imgHtml}
          <h4 class="font-serif font-bold text-xs sm:text-sm text-[var(--text-primary)] truncate">${dish.name}</h4>
          <p class="text-[10px] text-[var(--text-secondary)] line-clamp-1 mt-0.5">${dish.description || ''}</p>
          <div class="flex items-center justify-between mt-1.5">
            <span class="font-mono font-bold text-xs sm:text-sm text-[var(--accent)]">₹${parseFloat(dish.price).toFixed(2)}</span>
            <span class="text-[10px] text-[var(--text-muted)]">⏱️ ${dish.preparationTime}m</span>
          </div>
        </div>
        <div>
          ${actionControl}
        </div>
      `;

      container.appendChild(card);
    });

    lucide.createIcons();
    updateCartBar();
  }

  function addToCart(dishId) {
    const dish = menuItems.find(i => i.id === dishId);
    if (!dish) return;
    const existing = cart.get(dishId);
    if (existing) existing.quantity++;
    else cart.set(dishId, { item: dish, quantity: 1 });
    renderDishes();
  }

  function removeFromCart(dishId) {
    const existing = cart.get(dishId);
    if (!existing) return;
    if (existing.quantity > 1) existing.quantity--;
    else cart.delete(dishId);
    renderDishes();
    if (cart.size === 0) closeRoomCartModal();
  }

  function updateCartBar() {
    const bar = document.getElementById('room-cart-bar');
    let count = 0;
    let total = 0;

    cart.forEach(c => {
      count += c.quantity;
      total += c.quantity * c.item.price;
    });

    if (count > 0) {
      bar.classList.remove('hidden');
      document.getElementById('room-cart-count').textContent = `${count} ${count === 1 ? 'dish' : 'dishes'} in order`;
      document.getElementById('room-cart-total').textContent = `₹${total.toFixed(2)}`;
    } else {
      bar.classList.add('hidden');
    }
  }

  function openRoomCartModal() {
    const container = document.getElementById('room-cart-modal-items');
    container.innerHTML = '';
    let total = 0;

    cart.forEach(({ item, quantity }) => {
      total += quantity * item.price;
      const row = document.createElement('div');
      row.className = 'flex justify-between items-center py-2.5 border-b border-[var(--border)]';
      row.innerHTML = `
        <div>
          <p class="font-bold text-[var(--text-primary)] text-xs">${item.name}</p>
          <span class="text-[10px] text-[var(--text-muted)] font-mono">₹${parseFloat(item.price).toFixed(2)} each</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-1.5 bg-[var(--bg-base)] border border-[var(--border)] rounded p-0.5">
            <button onclick="removeFromCart('${item.id}'); openRoomCartModal();" class="w-5 h-5 rounded text-xs font-bold hover:bg-red-500 hover:text-white">-</button>
            <span class="w-4 text-center font-mono font-bold text-[var(--accent)] text-xs">${quantity}</span>
            <button onclick="addToCart('${item.id}'); openRoomCartModal();" class="w-5 h-5 rounded text-xs font-bold bg-[var(--accent)] text-white hover:bg-[var(--accent-hover)]">+</button>
          </div>
          <span class="font-mono font-bold text-xs text-[var(--text-primary)] w-14 text-right">₹${(item.price * quantity).toFixed(2)}</span>
        </div>
      `;
      container.appendChild(row);
    });

    document.getElementById('room-modal-grand-total').textContent = `₹${total.toFixed(2)}`;
    document.getElementById('room-cart-modal').classList.remove('hidden');
    lucide.createIcons();
  }

  function closeRoomCartModal() {
    document.getElementById('room-cart-modal').classList.add('hidden');
  }

  async function submitRoomOrder() {
    const btn = document.getElementById('room-place-order-btn');
    const sessionToken = sessionStorage.getItem(SESSION_KEY);
    const notes = document.getElementById('room-order-notes').value.trim();

    if (cart.size === 0) return;
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Sending to Kitchen...';

    const items = [];
    cart.forEach(({ item, quantity }) => {
      items.push({ menuItemId: item.id, quantity });
    });

    try {
      const res = await fetch('/api/public/room/orders', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          hotelId: HOTEL_ID,
          roomNumber: ROOM_NUMBER,
          items,
          sessionToken,
          notes: notes || null,
        })
      });
      const data = await res.json();
      if (data.success) {
        window.playNotificationSound();
        window.showToast('🍽️ Room service order placed!');
        cart.clear();
        closeRoomCartModal();
        renderDishes();
        loadLiveStatus();
        toggleTrackingSheet();
      } else {
        alert(data.error?.message || 'Failed to place order.');
      }
    } catch (err) {
      console.error(err);
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<span>Transmit Room Service Order</span><i data-lucide="arrow-right" class="w-4 h-4"></i>';
      lucide.createIcons();
    }
  }

  async function requestHotelService(serviceName, category) {
    const sessionToken = sessionStorage.getItem(SESSION_KEY);
    try {
      const res = await fetch('/api/public/room/services', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          hotelId: HOTEL_ID,
          roomNumber: ROOM_NUMBER,
          serviceName,
          category,
          sessionToken,
        })
      });
      const data = await res.json();
      if (data.success) {
        window.showToast(`✨ ${serviceName} requested!`);
        loadLiveStatus();
        toggleTrackingSheet();
      } else {
        alert(data.error?.message || 'Request failed');
      }
    } catch(err) {
      console.error(err);
    }
  }

  function promptWakeupCall() {
    const time = prompt('Enter requested Wake-up Call time (e.g. 06:30 AM):');
    if (time) {
      requestHotelService(`Wake-up Call at ${time}`, 'concierge');
    }
  }

  async function loadLiveStatus() {
    const sessionToken = sessionStorage.getItem(SESSION_KEY);
    if (!sessionToken) return;

    try {
      const res = await fetch(`/api/public/room/orders?hotelId=${HOTEL_ID}&roomNumber=${ROOM_NUMBER}&sessionToken=${sessionToken}`);
      const data = await res.json();
      if (data.success && data.data) {
        activeLiveOrders = data.data.orders || [];
        activeLiveServices = data.data.services || [];

        const hasActive = activeLiveOrders.some(o => o.status !== 'cancelled') || activeLiveServices.some(s => s.status !== 'completed');
        if (hasActive) {
          document.getElementById('room-tracking-btn').classList.remove('hidden');
          renderTrackingSheet();
        }
      }
    } catch(err) {
      console.error(err);
    }
  }

  function toggleTrackingSheet() {
    const modal = document.getElementById('tracking-modal');
    modal.classList.toggle('hidden');
    renderTrackingSheet();
  }

  function renderTrackingSheet() {
    const container = document.getElementById('room-tracking-list');
    container.innerHTML = '';

    if (activeLiveOrders.length === 0 && activeLiveServices.length === 0) {
      container.innerHTML = '<div class="p-8 text-center text-xs text-[var(--text-muted)]">No active orders or service requests.</div>';
      return;
    }

    // Render Orders
    activeLiveOrders.forEach(order => {
      const card = document.createElement('div');
      card.className = 'p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)] space-y-2';

      let statusMsg = 'Kitchen received ticket';
      if (order.status === 'preparing') statusMsg = 'Chef is preparing in kitchen 🔥';
      else if (order.status === 'ready') statusMsg = 'On the way to Room ' + ROOM_NUMBER + ' 🛎️';
      else if (order.status === 'completed') statusMsg = 'Delivered to room ✅';

      card.innerHTML = `
        <div class="flex justify-between items-center">
          <span class="font-bold text-xs text-[var(--text-primary)]">🍽️ Room Service Meal</span>
          <span class="font-mono font-bold text-xs text-[var(--accent)]">₹${parseFloat(order.total_amount).toFixed(2)}</span>
        </div>
        <p class="text-xs font-bold text-[var(--accent)] mt-1">${statusMsg}</p>
      `;
      container.appendChild(card);
    });

    // Render Service Requests
    activeLiveServices.forEach(srv => {
      const card = document.createElement('div');
      card.className = 'p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)] flex items-center justify-between';

      let statusBadge = '<span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 font-bold text-[10px]">Pending Dispatch</span>';
      if (srv.status === 'in_progress') statusBadge = '<span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-600 font-bold text-[10px]">Staff En Route 🚀</span>';
      else if (srv.status === 'completed') statusBadge = '<span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 font-bold text-[10px]">Resolved ✅</span>';

      card.innerHTML = `
        <div>
          <span class="font-bold text-xs text-[var(--text-primary)]">${srv.service_name}</span>
          <p class="text-[10px] text-[var(--text-muted)]">${srv.category}</p>
        </div>
        ${statusBadge}
      `;
      container.appendChild(card);
    });

    lucide.createIcons();
  }
</script>
@endsection
