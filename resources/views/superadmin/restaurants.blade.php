@extends('layouts.superadmin')

@section('title', 'Restaurants Management - Super Admin')
@section('page_title', 'Restaurant Tenants & Subscriptions')

@section('superadmin_content')
<div class="space-y-6">
  <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="font-serif font-bold text-base text-[var(--text-primary)]">All Registered Restaurants</h3>
        <p class="text-xs text-[var(--text-secondary)]">Manage subscription tiers and landing page visibility.</p>
      </div>
      <button onclick="loadRestaurants()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">
        <i data-lucide="refresh-cw" id="sa-ref-icon" class="w-4 h-4"></i>
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-[var(--bg-base)] text-[var(--text-muted)] uppercase tracking-wider text-[10px] border-b border-[var(--border)]">
          <tr>
            <th class="p-3">Restaurant</th>
            <th class="p-3">Owner / Contact</th>
            <th class="p-3">Plan Tier</th>
            <th class="p-3">Tables / Orders</th>
            <th class="p-3">Show on Landing</th>
            <th class="p-3">Action</th>
          </tr>
        </thead>
        <tbody id="sa-rest-body" class="divide-y divide-[var(--border)]">
          <tr><td colspan="6" class="p-8 text-center text-[var(--text-muted)]">Loading restaurants...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  let restaurants = [];

  document.addEventListener('DOMContentLoaded', () => {
    loadRestaurants();
  });

  async function loadRestaurants() {
    const icon = document.getElementById('sa-ref-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/superadmin/restaurants', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        restaurants = data.data;
        renderRestaurants(restaurants);
      }
    } catch (e) {
      console.error(e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function renderRestaurants(list) {
    const tbody = document.getElementById('sa-rest-body');
    tbody.innerHTML = '';

    if (list.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-[var(--text-muted)]">No restaurants found.</td></tr>';
      return;
    }

    list.forEach(r => {
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-[var(--bg-hover)] transition';

      const currentTier = r.plan?.tier || 'FREE';

      tr.innerHTML = `
        <td class="p-3">
          <p class="font-bold text-[var(--text-primary)]">${r.restaurantName || 'Unnamed'}</p>
          <p class="text-[10px] text-[var(--text-muted)]">@${r.username} • ${r.cuisine || 'Cuisine unset'}</p>
        </td>
        <td class="p-3 text-[var(--text-secondary)]">
          <p>${r.ownerName || '-'}</p>
          <p class="text-[10px] text-[var(--text-muted)]">${r.email}</p>
        </td>
        <td class="p-3">
          <select onchange="updatePlan('${r.id}', this.value)" class="px-2 py-1 text-xs font-bold rounded bg-[var(--bg-base)] border border-[var(--border)] focus:border-[var(--accent)]">
            <option value="FREE" ${currentTier === 'FREE' ? 'selected' : ''}>FREE (5 Tables)</option>
            <option value="PRO" ${currentTier === 'PRO' ? 'selected' : ''}>PRO (25 Tables)</option>
            <option value="PREMIUM" ${currentTier === 'PREMIUM' ? 'selected' : ''}>PREMIUM (100 Tables)</option>
          </select>
        </td>
        <td class="p-3 font-mono">
          <span>${r._count?.tables || 0} tables</span> • <span>${r._count?.orders || 0} orders</span>
        </td>
        <td class="p-3">
          <label class="flex items-center gap-1.5 cursor-pointer">
            <input type="checkbox" onchange="toggleLanding('${r.id}', this.checked)" ${r.showOnLanding ? 'checked' : ''} class="rounded border-[var(--border)] text-[var(--accent)]">
            <span class="text-[11px] font-semibold">${r.showOnLanding ? 'Visible' : 'Hidden'}</span>
          </label>
        </td>
        <td class="p-3">
          <button onclick="editCuisine('${r.id}', '${r.cuisine || ''}')" class="px-2 py-1 rounded border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[10px] font-semibold">
            Edit Cuisine
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    lucide.createIcons();
  }

  async function updatePlan(ownerId, tier) {
    try {
      const res = await fetch('/api/superadmin/restaurants', {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ ownerId, tier })
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Subscription plan updated!');
      }
    } catch(e) {
      console.error(e);
    }
  }

  async function toggleLanding(ownerId, showOnLanding) {
    try {
      await fetch('/api/superadmin/restaurants', {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ ownerId, showOnLanding })
      });
      window.showToast('Landing page showcase updated');
    } catch(e) {
      console.error(e);
    }
  }

  async function editCuisine(ownerId, current) {
    const cuisine = prompt('Enter cuisine name (e.g. Italian Bistro, Indian Cuisine):', current);
    if (cuisine === null) return;
    try {
      await fetch('/api/superadmin/restaurants', {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ ownerId, cuisine })
      });
      window.showToast('Cuisine updated');
      loadRestaurants();
    } catch(e) {
      console.error(e);
    }
  }
</script>
@endsection
