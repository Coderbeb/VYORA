@extends('layouts.dashboard')

@section('title', 'Live Orders Board - Vyora')
@section('page_title', 'Real-time Orders Board')

@push('styles')
<style>
  /* Premium Micro-Animations & Scroll Architecture */
  @keyframes livePulse {
    0% { transform: scale(0.95); opacity: 0.8; }
    50% { transform: scale(1.08); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.8; }
  }
  .animate-live-beacon {
    animation: livePulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }
  @keyframes urgentPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
    50% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
  }
  .animate-urgent-ring {
    animation: urgentPulse 1.8s infinite;
  }
  /* Independent lane scrollbar */
  .lane-scroll::-webkit-scrollbar {
    width: 5px;
  }
  .lane-scroll::-webkit-scrollbar-track {
    background: transparent;
  }
  .lane-scroll::-webkit-scrollbar-thumb {
    background: rgba(3, 77, 55, 0.15);
    border-radius: 9999px;
  }
  [data-theme="dark"] .lane-scroll::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
  }
</style>
@endpush

@section('dashboard_content')
<div class="space-y-6">

  <!-- 1. TOP OPERATIONAL HUD: METRICS & CONTROLS -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <!-- Metric 1: Pending (Needs Kitchen Action) -->
    <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border-2 border-orange-500/30 shadow-xs flex flex-col justify-between relative overflow-hidden transition hover:shadow-md">
      <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-orange-500/10 pointer-events-none"></div>
      <div class="flex items-center justify-between pb-2 border-b border-[var(--border)]">
        <span class="text-xs font-bold uppercase tracking-wider text-orange-600 flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
          <span>Pending Attention</span>
        </span>
        <i data-lucide="bell-ring" class="w-4 h-4 text-orange-500"></i>
      </div>
      <div class="pt-2 flex items-baseline justify-between">
        <h3 id="stat-pending-count" class="font-sans font-black text-2xl sm:text-3xl text-slate-900">0</h3>
        <span id="stat-pending-sum" class="font-mono text-xs font-bold text-orange-600">₹0.00</span>
      </div>
      <p class="text-xs text-slate-600 font-medium mt-1">Awaiting kitchen acceptance</p>
    </div>

    <!-- Metric 2: Cooking (In the Fire) -->
    <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col justify-between relative overflow-hidden transition hover:shadow-md">
      <div class="flex items-center justify-between pb-2 border-b border-[var(--border)]">
        <span class="text-xs font-bold uppercase tracking-wider text-amber-600 flex items-center gap-1.5">
          <i data-lucide="flame" class="w-3.5 h-3.5 text-amber-500"></i>
          <span>In the Fire</span>
        </span>
        <span class="text-xs font-bold text-amber-700 bg-amber-500/10 px-2 py-0.5 rounded">Cooking</span>
      </div>
      <div class="pt-2 flex items-baseline justify-between">
        <h3 id="stat-cooking-count" class="font-sans font-black text-2xl sm:text-3xl text-slate-900">0</h3>
        <span id="stat-cooking-sum" class="font-mono text-xs font-bold text-amber-600">₹0.00</span>
      </div>
      <p class="text-xs text-slate-600 font-medium mt-1">Active chef preparation</p>
    </div>

    <!-- Metric 3: Ready (Plated for Pass) -->
    <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col justify-between relative overflow-hidden transition hover:shadow-md">
      <div class="flex items-center justify-between pb-2 border-b border-[var(--border)]">
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 flex items-center gap-1.5">
          <i data-lucide="utensils" class="w-3.5 h-3.5 text-emerald-500"></i>
          <span>Ready to Serve</span>
        </span>
        <span class="text-xs font-bold text-emerald-700 bg-emerald-500/10 px-2 py-0.5 rounded">Pass Ready</span>
      </div>
      <div class="pt-2 flex items-baseline justify-between">
        <h3 id="stat-ready-count" class="font-sans font-black text-2xl sm:text-3xl text-slate-900">0</h3>
        <span id="stat-ready-sum" class="font-mono text-xs font-bold text-emerald-600">₹0.00</span>
      </div>
      <p class="text-xs text-slate-600 font-medium mt-1">Waiting table / room delivery</p>
    </div>

    <!-- Metric 4: Fulfilled Today -->
    <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col justify-between relative overflow-hidden transition hover:shadow-md">
      <div class="flex items-center justify-between pb-2 border-b border-[var(--border)]">
        <span class="text-xs font-bold uppercase tracking-wider text-[var(--accent)] flex items-center gap-1.5">
          <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[var(--accent)]"></i>
          <span>Fulfilled Today</span>
        </span>
        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">Settled</span>
      </div>
      <div class="pt-2 flex items-baseline justify-between">
        <h3 id="stat-completed-count" class="font-sans font-black text-2xl sm:text-3xl text-slate-900">0</h3>
        <span id="stat-completed-sum" class="font-mono text-xs font-bold text-[var(--accent)]">₹0.00</span>
      </div>
      <p class="text-xs text-slate-600 font-medium mt-1">Completed orders</p>
    </div>
  </div>

  <!-- 2. CONTROL TOOLBAR & FILTER CONSOLE -->
  <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
    <!-- Left: Search Box & Clear -->
    <div class="relative flex-1 max-w-md">
      <input type="text" id="order-search" oninput="filterOrders()" placeholder="Filter by table, room, hotel, item, note..." class="w-full pl-9 pr-9 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)] transition shadow-xs">
      <i data-lucide="search" class="w-4 h-4 text-[var(--text-muted)] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
      <button id="search-clear-btn" onclick="clearSearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-3.5 h-3.5"></i>
      </button>
    </div>

    <!-- Center: Origin Filter Pills -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
      <button onclick="setOriginFilter('all')" id="filter-origin-all" class="px-3 py-1.5 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-xs shrink-0">
        All Orders
      </button>
      <button onclick="setOriginFilter('dine_in')" id="filter-origin-dine_in" class="px-3 py-1.5 rounded-full text-xs font-semibold border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] transition flex items-center gap-1 shrink-0">
        <i data-lucide="utensils" class="w-3 h-3"></i>
        <span>Tables</span>
      </button>
      <button onclick="setOriginFilter('room_service')" id="filter-origin-room_service" class="px-3 py-1.5 rounded-full text-xs font-semibold border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] transition flex items-center gap-1 shrink-0">
        <i data-lucide="hotel" class="w-3 h-3 text-purple-500"></i>
        <span>Room Service</span>
      </button>
      <button onclick="setOriginFilter('urgent')" id="filter-origin-urgent" class="px-3 py-1.5 rounded-full text-xs font-semibold border border-[var(--border)] text-red-600 hover:bg-red-500/10 transition flex items-center gap-1 shrink-0">
        <i data-lucide="clock" class="w-3 h-3 text-red-500"></i>
        <span>Urgent (>10m)</span>
      </button>
    </div>

    <!-- Right: Quick Actions & Live Indicator -->
    <div class="flex items-center gap-2 justify-end shrink-0">
      <!-- Sound Chime Toggle -->
      <button onclick="toggleAudioAlerts()" id="audio-toggle-btn" class="px-3 py-1.5 rounded-[var(--radius-md)] border border-[var(--border)] bg-[var(--bg-base)] hover:bg-[var(--bg-hover)] text-xs font-semibold text-[var(--text-secondary)] transition flex items-center gap-1.5 shadow-xs active:scale-95" title="Toggle audio chime on new orders">
        <i data-lucide="volume-2" id="audio-icon" class="w-3.5 h-3.5 text-emerald-600"></i>
        <span id="audio-text" class="hidden sm:inline">Chime Active</span>
      </button>

      <!-- Manual Refresh Button -->
      <button onclick="loadOrders()" class="p-2 rounded-[var(--radius-md)] border border-[var(--border)] bg-[var(--bg-base)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition active:scale-90 shadow-xs" title="Force live refresh">
        <i data-lucide="refresh-cw" id="refresh-icon" class="w-4 h-4"></i>
      </button>

      <!-- Live Broadcast Badge -->
      <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-emerald-500/10 text-emerald-600 font-bold text-[11px] border border-emerald-500/20 shadow-xs">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-live-beacon"></span>
        <span>Auto-Sync 5s</span>
      </div>
    </div>
  </div>

  <!-- Mobile Stage Switcher Tabs (For smaller screens) -->
  <div class="flex lg:hidden items-center gap-1 p-1 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] overflow-x-auto text-xs font-bold scrollbar-none">
    <button onclick="switchMobileStage('all')" id="m-stage-all" class="flex-1 min-w-[70px] py-1.5 rounded-[var(--radius-md)] bg-[var(--accent)] text-white text-center">All</button>
    <button onclick="switchMobileStage('pending')" id="m-stage-pending" class="flex-1 min-w-[85px] py-1.5 rounded-[var(--radius-md)] text-[var(--text-secondary)] text-center">Pending (<span id="m-cnt-pending">0</span>)</button>
    <button onclick="switchMobileStage('preparing')" id="m-stage-preparing" class="flex-1 min-w-[85px] py-1.5 rounded-[var(--radius-md)] text-[var(--text-secondary)] text-center">Cooking (<span id="m-cnt-preparing">0</span>)</button>
    <button onclick="switchMobileStage('ready')" id="m-stage-ready" class="flex-1 min-w-[80px] py-1.5 rounded-[var(--radius-md)] text-[var(--text-secondary)] text-center">Ready (<span id="m-cnt-ready">0</span>)</button>
    <button onclick="switchMobileStage('completed')" id="m-stage-completed" class="flex-1 min-w-[85px] py-1.5 rounded-[var(--radius-md)] text-[var(--text-secondary)] text-center">Served (<span id="m-cnt-completed">0</span>)</button>
  </div>

  <!-- 3. KANBAN 4-LANE BOARD -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 xl:gap-5 items-start">
    
    <!-- LANE 1: PENDING (NEW TICKETS) -->
    <div id="lane-container-pending" class="rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border-2 border-orange-500/30 shadow-xs flex flex-col overflow-hidden">
      <!-- Sticky Lane Header -->
      <div class="sticky top-0 z-20 px-4 py-3 bg-[var(--bg-surface)]/95 backdrop-blur-md border-b border-orange-500/20 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-ping"></span>
          <h3 class="font-bold text-xs uppercase tracking-wider text-orange-600">1. Pending</h3>
        </div>
        <div class="flex items-center gap-1.5">
          <span id="count-pending" class="px-2 py-0.5 rounded-full bg-orange-500/15 text-orange-600 font-bold font-mono text-xs">0</span>
          <span id="sum-pending" class="text-[10px] font-mono text-[var(--text-muted)] hidden xl:inline">₹0</span>
        </div>
      </div>
      <!-- Scrollable Cards Column -->
      <div id="col-pending" class="p-3.5 space-y-3.5 min-h-[350px] max-h-[calc(100vh-290px)] overflow-y-auto lane-scroll">
        <div class="p-10 text-center text-xs text-[var(--text-muted)] flex flex-col items-center justify-center gap-2">
          <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500/40"></i>
          <span class="font-semibold">All Pending Orders Accepted</span>
          <span class="text-[10px]">New incoming orders will chime here live.</span>
        </div>
      </div>
    </div>

    <!-- LANE 2: PREPARING (IN KITCHEN) -->
    <div id="lane-container-preparing" class="rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col overflow-hidden">
      <!-- Sticky Lane Header -->
      <div class="sticky top-0 z-20 px-4 py-3 bg-[var(--bg-surface)]/95 backdrop-blur-md border-b border-[var(--border)] flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
          <h3 class="font-bold text-xs uppercase tracking-wider text-amber-600">2. Preparing</h3>
        </div>
        <div class="flex items-center gap-1.5">
          <span id="count-preparing" class="px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-600 font-bold font-mono text-xs">0</span>
          <span id="sum-preparing" class="text-[10px] font-mono text-[var(--text-muted)] hidden xl:inline">₹0</span>
        </div>
      </div>
      <!-- Scrollable Cards Column -->
      <div id="col-preparing" class="p-3.5 space-y-3.5 min-h-[350px] max-h-[calc(100vh-290px)] overflow-y-auto lane-scroll">
        <div class="p-10 text-center text-xs text-[var(--text-muted)] flex flex-col items-center justify-center gap-2">
          <i data-lucide="chef-hat" class="w-8 h-8 text-amber-500/40"></i>
          <span class="font-semibold">Kitchen Station Clear</span>
          <span class="text-[10px]">Accepted tickets move here for prep.</span>
        </div>
      </div>
    </div>

    <!-- LANE 3: READY TO SERVE (PLATED) -->
    <div id="lane-container-ready" class="rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col overflow-hidden">
      <!-- Sticky Lane Header -->
      <div class="sticky top-0 z-20 px-4 py-3 bg-[var(--bg-surface)]/95 backdrop-blur-md border-b border-[var(--border)] flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
          <h3 class="font-bold text-xs uppercase tracking-wider text-emerald-600">3. Ready</h3>
        </div>
        <div class="flex items-center gap-1.5">
          <span id="count-ready" class="px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-600 font-bold font-mono text-xs">0</span>
          <span id="sum-ready" class="text-[10px] font-mono text-[var(--text-muted)] hidden xl:inline">₹0</span>
        </div>
      </div>
      <!-- Scrollable Cards Column -->
      <div id="col-ready" class="p-3.5 space-y-3.5 min-h-[350px] max-h-[calc(100vh-290px)] overflow-y-auto lane-scroll">
        <div class="p-10 text-center text-xs text-[var(--text-muted)] flex flex-col items-center justify-center gap-2">
          <i data-lucide="bell" class="w-8 h-8 text-emerald-500/40"></i>
          <span class="font-semibold">No Plated Orders Waiting</span>
          <span class="text-[10px]">Dishes ready for pickup will appear here.</span>
        </div>
      </div>
    </div>

    <!-- LANE 4: COMPLETED (SERVED / CLOSED) -->
    <div id="lane-container-completed" class="rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col overflow-hidden">
      <!-- Sticky Lane Header -->
      <div class="sticky top-0 z-20 px-4 py-3 bg-[var(--bg-surface)]/95 backdrop-blur-md border-b border-[var(--border)] flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span>
          <h3 class="font-bold text-xs uppercase tracking-wider text-slate-600">4. Served</h3>
        </div>
        <div class="flex items-center gap-1.5">
          <span id="count-completed" class="px-2 py-0.5 rounded-full bg-slate-500/15 text-slate-600 font-bold font-mono text-xs">0</span>
          <span id="sum-completed" class="text-[10px] font-mono text-[var(--text-muted)] hidden xl:inline">₹0</span>
        </div>
      </div>
      <!-- Scrollable Cards Column -->
      <div id="col-completed" class="p-3.5 space-y-3.5 min-h-[350px] max-h-[calc(100vh-290px)] overflow-y-auto lane-scroll">
        <div class="p-10 text-center text-xs text-[var(--text-muted)] flex flex-col items-center justify-center gap-2">
          <i data-lucide="receipt" class="w-8 h-8 text-slate-400/40"></i>
          <span class="font-semibold">No Fulfilled Orders Yet</span>
          <span class="text-[10px]">Completed orders will be archived here.</span>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- CANCEL ORDER REASON MODAL -->
<div id="cancel-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-2xl w-full max-w-md p-6 animate-in zoom-in-95 duration-200">
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
      <div class="flex items-center gap-2 text-red-600">
        <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
          <i data-lucide="alert-triangle" class="w-4 h-4"></i>
        </div>
        <h3 class="font-serif font-bold text-base text-[var(--text-primary)]">Cancel Meal Ticket</h3>
      </div>
      <button onclick="closeCancelModal()" class="p-1.5 rounded-full text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <p class="text-xs text-[var(--text-secondary)] mb-3">Please specify why this order is being cancelled. This will notify the guest session and cancel kitchen billing.</p>

    <!-- Quick Preset Reasons -->
    <div class="mb-3 space-y-1.5">
      <label class="block text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Quick Reason</label>
      <div class="flex flex-wrap gap-1.5 text-xs">
        <button type="button" onclick="setCancelReason('Dish item 86\'d (Out of stock)')" class="px-2.5 py-1 rounded-full border border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-red-500 hover:text-red-600 text-[11px] transition">Item Out of Stock</button>
        <button type="button" onclick="setCancelReason('Guest requested cancellation')" class="px-2.5 py-1 rounded-full border border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-red-500 hover:text-red-600 text-[11px] transition">Guest Cancelled</button>
        <button type="button" onclick="setCancelReason('Duplicate order placed')" class="px-2.5 py-1 rounded-full border border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-red-500 hover:text-red-600 text-[11px] transition">Duplicate Order</button>
        <button type="button" onclick="setCancelReason('Kitchen closing / Over capacity')" class="px-2.5 py-1 rounded-full border border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-red-500 hover:text-red-600 text-[11px] transition">Kitchen Closing</button>
      </div>
    </div>

    <textarea id="cancel-reason-input" rows="2" placeholder="Provide additional details..." class="w-full px-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-md)] focus:outline-none focus:border-red-500 text-[var(--text-primary)] mb-4"></textarea>

    <div class="flex justify-end gap-2.5 pt-2 border-t border-[var(--border)]">
      <button onclick="closeCancelModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-md)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition">Back</button>
      <button onclick="confirmCancelOrder()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-md)] bg-red-600 hover:bg-red-700 text-white shadow-sm transition active:scale-95">Confirm Cancellation</button>
    </div>
  </div>
</div>

<script>
  // Bespoke Vector Dietary Emblems
  const VYORA_DIET = {
    vegDot: `<svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#059669" stroke-width="1.8" fill="#ECFDF5"/><circle cx="8" cy="8" r="3.5" fill="#059669"/></svg>`,
    nonVegDot: `<svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3.5" stroke="#DC2626" stroke-width="1.8" fill="#FEF2F2"/><polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/></svg>`
  };

  let allOrders = [];
  let pendingCancelOrderId = null;
  let activeOriginFilter = 'all';
  let activeMobileStage = 'all';
  let audioAlertsActive = true;
  let pollTimer = null;

  document.addEventListener('DOMContentLoaded', () => {
    loadOrders();
    pollTimer = setInterval(loadOrders, 5000);

    window.addEventListener('qrestro:order:new', (e) => {
      loadOrders();
      if (audioAlertsActive && window.playNotificationSound) {
        window.playNotificationSound();
      }
    });

    window.addEventListener('qrestro:order:updated', () => loadOrders());
  });

  function toggleAudioAlerts() {
    audioAlertsActive = !audioAlertsActive;
    const icon = document.getElementById('audio-icon');
    const text = document.getElementById('audio-text');
    if (audioAlertsActive) {
      icon.className = 'w-3.5 h-3.5 text-emerald-600';
      text.textContent = 'Chime Active';
      window.playNotificationSound();
      window.showToast('Audio chime alerts enabled!', 'bell');
    } else {
      icon.className = 'w-3.5 h-3.5 text-slate-400';
      text.textContent = 'Chime Muted';
      window.showToast('Audio chime muted.', 'info');
    }
  }

  function formatTimeAgo(isoString) {
    if (!isoString) return { text: 'just now', minutes: 0 };
    const diffSec = Math.max(0, Math.floor((Date.now() - new Date(isoString).getTime()) / 1000));
    const mins = Math.floor(diffSec / 60);
    if (mins < 1) return { text: 'just now', minutes: 0 };
    if (mins < 60) return { text: `${mins}m ago`, minutes: mins };
    const hours = Math.floor(mins / 60);
    if (hours < 24) return { text: `${hours}h ago`, minutes: mins };
    return { text: `${Math.floor(hours / 24)}d ago`, minutes: mins };
  }

  async function loadOrders() {
    const icon = document.getElementById('refresh-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/orders?limit=100', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && Array.isArray(data.data)) {
        allOrders = data.data;
        updateHUDMetrics(allOrders);
        filterOrders();
      }
    } catch(e) {
      console.error('Error loading orders:', e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 350);
    }
  }

  function updateHUDMetrics(orders) {
    let pCount = 0, pSum = 0;
    let cCount = 0, cSum = 0;
    let rCount = 0, rSum = 0;
    let dCount = 0, dSum = 0;

    orders.forEach(o => {
      const amt = parseFloat(o.totalAmount) || 0;
      if (o.status === 'pending') { pCount++; pSum += amt; }
      else if (o.status === 'preparing') { cCount++; cSum += amt; }
      else if (o.status === 'ready') { rCount++; rSum += amt; }
      else if (o.status === 'completed') { dCount++; dSum += amt; }
    });

    document.getElementById('stat-pending-count').textContent = pCount;
    document.getElementById('stat-pending-sum').textContent = `₹${pSum.toFixed(2)}`;
    document.getElementById('stat-cooking-count').textContent = cCount;
    document.getElementById('stat-cooking-sum').textContent = `₹${cSum.toFixed(2)}`;
    document.getElementById('stat-ready-count').textContent = rCount;
    document.getElementById('stat-ready-sum').textContent = `₹${rSum.toFixed(2)}`;
    document.getElementById('stat-completed-count').textContent = dCount;
    document.getElementById('stat-completed-sum').textContent = `₹${dSum.toFixed(2)}`;

    // Mobile badge counters
    const mP = document.getElementById('m-cnt-pending'); if (mP) mP.textContent = pCount;
    const mC = document.getElementById('m-cnt-preparing'); if (mC) mC.textContent = cCount;
    const mR = document.getElementById('m-cnt-ready'); if (mR) mR.textContent = rCount;
    const mD = document.getElementById('m-cnt-completed'); if (mD) mD.textContent = dCount;

    // Header sum labels
    const sP = document.getElementById('sum-pending'); if (sP) sP.textContent = `₹${pSum.toFixed(0)}`;
    const sC = document.getElementById('sum-preparing'); if (sC) sC.textContent = `₹${cSum.toFixed(0)}`;
    const sR = document.getElementById('sum-ready'); if (sR) sR.textContent = `₹${rSum.toFixed(0)}`;
    const sD = document.getElementById('sum-completed'); if (sD) sD.textContent = `₹${dSum.toFixed(0)}`;
  }

  function setOriginFilter(filter) {
    activeOriginFilter = filter;
    ['all', 'dine_in', 'room_service', 'urgent'].forEach(f => {
      const btn = document.getElementById(`filter-origin-${f}`);
      if (!btn) return;
      if (f === filter) {
        btn.className = 'px-3 py-1.5 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-xs shrink-0 flex items-center gap-1';
      } else {
        const textCol = f === 'urgent' ? 'text-red-600' : 'text-[var(--text-secondary)]';
        btn.className = `px-3 py-1.5 rounded-full text-xs font-semibold border border-[var(--border)] ${textCol} hover:bg-[var(--bg-hover)] transition flex items-center gap-1 shrink-0`;
      }
    });
    filterOrders();
  }

  function switchMobileStage(stage) {
    activeMobileStage = stage;
    ['all', 'pending', 'preparing', 'ready', 'completed'].forEach(s => {
      const btn = document.getElementById(`m-stage-${s}`);
      if (!btn) return;
      if (s === stage) {
        btn.className = 'flex-1 min-w-[70px] py-1.5 rounded-[var(--radius-md)] bg-[var(--accent)] text-white text-center';
      } else {
        btn.className = 'flex-1 min-w-[70px] py-1.5 rounded-[var(--radius-md)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] text-center';
      }
    });

    ['pending', 'preparing', 'ready', 'completed'].forEach(lane => {
      const container = document.getElementById(`lane-container-${lane}`);
      if (!container) return;
      if (stage === 'all' || stage === lane) {
        container.classList.remove('hidden');
      } else {
        container.classList.add('hidden');
      }
    });
  }

  function filterOrders() {
    const searchEl = document.getElementById('order-search');
    const q = searchEl ? searchEl.value.toLowerCase().trim() : '';
    const clearBtn = document.getElementById('search-clear-btn');
    if (clearBtn) {
      if (q) clearBtn.classList.remove('hidden');
      else clearBtn.classList.add('hidden');
    }

    let filtered = allOrders;

    // Origin / Type Filter
    if (activeOriginFilter === 'dine_in') {
      filtered = filtered.filter(o => o.orderType !== 'room_service');
    } else if (activeOriginFilter === 'room_service') {
      filtered = filtered.filter(o => o.orderType === 'room_service');
    } else if (activeOriginFilter === 'urgent') {
      filtered = filtered.filter(o => {
        if (o.status === 'completed' || o.status === 'cancelled') return false;
        const timeInfo = formatTimeAgo(o.createdAt);
        return timeInfo.minutes >= 10;
      });
    }

    // Search Query Filter
    if (q) {
      filtered = filtered.filter(o => {
        const matchTable = `table #${o.tableNumber}`.toLowerCase().includes(q) || String(o.tableNumber).includes(q);
        const matchRoom = o.roomNumber ? (`room #${o.roomNumber}`.toLowerCase().includes(q) || String(o.roomNumber).includes(q)) : false;
        const matchHotel = o.hotelName ? o.hotelName.toLowerCase().includes(q) : false;
        const matchNotes = o.notes ? o.notes.toLowerCase().includes(q) : false;
        const matchItems = (o.items || []).some(i => (i.menuItemName || '').toLowerCase().includes(q));
        return matchTable || matchRoom || matchHotel || matchNotes || matchItems;
      });
    }

    renderKanban(filtered);
  }

  function clearSearch() {
    const searchInput = document.getElementById('order-search');
    searchInput.value = '';
    const clearBtn = document.getElementById('search-clear-btn');
    if (clearBtn) clearBtn.classList.add('hidden');
    filterOrders();
  }

  function renderKanban(orders) {
    const cols = {
      pending: document.getElementById('col-pending'),
      preparing: document.getElementById('col-preparing'),
      ready: document.getElementById('col-ready'),
      completed: document.getElementById('col-completed'),
    };

    const counts = { pending: 0, preparing: 0, ready: 0, completed: 0 };
    Object.values(cols).forEach(col => { if (col) col.innerHTML = ''; });

    orders.forEach((order, idx) => {
      if (order.status === 'cancelled') return;
      if (counts[order.status] !== undefined) counts[order.status]++;

      const col = cols[order.status];
      if (!col) return;

      const card = createOrderCard(order, idx + 1);
      col.appendChild(card);
    });

    // Update count labels and empty states
    Object.keys(counts).forEach(key => {
      const badge = document.getElementById(`count-${key}`);
      if (badge) badge.textContent = counts[key];
      if (counts[key] === 0 && cols[key]) {
        let emptyIcon = 'check-circle';
        let emptyTitle = 'No tickets';
        let emptySubtitle = 'All tickets processed';
        if (key === 'pending') {
          emptyIcon = 'check-circle';
          emptyTitle = 'All Pending Tickets Accepted';
          emptySubtitle = 'New customer orders will chime here live.';
        } else if (key === 'preparing') {
          emptyIcon = 'chef-hat';
          emptyTitle = 'Kitchen Station Clear';
          emptySubtitle = 'Accepted tickets move here for prep.';
        } else if (key === 'ready') {
          emptyIcon = 'bell';
          emptyTitle = 'No Plated Orders Waiting';
          emptySubtitle = 'Dishes ready for delivery will appear here.';
        } else if (key === 'completed') {
          emptyIcon = 'receipt';
          emptyTitle = 'No Fulfilled Orders';
          emptySubtitle = 'Completed orders will be logged here.';
        }

        cols[key].innerHTML = `
          <div class="p-10 text-center text-xs text-[var(--text-muted)] flex flex-col items-center justify-center gap-2">
            <i data-lucide="${emptyIcon}" class="w-8 h-8 opacity-40"></i>
            <span class="font-bold text-[var(--text-primary)]">${emptyTitle}</span>
            <span class="text-[10px]">${emptySubtitle}</span>
          </div>
        `;
      }
    });

    if (window.lucide) lucide.createIcons();
  }

  function createOrderCard(order, seqIndex) {
    const card = document.createElement('div');
    const isPending = order.status === 'pending';
    const isRoomService = order.orderType === 'room_service';
    const timeInfo = formatTimeAgo(order.createdAt);
    const isUrgent = isPending && timeInfo.minutes >= 10;

    // Card Surface Styling
    let cardBorder = 'border-[var(--border)]';
    if (isUrgent) {
      cardBorder = 'border-red-500 animate-urgent-ring';
    } else if (isPending) {
      cardBorder = 'border-orange-500/40 hover:border-orange-500';
    } else if (order.status === 'preparing') {
      cardBorder = 'border-amber-500/30 hover:border-amber-500';
    } else if (order.status === 'ready') {
      cardBorder = 'border-emerald-500/30 hover:border-emerald-500';
    }

    card.className = `p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border-2 ${cardBorder} shadow-xs hover:shadow-md transition-all duration-200 space-y-3 relative overflow-hidden animate-in fade-in duration-200`;

    // Time Urgency Pill
    let timePillClass = 'bg-gray-500/10 text-slate-700';
    let timeIcon = '<i data-lucide="clock" class="w-3 h-3"></i>';
    if (timeInfo.minutes >= 15) {
      timePillClass = 'bg-red-500/15 text-red-600 font-extrabold animate-pulse';
      timeIcon = '<i data-lucide="alert-circle" class="w-3 h-3 text-red-600"></i>';
    } else if (timeInfo.minutes >= 8) {
      timePillClass = 'bg-amber-500/15 text-amber-700 font-bold';
      timeIcon = '<i data-lucide="clock" class="w-3 h-3 text-amber-600"></i>';
    } else {
      timePillClass = 'bg-emerald-500/10 text-emerald-800 font-semibold';
      timeIcon = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>';
    }

    // Location Header
    const locationBadge = isRoomService
      ? `<div class="flex items-center gap-2.5">
           <span class="w-9 h-9 rounded-xl bg-purple-500/15 text-purple-700 font-bold text-sm flex items-center justify-center shrink-0">
             <i data-lucide="hotel" class="w-4 h-4"></i>
           </span>
           <div class="min-w-0">
             <div class="flex items-center gap-1.5">
               <h4 class="font-extrabold text-sm sm:text-base text-slate-900">Room #${order.roomNumber}</h4>
               <span class="text-[11px] px-2 py-0.5 rounded-full bg-purple-500/15 text-purple-700 font-bold uppercase tracking-wide">Folio</span>
             </div>
             <p class="text-xs text-purple-700 font-semibold truncate max-w-[140px]">${order.hotelName || 'Partner Hotel'}</p>
           </div>
         </div>`
      : `<div class="flex items-center gap-2.5">
           <span class="w-9 h-9 rounded-xl bg-[var(--accent-glow)] text-[var(--accent)] font-sans font-black text-sm flex items-center justify-center shrink-0 shadow-xs border border-[var(--border)]">
             #${order.tableNumber}
           </span>
           <div class="min-w-0">
             <div class="flex items-center gap-1.5">
               <h4 class="font-extrabold text-sm sm:text-base text-slate-900">Table #${order.tableNumber}</h4>
               <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-800 font-bold uppercase tracking-wide">Dine-in</span>
             </div>
             <p class="text-xs text-slate-600 font-medium">Dining Floor</p>
           </div>
         </div>`;

    // Line Items HTML
    const itemsHtml = (order.items || []).map(i => {
      const isVeg = i.isVeg !== false && i.is_veg !== false && i.isVeg !== 0 && i.is_veg !== 0;
      const dietDot = isVeg ? VYORA_DIET.vegDot : VYORA_DIET.nonVegDot;

      return `
        <div class="flex items-center justify-between py-1.5 border-b border-[var(--border)] last:border-0 text-xs sm:text-sm gap-2">
          <div class="flex items-center gap-2 min-w-0">
            <span class="w-5 h-5 rounded-md bg-[var(--bg-base)] border border-slate-300 font-mono font-bold text-xs text-slate-900 flex items-center justify-center shrink-0">
              ${i.quantity}x
            </span>
            ${dietDot}
            <span class="font-bold text-slate-900 truncate text-xs sm:text-sm">${i.menuItemName}</span>
          </div>
          <span class="font-mono font-bold text-slate-800 text-xs sm:text-sm shrink-0">₹${(i.price * i.quantity).toFixed(2)}</span>
        </div>
      `;
    }).join('');

    // Chef Note Callout
    const notesHtml = order.notes ? `
      <div class="p-3 rounded-[var(--radius-md)] bg-amber-500/15 border-l-4 border-amber-600 border border-amber-500/30 text-xs text-amber-950 flex items-start gap-2 shadow-xs">
        <i data-lucide="message-square" class="w-4 h-4 text-amber-700 shrink-0 mt-0.5"></i>
        <div class="min-w-0">
          <span class="font-extrabold text-[11px] uppercase tracking-wider text-amber-800 block">Chef Instruction</span>
          <span class="text-xs font-bold leading-relaxed">${order.notes}</span>
        </div>
      </div>
    ` : '';

    // Action Controls per Lane Stage
    let actionBtn = '';
    if (order.status === 'pending') {
      actionBtn = `
        <button onclick="updateOrderStatus('${order.id}', 'preparing')" class="flex-1 py-2.5 px-3 rounded-[var(--radius-md)] bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 active:scale-95">
          <i data-lucide="flame" class="w-4 h-4"></i>
          <span>Accept & Cook</span>
        </button>
      `;
    } else if (order.status === 'preparing') {
      actionBtn = `
        <button onclick="updateOrderStatus('${order.id}', 'ready')" class="flex-1 py-2.5 px-3 rounded-[var(--radius-md)] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 active:scale-95">
          <i data-lucide="check" class="w-4 h-4"></i>
          <span>Plate Up / Ready</span>
        </button>
      `;
    } else if (order.status === 'ready') {
      actionBtn = `
        <button onclick="updateOrderStatus('${order.id}', 'completed')" class="flex-1 py-2.5 px-3 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 active:scale-95">
          <i data-lucide="check-check" class="w-4 h-4"></i>
          <span>Served at Table</span>
        </button>
      `;
    } else if (order.status === 'completed') {
      actionBtn = `
        <div class="flex-1 py-1.5 px-2.5 rounded-[var(--radius-md)] bg-slate-500/10 text-slate-600 font-semibold text-xs flex items-center justify-center gap-1">
          <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
          <span>Fulfilled</span>
        </div>
      `;
    }

    // Cancel Button
    const cancelBtn = order.status !== 'completed' ? `
      <button onclick="openCancelModal('${order.id}')" class="p-2.5 rounded-[var(--radius-md)] border border-red-500/20 text-red-500 hover:bg-red-500/10 hover:border-red-500/40 transition active:scale-90" title="Cancel Ticket">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    ` : '';

    const itemCount = (order.items || []).reduce((acc, curr) => acc + (curr.quantity || 1), 0);

    card.innerHTML = `
      <!-- Card Top: Table Info & Live Elapsed Timer -->
      <div class="flex items-center justify-between pb-2.5 border-b border-[var(--border)] gap-2">
        ${locationBadge}
        <div class="flex items-center gap-1 shrink-0">
          <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold flex items-center gap-1.5 ${timePillClass}">
            ${timeIcon}
            <span>${timeInfo.text}</span>
          </span>
        </div>
      </div>

      <!-- Line Items List -->
      <div class="space-y-1 py-1">
        ${itemsHtml}
      </div>

      <!-- Special Instructions Callout -->
      ${notesHtml}

      <!-- Financial Total Bar -->
      <div class="flex items-center justify-between pt-2.5 border-t border-[var(--border)] text-xs">
        <span class="text-slate-600 text-xs font-bold">${itemCount} ${itemCount === 1 ? 'item' : 'items'} in ticket</span>
        <div class="flex items-baseline gap-1.5">
          <span class="text-xs text-slate-600 font-bold uppercase tracking-wider">Total</span>
          <span class="font-mono font-black text-base text-slate-900">₹${parseFloat(order.totalAmount).toFixed(2)}</span>
        </div>
      </div>

      <!-- Action Buttons Bar -->
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
        let msg = `Order moved to ${newStatus}`;
        if (newStatus === 'preparing') msg = 'Ticket accepted • Chef cooking started';
        else if (newStatus === 'ready') msg = 'Order plated • Ready for table dispatch';
        else if (newStatus === 'completed') msg = 'Order served & settled';
        
        window.showToast(msg, newStatus === 'preparing' ? 'order' : 'success');
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

  function setCancelReason(reason) {
    document.getElementById('cancel-reason-input').value = reason;
  }

  async function confirmCancelOrder() {
    const reason = document.getElementById('cancel-reason-input').value.trim();
    if (!reason) {
      alert('Please specify a cancellation reason.');
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
        window.showToast('Order ticket cancelled.', 'info');
        closeCancelModal();
        loadOrders();
      }
    } catch(e) {
      console.error(e);
    }
  }
</script>
@endsection
