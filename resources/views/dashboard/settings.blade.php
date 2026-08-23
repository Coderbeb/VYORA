@extends('layouts.dashboard')

@section('title', 'Property & Venue Settings - Vyora')
@section('page_title', 'Restaurant Settings & Profile')

@section('dashboard_content')
<div class="space-y-6 max-w-4xl">
  <!-- Profile & Restaurant Info Card -->
  <div class="p-6 md:p-8 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-[var(--border)]">
      <div>
        <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Venue Profile</h3>
        <p class="text-xs text-[var(--text-secondary)]">Public details visible on your dining menus.</p>
      </div>
      <div class="w-10 h-10 rounded-xl bg-[var(--accent)] text-white font-bold text-lg flex items-center justify-center font-serif">
        <span id="sett-avatar">Q</span>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
      <div>
        <span class="text-[var(--text-muted)] font-semibold uppercase">Restaurant Name</span>
        <p id="sett-rest-name" class="font-bold text-sm text-[var(--text-primary)] mt-1">-</p>
      </div>
      <div>
        <span class="text-[var(--text-muted)] font-semibold uppercase">Owner / Manager</span>
        <p id="sett-owner-name" class="font-bold text-sm text-[var(--text-primary)] mt-1">-</p>
      </div>
      <div>
        <span class="text-[var(--text-muted)] font-semibold uppercase">Email Address</span>
        <p id="sett-email" class="font-semibold text-sm text-[var(--text-primary)] mt-1">-</p>
      </div>
      <div>
        <span class="text-[var(--text-muted)] font-semibold uppercase">Phone</span>
        <p id="sett-phone" class="font-semibold text-sm text-[var(--text-primary)] mt-1">-</p>
      </div>
      <div>
        <span class="text-[var(--text-muted)] font-semibold uppercase">Cuisine / Category</span>
        <p id="sett-cuisine" class="font-semibold text-sm text-[var(--text-primary)] mt-1">-</p>
      </div>
      <div>
        <span class="text-[var(--text-muted)] font-semibold uppercase">Login Username</span>
        <p id="sett-username" class="font-mono font-semibold text-sm text-[var(--accent)] mt-1">-</p>
      </div>
    </div>
  </div>

  <!-- Active Subscription Tier Card -->
  <div class="p-6 md:p-8 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-[var(--border)]">
      <div>
        <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Subscription Plan</h3>
        <p class="text-xs text-[var(--text-secondary)]">Your active service tier and dining table allowance.</p>
      </div>
      <span id="sett-plan-badge" class="px-3 py-1 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-xs uppercase">
        FREE TIER
      </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
      <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)]">
        <span class="text-[var(--text-muted)] font-semibold">Tier</span>
        <h4 id="sett-plan-tier" class="font-bold text-base text-[var(--text-primary)] mt-1">FREE</h4>
      </div>
      <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)]">
        <span class="text-[var(--text-muted)] font-semibold">Table Limit</span>
        <h4 id="sett-plan-tables" class="font-bold text-base text-[var(--text-primary)] mt-1">5 Tables</h4>
      </div>
      <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)]">
        <span class="text-[var(--text-muted)] font-semibold">Monthly Cost</span>
        <h4 id="sett-plan-price" class="font-bold text-base text-[var(--accent)] mt-1">$0.00</h4>
      </div>
    </div>
  </div>

  <!-- Security & Password Card -->
  <div class="p-6 md:p-8 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex items-center justify-between">
    <div>
      <h3 class="font-serif font-bold text-base text-[var(--text-primary)]">Security & Access</h3>
      <p class="text-xs text-[var(--text-secondary)]">Update your administrative credentials.</p>
    </div>
    <button onclick="openChangePasswordModal()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] transition flex items-center gap-1.5">
      <i data-lucide="lock" class="w-3.5 h-3.5"></i>
      <span>Change Password</span>
    </button>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', async () => {
    try {
      const res = await fetch('/api/auth/profile', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        const u = data.data;
        document.getElementById('sett-rest-name').textContent = u.restaurantName || '-';
        document.getElementById('sett-owner-name').textContent = u.ownerName || '-';
        document.getElementById('sett-email').textContent = u.email || '-';
        document.getElementById('sett-phone').textContent = u.phone || 'Not set';
        document.getElementById('sett-cuisine').textContent = u.cuisine || 'General Restaurant';
        document.getElementById('sett-username').textContent = '@' + u.username;
        document.getElementById('sett-avatar').textContent = (u.restaurantName || 'Q').charAt(0).toUpperCase();

        if (u.plan) {
          document.getElementById('sett-plan-badge').textContent = u.plan.tier + ' PLAN';
          document.getElementById('sett-plan-tier').textContent = u.plan.tier;
          document.getElementById('sett-plan-tables').textContent = u.plan.max_tables + ' Tables';
          document.getElementById('sett-plan-price').textContent = '$' + parseFloat(u.plan.price).toFixed(2);
        }
      }
    } catch(err) {
      console.error(err);
    }
  });
</script>
@endsection
