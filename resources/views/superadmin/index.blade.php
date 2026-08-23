@extends('layouts.superadmin')

@section('title', 'Super Admin Overview - Vyora')
@section('page_title', 'SaaS Platform Analytics & Overview')

@section('superadmin_content')
<div class="space-y-6">
  <!-- Key Metrics 4-Card Responsive Grid -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <!-- Total Tenants -->
    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-2 sm:mb-3">
        <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Total Tenants</span>
        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-[var(--accent-glow)] text-[var(--accent)] flex items-center justify-center">
          <i data-lucide="store" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="sa-tenants" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--text-primary)]">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-0.5 truncate"><span id="sa-hotels-count">0</span> Hotels • <span id="sa-restaurants-count">0</span> Dining</p>
      </div>
    </div>

    <!-- Platform Gross Revenue -->
    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-2 sm:mb-3">
        <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Gross Platform GMV</span>
        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <i data-lucide="dollar-sign" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="sa-revenue" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--accent)]">₹0.00</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-0.5">Completed order volume</p>
      </div>
    </div>

    <!-- Total System Orders -->
    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-2 sm:mb-3">
        <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Total System Orders</span>
        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <i data-lucide="shopping-bag" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="sa-orders" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--text-primary)]">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-0.5">Dining & In-room orders</p>
      </div>
    </div>

    <!-- Active Rooms & Tables -->
    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-2 sm:mb-3">
        <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Inventory Capacity</span>
        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
          <i data-lucide="grid" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="sa-inventory" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--text-primary)]">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-0.5 truncate"><span id="sa-tables">0</span> Tables • <span id="sa-rooms">0</span> Rooms</p>
      </div>
    </div>
  </div>

  <!-- Quick Action Navigation Cards (Mobile-friendly stacked grid) -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Tenants Card -->
    <div class="p-5 sm:p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between space-y-4">
      <div>
        <div class="flex items-center gap-2.5 mb-2">
          <div class="w-8 h-8 rounded-lg bg-[var(--accent-glow)] text-[var(--accent)] flex items-center justify-center">
            <i data-lucide="store" class="w-4 h-4"></i>
          </div>
          <h3 class="font-serif font-bold text-base sm:text-lg text-[var(--text-primary)]">Tenants & Properties</h3>
        </div>
        <p class="text-xs text-[var(--text-secondary)]">Manage hotels, restaurants, active subscriptions, and landing page visibility.</p>
      </div>
      <div class="flex items-center gap-2 pt-2 border-t border-[var(--border)]">
        <a href="/superadmin/restaurants" class="flex-1 py-2 text-center rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white text-xs font-bold transition shadow-sm">
          Manage Tenants
        </a>
      </div>
    </div>

    <!-- Customizations Card -->
    <div class="p-5 sm:p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between space-y-4">
      <div>
        <div class="flex items-center gap-2.5 mb-2">
          <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
            <i data-lucide="sliders" class="w-4 h-4"></i>
          </div>
          <h3 class="font-serif font-bold text-base sm:text-lg text-[var(--text-primary)]">SaaS Platform Settings</h3>
        </div>
        <p class="text-xs text-[var(--text-secondary)]">Configure subscription pricing tiers, platform branding, currency format, and Pusher keys.</p>
      </div>
      <div class="flex items-center gap-2 pt-2 border-t border-[var(--border)]">
        <a href="/superadmin/settings" class="flex-1 py-2 text-center rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-xs font-bold text-[var(--text-primary)] transition">
          Configure Customizations
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', async () => {
    try {
      const res = await fetch('/api/superadmin/stats', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        const s = data.data;
        document.getElementById('sa-tenants').textContent = s.totalTenants || 0;
        document.getElementById('sa-hotels-count').textContent = s.totalHotels || 0;
        document.getElementById('sa-restaurants-count').textContent = s.totalRestaurants || 0;
        document.getElementById('sa-orders').textContent = s.totalOrders || 0;
        document.getElementById('sa-revenue').textContent = '₹' + parseFloat(s.platformRevenue || 0).toFixed(2);
        document.getElementById('sa-tables').textContent = s.activeTables || 0;
        document.getElementById('sa-rooms').textContent = s.activeRooms || 0;
        document.getElementById('sa-inventory').textContent = (s.activeTables || 0) + (s.activeRooms || 0);
      }
    } catch (err) {
      console.error(err);
    }
  });
</script>
@endsection
