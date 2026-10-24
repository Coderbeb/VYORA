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
            <h1 id="restaurant-name-header" class="font-serif font-bold text-sm sm:text-base tracking-tight truncate max-w-[150px] sm:max-w-xs text-[var(--text-primary)]">
              Loading Menu...
            </h1>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping shrink-0" title="Live Venue Connection"></span>
          </div>
          <div class="flex items-center gap-1.5 text-[10px]">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold uppercase tracking-wider">
              <i data-lucide="utensils" class="w-2.5 h-2.5"></i> Table #{{ $tableNumber }}
            </span>
            <span class="text-[var(--text-muted)] hidden sm:inline">• In-Dining</span>
          </div>
        </div>
      </div>

      <!-- Quick Action Controls -->
      <div class="flex items-center gap-2">
        <!-- Live Order Tracking Pill (appears when orders active) -->
        <button id="tracking-toggle-btn" onclick="toggleTrackingSheet()" class="hidden px-2.5 py-1.5 rounded-full bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/25 font-bold text-[11px] flex items-center gap-1.5 shadow-xs transition active:scale-95 animate-pulse">
          <span class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
          <span id="tracking-status-text">Track (<span id="tracking-active-count">0</span>)</span>
        </button>

        <!-- Theme Switcher -->
        <button onclick="toggleTheme()" class="p-2 rounded-full border border-[var(--border)] bg-[var(--bg-base)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition active:scale-90" title="Toggle Theme">
          <i data-lucide="sun" class="w-4 h-4 hidden dark:block text-amber-400"></i>
          <i data-lucide="moon" class="w-4 h-4 block dark:hidden text-slate-600"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Main Scrollable Mobile Container -->
  <main class="max-w-2xl mx-auto px-4 pt-3.5 space-y-3.5">
    <!-- Welcome & Table Service Quick Bar -->
    <div class="p-3.5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col gap-2.5">
      <div class="flex items-center justify-between text-xs">
        <span class="text-[var(--text-secondary)] font-medium flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-[var(--accent)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span>Waiter & Table Service</span>
        </span>
        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded-full flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Attendant on Floor
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
      <button onclick="setDietFilter('all')" id="diet-tab-all" class="flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition bg-[var(--accent)] text-white shadow-xs flex items-center justify-center gap-1.5">
        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>All Items</span>
      </button>
      <button onclick="setDietFilter('veg')" id="diet-tab-veg" class="flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-emerald-700 dark:text-emerald-300 hover:bg-emerald-500/10 flex items-center justify-center gap-1.5">
        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none">
          <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#059669" stroke-width="1.8" fill="#ECFDF5"/>
          <circle cx="8" cy="8" r="3.5" fill="#059669"/>
        </svg>
        <span>Pure Veg</span>
      </button>
      <button onclick="setDietFilter('nonveg')" id="diet-tab-nonveg" class="flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-orange-700 dark:text-orange-400 hover:bg-orange-500/10 flex items-center justify-center gap-1.5">
        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none">
          <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#DC2626" stroke-width="1.8" fill="#FEF2F2"/>
          <polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/>
        </svg>
        <span>Non-Veg</span>
      </button>
    </div>

    <!-- Category Pills Filter (Horizontal Smooth Scroll) -->
    <div id="cust-category-tabs" class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none -mx-4 px-4">
      <button onclick="selectCustomerCategory('all')" id="cust-tab-all" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-xs shrink-0 active:scale-95">
        All Categories
      </button>
    </div>

    <!-- Dish List Container (2-Column Grid on Mobile) -->
    <div id="cust-dishes-container" class="grid grid-cols-2 gap-2.5 sm:gap-3.5 pt-1">
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

      <button onclick="openCartModal()" class="px-5 py-2.5 sm:py-3 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-md transition-transform flex items-center gap-2 active:scale-95">
        <span>View Order</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <!-- Cart & Checkout Modal / Bottom Sheet -->
  <div id="cart-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm transition-opacity">
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-t-[28px] sm:rounded-[var(--radius-xl)] shadow-2xl w-full max-w-lg p-5 sm:p-6 max-h-[85vh] flex flex-col justify-between animate-in slide-in-from-bottom duration-300">
      <!-- Swipe Handle Indicator for mobile -->
      <div class="w-12 h-1 bg-[var(--border)] rounded-full mx-auto mb-3 sm:hidden"></div>

      <div class="overflow-y-auto pr-1">
        <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-[var(--accent-glow)] text-[var(--accent)] flex items-center justify-center font-bold">
              <i data-lucide="shopping-bag" class="w-4 h-4"></i>
            </div>
            <div>
              <h3 class="font-serif font-bold text-base sm:text-lg text-[var(--text-primary)]">Your Table Order</h3>
              <p class="text-[10px] text-[var(--text-muted)]">Table #{{ $tableNumber }} • Review items</p>
            </div>
          </div>
          <button onclick="closeCartModal()" class="p-1.5 rounded-full text-[var(--text-muted)] hover:text-[var(--text-primary)] bg-[var(--bg-base)]">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        </div>

        <!-- Cart Items Container -->
        <div id="cart-modal-items" class="divide-y divide-[var(--border)] text-xs mb-4">
          <!-- Populated by JS -->
        </div>

        <!-- Quick Dietary Cooking Instructions Notes -->
        <div class="mb-4 space-y-1.5">
          <label class="block text-xs font-semibold text-[var(--text-secondary)]">Cooking Instructions / Dietary Notes</label>
          <div class="flex items-center gap-1.5 flex-wrap text-[10px] mb-1">
            <button type="button" onclick="appendNote('Less spicy')" class="px-2.5 py-1 rounded-full border border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-[var(--accent)] hover:text-[var(--text-primary)] transition flex items-center gap-1 active:scale-95 shadow-xs">
              <svg class="w-3 h-3 text-red-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c1 3 4 5 4 9a6 6 0 0 1-12 0c0-4 3-6 4-9"/><path d="M10 17a2 2 0 0 0 4 0"/></svg>
              <span>Less spicy</span>
            </button>
            <button type="button" onclick="appendNote('No onions/garlic')" class="px-2.5 py-1 rounded-full border border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-[var(--accent)] hover:text-[var(--text-primary)] transition flex items-center gap-1 active:scale-95 shadow-xs">
              <svg class="w-3 h-3 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 4 13C4 9.13 7.13 6 11 6c1.66 0 3.2.58 4.42 1.55L20 3"/><path d="M21 21L3 3"/></svg>
              <span>No onion/garlic</span>
            </button>
            <button type="button" onclick="appendNote('Extra crispy')" class="px-2.5 py-1 rounded-full border border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-[var(--accent)] hover:text-[var(--text-primary)] transition flex items-center gap-1 active:scale-95 shadow-xs">
              <svg class="w-3 h-3 text-amber-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <span>Extra crispy</span>
            </button>
            <button type="button" onclick="appendNote('Sauce on side')" class="px-2.5 py-1 rounded-full border border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-[var(--accent)] hover:text-[var(--text-primary)] transition flex items-center gap-1 active:scale-95 shadow-xs">
              <svg class="w-3 h-3 text-indigo-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14v3a7 7 0 0 1-14 0V8z"/><path d="M8 8V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v3"/><line x1="4" y1="19" x2="20" y2="19"/></svg>
              <span>Sauce on side</span>
            </button>
          </div>
          <textarea id="order-notes" rows="2" placeholder="e.g. Less spicy, dressing on the side..." class="w-full px-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]"></textarea>
        </div>

        <!-- Price Summary -->
        <div class="pt-3 border-t-2 border-[var(--border)] flex justify-between items-center text-sm font-bold text-[var(--text-primary)]">
          <span>Order Subtotal</span>
          <span id="modal-grand-total" class="font-mono text-base text-[var(--accent)]">₹0.00</span>
        </div>
      </div>

      <!-- Place Order Submit Button -->
      <div class="pt-4 border-t border-[var(--border)]">
        <button onclick="placeOrder()" id="place-order-btn" class="w-full py-3.5 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-sm shadow-md transition-transform flex items-center justify-center gap-2 active:scale-95">
          <span>Submit Order to Kitchen</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Live Order Tracking Sheet Modal -->
  <div id="tracking-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm transition-opacity">
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-t-[28px] sm:rounded-[var(--radius-xl)] shadow-2xl w-full max-w-lg p-5 sm:p-6 max-h-[85vh] overflow-y-auto animate-in slide-in-from-bottom duration-300">
      <div class="w-12 h-1 bg-[var(--border)] rounded-full mx-auto mb-3 sm:hidden"></div>

      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-orange-500/10 text-orange-600 flex items-center justify-center font-bold">
            <i data-lucide="flame" class="w-4 h-4"></i>
          </div>
          <div>
            <h3 class="font-serif font-bold text-base sm:text-lg text-[var(--text-primary)]">Live Kitchen Status</h3>
            <p class="text-[10px] text-[var(--text-muted)]">Real-time order progress for Table #{{ $tableNumber }}</p>
          </div>
        </div>
        <button onclick="toggleTrackingSheet()" class="p-1.5 rounded-full text-[var(--text-muted)] hover:text-[var(--text-primary)] bg-[var(--bg-base)]">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div id="tracking-orders-list" class="space-y-4">
        <!-- Injected by JS -->
      </div>
    </div>
  </div>
</div>

<script>
  const OWNER_ID = "{{ $ownerId }}";
  const TABLE_NUMBER = parseInt("{{ $tableNumber }}", 10);
  const SESSION_KEY = `sessionToken_${OWNER_ID}_${TABLE_NUMBER}`;

  // Bespoke Luxury Vector Icons Suite
  const VYORA_ICONS = {
    vegEmblem: `<div class="flex items-center gap-1 bg-emerald-500/10 border border-emerald-500/25 px-1.5 py-0.5 rounded text-[9px] font-bold text-emerald-700 dark:text-emerald-300 shrink-0">
      <svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none">
        <rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#059669" stroke-width="1.8" fill="#ECFDF5"/>
        <circle cx="8" cy="8" r="3.5" fill="#059669"/>
      </svg>
      <span>Pure Veg</span>
    </div>`,

    nonVegEmblem: `<div class="flex items-center gap-1 bg-red-500/10 border border-red-500/25 px-1.5 py-0.5 rounded text-[9px] font-bold text-red-700 dark:text-red-300 shrink-0">
      <svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none">
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
  let cart = new Map(); // id -> { item, quantity }
  let activeCatFilter = 'all';
  let activeDietFilter = 'all'; // 'all', 'veg', 'nonveg'
  let activeLiveOrders = [];
  let livePollInterval = null;

  document.addEventListener('DOMContentLoaded', () => {
    loadMenu();
    loadLiveOrders();
    initPusherListener();

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
          if (data && data.tableNumber === TABLE_NUMBER) {
            loadLiveOrders();
            if (window.playNotificationSound) window.playNotificationSound();
          }
        });

        channel.bind('table:reset', function(data) {
          if (data && data.tableNumber === TABLE_NUMBER) {
            sessionStorage.removeItem(SESSION_KEY);
            activeLiveOrders = [];
            document.getElementById('tracking-toggle-btn').classList.add('hidden');
            document.getElementById('tracking-modal').classList.add('hidden');
            window.showToast('Your table session has been settled. Thank you for dining with us!');
          }
        });
      } catch(err) {
        console.warn('Pusher initialization error:', err);
      }
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

        allMenuItems = data.data.items || [];
        allCategories = data.data.categories || [];

        renderCategoryTabs();
        renderDishes();
      } else {
        document.getElementById('cust-dishes-container').innerHTML = `
          <div class="col-span-full p-8 rounded-[var(--radius-xl)] bg-red-500/10 border border-red-500/20 text-center text-xs text-red-600">
            <h4 class="font-bold text-sm mb-1">${data.error?.code === 'SESSION_EXPIRED' ? 'Session Expired' : 'Access Restricted'}</h4>
            <p>${data.error?.message || 'Please scan the table QR code to view the live menu.'}</p>
          </div>
        `;
      }
    } catch(err) {
      console.error('Menu load error:', err);
    }
  }

  function renderCategoryTabs() {
    const container = document.getElementById('cust-category-tabs');
    container.innerHTML = `
      <button onclick="selectCustomerCategory('all')" id="cust-tab-all" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition shrink-0 active:scale-95 ${activeCatFilter === 'all' ? 'bg-[var(--accent)] text-white shadow-xs' : 'border border-[var(--border)] bg-[var(--bg-surface)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]'}">
        All Categories
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
        ? 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition bg-[var(--accent)] text-white shadow-xs flex items-center justify-center gap-1.5'
        : 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] flex items-center justify-center gap-1.5';
    }
    if (tabVeg) {
      tabVeg.className = filter === 'veg'
        ? 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition bg-emerald-600 text-white shadow-xs flex items-center justify-center gap-1.5'
        : 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-emerald-700 dark:text-emerald-300 hover:bg-emerald-500/10 flex items-center justify-center gap-1.5';
    }
    if (tabNonVeg) {
      tabNonVeg.className = filter === 'nonveg'
        ? 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition bg-orange-600 text-white shadow-xs flex items-center justify-center gap-1.5'
        : 'flex-1 py-1.5 rounded-[var(--radius-md)] text-center transition text-orange-700 dark:text-orange-400 hover:bg-orange-500/10 flex items-center justify-center gap-1.5';
    }
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

    if (filtered.length === 0) {
      container.innerHTML = `
        <div class="col-span-full p-10 text-center rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] text-xs text-[var(--text-muted)] space-y-1">
          <p class="font-bold text-sm text-[var(--text-primary)]">No dishes found</p>
          <p>Try clearing filters or search terms.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = '';
    filtered.forEach(dish => {
      const isNonVeg = isNonVegDish(dish);
      const qty = cart.get(dish.id)?.quantity || 0;

      // Card Element with special Non-Veg Reddish-Orange Color styling
      const card = document.createElement('div');
      
      if (isNonVeg) {
        // As requested: "if produt is non-veg then it should be color orange type reddish to that product card"
        card.className = 'p-3 sm:p-3.5 rounded-[var(--radius-xl)] bg-gradient-to-br from-[#fff7ed] via-[#fff1f2] to-[#ffedd5] dark:from-[#2a1207] dark:via-[#350f14] dark:to-[#220d06] border border-orange-300 dark:border-orange-500/40 shadow-[0_4px_16px_-2px_rgba(234,88,12,0.15)] flex flex-col justify-between space-y-2.5 transition-all duration-200 hover:shadow-md hover:border-orange-500 active:scale-[0.99] relative overflow-hidden';
      } else {
        // Pure crisp alabaster card for Veg
        card.className = 'p-3 sm:p-3.5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col justify-between space-y-2.5 transition-all duration-200 hover:shadow-md hover:border-emerald-500/40 active:scale-[0.99] relative overflow-hidden';
      }

      // Veg / Non-Veg Emblem Badge
      const emblemBadge = isNonVeg ? VYORA_ICONS.nonVegEmblem : VYORA_ICONS.vegEmblem;

      // Dish Image or Tasteful Luxury Icon Pattern
      const imgHtml = dish.imageUrl ? `
        <div class="relative w-full h-24 sm:h-28 rounded-[var(--radius-lg)] overflow-hidden border border-[var(--border)] mb-1.5 bg-black/5">
          <img src="${dish.imageUrl}" alt="${dish.name}" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
        </div>
      ` : `
        <div class="relative w-full h-16 sm:h-20 rounded-[var(--radius-lg)] overflow-hidden border border-[var(--border)] mb-1.5 flex items-center justify-center ${isNonVeg ? 'bg-orange-500/10 text-orange-600' : 'bg-emerald-500/10 text-emerald-600'}">
          <svg class="w-6 h-6 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 4V2"/>
            <path d="M4 18h16"/>
            <path d="M4 18a8 8 0 0 1 16 0"/>
            <circle cx="12" cy="4" r="2"/>
          </svg>
        </div>
      `;

      // Stepper Control
      let actionControl = '';
      if (qty === 0) {
        actionControl = `
          <button onclick="addToCart('${dish.id}', event)" class="w-full py-1.5 sm:py-2 rounded-[var(--radius-md)] ${isNonVeg ? 'bg-orange-600 hover:bg-orange-700 text-white' : 'bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white'} font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-1.5 active:scale-95">
            ${VYORA_ICONS.plus}
            <span>Add</span>
          </button>
        `;
      } else {
        actionControl = `
          <div class="flex items-center justify-between bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-md)] p-0.5 shadow-xs">
            <button onclick="removeFromCart('${dish.id}')" class="w-7 h-7 rounded-[var(--radius-sm)] bg-[var(--bg-surface)] text-[var(--text-primary)] font-extrabold text-xs flex items-center justify-center hover:bg-red-500 hover:text-white transition active:scale-75 shadow-xs">
              ${VYORA_ICONS.minus}
            </button>
            <span class="font-extrabold text-xs ${isNonVeg ? 'text-orange-600' : 'text-[var(--accent)]'} font-mono">${qty}</span>
            <button onclick="addToCart('${dish.id}', event)" class="w-7 h-7 rounded-[var(--radius-sm)] ${isNonVeg ? 'bg-orange-600 text-white hover:bg-orange-700' : 'bg-[var(--accent)] text-white hover:bg-[var(--accent-hover)]'} font-extrabold text-xs flex items-center justify-center transition active:scale-75 shadow-xs">
              ${VYORA_ICONS.plus}
            </button>
          </div>
        `;
      }

      card.innerHTML = `
        <div class="overflow-hidden relative">
          <div class="flex items-center justify-between mb-1">
            ${emblemBadge}
            <span class="text-[9px] text-[var(--text-muted)] font-mono flex items-center">${VYORA_ICONS.clock} ${dish.preparationTime || 15}m</span>
          </div>
          ${imgHtml}
          <h4 class="font-serif font-bold text-xs sm:text-sm text-[var(--text-primary)] truncate mt-1">${dish.name}</h4>
          <p class="text-[10px] text-[var(--text-secondary)] line-clamp-2 mt-0.5 min-h-[26px]">${dish.description || 'Prepared fresh by chef.'}</p>
          <div class="flex items-center justify-between mt-1.5 pt-1 border-t border-[var(--border)]">
            <span class="font-mono font-bold text-xs sm:text-sm ${isNonVeg ? 'text-orange-700 dark:text-orange-300' : 'text-[var(--accent)]'}">₹${parseFloat(dish.price).toFixed(2)}</span>
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

  function addToCart(dishId, event) {
    const dish = allMenuItems.find(i => i.id === dishId);
    if (!dish) return;

    const existing = cart.get(dishId);
    if (existing) {
      existing.quantity++;
    } else {
      cart.set(dishId, { item: dish, quantity: 1 });
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
    if (document.getElementById('cart-modal').classList.contains('hidden') === false) {
      renderCartModalItems();
    }
  }

  function removeFromCart(dishId) {
    const existing = cart.get(dishId);
    if (!existing) return;
    if (existing.quantity > 1) {
      existing.quantity--;
    } else {
      cart.delete(dishId);
    }
    renderDishes();
    if (cart.size === 0) closeCartModal();
    else if (document.getElementById('cart-modal').classList.contains('hidden') === false) {
      renderCartModalItems();
    }
  }

  function updateCartBar() {
    const bar = document.getElementById('bottom-cart-bar');
    let count = 0;
    let total = 0;

    cart.forEach(c => {
      count += c.quantity;
      total += c.quantity * c.item.price;
    });

    if (count > 0) {
      bar.classList.remove('hidden');
      document.getElementById('cart-item-count-badge').textContent = count;
      document.getElementById('cart-item-count').textContent = `${count} ${count === 1 ? 'item' : 'items'} in order`;
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
            <button onclick="removeFromCart('${item.id}')" class="w-6 h-6 rounded text-xs font-bold hover:bg-red-500 hover:text-white transition active:scale-75 flex items-center justify-center">${VYORA_ICONS.minus}</button>
            <span class="w-5 text-center font-mono font-bold text-xs text-[var(--accent)]">${quantity}</span>
            <button onclick="addToCart('${item.id}')" class="w-6 h-6 rounded text-xs font-bold bg-[var(--accent)] text-white hover:opacity-90 transition active:scale-75 flex items-center justify-center">${VYORA_ICONS.plus}</button>
          </div>
          <span class="font-mono font-bold text-xs text-[var(--text-primary)] w-14 text-right">₹${(item.price * quantity).toFixed(2)}</span>
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
    btn.innerHTML = `<span class="inline-flex items-center gap-2">${VYORA_ICONS.spinner} Transmitting to Kitchen...</span>`;

    const items = [];
    cart.forEach(({ item, quantity }) => {
      items.push({ menuItemId: item.id, quantity });
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
        window.showToast('Order transmitted to kitchen!', 'order');
        cart.clear();
        closeCartModal();
        renderDishes();
        await loadLiveOrders();
        toggleTrackingSheet();
      } else {
        alert(data.error?.message || 'Failed to place order.');
      }
    } catch (err) {
      console.error('Order placement error:', err);
      alert('Network error. Please try again.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<span>Submit Order to Kitchen</span><i data-lucide="arrow-right" class="w-4 h-4"></i>';
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
        window.showToast(type === 'water' ? 'Water request dispatched!' : 'Waiter has been notified!', type);
        
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
      if (data.success && Array.isArray(data.data)) {
        activeLiveOrders = data.data.filter(o => o.status !== 'cancelled');
        const trackingBtn = document.getElementById('tracking-toggle-btn');
        const countSpan = document.getElementById('tracking-active-count');
        
        if (activeLiveOrders.length > 0) {
          trackingBtn.classList.remove('hidden');
          if (countSpan) countSpan.textContent = activeLiveOrders.length;
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
    container.innerHTML = '';

    if (activeLiveOrders.length === 0) {
      container.innerHTML = `
        <div class="p-8 text-center text-xs text-[var(--text-muted)] space-y-1">
          <p class="font-bold text-sm text-[var(--text-primary)]">No active orders right now</p>
          <p>Dishes placed in this session will appear here with live kitchen updates.</p>
        </div>
      `;
      return;
    }

    const steps = ['pending', 'preparing', 'ready', 'completed'];

    activeLiveOrders.forEach((order, idx) => {
      const card = document.createElement('div');
      card.className = 'p-4 rounded-[var(--radius-xl)] bg-[var(--bg-base)] border border-[var(--border)] shadow-xs space-y-3';

      const currentStepIdx = steps.indexOf(order.status);
      const itemsList = (order.items || []).map(i => `<span class="inline-block font-semibold">${i.quantity}x ${i.menuItemName}</span>`).join(', ');

      let statusMsg = 'Kitchen received your ticket';
      let statusIcon = VYORA_ICONS.stepTicket;
      let statusColor = 'text-orange-600 dark:text-orange-400 bg-orange-500/10 border-orange-500/20';
      if (order.status === 'preparing') {
        statusMsg = 'Chef is preparing your meal';
        statusIcon = VYORA_ICONS.stepFlame;
        statusColor = 'text-amber-600 dark:text-amber-400 bg-amber-500/10 border-amber-500/20';
      } else if (order.status === 'ready') {
        statusMsg = 'Plated & Ready to serve!';
        statusIcon = VYORA_ICONS.stepPlate;
        statusColor = 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border-emerald-500/20';
      } else if (order.status === 'completed') {
        statusMsg = 'Served at your table';
        statusIcon = VYORA_ICONS.stepCheck;
        statusColor = 'text-slate-600 dark:text-slate-400 bg-slate-500/10 border-slate-500/20';
      }

      card.innerHTML = `
        <div class="flex justify-between items-start pb-2 border-b border-[var(--border)]">
          <div>
            <div class="flex items-center gap-1.5">
              <span class="w-5 h-5 rounded-full bg-[var(--accent)] text-white text-[10px] font-bold flex items-center justify-center font-mono">#${idx + 1}</span>
              <span class="font-bold text-xs text-[var(--text-primary)]">Order Ticket</span>
            </div>
            <p class="text-[11px] text-[var(--text-secondary)] mt-1">${itemsList}</p>
          </div>
          <span class="font-mono font-bold text-xs text-[var(--accent)] shrink-0">₹${parseFloat(order.totalAmount).toFixed(2)}</span>
        </div>

        <!-- Animated Progress Stepper -->
        <div class="py-2">
          <div class="flex items-center justify-between relative mb-2.5 px-2">
            <!-- Background progress track -->
            <div class="absolute top-1/2 left-4 right-4 h-1 bg-[var(--border)] -translate-y-1/2 z-0">
              <div class="h-full bg-[var(--accent)] transition-all duration-500" style="width: ${Math.min(100, Math.max(0, currentStepIdx / 3 * 100))}%"></div>
            </div>

            <!-- Step 1: Pending -->
            <div class="relative z-10 flex flex-col items-center">
              <div class="w-7 h-7 rounded-full ${currentStepIdx >= 0 ? 'bg-[var(--accent)] text-white ring-2 ring-[var(--accent-glow)]' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'} ${currentStepIdx === 0 ? 'animate-pulse-ring' : ''} flex items-center justify-center font-bold text-xs transition-all shadow-xs">
                ${currentStepIdx > 0 ? VYORA_ICONS.stepCheck : VYORA_ICONS.stepTicket}
              </div>
              <span class="text-[9px] mt-1 font-semibold text-[var(--text-muted)]">Received</span>
            </div>

            <!-- Step 2: Preparing -->
            <div class="relative z-10 flex flex-col items-center">
              <div class="w-7 h-7 rounded-full ${currentStepIdx >= 1 ? 'bg-[var(--accent)] text-white ring-2 ring-[var(--accent-glow)]' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'} ${currentStepIdx === 1 ? 'animate-pulse-ring' : ''} flex items-center justify-center font-bold text-xs transition-all shadow-xs">
                ${currentStepIdx > 1 ? VYORA_ICONS.stepCheck : VYORA_ICONS.stepFlame}
              </div>
              <span class="text-[9px] mt-1 font-semibold text-[var(--text-muted)]">Cooking</span>
            </div>

            <!-- Step 3: Ready -->
            <div class="relative z-10 flex flex-col items-center">
              <div class="w-7 h-7 rounded-full ${currentStepIdx >= 2 ? 'bg-[var(--accent)] text-white ring-2 ring-[var(--accent-glow)]' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'} ${currentStepIdx === 2 ? 'animate-pulse-ring' : ''} flex items-center justify-center font-bold text-xs transition-all shadow-xs">
                ${currentStepIdx > 2 ? VYORA_ICONS.stepCheck : VYORA_ICONS.stepPlate}
              </div>
              <span class="text-[9px] mt-1 font-semibold text-[var(--text-muted)]">Plated</span>
            </div>

            <!-- Step 4: Completed -->
            <div class="relative z-10 flex flex-col items-center">
              <div class="w-7 h-7 rounded-full ${currentStepIdx >= 3 ? 'bg-emerald-600 text-white ring-2 ring-emerald-500/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'} flex items-center justify-center font-bold text-xs transition-all shadow-xs">
                ${VYORA_ICONS.stepCheck}
              </div>
              <span class="text-[9px] mt-1 font-semibold text-[var(--text-muted)]">Served</span>
            </div>
          </div>

          <div class="text-center pt-1.5 flex justify-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border text-xs font-bold ${statusColor} shadow-xs">
              ${statusIcon}
              <span>${statusMsg}</span>
            </span>
          </div>
        </div>
      `;
      container.appendChild(card);
    });

    if (window.lucide) lucide.createIcons();
  }
</script>
@endsection
