@extends('layouts.superadmin')

@section('title', 'Super Admin Overview - Vyora')
@section('page_title', 'Platform Analytics & Overview')

@section('superadmin_content')
<div class="space-y-8">
  <!-- Stats Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="p-6 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
      <span class="text-xs font-semibold text-[var(--text-secondary)] uppercase">Total Restaurants</span>
      <h3 id="sa-restaurants" class="font-serif text-3xl font-bold text-[var(--text-primary)] mt-2">0</h3>
    </div>
    <div class="p-6 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
      <span class="text-xs font-semibold text-[var(--text-secondary)] uppercase">Total System Orders</span>
      <h3 id="sa-orders" class="font-serif text-3xl font-bold text-[var(--text-primary)] mt-2">0</h3>
    </div>
    <div class="p-6 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
      <span class="text-xs font-semibold text-[var(--text-secondary)] uppercase">Gross Platform Revenue</span>
      <h3 id="sa-revenue" class="font-serif text-3xl font-bold text-[var(--accent)] mt-2">$0.00</h3>
    </div>
    <div class="p-6 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
      <span class="text-xs font-semibold text-[var(--text-secondary)] uppercase">Active QR Tables</span>
      <h3 id="sa-tables" class="font-serif text-3xl font-bold text-[var(--text-primary)] mt-2">0</h3>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex items-center justify-between">
    <div>
      <h3 class="font-serif font-bold text-base text-[var(--text-primary)]">Manage Restaurant Tenants</h3>
      <p class="text-xs text-[var(--text-secondary)]">Oversee tenant tiers, plan allocations, and landing page showcase status.</p>
    </div>
    <a href="/superadmin/restaurants" class="px-4 py-2 bg-[var(--accent)] text-white font-bold text-xs rounded-[var(--radius-sm)] hover:bg-[var(--accent-hover)] transition flex items-center gap-1.5 shadow-sm">
      <span>View All Restaurants</span>
      <i data-lucide="arrow-right" class="w-4 h-4"></i>
    </a>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', async () => {
    try {
      const res = await fetch('/api/superadmin/stats', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        document.getElementById('sa-restaurants').textContent = data.data.totalRestaurants;
        document.getElementById('sa-orders').textContent = data.data.totalOrders;
        document.getElementById('sa-revenue').textContent = '$' + parseFloat(data.data.platformRevenue).toFixed(2);
        document.getElementById('sa-tables').textContent = data.data.activeTables;
      }
    } catch (err) {
      console.error(err);
    }
  });
</script>
@endsection
