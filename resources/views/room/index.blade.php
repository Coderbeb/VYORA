@extends('layouts.app')

@section('title', 'Guest Room Portal - Vyora')

@section('content')
<div class="min-h-screen pb-32 bg-[var(--bg-base)] text-[var(--text-primary)]">
  <!-- Top Bar -->
  <header class="sticky top-0 z-30 bg-[var(--bg-surface)]/90 backdrop-blur-md border-b border-[var(--border)] px-4 py-3 shadow-sm">
    <div class="max-w-2xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[var(--accent)] text-white flex items-center justify-center font-bold text-base font-serif">
          V
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

      <!-- Dietary Filter Tabs (Veg vs Non-Veg with authentic luxury vector emblems) -->
      <div class="flex items-center gap-1.5 p-1 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs text-xs font-bold">
        <button onclick="setRoomDietFilter('all')" id="room-diet-tab-all" class="flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition bg-[var(--accent)] text-white shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
          <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          <span>All</span>
        </button>
        <button onclick="setRoomDietFilter('veg')" id="room-diet-tab-veg" class="flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-emerald-700 dark:text-emerald-300 hover:bg-emerald-500/10 flex items-center justify-center gap-1.5 cursor-pointer">
          <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none">
            <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#059669" stroke-width="1.8" fill="#ECFDF5"/>
            <circle cx="8" cy="8" r="3.5" fill="#059669"/>
          </svg>
          <span>Pure Veg</span>
        </button>
        <button onclick="setRoomDietFilter('nonveg')" id="room-diet-tab-nonveg" class="flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-orange-700 dark:text-orange-400 hover:bg-orange-500/10 flex items-center justify-center gap-1.5 cursor-pointer">
          <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none">
            <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#DC2626" stroke-width="1.8" fill="#FEF2F2"/>
            <polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/>
          </svg>
          <span>Non-Veg</span>
        </button>
      </div>

      <!-- Quick Price Sort Filters (Low to High / High to Low) -->
      <div class="flex items-center justify-between gap-2 px-1">
        <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 flex items-center gap-1">
          <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
          <span>Sort:</span>
        </span>
        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none">
          <button onclick="setRoomSortFilter('default')" id="room-sort-btn-default" class="px-3 py-1 rounded-full text-[11px] font-black bg-[#034d37] text-white shadow-xs transition shrink-0 cursor-pointer">
            Recommended
          </button>
          <button onclick="setRoomSortFilter('price_asc')" id="room-sort-btn-price_asc" class="px-3 py-1 rounded-full text-[11px] font-bold border border-slate-300 bg-white text-slate-700 hover:border-slate-400 transition shrink-0 flex items-center gap-1 cursor-pointer">
            <span>₹ Low → High</span>
          </button>
          <button onclick="setRoomSortFilter('price_desc')" id="room-sort-btn-price_desc" class="px-3 py-1 rounded-full text-[11px] font-bold border border-slate-300 bg-white text-slate-700 hover:border-slate-400 transition shrink-0 flex items-center gap-1 cursor-pointer">
            <span>₹ High → Low</span>
          </button>
        </div>
      </div>

      <!-- Category Filter Tabs -->
      <div id="dining-cat-tabs" class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <button onclick="selectCategory('all')" id="cat-tab-all" class="px-4 py-1.5 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm shrink-0">
          All Dishes
        </button>
      </div>

      <!-- Dishes List Container (Minimum 2 per row) -->
      <div id="dining-dishes-list" class="grid grid-cols-2 gap-3 sm:gap-4 pt-2">
        <div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)] flex flex-col items-center justify-center gap-2">
          <svg class="w-6 h-6 animate-spin text-[var(--accent)]" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          <span>Loading in-room menu...</span>
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
  <div id="room-cart-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm transition-opacity">
    <div id="room-cart-sheet" class="bg-white border-2 border-slate-200 rounded-t-[28px] sm:rounded-2xl shadow-2xl w-full max-w-lg p-5 sm:p-6 max-h-[85vh] flex flex-col justify-between animate-in slide-in-from-bottom duration-300 relative">
      <!-- Swipe Down Drag Handle -->
      <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-3 cursor-grab active:cursor-grabbing"></div>

      <div class="overflow-y-auto pr-1">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
          <div class="flex items-center gap-2">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-[var(--accent)]"></i>
            <h3 class="font-sans font-black text-lg text-slate-900">Room Service Order</h3>
          </div>
          <button onclick="closeRoomCartModal()" class="p-1.5 rounded-full text-slate-500 hover:text-slate-900 bg-slate-100 cursor-pointer">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        </div>

        <div id="room-cart-modal-items" class="divide-y divide-slate-100 text-xs mb-4">
          <!-- Populated by JS -->
        </div>

        <div class="mb-4">
          <label class="block text-xs font-bold text-slate-700 mb-1">Kitchen & Delivery Notes</label>
          <textarea id="room-order-notes" rows="2" placeholder="e.g. Cutlery for 2, extra napkins, bell ring on delivery" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:border-[var(--accent)] text-slate-900 font-medium"></textarea>
        </div>

        <div class="pt-3 border-t-2 border-slate-200 flex justify-between items-center text-sm font-bold text-slate-900">
          <span>Order Subtotal</span>
          <span id="room-modal-grand-total" class="font-mono text-base text-[#034d37] font-black">₹0.00</span>
        </div>
      </div>

      <div class="pt-4 border-t border-slate-200">
        <button onclick="submitRoomOrder()" id="room-place-order-btn" class="w-full py-3.5 rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-sm shadow-md transition-transform flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
          <span>Transmit Room Service Order</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Single Product Details Modal / Bottom Sheet -->
  <div id="dish-detail-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm transition-opacity">
    <div id="dish-detail-sheet" class="bg-white border-2 border-slate-200 rounded-t-[28px] sm:rounded-2xl shadow-2xl w-full max-w-lg p-5 sm:p-6 max-h-[88vh] flex flex-col justify-between animate-in slide-in-from-bottom duration-300 relative">
      <!-- Swipe Down Drag Handle -->
      <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-3 cursor-grab active:cursor-grabbing"></div>

      <!-- Close Button -->
      <button onclick="closeDishDetailModal()" class="absolute top-4 right-4 z-10 p-1.5 rounded-full bg-slate-900/80 hover:bg-slate-950 text-white transition cursor-pointer">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>

      <div class="overflow-y-auto pr-1 space-y-4">
        <!-- Hero Dish Image -->
        <div id="detail-image-container" class="relative w-full h-52 sm:h-60 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
          <img id="detail-image" src="" alt="" class="w-full h-full object-cover">
        </div>

        <!-- Badges & Prep Time -->
        <div class="flex items-center justify-between gap-2 flex-wrap">
          <div class="flex items-center gap-2 flex-wrap">
            <span id="detail-diet-badge"></span>
            <span id="detail-category-badge" class="px-2.5 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold"></span>
          </div>
          <span id="detail-prep-time" class="text-xs font-mono font-bold text-slate-600 flex items-center gap-1"></span>
        </div>

        <!-- Dish Title & Price -->
        <div class="flex items-baseline justify-between gap-3">
          <h3 id="detail-title" class="font-sans font-black text-xl sm:text-2xl text-slate-900 leading-tight"></h3>
          <span id="detail-price" class="font-mono font-black text-2xl text-[#034d37] shrink-0"></span>
        </div>

        <!-- Full Description -->
        <div>
          <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 mb-1">About This Dish</h4>
          <p id="detail-desc" class="text-xs sm:text-sm text-slate-700 leading-relaxed font-medium"></p>
        </div>
      </div>

      <!-- Bottom Order CTA -->
      <div class="pt-4 mt-3 border-t border-slate-200 flex items-center gap-3">
        <!-- Stepper -->
        <div class="flex items-center bg-slate-100 border-2 border-slate-300 rounded-xl p-1 shrink-0">
          <button onclick="decrementDetailQty()" class="w-9 h-9 rounded-lg bg-white text-slate-800 font-black hover:bg-slate-200 transition flex items-center justify-center cursor-pointer shadow-xs active:scale-90">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
          </button>
          <span id="detail-qty" class="w-8 text-center font-mono font-black text-base text-slate-900">1</span>
          <button onclick="incrementDetailQty()" class="w-9 h-9 rounded-lg bg-[#034d37] text-white font-black hover:bg-[#023d2b] transition flex items-center justify-center cursor-pointer shadow-xs active:scale-90">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          </button>
        </div>

        <!-- Add to Order Button -->
        <button onclick="addDetailToCart()" id="detail-add-btn" class="flex-1 py-3.5 px-4 rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-sm shadow-md transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
          <span>Add to In-Room Order</span>
          <span id="detail-btn-price" class="font-mono opacity-90 font-bold">• ₹0.00</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Live Tracking Timeline Modal -->
  <div id="tracking-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm transition-opacity">
    <div id="room-tracking-sheet" class="bg-white border-2 border-slate-200 rounded-t-[28px] sm:rounded-2xl shadow-2xl w-full max-w-lg p-5 sm:p-6 max-h-[85vh] overflow-y-auto animate-in slide-in-from-bottom duration-300">
      <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-3 cursor-grab active:cursor-grabbing"></div>

      <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
        <h3 class="font-sans font-black text-base sm:text-lg text-slate-900">Active Orders & Requests</h3>
        <button onclick="toggleTrackingSheet()" class="p-1.5 rounded-full text-slate-500 hover:text-slate-900 bg-slate-100 cursor-pointer">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div id="room-tracking-list" class="space-y-4">
        <!-- Injected by JS -->
      </div>
    </div>
  </div>

  <!-- Room Stay Concluded / Settled Modal -->
  <div id="room-session-settled-modal" class="fixed inset-0 bg-black/75 z-50 flex items-center justify-center p-4 hidden backdrop-blur-md transition-opacity">
    <div class="bg-white border-2 border-slate-200 rounded-3xl shadow-2xl w-full max-w-sm p-6 text-center space-y-4 animate-in zoom-in-95 duration-200">
      <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto border-2 border-emerald-200 shadow-xs">
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div class="space-y-1.5">
        <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[11px] uppercase tracking-wide">
          Stay Concluded
        </span>
        <h3 id="room-settled-modal-title" class="font-sans font-black text-xl text-slate-900">Room Stay Concluded</h3>
        <p id="room-settled-modal-desc" class="text-xs text-slate-600 leading-relaxed font-medium">Your stay for Room #{{ $roomNumber }} has ended. Thank you for staying with us! Please scan the QR code in your room to start a new guest session.</p>
      </div>
      <div class="pt-2">
        <button onclick="location.reload()" class="w-full py-3.5 rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-sm shadow-md transition-all active:scale-95 cursor-pointer flex items-center justify-center gap-2">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
          <span>Scan Room QR Code</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  const HOTEL_ID = "{{ $hotelId }}";
  const ROOM_NUMBER = "{{ $roomNumber }}";
  const SESSION_KEY = `roomSessionToken_${HOTEL_ID}_${ROOM_NUMBER}`;

  // Bespoke Luxury Vector Icons Suite
  const VYORA_ICONS = {
    vegEmblem: `<div class="flex items-center gap-1.5 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded text-[11px] font-bold text-emerald-800 dark:text-emerald-300 shrink-0">
      <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none">
        <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#059669" stroke-width="1.8" fill="#ECFDF5"/>
        <circle cx="8" cy="8" r="3.5" fill="#059669"/>
      </svg>
      <span>Veg</span>
    </div>`,

    nonVegEmblem: `<div class="flex items-center gap-1.5 bg-red-500/10 border border-red-500/30 px-2 py-0.5 rounded text-[11px] font-bold text-red-800 dark:text-red-300 shrink-0">
      <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none">
        <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#DC2626" stroke-width="1.8" fill="#FEF2F2"/>
        <polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/>
      </svg>
      <span>Non-Veg</span>
    </div>`,

    vegDot: `<svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none">
      <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#059669" stroke-width="1.8" fill="#ECFDF5"/>
      <circle cx="8" cy="8" r="3.5" fill="#059669"/>
    </svg>`,

    nonVegDot: `<svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none">
      <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#DC2626" stroke-width="1.8" fill="#FEF2F2"/>
      <polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/>
    </svg>`,

    clock: `<svg class="w-3 h-3 text-[var(--text-muted)] shrink-0 inline-block mr-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="9"/>
      <polyline points="12 7 12 12 15 14"/>
    </svg>`,

    plus: `<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>`,

    minus: `<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>`,

    spinner: `<svg class="w-4 h-4 animate-spin shrink-0 inline-block mr-1.5" viewBox="0 0 24 24" fill="none">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>`
  };

  let hotelPortalData = null;
  let menuItems = [];
  let categories = [];
  let cart = new Map();
  let selectedCategory = 'all';
  let activeDietFilter = 'all';
  let activeSortFilter = 'default';
  let activeDetailDish = null;
  let detailQty = 1;
  let activeLiveOrders = [];
  let activeLiveServices = [];

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Non-Veg Detection Helper
  function isNonVegDish(item) {
    if (item.isVeg === false || item.is_veg === false || item.isVeg === 0 || item.is_veg === 0) return true;
    if (item.isVeg === true || item.is_veg === true || item.isVeg === 1 || item.is_veg === 1) return false;
    const text = ((item.name || '') + ' ' + (item.description || '')).toLowerCase();
    const nonVegKeywords = ['chicken', 'mutton', 'fish', 'egg', 'beef', 'pork', 'prawn', 'crab', 'meat', 'lamb', 'bacon', 'pepperoni', 'ham', 'turkey', 'non-veg', 'non veg'];
    return nonVegKeywords.some(kw => text.includes(kw));
  }

  function setRoomDietFilter(filter) {
    activeDietFilter = filter;
    const tabAll = document.getElementById('room-diet-tab-all');
    const tabVeg = document.getElementById('room-diet-tab-veg');
    const tabNonVeg = document.getElementById('room-diet-tab-nonveg');

    if (tabAll) {
      tabAll.className = filter === 'all'
        ? 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition bg-[var(--accent)] text-white shadow-xs flex items-center justify-center gap-1.5 cursor-pointer'
        : 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-slate-700 hover:bg-slate-100 flex items-center justify-center gap-1.5 cursor-pointer';
    }
    if (tabVeg) {
      tabVeg.className = filter === 'veg'
        ? 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition bg-emerald-700 text-white shadow-xs flex items-center justify-center gap-1.5 cursor-pointer'
        : 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-emerald-800 hover:bg-emerald-50 flex items-center justify-center gap-1.5 cursor-pointer';
    }
    if (tabNonVeg) {
      tabNonVeg.className = filter === 'nonveg'
        ? 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition bg-orange-700 text-white shadow-xs flex items-center justify-center gap-1.5 cursor-pointer'
        : 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-orange-800 hover:bg-orange-50 flex items-center justify-center gap-1.5 cursor-pointer';
    }
    renderDishes();
  }

  function setRoomSortFilter(sortType) {
    activeSortFilter = sortType;
    ['default', 'price_asc', 'price_desc'].forEach(s => {
      const btn = document.getElementById(`room-sort-btn-${s}`);
      if (!btn) return;
      if (s === sortType) {
        btn.className = 'px-3 py-1 rounded-full text-[11px] font-black bg-[#034d37] text-white shadow-xs transition shrink-0 cursor-pointer';
      } else {
        btn.className = 'px-3 py-1 rounded-full text-[11px] font-bold border border-slate-300 bg-white text-slate-700 hover:border-slate-400 transition shrink-0 flex items-center gap-1 cursor-pointer';
      }
    });
    renderDishes();
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadPortalInfo();
    initPusher();

    // Enable touch drag-down gesture dismiss for bottom sheets (Cart, Product Detail, Tracking)
    enableSwipeDownToDismiss('room-cart-modal', 'room-cart-sheet', closeRoomCartModal);
    enableSwipeDownToDismiss('dish-detail-modal', 'dish-detail-sheet', closeDishDetailModal);
    enableSwipeDownToDismiss('tracking-modal', 'room-tracking-sheet', toggleTrackingSheet);
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
          showRoomSessionSettledScreen('Your room stay has concluded. Thank you for staying with us! Please scan the room QR code if you are beginning a new stay.');
        }
      });
    }
  }

  function showRoomSessionSettledScreen(message) {
    sessionStorage.removeItem(SESSION_KEY);
    activeLiveOrders = [];
    activeLiveServices = [];
    cart.clear();

    const cartModal = document.getElementById('room-cart-modal');
    if (cartModal) cartModal.classList.add('hidden');
    const detailModal = document.getElementById('dish-detail-modal');
    if (detailModal) detailModal.classList.add('hidden');
    const trackingModal = document.getElementById('tracking-modal');
    if (trackingModal) trackingModal.classList.add('hidden');
    const cartBar = document.getElementById('room-cart-bar');
    if (cartBar) cartBar.classList.add('hidden');
    const trackingBtn = document.getElementById('room-tracking-btn');
    if (trackingBtn) trackingBtn.classList.add('hidden');

    const modal = document.getElementById('room-session-settled-modal');
    if (modal) {
      const descEl = document.getElementById('room-settled-modal-desc');
      if (descEl) descEl.textContent = message || 'Your room stay has concluded. Thank you for staying with us! Please scan the QR code in your room to start a new guest session.';
      modal.classList.remove('hidden');
    }

    const diningList = document.getElementById('dining-dishes-list');
    if (diningList) {
      diningList.innerHTML = `
        <div class="col-span-full p-8 sm:p-12 text-center rounded-3xl bg-white border-2 border-emerald-500/20 shadow-lg space-y-4">
          <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto border border-emerald-200">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div class="space-y-1">
            <h3 class="font-sans font-black text-xl text-slate-900">Room Stay Concluded</h3>
            <p class="text-xs sm:text-sm text-slate-600 max-w-sm mx-auto font-medium">${message || 'Your room stay has concluded. Please scan the QR code in your room to start a new guest session.'}</p>
          </div>
          <div class="pt-2">
            <button onclick="location.reload()" class="px-6 py-3 rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-xs shadow-md transition-all active:scale-95 cursor-pointer flex items-center justify-center gap-2 mx-auto">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
              <span>Scan QR Code to Start Stay</span>
            </button>
          </div>
        </div>
      `;
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

        if (code) {
          window.history.replaceState({}, '', window.location.pathname);
        }

        document.getElementById('food-billing-text').textContent = hotelPortalData.food.billingNote;

        menuItems = hotelPortalData.food.items || [];
        categories = hotelPortalData.food.categories || [];

        renderCategoryTabs();
        renderDishes();
        loadLiveStatus();
      } else {
        if (data.error?.code === 'SESSION_EXPIRED') {
          showRoomSessionSettledScreen(data.error?.message);
        } else {
          showRoomSessionSettledScreen(data.error?.message || 'Access Denied: Please scan the QR code in your room.');
        }
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
    if (activeDietFilter === 'veg') {
      filtered = filtered.filter(i => !isNonVegDish(i));
    } else if (activeDietFilter === 'nonveg') {
      filtered = filtered.filter(i => isNonVegDish(i));
    }
    if (q) {
      filtered = filtered.filter(i => (i.name || '').toLowerCase().includes(q) || (i.description && i.description.toLowerCase().includes(q)));
    }

    // Sort filter (Low to High / High to Low)
    if (activeSortFilter === 'price_asc') {
      filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
    } else if (activeSortFilter === 'price_desc') {
      filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
    }

    if (filtered.length === 0) {
      container.innerHTML = `
        <div class="col-span-full p-10 text-center rounded-2xl bg-white border border-slate-200 text-xs text-slate-500 space-y-1">
          <p class="font-bold text-sm text-slate-800">No dishes found</p>
          <p>Try clearing filters or search terms.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = '';
    filtered.forEach(dish => {
      const isNonVeg = isNonVegDish(dish);
      const qty = cart.get(dish.id)?.quantity || 0;

      // Modern Apple-HIG product card (Clean, luxury alabaster card)
      const card = document.createElement('div');
      card.className = 'group p-3 sm:p-3.5 rounded-2xl bg-white border border-slate-200 shadow-xs hover:shadow-md transition-all duration-200 active:scale-[0.99] flex flex-col justify-between space-y-2 relative overflow-hidden cursor-pointer';

      // Tap card body to open One Product Details Page
      card.onclick = (e) => {
        if (e.target.closest('button')) return;
        openDishDetailModal(dish.id);
      };

      // Floating Diet Emblem
      const emblemBadge = isNonVeg ? `
        <span class="inline-flex items-center gap-1 bg-white/95 backdrop-blur-md px-1.5 py-0.5 rounded-md border border-red-200 text-[10px] font-black text-red-700 shadow-xs">
          ${VYORA_ICONS.nonVegDot} Non-Veg
        </span>
      ` : `
        <span class="inline-flex items-center gap-1 bg-white/95 backdrop-blur-md px-1.5 py-0.5 rounded-md border border-emerald-200 text-[10px] font-black text-emerald-800 shadow-xs">
          ${VYORA_ICONS.vegDot} Veg
        </span>
      `;

      // Modern Squircle Image with Floating Tags
      const imgHtml = dish.imageUrl ? `
        <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-100 border border-slate-200 mb-1">
          <img src="${dish.imageUrl}" alt="${escapeHtml(dish.name)}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          <div class="absolute top-2 left-2">
            ${emblemBadge}
          </div>
          <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur-md text-white text-[10px] font-mono font-bold flex items-center gap-1 shadow-xs">
            ${VYORA_ICONS.clock} ${dish.preparationTime || 15}m
          </div>
        </div>
      ` : `
        <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden mb-1 flex items-center justify-center ${isNonVeg ? 'bg-orange-50 text-orange-400' : 'bg-emerald-50 text-emerald-500'} border border-slate-200">
          <svg class="w-8 h-8 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4V2"/><path d="M4 18h16"/><path d="M4 18a8 8 0 0 1 16 0"/><circle cx="12" cy="4" r="2"/></svg>
          <div class="absolute top-2 left-2">
            ${emblemBadge}
          </div>
          <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur-md text-white text-[10px] font-mono font-bold flex items-center gap-1 shadow-xs">
            ${VYORA_ICONS.clock} ${dish.preparationTime || 15}m
          </div>
        </div>
      `;

      // Stepper Control
      let actionControl = '';
      if (qty === 0) {
        actionControl = `
          <button onclick="event.stopPropagation(); addToCart('${dish.id}')" class="w-full py-2 rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-xs shadow-xs transition-all flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer">
            ${VYORA_ICONS.plus}
            <span>Add</span>
          </button>
        `;
      } else {
        actionControl = `
          <div class="flex items-center justify-between bg-slate-100 border border-slate-300 rounded-xl p-0.5 shadow-xs" onclick="event.stopPropagation()">
            <button onclick="event.stopPropagation(); removeFromCart('${dish.id}')" class="w-7 h-7 rounded-lg bg-white text-slate-800 font-black hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center shadow-xs active:scale-75 cursor-pointer">
              ${VYORA_ICONS.minus}
            </button>
            <span class="font-mono font-black text-xs text-slate-900 px-2">${qty}</span>
            <button onclick="event.stopPropagation(); addToCart('${dish.id}')" class="w-7 h-7 rounded-lg bg-[#034d37] text-white font-black hover:bg-[#023d2b] transition flex items-center justify-center shadow-xs active:scale-75 cursor-pointer">
              ${VYORA_ICONS.plus}
            </button>
          </div>
        `;
      }

      card.innerHTML = `
        <div class="overflow-hidden">
          ${imgHtml}
          <h4 class="font-sans font-black text-sm sm:text-base text-slate-900 leading-snug line-clamp-1 group-hover:text-[#034d37] transition-colors mt-1">${escapeHtml(dish.name)}</h4>
          <p class="text-[11px] sm:text-xs text-slate-600 font-medium line-clamp-2 mt-0.5 leading-relaxed min-h-[30px]">${escapeHtml(dish.description || 'Prepared fresh by master chef.')}</p>
          <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-100">
            <span class="font-mono font-black text-base text-slate-900">₹${parseFloat(dish.price).toFixed(2)}</span>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tap for info</span>
          </div>
        </div>
        <div class="pt-1 relative">
          ${actionControl}
        </div>
      `;

      container.appendChild(card);
    });

    if (window.lucide) lucide.createIcons();
    updateCartBar();
  }

  // --- ONE PRODUCT DETAILS PAGE (MODAL) ---
  function openDishDetailModal(dishId) {
    const dish = menuItems.find(i => i.id === dishId);
    if (!dish) return;

    activeDetailDish = dish;
    const inCartQty = cart.get(dish.id)?.quantity || 0;
    detailQty = inCartQty > 0 ? inCartQty : 1;

    const isNonVeg = isNonVegDish(dish);
    const imgEl = document.getElementById('detail-image');
    if (dish.imageUrl) {
      imgEl.src = dish.imageUrl;
      document.getElementById('detail-image-container').classList.remove('hidden');
    } else {
      document.getElementById('detail-image-container').classList.add('hidden');
    }

    document.getElementById('detail-diet-badge').innerHTML = isNonVeg
      ? `<span class="inline-flex items-center gap-1 bg-red-50 border border-red-300 text-red-800 px-2.5 py-1 rounded-md text-xs font-black shadow-2xs">${VYORA_ICONS.nonVegDot} Non-Veg</span>`
      : `<span class="inline-flex items-center gap-1 bg-emerald-50 border border-emerald-300 text-emerald-800 px-2.5 py-1 rounded-md text-xs font-black shadow-2xs">${VYORA_ICONS.vegDot} Pure Veg</span>`;

    document.getElementById('detail-category-badge').textContent = dish.category?.name || 'Chef Special';
    document.getElementById('detail-prep-time').innerHTML = `${VYORA_ICONS.clock} Prep: ${dish.preparationTime || 15} mins`;
    document.getElementById('detail-title').textContent = dish.name;
    document.getElementById('detail-price').textContent = `₹${parseFloat(dish.price).toFixed(2)}`;
    document.getElementById('detail-desc').textContent = dish.description || 'Prepared fresh with fine ingredients and authentic spices by our master culinary team.';

    updateDetailPriceUI();
    document.getElementById('dish-detail-modal').classList.remove('hidden');
  }

  function closeDishDetailModal() {
    activeDetailDish = null;
    document.getElementById('dish-detail-modal').classList.add('hidden');
  }

  function incrementDetailQty() {
    detailQty++;
    updateDetailPriceUI();
  }

  function decrementDetailQty() {
    if (detailQty > 1) {
      detailQty--;
      updateDetailPriceUI();
    }
  }

  function updateDetailPriceUI() {
    if (!activeDetailDish) return;
    document.getElementById('detail-qty').textContent = detailQty;
    const subtotal = parseFloat(activeDetailDish.price) * detailQty;
    document.getElementById('detail-btn-price').textContent = `• ₹${subtotal.toFixed(2)}`;
  }

  function addDetailToCart() {
    if (!activeDetailDish) return;
    cart.set(activeDetailDish.id, { item: activeDetailDish, quantity: detailQty });
    renderDishes();
    closeDishDetailModal();
    window.showToast?.(`Added ${detailQty}x ${activeDetailDish.name} to order!`);
  }

  // --- MOBILE BOTTOM SHEET SWIPE-DOWN GESTURE HANDLER ---
  function enableSwipeDownToDismiss(modalId, sheetContainerId, closeCallback) {
    const modal = document.getElementById(modalId);
    const sheet = document.getElementById(sheetContainerId);
    if (!modal || !sheet) return;

    let startY = 0;
    let currentY = 0;
    let isDragging = false;

    sheet.addEventListener('touchstart', (e) => {
      const scrollable = sheet.querySelector('.overflow-y-auto') || sheet;
      if (scrollable.scrollTop <= 5) {
        startY = e.touches[0].clientY;
        currentY = startY;
        isDragging = true;
        sheet.style.transition = 'none';
      }
    }, { passive: true });

    sheet.addEventListener('touchmove', (e) => {
      if (!isDragging) return;
      currentY = e.touches[0].clientY;
      const deltaY = currentY - startY;

      if (deltaY > 0) {
        sheet.style.transform = `translateY(${deltaY}px)`;
        if (e.cancelable) e.preventDefault();
      }
    }, { passive: false });

    sheet.addEventListener('touchend', (e) => {
      if (!isDragging) return;
      isDragging = false;
      const deltaY = currentY - startY;
      sheet.style.transition = 'transform 0.3s cubic-bezier(0.16, 1, 0.3, 1)';

      if (deltaY > 80) {
        sheet.style.transform = 'translateY(100%)';
        setTimeout(() => {
          sheet.style.transform = '';
          closeCallback();
        }, 220);
      } else {
        sheet.style.transform = 'translateY(0)';
        setTimeout(() => {
          sheet.style.transform = '';
        }, 300);
      }
    }, { passive: true });
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
      const isNonVeg = isNonVegDish(item);

      const row = document.createElement('div');
      row.className = 'flex justify-between items-center py-2.5 border-b border-[var(--border)] last:border-0';
      row.innerHTML = `
        <div class="pr-2 min-w-0 flex-1">
          <div class="flex items-center gap-1.5">
            ${isNonVeg ? VYORA_ICONS.nonVegDot : VYORA_ICONS.vegDot}
            <p class="font-bold text-[var(--text-primary)] text-xs truncate">${item.name}</p>
          </div>
          <span class="text-[10px] text-[var(--text-muted)] font-mono pl-4">₹${parseFloat(item.price).toFixed(2)} each</span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <div class="flex items-center bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] p-0.5">
            <button onclick="removeFromCart('${item.id}'); openRoomCartModal();" class="w-6 h-6 rounded text-xs font-bold hover:bg-red-500 hover:text-white transition active:scale-75 flex items-center justify-center">${VYORA_ICONS.minus}</button>
            <span class="w-4 text-center font-mono font-bold text-[var(--accent)] text-xs">${quantity}</span>
            <button onclick="addToCart('${item.id}'); openRoomCartModal();" class="w-6 h-6 rounded text-xs font-bold bg-[var(--accent)] text-white hover:bg-[var(--accent-hover)] transition active:scale-75 flex items-center justify-center">${VYORA_ICONS.plus}</button>
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
    btn.innerHTML = `<span class="inline-flex items-center gap-2">${VYORA_ICONS.spinner} Transmitting Order...</span>`;

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
        window.showToast('Room service order placed!', 'order');
        cart.clear();
        closeRoomCartModal();
        renderDishes();
        loadLiveStatus();
        toggleTrackingSheet();
      } else {
        if (data.error?.code === 'SESSION_EXPIRED') {
          showRoomSessionSettledScreen('Cannot place order: Your room stay has concluded. Please scan the QR code in your room if you are starting a new stay.');
          return;
        }
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
        window.showToast(`${serviceName} requested!`, 'bell');
        loadLiveStatus();
        toggleTrackingSheet();
      } else {
        if (data.error?.code === 'SESSION_EXPIRED') {
          showRoomSessionSettledScreen('Your room stay has concluded. Please scan the QR code in your room if you are starting a new stay.');
          return;
        }
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
      if (!data.success && data.error?.code === 'SESSION_EXPIRED') {
        showRoomSessionSettledScreen('Your room stay has concluded. Please scan the QR code in your room if you are starting a new stay.');
        return;
      }
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
      let statusColor = 'text-orange-600 bg-orange-500/10 border-orange-500/20';
      if (order.status === 'preparing') {
        statusMsg = 'Chef is preparing in kitchen';
        statusColor = 'text-amber-600 bg-amber-500/10 border-amber-500/20';
      } else if (order.status === 'ready') {
        statusMsg = 'On the way to Room #' + ROOM_NUMBER;
        statusColor = 'text-emerald-600 bg-emerald-500/10 border-emerald-500/20';
      } else if (order.status === 'completed') {
        statusMsg = 'Delivered to room';
        statusColor = 'text-slate-600 bg-slate-500/10 border-slate-500/20';
      }

      card.innerHTML = `
        <div class="flex justify-between items-center pb-2 border-b border-[var(--border)]">
          <div class="flex items-center gap-1.5">
            <span class="w-6 h-6 rounded-md bg-[var(--accent-glow)] text-[var(--accent)] flex items-center justify-center">
              <i data-lucide="utensils" class="w-3.5 h-3.5"></i>
            </span>
            <span class="font-bold text-xs text-[var(--text-primary)]">Room Service Meal</span>
          </div>
          <span class="font-mono font-bold text-xs text-[var(--accent)]">₹${parseFloat(order.total_amount).toFixed(2)}</span>
        </div>
        <div class="pt-1">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[11px] font-bold ${statusColor}">
            <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
            <span>${statusMsg}</span>
          </span>
        </div>
      `;
      container.appendChild(card);
    });

    // Render Service Requests
    activeLiveServices.forEach(srv => {
      const card = document.createElement('div');
      card.className = 'p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)] flex items-center justify-between';

      let statusBadge = '<span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-600 border border-amber-500/20 font-bold text-[10px]">Pending Dispatch</span>';
      if (srv.status === 'in_progress') statusBadge = '<span class="px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-600 border border-blue-500/20 font-bold text-[10px] flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping"></span> Staff En Route</span>';
      else if (srv.status === 'completed') statusBadge = '<span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 font-bold text-[10px]">Resolved</span>';

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
