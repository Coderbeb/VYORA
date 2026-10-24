@extends('layouts.dashboard')

@section('title', 'Dashboard - Vyora')
@section('page_title', 'Overview & Real-time Metrics')

@section('dashboard_content')
<div class="space-y-8">
  <!-- Welcome Banner -->
  <div class="p-6 md:p-8 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl md:text-2xl font-bold font-serif text-[var(--text-primary)] mb-1">
        Welcome back, <span id="dash-welcome-name" class="text-[var(--accent)]">Chef</span>
      </h2>
      <p class="text-xs text-[var(--text-secondary)]">Here is what's happening at your restaurant today.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="/dashboard/orders" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm transition flex items-center gap-1.5">
        <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
        <span>Live Orders</span>
      </a>
      <a href="/dashboard/billing" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] transition flex items-center gap-1.5">
        <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
        <span>Billing</span>
      </a>
    </div>
  </div>

  <!-- Key Metrics 4-Card Grid -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Today's Revenue -->
    <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Today's Revenue</span>
        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <i data-lucide="dollar-sign" class="w-4 h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="stat-revenue" class="font-serif text-2xl md:text-3xl font-bold text-[var(--text-primary)]">₹0.00</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-1">Non-cancelled orders today</p>
      </div>
    </div>

    <!-- Active Orders -->
    <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Active Orders</span>
        <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <i data-lucide="clock" class="w-4 h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="stat-pending" class="font-serif text-2xl md:text-3xl font-bold text-[var(--text-primary)]">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-1">Pending & Preparing</p>
      </div>
    </div>

    <!-- Tables Count -->
    <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Active Tables</span>
        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
          <i data-lucide="qr-code" class="w-4 h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="stat-tables" class="font-serif text-2xl md:text-3xl font-bold text-[var(--text-primary)]">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-1">Configured QR tables</p>
      </div>
    </div>

    <!-- Menu Items -->
    <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Menu Catalog</span>
        <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
          <i data-lucide="utensils" class="w-4 h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="stat-menu" class="font-serif text-2xl md:text-3xl font-bold text-[var(--text-primary)]">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-1">Active menu dishes</p>
      </div>
    </div>
  </div>

  <!-- Recent Live Orders -->
  <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-base font-bold font-serif text-[var(--text-primary)]">Recent Dining Orders</h3>
        <p class="text-xs text-[var(--text-secondary)]">Latest tickets received across all active tables.</p>
      </div>
      <a href="/dashboard/orders" class="text-xs font-bold text-[var(--accent)] hover:underline flex items-center gap-1">
        <span>View Kanban Board</span>
        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-[var(--bg-base)] text-[var(--text-muted)] uppercase tracking-wider text-[10px] border-b border-[var(--border)]">
          <tr>
            <th class="p-3">Table #</th>
            <th class="p-3">Items</th>
            <th class="p-3">Total</th>
            <th class="p-3">Status</th>
            <th class="p-3">Time</th>
          </tr>
        </thead>
        <tbody id="recent-orders-body" class="divide-y divide-[var(--border)]">
          <tr>
            <td colspan="5" class="p-8 text-center text-[var(--text-muted)]">Loading orders...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const ownerStr = localStorage.getItem('owner');
    if (ownerStr) {
      try {
        const owner = JSON.parse(ownerStr);
        document.getElementById('dash-welcome-name').textContent = owner.restaurantName || owner.ownerName || 'Chef';
      } catch(e) {}
    }
    loadStats();
    loadRecentOrders();

    // Listen for live socket events
    window.addEventListener('qrestro:order:new', () => {
      loadStats();
      loadRecentOrders();
    });
    window.addEventListener('qrestro:order:updated', () => {
      loadStats();
      loadRecentOrders();
    });
  });

  async function loadStats() {
    try {
      const res = await fetch('/api/stats', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        document.getElementById('stat-revenue').textContent = '₹' + parseFloat(data.data.todayRevenue).toFixed(2);
        document.getElementById('stat-pending').textContent = data.data.pendingOrders;
        document.getElementById('stat-tables').textContent = data.data.tables;
        document.getElementById('stat-menu').textContent = data.data.menuItems;
      }
    } catch (err) {
      console.error('Failed to load stats:', err);
    }
  }

  async function loadRecentOrders() {
    try {
      const res = await fetch('/api/orders?limit=6', { headers: window.getAuthHeader() });
      const data = await res.json();
      const tbody = document.getElementById('recent-orders-body');
      
      if (data.success && data.data && data.data.length > 0) {
        tbody.innerHTML = '';
        data.data.forEach(order => {
          const itemsSummary = order.items.map(i => `${i.quantity}x ${i.menuItemName}`).join(', ');
          const time = new Date(order.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
          
          let statusBadge = '';
          if (order.status === 'pending') statusBadge = '<span class="px-2 py-0.5 rounded-full bg-orange-500/10 text-orange-600 font-bold">Pending</span>';
          else if (order.status === 'preparing') statusBadge = '<span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 font-bold">Preparing</span>';
          else if (order.status === 'ready') statusBadge = '<span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 font-bold">Ready</span>';
          else if (order.status === 'completed') statusBadge = '<span class="px-2 py-0.5 rounded-full bg-slate-500/10 text-slate-600 font-bold">Completed</span>';
          else statusBadge = '<span class="px-2 py-0.5 rounded-full bg-red-500/10 text-red-600 font-bold">Cancelled</span>';

          const locText = order.orderType === 'room_service'
            ? `<span class="inline-flex items-center gap-1 text-purple-600 dark:text-purple-400 font-bold"><i data-lucide="hotel" class="w-3.5 h-3.5"></i> <span>${order.hotelName || 'Hotel'} • Room #${order.roomNumber}</span></span>`
            : `Table #${order.tableNumber}`;

          const tr = document.createElement('tr');
          tr.className = 'hover:bg-[var(--bg-hover)] transition';
          tr.innerHTML = `
            <td class="p-3 font-bold text-[var(--text-primary)]">${locText}</td>
            <td class="p-3 text-[var(--text-secondary)] max-w-xs truncate" title="${itemsSummary}">${itemsSummary}</td>
            <td class="p-3 font-bold text-[var(--text-primary)]">₹${parseFloat(order.totalAmount).toFixed(2)}</td>
            <td class="p-3">${statusBadge}</td>
            <td class="p-3 text-[var(--text-muted)]">${time}</td>
          `;
          tbody.appendChild(tr);
        });

        if (window.lucide) lucide.createIcons();
      } else {
        tbody.innerHTML = '<tr><td colspan="5" class="p-8 text-center text-[var(--text-muted)]">No orders placed today yet. Scan a table QR to test!</td></tr>';
      }
    } catch (err) {
      console.error('Failed to load recent orders:', err);
    }
  }
</script>
@endsection
