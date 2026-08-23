@extends('layouts.dashboard')

@section('title', 'Reports & Analytics - Vyora')
@section('page_title', 'Financial Reports & Analytics')

@section('dashboard_content')
<div class="space-y-6">
  <!-- Preset Range Switcher -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <div>
      <h2 class="font-serif font-bold text-base text-[var(--text-primary)]">Sales & Performance</h2>
      <p class="text-xs text-[var(--text-secondary)]">Aggregated from completed dining sessions.</p>
    </div>
    <div class="flex items-center gap-2">
      <button onclick="setPreset('today')" id="preset-today" class="px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm">Today</button>
      <button onclick="setPreset('7days')" id="preset-7days" class="px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">7 Days</button>
      <button onclick="setPreset('30days')" id="preset-30days" class="px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">30 Days</button>
    </div>
  </div>

  <!-- Key Metrics Row (Minimum 2 per row on mobile) -->
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
      <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase">Completed Orders</span>
      <h3 id="rep-orders" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--text-primary)] mt-1 sm:mt-2">0</h3>
    </div>
    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
      <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase">Total Revenue</span>
      <h3 id="rep-revenue" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--accent)] mt-1 sm:mt-2">₹0.00</h3>
    </div>
    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm col-span-2 sm:col-span-1">
      <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase">Avg Ticket Size</span>
      <h3 id="rep-avg" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--text-primary)] mt-1 sm:mt-2">₹0.00</h3>
    </div>
  </div>

  <!-- Daily Sales Visualizer -->
  <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
    <h3 class="font-serif font-bold text-base text-[var(--text-primary)] mb-4">Daily Revenue Breakdown</h3>
    <div id="chart-bars" class="h-48 flex items-end gap-2 overflow-x-auto pt-8 pb-2">
      <!-- Injected by JS -->
    </div>
  </div>

  <!-- Top Selling Dishes -->
  <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
    <h3 class="font-serif font-bold text-base text-[var(--text-primary)] mb-4">Top 10 Selling Dishes</h3>
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-[var(--bg-base)] text-[var(--text-muted)] uppercase tracking-wider text-[10px] border-b border-[var(--border)]">
          <tr>
            <th class="p-3">Rank</th>
            <th class="p-3">Dish Name</th>
            <th class="p-3">Portions Sold</th>
            <th class="p-3">Total Revenue</th>
          </tr>
        </thead>
        <tbody id="top-items-body" class="divide-y divide-[var(--border)]">
          <tr><td colspan="4" class="p-6 text-center text-[var(--text-muted)]">No sales data in this period.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  let activePreset = 'today';

  document.addEventListener('DOMContentLoaded', () => {
    loadReports(activePreset);
  });

  function setPreset(p) {
    activePreset = p;
    ['today', '7days', '30days'].forEach(k => {
      const btn = document.getElementById(`preset-${k}`);
      if (k === p) {
        btn.className = 'px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm';
      } else {
        btn.className = 'px-3.5 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]';
      }
    });
    loadReports(p);
  }

  async function loadReports(preset) {
    const end = new Date();
    const start = new Date();
    if (preset === 'today') {
      start.setHours(0, 0, 0, 0);
    } else if (preset === '7days') {
      start.setDate(start.getDate() - 7);
      start.setHours(0, 0, 0, 0);
    } else {
      start.setDate(start.getDate() - 30);
      start.setHours(0, 0, 0, 0);
    }

    try {
      const res = await fetch(`/api/reports?startDate=${start.toISOString()}&endDate=${end.toISOString()}`, {
        headers: window.getAuthHeader(),
      });
      const data = await res.json();
      if (data.success && data.data) {
        const m = data.data.metrics;
        document.getElementById('rep-orders').textContent = m.totalOrders;
        document.getElementById('rep-revenue').textContent = '₹' + parseFloat(m.totalRevenue).toFixed(2);
        document.getElementById('rep-avg').textContent = '₹' + parseFloat(m.averageTicketSize).toFixed(2);

        renderChart(data.data.dailySales || []);
        renderTopItems(data.data.topItems || []);
      }
    } catch (err) {
      console.error(err);
    }
  }

  function renderChart(dailySales) {
    const container = document.getElementById('chart-bars');
    container.innerHTML = '';

    if (dailySales.length === 0) {
      container.innerHTML = '<div class="w-full text-center text-xs text-[var(--text-muted)]">No revenue recorded in this timeframe.</div>';
      return;
    }

    const maxRev = Math.max(...dailySales.map(d => d.revenue), 10);

    dailySales.forEach(day => {
      const heightPercent = Math.max(8, Math.round((day.revenue / maxRev) * 100));
      const col = document.createElement('div');
      col.className = 'flex-1 min-w-[36px] flex flex-col items-center gap-1 h-full justify-end group';
      col.innerHTML = `
        <div class="opacity-0 group-hover:opacity-100 transition text-[10px] font-mono text-[var(--accent)] font-bold mb-1">₹${day.revenue.toFixed(0)}</div>
        <div class="w-full rounded-t-[var(--radius-sm)] bg-[var(--accent)] group-hover:bg-[var(--accent-hover)] transition-all" style="height: ${heightPercent}%;"></div>
        <span class="text-[9px] text-[var(--text-muted)] mt-1 truncate max-w-full font-mono">${day.date.slice(5)}</span>
      `;
      container.appendChild(col);
    });
  }

  function renderTopItems(items) {
    const tbody = document.getElementById('top-items-body');
    tbody.innerHTML = '';

    if (items.length === 0) {
      tbody.innerHTML = '<tr><td colspan="4" class="p-6 text-center text-[var(--text-muted)]">No item sales found in selected range.</td></tr>';
      return;
    }

    items.forEach((item, idx) => {
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-[var(--bg-hover)] transition';
      tr.innerHTML = `
        <td class="p-3 font-bold text-[var(--accent)]">#${idx + 1}</td>
        <td class="p-3 font-semibold text-[var(--text-primary)]">${item.name}</td>
        <td class="p-3 font-mono">${item.quantity} orders</td>
        <td class="p-3 font-mono font-bold text-[var(--text-primary)]">₹${parseFloat(item.revenue).toFixed(2)}</td>
      `;
      tbody.appendChild(tr);
    });
  }
</script>
@endsection
