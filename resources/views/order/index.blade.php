@extends('layouts.app')

@section('title', 'Digital Menu - Vyora')

@push('styles')
<style>
  /* Premium Micro-Animations & Custom Accents */
  @keyframes floatUpFade {
    0% { opacity: 1; transform: translate(-50%, 0) scale(1); }
    100% { opacity: 0; transform: translate(-50%, -28px) scale(1.3); }
  }
  .floating-plus-particle {
    position: absolute;
    top: -8px;
    left: 50%;
    transform: translateX(-50%);
    font-weight: 800;
    font-size: 11px;
    color: var(--accent);
    pointer-events: none;
    animation: floatUpFade 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
  @keyframes cartBarBounce {
    0% { transform: translateY(100%); }
    70% { transform: translateY(-4px); }
    100% { transform: translateY(0); }
  }
  .animate-cart-slide-up {
    animation: cartBarBounce 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
  @keyframes pulseRing {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.5); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(234, 88, 12, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 88, 12, 0); }
  }
  .animate-pulse-ring {
    animation: pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }
  @keyframes shimmerEffect {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
  }
  .shimmer-bg {
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0) 100%);
    background-size: 200% 100%;
    animation: shimmerEffect 2.5s infinite;
  }
  /* Tap highlight prevention for clean native app feel */
  button, a { -webkit-tap-highlight-color: transparent; }
</style>
@endpush

@section('content')
<div class="min-h-screen pb-36 bg-[var(--bg-base)] text-[var(--text-primary)] transition-colors select-none">
  <!-- Sticky Premium Mobile Top App Bar -->
  <header class="sticky top-0 z-40 bg-[var(--bg-surface)]/95 backdrop-blur-xl border-b border-[var(--border)] px-4 py-2.5 shadow-xs transition-colors">
    <div class="max-w-2xl mx-auto flex items-center justify-between">
      <!-- Brand & Table Info -->
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-[var(--radius-md)] bg-[var(--accent)] text-white flex items-center justify-center font-bold text-base shadow-sm ring-2 ring-[var(--accent-glow)] shrink-0 font-serif">
          V
        </div>
        <div class="min-w-0">
          <div class="flex items-center gap-1.5">
            <h1 id="restaurant-name-header" class="font-sans font-extrabold text-base sm:text-lg tracking-tight truncate max-w-[160px] sm:max-w-xs text-slate-900 dark:text-slate-50">
              Loading Menu...
            </h1>
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping shrink-0" title="Live Venue Connection"></span>
          </div>
          <div class="flex items-center gap-1.5 text-xs">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold uppercase tracking-wider">
              <i data-lucide="utensils" class="w-3 h-3"></i> <span id="header-table-badge">Table #{{ $tableNumber }}</span>
            </span>
            <span id="header-dining-floor" class="text-slate-500 dark:text-slate-400 font-medium hidden sm:inline">• Dining Floor</span>
          </div>
        </div>
      </div>

      <!-- Quick Action Controls -->
      <div class="flex items-center gap-2">
        <!-- Live Order Tracking Pill (appears when orders active) -->
        <button id="tracking-toggle-btn" onclick="toggleTrackingSheet()" class="hidden px-3 py-1.5 rounded-full bg-orange-500/15 text-orange-700 dark:text-orange-300 border border-orange-500/30 font-bold text-xs flex items-center gap-1.5 shadow-xs transition active:scale-95 animate-pulse">
          <span class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
          <span id="tracking-status-text">Track · ट्रैक (<span id="tracking-active-count">0</span>)</span>
        </button>

        <!-- Bilingual Switcher Pill [ EN | हिन्दी ] -->
        <div class="flex items-center rounded-full p-0.5 border border-slate-300 dark:border-zinc-700 bg-[var(--bg-base)] text-xs font-bold shadow-2xs">
          <button type="button" id="lang-btn-en" onclick="setLanguage('en')" class="px-2.5 py-1 rounded-full text-[11px] font-bold transition-all bg-[#034d37] text-white shadow-xs cursor-pointer">EN</button>
          <button type="button" id="lang-btn-hi" onclick="setLanguage('hi')" class="px-2.5 py-1 rounded-full text-[11px] font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 cursor-pointer">हिन्दी</button>
        </div>


      </div>
    </div>
  </header>

  <!-- Main Scrollable Mobile Container -->
  <main class="max-w-2xl mx-auto px-4 pt-3.5 space-y-3.5">
    <!-- Welcome & Table Service Quick Bar -->
    <div class="p-3.5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col gap-2.5">
      <div class="flex items-center justify-between text-xs">
        <span class="text-slate-700 dark:text-slate-200 font-bold flex items-center gap-1.5">
          <svg class="w-4 h-4 text-[var(--accent)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span id="text-staff-assistance">Staff & Table Assistance</span>
        </span>
        <span class="text-xs text-emerald-800 dark:text-emerald-300 font-bold bg-emerald-500/15 border border-emerald-500/30 px-2.5 py-0.5 rounded-full flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> <span id="text-floor-attendant">Floor Attendant Active</span>
        </span>
      </div>
      <div class="grid grid-cols-2 gap-2">
        <button onclick="requestService('waiter')" id="btn-waiter" class="py-2.5 px-3 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] hover:border-[var(--accent)] hover:bg-[var(--accent-glow)] text-xs font-bold text-[var(--text-primary)] transition-all flex items-center justify-center gap-2 shadow-xs active:scale-95 group">
          <span class="w-6 h-6 rounded-md bg-[var(--accent-glow)] text-[var(--accent)] flex items-center justify-center group-hover:scale-110 transition-transform">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          </span>
          <span id="text-waiter">Call Waiter</span>
        </button>
        <button onclick="requestService('water')" id="btn-water" class="py-2.5 px-3 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] hover:border-blue-500 hover:bg-blue-500/10 text-xs font-bold text-[var(--text-primary)] transition-all flex items-center justify-center gap-2 shadow-xs active:scale-95 group">
          <span class="w-6 h-6 rounded-md bg-blue-500/15 text-blue-500 flex items-center justify-center group-hover:scale-110 transition-transform">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
          </span>
          <span id="text-water">Request Water</span>
        </button>
      </div>
    </div>

    <!-- Search Box with Clear Button -->
    <div class="relative">
      <input type="text" id="cust-search" oninput="filterCustomerDishes()" placeholder="Search dishes, drinks, ingredients..." class="w-full pl-9 pr-9 py-2.5 text-xs bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-lg)] focus:outline-none focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent-glow)] text-[var(--text-primary)] placeholder-[var(--text-muted)] shadow-xs transition">
      <i data-lucide="search" class="w-4 h-4 text-[var(--text-muted)] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
      <button id="search-clear-btn" onclick="clearSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 p-0.5 rounded-full text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-3.5 h-3.5"></i>
      </button>
    </div>

    <!-- Dietary Filter Tabs (Crucial for Veg vs Non-Veg distinction with authentic luxury vector emblems) -->
    <div class="flex items-center gap-1.5 p-1 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs text-xs font-bold">
      <button onclick="setDietFilter('all')" id="diet-tab-all" class="flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition bg-[var(--accent)] text-white shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span id="text-diet-all">All Items</span>
      </button>
      <button onclick="setDietFilter('veg')" id="diet-tab-veg" class="flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-emerald-800 hover:bg-emerald-50 flex items-center justify-center gap-1.5 cursor-pointer">
        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none">
          <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#059669" stroke-width="1.8" fill="#ECFDF5"/>
          <circle cx="8" cy="8" r="3.5" fill="#059669"/>
        </svg>
        <span id="text-diet-veg">Pure Veg</span>
      </button>
      <button onclick="setDietFilter('nonveg')" id="diet-tab-nonveg" class="flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-orange-800 hover:bg-orange-50 flex items-center justify-center gap-1.5 cursor-pointer">
        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none">
          <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#DC2626" stroke-width="1.8" fill="#FEF2F2"/>
          <polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/>
        </svg>
        <span id="text-diet-nonveg">Non-Veg</span>
      </button>
    </div>

    <!-- Quick Price Sort Filters (Low to High / High to Low) -->
    <div class="flex items-center justify-between gap-2 px-1">
      <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 flex items-center gap-1">
        <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
        <span id="text-sort-label">Sort:</span>
      </span>
      <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none">
        <button onclick="setSortFilter('default')" id="sort-btn-default" class="px-3 py-1 rounded-full text-[11px] font-black bg-[#034d37] text-white shadow-xs transition shrink-0 cursor-pointer">
          <span id="text-sort-default">Recommended</span>
        </button>
        <button onclick="setSortFilter('price_asc')" id="sort-btn-price_asc" class="px-3 py-1 rounded-full text-[11px] font-bold border border-slate-300 bg-white text-slate-700 hover:border-slate-400 transition shrink-0 flex items-center gap-1 cursor-pointer">
          <span id="text-sort-asc">₹ Low → High</span>
        </button>
        <button onclick="setSortFilter('price_desc')" id="sort-btn-price_desc" class="px-3 py-1 rounded-full text-[11px] font-bold border border-slate-300 bg-white text-slate-700 hover:border-slate-400 transition shrink-0 flex items-center gap-1 cursor-pointer">
          <span id="text-sort-desc">₹ High → Low</span>
        </button>
      </div>
    </div>

    <!-- Category Pills Filter (Horizontal Smooth Scroll) -->
    <div id="cust-category-tabs" class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none -mx-4 px-4">
      <button onclick="selectCustomerCategory('all')" id="cust-tab-all" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-xs shrink-0 active:scale-95">
        All Categories
      </button>
    </div>

    <!-- Dish List Container (2-Column Grid on Mobile) -->
    <div id="cust-dishes-container" class="grid grid-cols-2 gap-3 sm:gap-4 pt-1">
      <div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)] flex flex-col items-center justify-center gap-2">
        <svg class="w-6 h-6 animate-spin text-[var(--accent)]" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <span>Loading digital menu...</span>
      </div>
    </div>
  </main>

  <!-- Sticky Bottom Cart Floating Bar -->
  <div id="bottom-cart-bar" class="fixed bottom-0 inset-x-0 p-3 sm:p-4 bg-[var(--bg-surface)]/95 backdrop-blur-xl border-t border-[var(--border)] z-40 hidden shadow-2xl transition-all duration-300 animate-cart-slide-up">
    <div class="max-w-2xl mx-auto flex items-center justify-between gap-3">
      <div class="flex items-center gap-2.5">
        <div class="relative">
          <div class="w-10 h-10 rounded-[var(--radius-md)] bg-[var(--accent)]/10 text-[var(--accent)] flex items-center justify-center">
            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
          </div>
          <span id="cart-item-count-badge" class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-[var(--accent)] text-white text-[10px] font-bold flex items-center justify-center shadow-xs">
            0
          </span>
        </div>
        <div>
          <span id="cart-item-count" class="text-[11px] font-medium text-[var(--text-secondary)]">0 items</span>
          <h4 id="cart-total-price" class="font-serif font-bold text-base sm:text-lg text-[var(--accent)] leading-tight">₹0.00</h4>
        </div>
      </div>

      <button onclick="openCartModal()" class="px-5 py-2.5 sm:py-3 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-md transition-transform flex items-center gap-2 active:scale-95 cursor-pointer">
        <span id="cart-view-order-btn-text">View Order</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <!-- Cart & Checkout Modal / Bottom Sheet with Gesture Swipe-Down -->
  <div id="cart-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm transition-opacity">
    <div id="cart-sheet" class="bg-white border-2 border-slate-200 rounded-t-[28px] sm:rounded-2xl shadow-2xl w-full max-w-lg p-5 sm:p-6 max-h-[85vh] flex flex-col justify-between animate-in slide-in-from-bottom duration-300 relative">
      <!-- Swipe Handle Indicator for mobile gesture dismiss -->
      <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-3 cursor-grab active:cursor-grabbing"></div>

      <div class="overflow-y-auto pr-1">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#034d37] flex items-center justify-center font-bold">
              <i data-lucide="shopping-bag" class="w-4 h-4"></i>
            </div>
            <div>
              <h3 id="cart-modal-title" class="font-sans font-black text-base sm:text-lg text-slate-900">Your Table Order</h3>
              <p id="cart-modal-subtitle" class="text-[10px] text-slate-500 font-semibold">Table #{{ $tableNumber }} • Review items</p>
            </div>
          </div>
          <button onclick="closeCartModal()" class="p-1.5 rounded-full text-slate-500 hover:text-slate-900 bg-slate-100 cursor-pointer">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        </div>

        <!-- Cart Items Container -->
        <div id="cart-modal-items" class="divide-y divide-slate-100 text-xs mb-4">
          <!-- Populated by JS -->
        </div>

        <!-- Quick Dietary Cooking Instructions Notes -->
        <div class="mb-4 space-y-1.5">
          <label id="cart-cooking-instructions-label" class="block text-xs font-bold text-slate-800">Cooking Instructions / Dietary Notes</label>
          <div class="flex items-center gap-1.5 flex-wrap text-[10px] mb-1">
            <button type="button" onclick="appendNote('Less spicy')" class="px-2.5 py-1 rounded-full border border-slate-300 bg-slate-50 text-slate-700 hover:border-[#034d37] hover:text-slate-900 transition flex items-center gap-1 active:scale-95 shadow-xs cursor-pointer">
              <svg class="w-3 h-3 text-red-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c1 3 4 5 4 9a6 6 0 0 1-12 0c0-4 3-6 4-9"/><path d="M10 17a2 2 0 0 0 4 0"/></svg>
              <span id="tag-less-spicy">Less spicy</span>
            </button>
            <button type="button" onclick="appendNote('No onions/garlic')" class="px-2.5 py-1 rounded-full border border-slate-300 bg-slate-50 text-slate-700 hover:border-[#034d37] hover:text-slate-900 transition flex items-center gap-1 active:scale-95 shadow-xs cursor-pointer">
              <svg class="w-3 h-3 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 4 13C4 9.13 7.13 6 11 6c1.66 0 3.2.58 4.42 1.55L20 3"/><path d="M21 21L3 3"/></svg>
              <span id="tag-no-onion-garlic">No onion/garlic</span>
            </button>
            <button type="button" onclick="appendNote('Extra crispy')" class="px-2.5 py-1 rounded-full border border-slate-300 bg-slate-50 text-slate-700 hover:border-[#034d37] hover:text-slate-900 transition flex items-center gap-1 active:scale-95 shadow-xs cursor-pointer">
              <svg class="w-3 h-3 text-amber-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <span id="tag-extra-crispy">Extra crispy</span>
            </button>
            <button type="button" onclick="appendNote('Sauce on side')" class="px-2.5 py-1 rounded-full border border-slate-300 bg-slate-50 text-slate-700 hover:border-[#034d37] hover:text-slate-900 transition flex items-center gap-1 active:scale-95 shadow-xs cursor-pointer">
              <svg class="w-3 h-3 text-indigo-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14v3a7 7 0 0 1-14 0V8z"/><path d="M8 8V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v3"/><line x1="4" y1="19" x2="20" y2="19"/></svg>
              <span id="tag-sauce-side">Sauce on side</span>
            </button>
          </div>
          <textarea id="order-notes" rows="2" placeholder="e.g. Less spicy, dressing on the side..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:border-[#034d37] focus:bg-white text-slate-900 font-medium"></textarea>
        </div>

        <!-- Price Summary -->
        <div class="pt-3 border-t-2 border-slate-200 flex justify-between items-center text-sm font-bold text-slate-900">
          <span id="cart-order-subtotal-label">Order Subtotal</span>
          <span id="modal-grand-total" class="font-mono text-base text-[#034d37] font-black">₹0.00</span>
        </div>
      </div>

      <!-- Place Order Submit Button -->
      <div class="pt-4 border-t border-slate-200">
        <button onclick="placeOrder()" id="place-order-btn" class="w-full py-3.5 rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-sm shadow-md transition-transform flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
          <span id="place-order-btn-text">Submit Order to Kitchen</span>
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
          <h4 id="detail-about-label" class="text-xs font-black uppercase tracking-wider text-slate-500 mb-1">About This Dish</h4>
          <p id="detail-desc" class="text-xs sm:text-sm text-slate-700 leading-relaxed font-medium"></p>
        </div>

        <!-- Portion Variants Section -->
        <div id="detail-variants-section" class="hidden space-y-2 pt-3 border-t border-slate-200">
          <div class="flex items-center justify-between">
            <h4 id="detail-portion-title" class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-1.5">
              Portion Size
            </h4>
            <span id="detail-portion-badge" class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">Required</span>
          </div>
          <div id="detail-variants-list" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <!-- Injected by JS -->
          </div>
        </div>

        <!-- Add-Ons Section -->
        <div id="detail-addons-section" class="hidden space-y-2 pt-3 border-t border-slate-200">
          <div class="flex items-center justify-between">
            <h4 id="detail-addons-title" class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-1.5">
              Add-Ons & Extras
            </h4>
            <span id="detail-addons-badge" class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">Optional</span>
          </div>
          <div id="detail-addons-list" class="space-y-1.5">
            <!-- Injected by JS -->
          </div>
        </div>

        <!-- Special Cooking Instructions -->
        <div id="detail-notes-section" class="space-y-1.5 pt-3 border-t border-slate-200">
          <label class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center justify-between">
            <span id="detail-notes-label">Special Instructions</span>
            <span id="detail-notes-badge" class="text-[10px] font-normal text-slate-400">Optional</span>
          </label>
          <input type="text" id="detail-item-notes" placeholder="e.g. Less spicy, no onion & garlic, make it crisp" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#034d37] focus:ring-1 focus:ring-[#034d37]/20 transition">
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
          <span id="detail-add-btn-text">Add to Order</span>
          <span id="detail-btn-price" class="font-mono opacity-90 font-bold">• ₹0.00</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Live Order Tracking Sheet Modal -->
  <div id="tracking-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm transition-opacity">
    <div id="tracking-sheet-content" class="bg-white border-2 border-slate-200 rounded-t-[28px] sm:rounded-2xl shadow-2xl w-full max-w-lg p-5 sm:p-6 max-h-[85vh] overflow-y-auto animate-in slide-in-from-bottom duration-300">
      <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-3 cursor-grab"></div>

      <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center font-bold">
            <i data-lucide="flame" class="w-4 h-4"></i>
          </div>
          <div>
            <h3 id="tracking-sheet-title" class="font-sans font-black text-base sm:text-lg text-slate-900">
              Live Kitchen Status
            </h3>
            <p id="tracking-sheet-subtitle" class="text-[10px] text-slate-500 font-semibold">Table #{{ $tableNumber }} Progress</p>
          </div>
        </div>
        <button onclick="toggleTrackingSheet()" class="p-1.5 rounded-full text-slate-500 hover:text-slate-900 bg-slate-100 cursor-pointer">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div id="tracking-orders-list" class="space-y-4">
        <!-- Injected by JS -->
      </div>
    </div>
  </div>

  <!-- Dining Session Settled / Concluded Modal -->
  <div id="session-settled-modal" class="fixed inset-0 bg-black/75 z-50 flex items-center justify-center p-4 hidden backdrop-blur-md transition-opacity">
    <div class="bg-white border-2 border-slate-200 rounded-3xl shadow-2xl w-full max-w-sm p-6 text-center space-y-4 animate-in zoom-in-95 duration-200">
      <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto border-2 border-emerald-200 shadow-xs">
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div class="space-y-1.5">
        <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[11px] uppercase tracking-wide">
          Bill Settled
        </span>
        <h3 id="settled-modal-title" class="font-sans font-black text-xl text-slate-900">Dining Session Concluded</h3>
        <p id="settled-modal-desc" class="text-xs text-slate-600 leading-relaxed font-medium">The bill for Table #{{ $tableNumber }} has been settled. Thank you for dining with us! Please scan the table QR code to start a new dining session.</p>
      </div>
      <div class="pt-2">
        <button onclick="location.reload()" class="w-full py-3.5 rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-sm shadow-md transition-all active:scale-95 cursor-pointer flex items-center justify-center gap-2">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
          <span>Scan Table QR Code</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  const OWNER_ID = "{{ $ownerId }}";
  const TABLE_NUMBER = parseInt("{{ $tableNumber }}", 10);
  const SESSION_KEY = `sessionToken_${OWNER_ID}_${TABLE_NUMBER}`;

  // Complete Natural Bilingual Dictionary Suite (English & Authentic Spoken Hindi)
  const TRANSLATIONS = {
    en: {
      tablePrefix: "Table #",
      diningFloor: "• Dining Floor",
      staffAssistance: "Staff & Table Assistance",
      floorAttendantActive: "Floor Attendant Active",
      callWaiter: "Call Waiter",
      requestWater: "Request Water",
      waiterDispatched: "Waiter has been notified!",
      waterDispatched: "Water request dispatched!",
      searchPlaceholder: "Search dishes, drinks, ingredients...",
      allItems: "All Items",
      pureVeg: "Pure Veg",
      nonVeg: "Non-Veg",
      sortLabel: "Sort:",
      recommended: "Recommended",
      priceLowHigh: "₹ Low → High",
      priceHighLow: "₹ High → Low",
      allCategories: "All Categories",
      add: "Add",
      customize: "Customize",
      tapForInfo: "Tap for info",
      customizable: "Customizable",
      inOrder: "in order",
      itemsInOrder: "items in order",
      itemInOrder: "item in order",
      viewOrder: "View Order",
      yourOrder: "Your Table Order",
      reviewItems: "Review items",
      cookingInstructions: "Cooking Instructions / Dietary Notes",
      lessSpicy: "Less spicy",
      noOnionGarlic: "No onion/garlic",
      extraCrispy: "Extra crispy",
      sauceOnSide: "Sauce on side",
      orderNotesPlaceholder: "e.g. Less spicy, dressing on the side...",
      orderSubtotal: "Order Subtotal",
      submitOrder: "Submit Order to Kitchen",
      transmitting: "Transmitting to Kitchen...",
      orderSuccessToast: "Order transmitted to kitchen!",
      aboutDish: "About This Dish",
      portionSize: "Portion Size",
      requiredBadge: "Required",
      addonsExtras: "Add-Ons & Extras",
      optionalBadge: "Optional",
      specialInstructions: "Special Instructions",
      notesPlaceholder: "e.g. Less spicy, no onion & garlic, make it crisp",
      addToOrder: "Add to Order",
      liveKitchenStatus: "Live Kitchen Status",
      trackPill: "Track",
      step1: "Received",
      step2: "Cooking",
      step3: "Ready",
      step4: "Served",
      statusPending: "Order Received",
      statusPreparing: "Cooking in Progress",
      statusReady: "Order Ready • Please collect from counter",
      statusCompleted: "Served to Table",
      statusCancelled: "Cancelled",
      orderFailedAlert: "Failed to place order. Please try again."
    },
    hi: {
      tablePrefix: "टेबल #",
      diningFloor: "• डाइनिंग एरिया",
      staffAssistance: "कर्मचारी एवं टेबल सहायता",
      floorAttendantActive: "अटेंडेंट उपलब्ध",
      callWaiter: "वेटर बुलाएं",
      requestWater: "पानी मंगवाएं",
      waiterDispatched: "वेटर को सूचित कर दिया गया है!",
      waterDispatched: "पानी पहुंचाने का अनुरोध भेजा गया!",
      searchPlaceholder: "व्यंजन, पेय या सामग्री खोजें...",
      allItems: "सभी व्यंजन",
      pureVeg: "शुद्ध शाकाहारी",
      nonVeg: "मांसाहारी",
      sortLabel: "क्रम:",
      recommended: "लोकप्रिय",
      priceLowHigh: "₹ कम से ज्यादा",
      priceHighLow: "₹ ज्यादा से कम",
      allCategories: "सभी श्रेणियां",
      add: "जोड़ें",
      customize: "कस्टमाइज़",
      tapForInfo: "विवरण देखें",
      customizable: "कस्टमाइज़ेबल",
      inOrder: "ऑर्डर में",
      itemsInOrder: "आइटम ऑर्डर में",
      itemInOrder: "आइटम ऑर्डर में",
      viewOrder: "ऑर्डर देखें",
      yourOrder: "आपकी ऑर्डर सूची",
      reviewItems: "चुने हुए व्यंजन",
      cookingInstructions: "पकाने के निर्देश / विशेष पसंद",
      lessSpicy: "कम तीखा",
      noOnionGarlic: "बिना प्याज / लहसुन",
      extraCrispy: "ज्यादा क्रिस्पी",
      sauceOnSide: "चटनी / सॉस अलग से",
      orderNotesPlaceholder: "जैसे कम तीखा, सलाद अलग से, बिना प्याज...",
      orderSubtotal: "कुल राशि",
      submitOrder: "रसोई में ऑर्डर भेजें",
      transmitting: "रसोई में भेजा जा रहा है...",
      orderSuccessToast: "ऑर्डर रसोई में पहुंच गया है!",
      aboutDish: "व्यंजन का विवरण",
      portionSize: "पोर्शन / साइज़ चुनें",
      requiredBadge: "अनिवार्य",
      addonsExtras: "अतिरिक्त आइटम और टॉपिंग",
      optionalBadge: "ऐच्छिक",
      specialInstructions: "विशेष निर्देश",
      notesPlaceholder: "जैसे कम तीखा, बिना प्याज-लहसुन, ज्यादा क्रिस्पी...",
      addToOrder: "ऑर्डर में जोड़ें",
      liveKitchenStatus: "लाइव किचन स्टेटस",
      trackPill: "ट्रैक करें",
      step1: "ऑर्डर मिला",
      step2: "पक रहा है",
      step3: "तैयार है",
      step4: "सर्व हो गया",
      statusPending: "ऑर्डर स्वीकार हुआ",
      statusPreparing: "रसोई में पक रहा है",
      statusReady: "भोजन तैयार है • कृपया काउंटर से प्राप्त करें",
      statusCompleted: "सर्व हो गया",
      statusCancelled: "रद्द हुआ",
      orderFailedAlert: "ऑर्डर दर्ज नहीं हो सका। कृपया पुनः प्रयास करें।"
    }
  };

  let currentLang = localStorage.getItem('vyora_lang') || 'en';

  function t(key) {
    return (TRANSLATIONS[currentLang] && TRANSLATIONS[currentLang][key]) || (TRANSLATIONS['en'] && TRANSLATIONS['en'][key]) || key;
  }

  function setLanguage(lang) {
    currentLang = lang;
    localStorage.setItem('vyora_lang', lang);

    const btnEn = document.getElementById('lang-btn-en');
    const btnHi = document.getElementById('lang-btn-hi');
    if (btnEn && btnHi) {
      if (lang === 'hi') {
        btnHi.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold transition-all bg-[#034d37] text-white shadow-xs cursor-pointer';
        btnEn.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 cursor-pointer';
      } else {
        btnEn.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold transition-all bg-[#034d37] text-white shadow-xs cursor-pointer';
        btnHi.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 cursor-pointer';
      }
    }

    const applyText = (id, text) => {
      const el = document.getElementById(id);
      if (el) el.textContent = text;
    };

    applyText('header-table-badge', `${t('tablePrefix')}${TABLE_NUMBER}`);
    applyText('header-dining-floor', t('diningFloor'));
    applyText('text-staff-assistance', t('staffAssistance'));
    applyText('text-floor-attendant', t('floorAttendantActive'));
    applyText('text-waiter', t('callWaiter'));
    applyText('text-water', t('requestWater'));

    const searchInput = document.getElementById('cust-search');
    if (searchInput) searchInput.placeholder = t('searchPlaceholder');

    applyText('text-diet-all', t('allItems'));
    applyText('text-diet-veg', t('pureVeg'));
    applyText('text-diet-nonveg', t('nonVeg'));

    applyText('text-sort-label', t('sortLabel'));
    applyText('text-sort-default', t('recommended'));
    applyText('text-sort-asc', t('priceLowHigh'));
    applyText('text-sort-desc', t('priceHighLow'));

    applyText('cust-tab-all', t('allCategories'));

    applyText('cart-view-order-btn-text', t('viewOrder'));
    applyText('cart-modal-title', t('yourOrder'));
    applyText('cart-modal-subtitle', `${t('tablePrefix')}${TABLE_NUMBER} • ${t('reviewItems')}`);
    applyText('cart-cooking-instructions-label', t('cookingInstructions'));
    applyText('tag-less-spicy', t('lessSpicy'));
    applyText('tag-no-onion-garlic', t('noOnionGarlic'));
    applyText('tag-extra-crispy', t('extraCrispy'));
    applyText('tag-sauce-side', t('sauceOnSide'));

    const orderNotes = document.getElementById('order-notes');
    if (orderNotes) orderNotes.placeholder = t('orderNotesPlaceholder');

    applyText('cart-order-subtotal-label', t('orderSubtotal'));
    applyText('place-order-btn-text', t('submitOrder'));

    applyText('detail-about-label', t('aboutDish'));
    applyText('detail-portion-title', t('portionSize'));
    applyText('detail-portion-badge', t('requiredBadge'));
    applyText('detail-addons-title', t('addonsExtras'));
    applyText('detail-addons-badge', t('optionalBadge'));
    applyText('detail-notes-label', t('specialInstructions'));
    applyText('detail-notes-badge', t('optionalBadge'));
    const detailNotes = document.getElementById('detail-item-notes');
    if (detailNotes) detailNotes.placeholder = t('notesPlaceholder');
    applyText('detail-add-btn-text', t('addToOrder'));

    applyText('tracking-sheet-title', t('liveKitchenStatus'));
    applyText('tracking-sheet-subtitle', `${t('tablePrefix')}${TABLE_NUMBER} Progress`);

    renderDishes();
    updateCartBar();
    if (document.getElementById('cart-modal') && !document.getElementById('cart-modal').classList.contains('hidden')) {
      renderCartModalItems();
    }
  }

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
    </svg>`,

    stepTicket: `<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`,

    stepFlame: `<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 3z"/></svg>`,

    stepPlate: `<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18"/><path d="M5 12a7 7 0 0 1 14 0"/><path d="M9 12v3"/><path d="M15 12v3"/><line x1="2" y1="18" x2="22" y2="18"/></svg>`,

    stepCheck: `<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`
  };

  let allMenuItems = [];
  let allCategories = [];
  let cart = new Map(); // cartKey -> { key, item, quantity, unitPrice, selectedVariant, selectedAddons, notes }
  let activeCatFilter = 'all';
  let activeDietFilter = 'all'; // 'all', 'veg', 'nonveg'
  let activeSortFilter = 'default'; // 'default', 'price_asc', 'price_desc'
  let activeDetailDish = null;
  let detailQty = 1;
  let selectedDetailVariant = null;
  let selectedDetailAddons = new Map(); // name -> { name, price }
  let activeLiveOrders = [];
  let livePollInterval = null;

  document.addEventListener('DOMContentLoaded', () => {
    setLanguage(currentLang);
    loadMenu();
    loadLiveOrders();
    initPusherListener();

    // Enable touch drag-down gesture dismiss for bottom sheets (Cart, Product Detail, Tracking)
    enableSwipeDownToDismiss('cart-modal', 'cart-sheet', closeCartModal);
    enableSwipeDownToDismiss('dish-detail-modal', 'dish-detail-sheet', closeDishDetailModal);
    enableSwipeDownToDismiss('tracking-modal', 'tracking-sheet-content', toggleTrackingSheet);

    // Auto-poll live orders every 5s so status updates without page reload
    livePollInterval = setInterval(loadLiveOrders, 5000);
  });

  // Non-Veg Detection Helper
  function isNonVegDish(item) {
    if (item.isVeg === false || item.is_veg === false || item.isVeg === 0 || item.is_veg === 0) return true;
    if (item.isVeg === true || item.is_veg === true || item.isVeg === 1 || item.is_veg === 1) return false;
    
    // Keyword heuristic fallback
    const text = ((item.name || '') + ' ' + (item.description || '')).toLowerCase();
    const nonVegKeywords = ['chicken', 'mutton', 'fish', 'egg', 'beef', 'pork', 'prawn', 'crab', 'meat', 'lamb', 'bacon', 'pepperoni', 'ham', 'turkey', 'non-veg', 'non veg'];
    return nonVegKeywords.some(kw => text.includes(kw));
  }

  function initPusherListener() {
    if (window.PUSHER_KEY && OWNER_ID && window.Pusher) {
      try {
        const pusher = new Pusher(window.PUSHER_KEY, { cluster: window.PUSHER_CLUSTER || 'mt1' });
        const channel = pusher.subscribe('restaurant.' + OWNER_ID);

        channel.bind('order:updated', function(data) {
          if (data && parseInt(data.tableNumber, 10) === TABLE_NUMBER) {
            loadLiveOrders();
            if (window.playNotificationSound) window.playNotificationSound();
          }
        });

        channel.bind('table:reset', function(data) {
          if (data && parseInt(data.tableNumber, 10) === TABLE_NUMBER) {
            showSessionSettledScreen('Your table bill has been settled. Thank you for dining with us! Please scan the table QR code to start a new session.', true);
          }
        });
      } catch(err) {
        console.warn('Pusher initialization error:', err);
      }
    }
  }

  function showSessionSettledScreen(message, isSettled = true) {
    sessionStorage.removeItem(SESSION_KEY);
    activeLiveOrders = [];
    cart.clear();
    
    // Close all open interactive sheets
    const cartModal = document.getElementById('cart-modal');
    if (cartModal) cartModal.classList.add('hidden');
    const detailModal = document.getElementById('dish-detail-modal');
    if (detailModal) detailModal.classList.add('hidden');
    const trackingModal = document.getElementById('tracking-modal');
    if (trackingModal) trackingModal.classList.add('hidden');
    const cartBar = document.getElementById('cust-cart-bar');
    if (cartBar) cartBar.classList.add('hidden');
    const trackingBtn = document.getElementById('tracking-toggle-btn');
    if (trackingBtn) trackingBtn.classList.add('hidden');

    // Show persistent session settled modal
    const modal = document.getElementById('session-settled-modal');
    if (modal) {
      const titleEl = document.getElementById('settled-modal-title');
      const descEl = document.getElementById('settled-modal-desc');
      if (titleEl) titleEl.textContent = isSettled ? 'Dining Session Concluded' : 'Access Restricted';
      if (descEl) descEl.textContent = message || 'The bill for Table #' + TABLE_NUMBER + ' has been settled. Thank you for dining with us! Please scan the table QR code to start a new dining session.';
      modal.classList.remove('hidden');
    }

    // Replace dishes container with informative banner
    const mainContainer = document.getElementById('cust-dishes-container');
    if (mainContainer) {
      mainContainer.innerHTML = `
        <div class="col-span-full p-8 sm:p-12 text-center rounded-3xl bg-white border-2 border-emerald-500/20 shadow-lg space-y-4">
          <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto border border-emerald-200">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div class="space-y-1">
            <h3 class="font-sans font-black text-xl text-slate-900">${isSettled ? 'Dining Session Concluded' : 'QR Scan Required'}</h3>
            <p class="text-xs sm:text-sm text-slate-600 max-w-sm mx-auto font-medium">${message || 'The bill for Table #' + TABLE_NUMBER + ' has been settled. Please scan the QR code on your table to start a new dining session.'}</p>
          </div>
          <div class="pt-2">
            <button onclick="location.reload()" class="px-6 py-3 rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-xs shadow-md transition-all active:scale-95 cursor-pointer flex items-center justify-center gap-2 mx-auto">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
              <span>Scan QR Code to Order Again</span>
            </button>
          </div>
        </div>
      `;
    }
  }

  async function loadMenu() {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code') || '';
    const storedSession = sessionStorage.getItem(SESSION_KEY) || '';

    try {
      const res = await fetch(`/api/public/menu?ownerId=${OWNER_ID}&tableNumber=${TABLE_NUMBER}&code=${code}&sessionToken=${storedSession}`);
      const data = await res.json();

      if (data.success && data.data) {
        const titleEl = document.getElementById('restaurant-name-header');
        if (titleEl) titleEl.textContent = data.data.restaurant?.restaurantName || 'Vyora Dining';
        
        if (data.data.sessionToken) {
          sessionStorage.setItem(SESSION_KEY, data.data.sessionToken);
        }

        // Clean the URL query params so they can't reload or bookmark the code param from browser history
        if (code) {
          window.history.replaceState({}, '', window.location.pathname);
        }

        allMenuItems = data.data.items || [];
        allCategories = data.data.categories || [];

        renderCategoryTabs();
        renderDishes();
      } else {
        if (data.error?.code === 'SESSION_EXPIRED') {
          showSessionSettledScreen(data.error?.message, true);
        } else {
          showSessionSettledScreen(data.error?.message || 'Access Denied: Please scan the table QR code to view the menu.', false);
        }
      }
    } catch(err) {
      console.error('Menu load error:', err);
    }
  }

  function renderCategoryTabs() {
    const container = document.getElementById('cust-category-tabs');
    container.innerHTML = `
      <button onclick="selectCustomerCategory('all')" id="cust-tab-all" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition shrink-0 active:scale-95 ${activeCatFilter === 'all' ? 'bg-[var(--accent)] text-white shadow-xs' : 'border border-[var(--border)] bg-[var(--bg-surface)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]'}">
        ${t('allCategories')}
      </button>
    `;

    allCategories.forEach(cat => {
      const btn = document.createElement('button');
      btn.onclick = () => selectCustomerCategory(cat.id);
      const isActive = activeCatFilter === cat.id;
      btn.className = `px-3.5 py-1.5 rounded-full text-xs font-bold transition shrink-0 active:scale-95 ${isActive ? 'bg-[var(--accent)] text-white shadow-xs' : 'border border-[var(--border)] bg-[var(--bg-surface)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]'}`;
      btn.textContent = cat.name;
      container.appendChild(btn);
    });
  }

  function selectCustomerCategory(catId) {
    activeCatFilter = catId;
    renderCategoryTabs();
    renderDishes();
  }

  function setDietFilter(filter) {
    activeDietFilter = filter;
    const tabAll = document.getElementById('diet-tab-all');
    const tabVeg = document.getElementById('diet-tab-veg');
    const tabNonVeg = document.getElementById('diet-tab-nonveg');

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

  function setSortFilter(sortType) {
    activeSortFilter = sortType;
    ['default', 'price_asc', 'price_desc'].forEach(s => {
      const btn = document.getElementById(`sort-btn-${s}`);
      if (!btn) return;
      if (s === sortType) {
        btn.className = 'px-3 py-1 rounded-full text-[11px] font-black bg-[#034d37] text-white shadow-xs transition shrink-0 cursor-pointer';
      } else {
        btn.className = 'px-3 py-1 rounded-full text-[11px] font-bold border border-slate-300 bg-white text-slate-700 hover:border-slate-400 transition shrink-0 flex items-center gap-1 cursor-pointer';
      }
    });
    renderDishes();
  }

  function filterCustomerDishes() {
    const q = document.getElementById('cust-search').value.trim();
    const clearBtn = document.getElementById('search-clear-btn');
    if (clearBtn) {
      if (q) clearBtn.classList.remove('hidden');
      else clearBtn.classList.add('hidden');
    }
    renderDishes();
  }

  function clearSearch() {
    const searchInput = document.getElementById('cust-search');
    searchInput.value = '';
    const clearBtn = document.getElementById('search-clear-btn');
    if (clearBtn) clearBtn.classList.add('hidden');
    renderDishes();
  }

  function renderDishes() {
    const container = document.getElementById('cust-dishes-container');
    const q = document.getElementById('cust-search').value.toLowerCase().trim();

    let filtered = allMenuItems.filter(i => i.isAvailable);

    // Category filter
    if (activeCatFilter !== 'all') {
      filtered = filtered.filter(i => i.categoryId === activeCatFilter);
    }

    // Dietary filter (Veg vs Non-Veg)
    if (activeDietFilter === 'veg') {
      filtered = filtered.filter(i => !isNonVegDish(i));
    } else if (activeDietFilter === 'nonveg') {
      filtered = filtered.filter(i => isNonVegDish(i));
    }

    // Search query filter
    if (q) {
      filtered = filtered.filter(i => 
        (i.name || '').toLowerCase().includes(q) || 
        (i.description && i.description.toLowerCase().includes(q))
      );
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
      const qty = getDishTotalQty(dish.id);
      const mods = getDishModifiers(dish);
      const hasModifiers = mods && ((mods.variants && mods.variants.length > 0) || (mods.addons && mods.addons.length > 0));

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

      const customizableTag = hasModifiers ? `
        <span class="inline-flex items-center gap-1 bg-amber-500/15 border border-amber-500/30 text-[9px] font-bold text-amber-800 dark:text-amber-300 px-1.5 py-0.5 rounded shadow-2xs">
          Customizable
        </span>
      ` : '';

      // Modern Squircle Image with Floating Tags
      const imgHtml = dish.imageUrl ? `
        <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-100 border border-slate-200 mb-1">
          <img src="${dish.imageUrl}" alt="${escapeHtml(dish.name)}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          <div class="absolute top-2 left-2 flex flex-col gap-1">
            ${emblemBadge}
            ${customizableTag}
          </div>
          <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur-md text-white text-[10px] font-mono font-bold flex items-center gap-1 shadow-xs">
            ${VYORA_ICONS.clock} ${dish.preparationTime || 15}m
          </div>
        </div>
      ` : `
        <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden mb-1 flex items-center justify-center ${isNonVeg ? 'bg-orange-50 text-orange-400' : 'bg-emerald-50 text-emerald-500'} border border-slate-200">
          <svg class="w-8 h-8 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4V2"/><path d="M4 18h16"/><path d="M4 18a8 8 0 0 1 16 0"/><circle cx="12" cy="4" r="2"/></svg>
          <div class="absolute top-2 left-2 flex flex-col gap-1">
            ${emblemBadge}
            ${customizableTag}
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
          <button onclick="event.stopPropagation(); ${hasModifiers ? `openDishDetailModal('${dish.id}')` : `addToCart('${dish.id}', event)`}" class="w-full py-2 rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-xs shadow-xs transition-all flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer">
            ${VYORA_ICONS.plus}
            <span>${hasModifiers ? t('customize') : t('add')}</span>
          </button>
        `;
      } else {
        if (hasModifiers) {
          actionControl = `
            <div class="flex items-center justify-between bg-slate-100 border border-slate-300 rounded-xl p-0.5 shadow-xs" onclick="event.stopPropagation()">
              <button onclick="event.stopPropagation(); removeFromCartDish('${dish.id}')" class="w-7 h-7 rounded-lg bg-white text-slate-800 font-black hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center shadow-xs active:scale-75 cursor-pointer">
                ${VYORA_ICONS.minus}
              </button>
              <span class="font-mono font-black text-xs text-slate-900 px-1">${qty} ${t('inOrder')}</span>
              <button onclick="event.stopPropagation(); openDishDetailModal('${dish.id}')" class="px-2 py-1.5 rounded-lg bg-[#034d37] text-white font-bold text-[11px] hover:bg-[#023d2b] transition flex items-center gap-1 shadow-xs active:scale-95 cursor-pointer">
                ${VYORA_ICONS.plus}
                <span>${t('add')}</span>
              </button>
            </div>
          `;
        } else {
          actionControl = `
            <div class="flex items-center justify-between bg-slate-100 border border-slate-300 rounded-xl p-0.5 shadow-xs" onclick="event.stopPropagation()">
              <button onclick="event.stopPropagation(); removeFromCartDish('${dish.id}')" class="w-7 h-7 rounded-lg bg-white text-slate-800 font-black hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center shadow-xs active:scale-75 cursor-pointer">
                ${VYORA_ICONS.minus}
              </button>
              <span class="font-mono font-black text-xs text-slate-900 px-2">${qty}</span>
              <button onclick="event.stopPropagation(); addToCart('${dish.id}', event)" class="w-7 h-7 rounded-lg bg-[#034d37] text-white font-black hover:bg-[#023d2b] transition flex items-center justify-center shadow-xs active:scale-75 cursor-pointer">
                ${VYORA_ICONS.plus}
              </button>
            </div>
          `;
        }
      }

      card.innerHTML = `
        <div class="overflow-hidden">
          ${imgHtml}
          <h4 class="font-sans font-black text-sm sm:text-base text-slate-900 leading-snug line-clamp-1 group-hover:text-[#034d37] transition-colors mt-1">${escapeHtml(dish.name)}</h4>
          <p class="text-[11px] sm:text-xs text-slate-600 font-medium line-clamp-2 mt-0.5 leading-relaxed min-h-[30px]">${escapeHtml(dish.description || 'Prepared fresh with fine ingredients.')}</p>
          <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-100">
            <span class="font-mono font-black text-base text-slate-900">₹${parseFloat(dish.price).toFixed(2)}</span>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">${hasModifiers ? t('customizable') : t('tapForInfo')}</span>
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

  function getDishTotalQty(dishId) {
    let sum = 0;
    cart.forEach(c => {
      if (c.item.id === dishId) sum += c.quantity;
    });
    return sum;
  }

  function getDishModifiers(dish) {
    if (!dish || !dish.modifiers) return null;
    let m = dish.modifiers;
    if (typeof m === 'string') {
      try { m = JSON.parse(m); } catch (e) { m = null; }
    }
    return m;
  }

  // --- ONE PRODUCT DETAILS PAGE (MODAL) ---
  function openDishDetailModal(dishId) {
    const dish = allMenuItems.find(i => i.id === dishId);
    if (!dish) return;

    activeDetailDish = dish;
    detailQty = 1;
    selectedDetailAddons.clear();

    const mods = getDishModifiers(dish);

    // Variants
    const variantsSection = document.getElementById('detail-variants-section');
    if (mods && mods.variants && mods.variants.length > 0) {
      selectedDetailVariant = mods.variants[0];
      variantsSection.classList.remove('hidden');
      renderVariantsUI(mods.variants);
    } else {
      selectedDetailVariant = null;
      variantsSection.classList.add('hidden');
    }

    // Add-Ons
    const addonsSection = document.getElementById('detail-addons-section');
    if (mods && mods.addons && mods.addons.length > 0) {
      addonsSection.classList.remove('hidden');
      renderAddonsUI(mods.addons);
    } else {
      addonsSection.classList.add('hidden');
    }

    // Special Instructions
    const notesInput = document.getElementById('detail-item-notes');
    if (notesInput) notesInput.value = '';

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
    document.getElementById('detail-desc').textContent = dish.description || 'Prepared fresh with fine ingredients and authentic spices by our master culinary team.';

    updateDetailPriceUI();
    document.getElementById('dish-detail-modal').classList.remove('hidden');
  }

  function renderVariantsUI(variants) {
    const list = document.getElementById('detail-variants-list');
    list.innerHTML = variants.map((v, i) => {
      const isSelected = selectedDetailVariant && selectedDetailVariant.name === v.name;
      const extra = parseFloat(v.price || 0);
      const priceText = extra > 0 ? `+₹${extra.toFixed(2)}` : 'Standard';
      return `
        <button type="button" onclick="selectDetailVariant(${i})" class="flex items-center justify-between p-2.5 rounded-xl border-2 ${isSelected ? 'border-[#034d37] bg-emerald-500/10 text-[#034d37]' : 'border-slate-200 bg-slate-50 text-slate-700'} font-bold text-xs transition active:scale-95 cursor-pointer text-left">
          <span class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-full border-2 ${isSelected ? 'border-[#034d37] bg-[#034d37]' : 'border-slate-300'} flex items-center justify-center">
              ${isSelected ? '<span class="w-1.5 h-1.5 rounded-full bg-white"></span>' : ''}
            </span>
            <span>${escapeHtml(v.name)}</span>
          </span>
          <span class="font-mono text-[11px] font-black ${isSelected ? 'text-[#034d37]' : 'text-slate-500'}">${priceText}</span>
        </button>
      `;
    }).join('');
  }

  function selectDetailVariant(index) {
    const mods = getDishModifiers(activeDetailDish);
    if (mods && mods.variants && mods.variants[index]) {
      selectedDetailVariant = mods.variants[index];
      renderVariantsUI(mods.variants);
      updateDetailPriceUI();
    }
  }

  function renderAddonsUI(addons) {
    const list = document.getElementById('detail-addons-list');
    list.innerHTML = addons.map((a, i) => {
      const isSelected = selectedDetailAddons.has(a.name);
      const price = parseFloat(a.price || 0);
      return `
        <button type="button" onclick="toggleDetailAddon(${i})" class="w-full flex items-center justify-between p-2.5 rounded-xl border ${isSelected ? 'border-[#034d37] bg-emerald-500/10 text-[#034d37]' : 'border-slate-200 bg-white text-slate-700'} font-bold text-xs transition active:scale-98 cursor-pointer">
          <span class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-md border ${isSelected ? 'border-[#034d37] bg-[#034d37] text-white' : 'border-slate-300'} flex items-center justify-center font-mono text-[10px]">
              ${isSelected ? '✓' : ''}
            </span>
            <span>${escapeHtml(a.name)}</span>
          </span>
          <span class="font-mono text-xs font-black ${isSelected ? 'text-[#034d37]' : 'text-slate-600'}">+₹${price.toFixed(2)}</span>
        </button>
      `;
    }).join('');
  }

  function toggleDetailAddon(index) {
    const mods = getDishModifiers(activeDetailDish);
    if (mods && mods.addons && mods.addons[index]) {
      const addon = mods.addons[index];
      if (selectedDetailAddons.has(addon.name)) {
        selectedDetailAddons.delete(addon.name);
      } else {
        selectedDetailAddons.set(addon.name, addon);
      }
      renderAddonsUI(mods.addons);
      updateDetailPriceUI();
    }
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
    const base = parseFloat(activeDetailDish.price);
    const variantExtra = parseFloat(selectedDetailVariant?.price || 0);
    let addonsExtra = 0;
    selectedDetailAddons.forEach(a => addonsExtra += parseFloat(a.price || 0));
    const unitPrice = base + variantExtra + addonsExtra;
    const total = unitPrice * detailQty;
    document.getElementById('detail-price').textContent = `₹${unitPrice.toFixed(2)}`;
    document.getElementById('detail-btn-price').textContent = `• ₹${total.toFixed(2)}`;
  }

  function addDetailToCart() {
    if (!activeDetailDish) return;
    const notes = document.getElementById('detail-item-notes')?.value.trim() || null;
    const variantName = selectedDetailVariant ? selectedDetailVariant.name : 'std';
    const addonsKey = Array.from(selectedDetailAddons.keys()).sort().join('_');
    const notesKey = notes || '';
    const cartKey = `${activeDetailDish.id}__${variantName}__${addonsKey}__${notesKey}`;

    const base = parseFloat(activeDetailDish.price);
    const variantExtra = parseFloat(selectedDetailVariant?.price || 0);
    let addonsExtra = 0;
    selectedDetailAddons.forEach(a => addonsExtra += parseFloat(a.price || 0));
    const unitPrice = base + variantExtra + addonsExtra;

    if (cart.has(cartKey)) {
      cart.get(cartKey).quantity += detailQty;
    } else {
      cart.set(cartKey, {
        key: cartKey,
        item: activeDetailDish,
        quantity: detailQty,
        unitPrice: unitPrice,
        selectedVariant: selectedDetailVariant ? { name: selectedDetailVariant.name, price: variantExtra } : null,
        selectedAddons: Array.from(selectedDetailAddons.values()),
        notes: notes,
      });
    }

    renderDishes();
    updateCartBar();
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

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function addToCart(dishId, event) {
    const dish = allMenuItems.find(i => i.id === dishId);
    if (!dish) return;

    const mods = getDishModifiers(dish);
    const hasModifiers = mods && ((mods.variants && mods.variants.length > 0) || (mods.addons && mods.addons.length > 0));

    if (hasModifiers) {
      openDishDetailModal(dishId);
      return;
    }

    const cartKey = `${dish.id}__std____`;
    const existing = cart.get(cartKey);
    if (existing) {
      existing.quantity++;
    } else {
      cart.set(cartKey, {
        key: cartKey,
        item: dish,
        quantity: 1,
        unitPrice: parseFloat(dish.price),
        selectedVariant: null,
        selectedAddons: [],
        notes: null
      });
    }

    // Micro-animation: spawn floating +1 element if event trigger is present
    if (event && event.currentTarget) {
      const btn = event.currentTarget;
      const particle = document.createElement('span');
      particle.className = 'floating-plus-particle';
      particle.textContent = '+1';
      btn.parentElement.appendChild(particle);
      setTimeout(() => particle.remove(), 650);
    }

    renderDishes();
    updateCartBar();
    if (document.getElementById('cart-modal').classList.contains('hidden') === false) {
      renderCartModalItems();
    }
  }

  function removeFromCartDish(dishId) {
    for (const [key, c] of cart.entries()) {
      if (c.item.id === dishId) {
        decrementCartKey(key);
        break;
      }
    }
  }

  function incrementCartKey(cartKey) {
    const item = cart.get(cartKey);
    if (!item) return;
    item.quantity++;
    updateCartBar();
    renderDishes();
    renderCartModalItems();
  }

  function decrementCartKey(cartKey) {
    const item = cart.get(cartKey);
    if (!item) return;
    if (item.quantity > 1) {
      item.quantity--;
    } else {
      cart.delete(cartKey);
    }
    updateCartBar();
    renderDishes();
    if (cart.size === 0) closeCartModal();
    else renderCartModalItems();
  }

  function updateCartBar() {
    const bar = document.getElementById('bottom-cart-bar');
    let count = 0;
    let total = 0;

    cart.forEach(c => {
      count += c.quantity;
      total += c.quantity * c.unitPrice;
    });

    if (count > 0) {
      bar.classList.remove('hidden');
      document.getElementById('cart-item-count-badge').textContent = count;
      document.getElementById('cart-item-count').textContent = `${count} ${count === 1 ? t('itemInOrder') : t('itemsInOrder')}`;
      document.getElementById('cart-total-price').textContent = `₹${total.toFixed(2)}`;
    } else {
      bar.classList.add('hidden');
    }
  }

  function openCartModal() {
    renderCartModalItems();
    document.getElementById('cart-modal').classList.remove('hidden');
  }

  function closeCartModal() {
    document.getElementById('cart-modal').classList.add('hidden');
  }

  function appendNote(text) {
    const noteArea = document.getElementById('order-notes');
    if (!noteArea) return;
    if (noteArea.value.trim().length > 0) {
      noteArea.value += ', ' + text;
    } else {
      noteArea.value = text;
    }
  }

  function renderCartModalItems() {
    const container = document.getElementById('cart-modal-items');
    container.innerHTML = '';
    let total = 0;

    cart.forEach(({ key, item, quantity, unitPrice, selectedVariant, selectedAddons, notes }) => {
      const lineTotal = unitPrice * quantity;
      total += lineTotal;
      const isNonVeg = isNonVegDish(item);

      const variantHtml = selectedVariant
        ? `<span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800 text-[10px] font-bold text-slate-700 dark:text-slate-300 mr-1 border border-slate-200 dark:border-zinc-700">${escapeHtml(selectedVariant.name)}</span>`
        : '';
      const addonsHtml = (selectedAddons && selectedAddons.length > 0)
        ? selectedAddons.map(a => `<span class="inline-block px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-[9px] font-bold mr-1 border border-emerald-200 dark:border-emerald-800">+ ${escapeHtml(a.name)} (₹${parseFloat(a.price).toFixed(0)})</span>`).join('')
        : '';
      const notesHtml = notes
        ? `<div class="text-[10px] text-amber-800 dark:text-amber-300 font-semibold italic mt-0.5 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span> ${escapeHtml(notes)}</div>`
        : '';

      const row = document.createElement('div');
      row.className = 'flex justify-between items-start py-2.5 border-b border-[var(--border)] last:border-0 gap-3';
      row.innerHTML = `
        <div class="pr-2 min-w-0 flex-1">
          <div class="flex items-center gap-1.5 flex-wrap">
            ${isNonVeg ? VYORA_ICONS.nonVegDot : VYORA_ICONS.vegDot}
            <p class="font-bold text-[var(--text-primary)] text-xs truncate">${escapeHtml(item.name)}</p>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-1">
            ${variantHtml}
            ${addonsHtml}
          </div>
          ${notesHtml}
          <span class="text-[10px] text-[var(--text-muted)] font-mono block mt-1">₹${unitPrice.toFixed(2)} each</span>
        </div>
        <div class="flex items-center gap-2 shrink-0 pt-0.5">
          <div class="flex items-center bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] p-0.5">
            <button onclick="decrementCartKey('${key}')" class="w-6 h-6 rounded text-xs font-bold hover:bg-red-500 hover:text-white transition active:scale-75 flex items-center justify-center cursor-pointer">${VYORA_ICONS.minus}</button>
            <span class="w-5 text-center font-mono font-bold text-xs text-[var(--accent)]">${quantity}</span>
            <button onclick="incrementCartKey('${key}')" class="w-6 h-6 rounded text-xs font-bold bg-[var(--accent)] text-white hover:opacity-90 transition active:scale-75 flex items-center justify-center cursor-pointer">${VYORA_ICONS.plus}</button>
          </div>
          <span class="font-mono font-bold text-xs text-[var(--text-primary)] w-14 text-right">₹${lineTotal.toFixed(2)}</span>
        </div>
      `;
      container.appendChild(row);
    });

    document.getElementById('modal-grand-total').textContent = `₹${total.toFixed(2)}`;
    if (window.lucide) lucide.createIcons();
  }

  async function placeOrder() {
    const btn = document.getElementById('place-order-btn');
    const sessionToken = sessionStorage.getItem(SESSION_KEY);
    const notes = document.getElementById('order-notes').value.trim();

    if (cart.size === 0) return;
    btn.disabled = true;
    btn.innerHTML = `<span class="inline-flex items-center gap-2">${VYORA_ICONS.spinner} ${t('transmitting')}</span>`;

    const items = [];
    cart.forEach(c => {
      items.push({
        menuItemId: c.item.id,
        quantity: c.quantity,
        selectedVariant: c.selectedVariant,
        selectedAddons: c.selectedAddons,
        notes: c.notes,
      });
    });

    try {
      const res = await fetch('/api/public/orders', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          ownerId: OWNER_ID,
          tableNumber: TABLE_NUMBER,
          items,
          sessionToken,
          notes: notes || null,
        })
      });
      const data = await res.json();
      if (data.success) {
        if (window.playNotificationSound) window.playNotificationSound();
        window.showToast(t('orderSuccessToast'), 'order');
        cart.clear();
        closeCartModal();
        renderDishes();
        await loadLiveOrders();
        toggleTrackingSheet();
      } else {
        if (data.error?.code === 'SESSION_EXPIRED') {
          showSessionSettledScreen('Cannot place order: This dining session has ended because the bill was settled. Please scan the QR code at your table to start a new session.', true);
          return;
        }
        const errorMsg = data.error?.message || data.message || t('orderFailedAlert');
        alert(errorMsg);
      }
    } catch (err) {
      console.error('Order placement error:', err);
      alert('Network error. Please try again.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = `<span>${t('submitOrder')}</span><i data-lucide="arrow-right" class="w-4 h-4"></i>`;
      if (window.lucide) lucide.createIcons();
    }
  }

  async function requestService(type) {
    const sessionToken = sessionStorage.getItem(SESSION_KEY);
    const btn = document.getElementById(`btn-${type}`);
    const textSpan = document.getElementById(`text-${type}`);
    const originalText = textSpan.textContent;
    btn.disabled = true;

    try {
      const res = await fetch('/api/service-request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          ownerId: OWNER_ID,
          tableNumber: TABLE_NUMBER,
          type,
          sessionToken,
        })
      });
      const data = await res.json();
      if (data.success) {
        if (window.playNotificationSound) window.playNotificationSound();
        window.showToast(type === 'water' ? t('waterDispatched') : t('waiterDispatched'), type);
        
        // Cooldown timer countdown
        let cooldown = 30;
        textSpan.textContent = `Wait ${cooldown}s`;
        const timer = setInterval(() => {
          cooldown--;
          if (cooldown <= 0) {
            clearInterval(timer);
            btn.disabled = false;
            textSpan.textContent = originalText;
          } else {
            textSpan.textContent = `Wait ${cooldown}s`;
          }
        }, 1000);
      } else {
        if (data.error?.code === 'SESSION_EXPIRED') {
          showSessionSettledScreen('Table session has been settled. Please scan the QR code on your table to start a new session.', true);
          return;
        }
        alert(data.error?.message || 'Request failed');
        btn.disabled = false;
      }
    } catch (err) {
      console.error('Service request error:', err);
      btn.disabled = false;
    }
  }

  async function loadLiveOrders() {
    const sessionToken = sessionStorage.getItem(SESSION_KEY);
    if (!sessionToken) return;

    try {
      const res = await fetch(`/api/public/orders?ownerId=${OWNER_ID}&tableNumber=${TABLE_NUMBER}&sessionToken=${sessionToken}`);
      const data = await res.json();
      if (!data.success && data.error?.code === 'SESSION_EXPIRED') {
        showSessionSettledScreen('Dining session has concluded. The table bill has been settled. Please scan the QR code at your table to start a new session.', true);
        return;
      }
      if (data.success && Array.isArray(data.data)) {
        activeLiveOrders = data.data.filter(o => o.status !== 'cancelled');
        const trackingBtn = document.getElementById('tracking-toggle-btn');
        const countSpan = document.getElementById('tracking-active-count');
        
        if (activeLiveOrders.length > 0) {
          trackingBtn.classList.remove('hidden');
          const hasReady = activeLiveOrders.some(o => ['ready', 'prepared', 'plate_up'].includes((o.status || '').toLowerCase().trim()));
          const hasPreparing = activeLiveOrders.some(o => ['preparing', 'cooking', 'in_kitchen'].includes((o.status || '').toLowerCase().trim()));

          if (hasReady) {
            trackingBtn.className = 'px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-500/40 font-bold text-xs flex items-center gap-1.5 shadow-sm transition active:scale-95 animate-bounce';
            trackingBtn.innerHTML = `
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
              <span id="tracking-status-text">Food Ready · भोजन तैयार है! (${activeLiveOrders.length})</span>
            `;
          } else if (hasPreparing) {
            trackingBtn.className = 'px-3 py-1.5 rounded-full bg-amber-500/15 text-amber-800 dark:text-amber-300 border border-amber-500/30 font-bold text-xs flex items-center gap-1.5 shadow-xs transition active:scale-95 animate-pulse';
            trackingBtn.innerHTML = `
              <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
              <span id="tracking-status-text">Cooking · पक रहा है (${activeLiveOrders.length})</span>
            `;
          } else {
            trackingBtn.className = 'px-3 py-1.5 rounded-full bg-orange-500/15 text-orange-700 dark:text-orange-300 border border-orange-500/30 font-bold text-xs flex items-center gap-1.5 shadow-xs transition active:scale-95 animate-pulse';
            trackingBtn.innerHTML = `
              <span class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
              <span id="tracking-status-text">Track · ट्रैक (${activeLiveOrders.length})</span>
            `;
          }

          renderTrackingOrders();
        } else {
          trackingBtn.classList.add('hidden');
        }
      }
    } catch(err) {
      console.error('Live orders check error:', err);
    }
  }

  function toggleTrackingSheet() {
    const modal = document.getElementById('tracking-modal');
    modal.classList.toggle('hidden');
    renderTrackingOrders();
  }

  function renderTrackingOrders() {
    const container = document.getElementById('tracking-orders-list');
    if (!container) return;
    container.innerHTML = '';

    if (activeLiveOrders.length === 0) {
      container.innerHTML = `
        <div class="p-8 text-center text-xs text-[var(--text-muted)] space-y-1">
          <p class="font-bold text-sm text-[var(--text-primary)]">No active orders right now · कोई सक्रिय ऑर्डर नहीं है</p>
          <p>Dishes placed in this session will appear here with live kitchen updates.</p>
          <p class="text-[11px] text-slate-500">इस सत्र में दिए गए ऑर्डर की लाइव स्थिति यहाँ दिखेगी।</p>
        </div>
      `;
      return;
    }

    const steps = ['pending', 'preparing', 'ready', 'completed'];

    activeLiveOrders.forEach((order, idx) => {
      const card = document.createElement('div');
      card.className = 'p-4 rounded-[var(--radius-xl)] bg-[var(--bg-base)] border border-[var(--border)] shadow-xs space-y-3';

      const normStatus = (order.status || '').toLowerCase().trim();
      let currentStepIdx = steps.indexOf(normStatus);
      if (currentStepIdx === -1) {
        if (normStatus === 'cooking' || normStatus === 'in_kitchen') currentStepIdx = 1;
        else if (normStatus === 'prepared' || normStatus === 'plate_up') currentStepIdx = 2;
        else if (normStatus === 'served' || normStatus === 'settled' || normStatus === 'paid') currentStepIdx = 3;
        else currentStepIdx = 0;
      }

      const itemsList = (order.items || []).map(i => `<span class="inline-block font-semibold">${i.quantity}x ${i.menuItemName}</span>`).join(', ');

      let statusTitleEn = 'Received';
      let statusTitleHi = 'ऑर्डर मिला';
      let statusMsgEn = 'Kitchen received your ticket';
      let statusMsgHi = 'किचन को आपका ऑर्डर मिल गया है';
      let statusIcon = VYORA_ICONS.stepTicket;
      let statusColor = 'text-orange-700 dark:text-orange-300 bg-orange-500/10 border-orange-500/25';

      if (currentStepIdx === 1) {
        statusTitleEn = 'Cooking';
        statusTitleHi = 'पक रहा है';
        statusMsgEn = 'Cooking in kitchen';
        statusMsgHi = 'किचन में आपका खाना पक रहा है';
        statusIcon = VYORA_ICONS.stepFlame;
        statusColor = 'text-amber-800 dark:text-amber-300 bg-amber-500/15 border-amber-500/30';
      } else if (currentStepIdx === 2) {
        statusTitleEn = 'Ready';
        statusTitleHi = 'तैयार है';
        statusMsgEn = 'Order is ready! Please collect from the counter';
        statusMsgHi = 'भोजन तैयार है, कृपया काउंटर से प्राप्त करें';
        statusIcon = VYORA_ICONS.stepPlate;
        statusColor = 'text-emerald-800 dark:text-emerald-300 bg-emerald-500/15 border-emerald-500/35 ring-1 ring-emerald-500/30';
      } else if (currentStepIdx === 3) {
        statusTitleEn = 'Served';
        statusTitleHi = 'सर्व हो गया';
        statusMsgEn = 'Delivered to your table';
        statusMsgHi = 'आपकी टेबल पर सर्व कर दिया गया है';
        statusIcon = VYORA_ICONS.stepCheck;
        statusColor = 'text-slate-700 dark:text-slate-300 bg-slate-500/10 border-slate-500/25';
      }

      card.innerHTML = `
        <div class="flex justify-between items-start pb-2 border-b border-[var(--border)]">
          <div>
            <div class="flex items-center gap-1.5">
              <span class="w-5 h-5 rounded-full bg-[var(--accent)] text-white text-[10px] font-bold flex items-center justify-center font-mono">#${idx + 1}</span>
              <span class="font-bold text-xs text-[var(--text-primary)]">Order Ticket · ऑर्डर #${idx + 1}</span>
            </div>
            <p class="text-[11px] text-[var(--text-secondary)] mt-1">${itemsList}</p>
          </div>
          <span class="font-mono font-bold text-xs text-[var(--accent)] shrink-0">₹${parseFloat(order.totalAmount).toFixed(2)}</span>
        </div>

        <!-- Animated Progress Stepper -->
        <div class="pt-3 pb-1">
          <div class="flex items-center justify-between relative mb-2.5 px-2">
            <!-- Background progress track centered exactly at circle middle (top-4 = 16px) -->
            <div class="absolute top-4 left-6 right-6 h-1 bg-slate-200 dark:bg-zinc-700 -translate-y-1/2 z-0">
              <div class="h-full bg-[var(--accent)] transition-all duration-500" style="width: ${Math.min(100, Math.max(0, currentStepIdx / 3 * 100))}%"></div>
            </div>

            <!-- Step 1: Pending -->
            <div class="relative z-10 flex flex-col items-center text-center">
              <div class="w-8 h-8 rounded-full ${currentStepIdx >= 0 ? 'bg-[var(--accent)] text-white ring-2 ring-[var(--accent-glow)]' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'} ${currentStepIdx === 0 ? 'animate-pulse-ring' : ''} flex items-center justify-center font-bold text-xs transition-all shadow-xs">
                ${currentStepIdx > 0 ? VYORA_ICONS.stepCheck : VYORA_ICONS.stepTicket}
              </div>
              <span class="text-[11px] mt-1.5 font-bold text-slate-800 dark:text-slate-200 leading-tight">Received</span>
              <span class="text-[9px] font-medium text-slate-500 dark:text-slate-400">ऑर्डर मिला</span>
            </div>

            <!-- Step 2: Preparing -->
            <div class="relative z-10 flex flex-col items-center text-center">
              <div class="w-8 h-8 rounded-full ${currentStepIdx >= 1 ? 'bg-[var(--accent)] text-white ring-2 ring-[var(--accent-glow)]' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'} ${currentStepIdx === 1 ? 'animate-pulse-ring' : ''} flex items-center justify-center font-bold text-xs transition-all shadow-xs">
                ${currentStepIdx > 1 ? VYORA_ICONS.stepCheck : VYORA_ICONS.stepFlame}
              </div>
              <span class="text-[11px] mt-1.5 font-bold text-slate-800 dark:text-slate-200 leading-tight">Cooking</span>
              <span class="text-[9px] font-medium text-slate-500 dark:text-slate-400">पक रहा है</span>
            </div>

            <!-- Step 3: Ready -->
            <div class="relative z-10 flex flex-col items-center text-center">
              <div class="w-8 h-8 rounded-full ${currentStepIdx >= 2 ? 'bg-emerald-600 text-white ring-2 ring-emerald-500/40 shadow-sm' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'} ${currentStepIdx === 2 ? 'animate-bounce' : ''} flex items-center justify-center font-bold text-xs transition-all shadow-xs">
                ${currentStepIdx > 2 ? VYORA_ICONS.stepCheck : VYORA_ICONS.stepPlate}
              </div>
              <span class="text-[11px] mt-1.5 font-bold ${currentStepIdx >= 2 ? 'text-emerald-700 dark:text-emerald-300 font-extrabold' : 'text-slate-800 dark:text-slate-200'} leading-tight">Ready</span>
              <span class="text-[9px] font-medium ${currentStepIdx >= 2 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'}">तैयार है</span>
            </div>

            <!-- Step 4: Completed -->
            <div class="relative z-10 flex flex-col items-center text-center">
              <div class="w-8 h-8 rounded-full ${currentStepIdx >= 3 ? 'bg-emerald-600 text-white ring-2 ring-emerald-500/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'} flex items-center justify-center font-bold text-xs transition-all shadow-xs">
                ${VYORA_ICONS.stepCheck}
              </div>
              <span class="text-[11px] mt-1.5 font-bold text-slate-800 dark:text-slate-200 leading-tight">Served</span>
              <span class="text-[9px] font-medium text-slate-500 dark:text-slate-400">सर्व हो गया</span>
            </div>
          </div>

          <!-- Prominent Live Status Message Capsule -->
          <div class="pt-2 flex justify-center">
            <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl sm:rounded-full border text-xs font-semibold ${statusColor} text-center leading-snug shadow-xs">
              <span class="shrink-0 flex items-center">${statusIcon}</span>
              <span class="break-words">${statusMsgEn} · ${statusMsgHi}</span>
            </div>
          </div>
        </div>
      `;
      container.appendChild(card);
    });

    if (window.lucide) lucide.createIcons();
  }
</script>
@endsection
