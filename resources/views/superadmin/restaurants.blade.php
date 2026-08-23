@extends('layouts.superadmin')

@section('title', 'Tenants & Properties - Super Admin')
@section('page_title', 'Tenant Management & Subscriptions')

@section('superadmin_content')
<div class="space-y-6">
  <!-- Controls & Actions Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
    <!-- Filters & Search -->
    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
      <div class="relative flex-1 sm:w-64">
        <input type="text" id="tenant-search" oninput="filterTenants()" placeholder="Search property, owner, email..." class="w-full pl-9 pr-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        <i data-lucide="search" class="w-4 h-4 text-[var(--text-muted)] absolute left-3 top-1/2 -translate-y-1/2"></i>
      </div>
      <select id="type-filter" onchange="filterTenants()" class="px-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none text-[var(--text-primary)]">
        <option value="ALL">All Types</option>
        <option value="HOTEL">Hotels 🏨</option>
        <option value="RESTAURANT">Restaurants 🍽️</option>
        <option value="BOTH">Combos (Hotel+Dining) 🏨🍽️</option>
      </select>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
      <button onclick="openOnboardModal()" class="flex-1 sm:flex-none px-4 py-2 bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs rounded-[var(--radius-sm)] transition flex items-center justify-center gap-1.5 shadow-sm">
        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
        <span>Onboard Tenant</span>
      </button>
      <button onclick="loadRestaurants()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Refresh">
        <i data-lucide="refresh-cw" id="sa-ref-icon" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <!-- Tenants Container (Desktop Table + Mobile Cards) -->
  <div class="rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm overflow-hidden">
    <!-- Desktop Table View (Hidden on mobile) -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-[var(--bg-base)] text-[var(--text-muted)] uppercase tracking-wider text-[10px] border-b border-[var(--border)]">
          <tr>
            <th class="p-3.5">Property / Business</th>
            <th class="p-3.5">Type</th>
            <th class="p-3.5">Owner Contact</th>
            <th class="p-3.5">Assigned Plan</th>
            <th class="p-3.5">Capacity & Orders</th>
            <th class="p-3.5 text-center">Showcase</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody id="sa-rest-body-desktop" class="divide-y divide-[var(--border)]">
          <tr><td colspan="7" class="p-8 text-center text-[var(--text-muted)]">Loading tenants...</td></tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Cards View (Visible on mobile only) -->
    <div id="sa-rest-body-mobile" class="md:hidden grid grid-cols-1 sm:grid-cols-2 gap-3 p-3">
      <div class="col-span-full p-8 text-center text-xs text-[var(--text-muted)]">Loading tenants...</div>
    </div>
  </div>
</div>

<!-- Onboard New Tenant Modal -->
<div id="onboard-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-2xl w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
      <div>
        <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Onboard New Property Tenant</h3>
        <p class="text-xs text-[var(--text-secondary)]">Create credentials and assign plan for a new hotel or restaurant.</p>
      </div>
      <button onclick="closeOnboardModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form onsubmit="submitOnboard(event)" class="space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Business Type *</label>
          <select id="onb-btype" required class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
            <option value="RESTAURANT">Dining Restaurant 🍽️</option>
            <option value="HOTEL">Hotel PMS 🏨</option>
            <option value="BOTH">Combo (Hotel + Dining) 🏨🍽️</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Property Name *</label>
          <input type="text" id="onb-restname" required placeholder="e.g. Grand Horizon Hotel" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Username (Login ID) *</label>
          <input type="text" id="onb-username" required placeholder="e.g. grandhorizon" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Password *</label>
          <input type="password" id="onb-password" required minlength="6" placeholder="••••••••" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Email Address *</label>
          <input type="email" id="onb-email" required placeholder="manager@venue.com" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Contact Person Name</label>
          <input type="text" id="onb-ownername" placeholder="e.g. Alexander Vance" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Phone Number</label>
          <input type="text" id="onb-phone" placeholder="+1 (555) 019-2834" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Subscription Plan</label>
          <select id="onb-plan" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
            <!-- Loaded dynamically -->
          </select>
        </div>
      </div>

      <div class="flex justify-end gap-3 pt-3 border-t border-[var(--border)]">
        <button type="button" onclick="closeOnboardModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Cancel</button>
        <button type="submit" id="onb-submit-btn" class="px-5 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm">Complete Onboarding</button>
      </div>
    </form>
  </div>
</div>

<script>
  let allTenants = [];
  let availablePlans = [];

  document.addEventListener('DOMContentLoaded', () => {
    loadRestaurants();
    loadPlansForOnboard();
  });

  async function loadPlansForOnboard() {
    try {
      const res = await fetch('/api/superadmin/plans', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        availablePlans = data.data;
        const sel = document.getElementById('onb-plan');
        sel.innerHTML = '<option value="">Default (Free / Trial)</option>' + availablePlans.map(p => `
          <option value="${p.id}">${p.tier} (₹${parseFloat(p.price).toFixed(2)}/mo - ${p.business_type})</option>
        `).join('');
      }
    } catch(e) {}
  }

  async function loadRestaurants() {
    const icon = document.getElementById('sa-ref-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/superadmin/restaurants', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        allTenants = data.data;
        filterTenants();
      }
    } catch (e) {
      console.error(e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function filterTenants() {
    const search = document.getElementById('tenant-search').value.toLowerCase().trim();
    const type = document.getElementById('type-filter').value;

    const filtered = allTenants.filter(t => {
      const matchType = (type === 'ALL' || t.businessType === type);
      const matchSearch = !search || 
        (t.restaurantName && t.restaurantName.toLowerCase().includes(search)) ||
        (t.username && t.username.toLowerCase().includes(search)) ||
        (t.email && t.email.toLowerCase().includes(search)) ||
        (t.ownerName && t.ownerName.toLowerCase().includes(search));
      return matchType && matchSearch;
    });

    renderDesktop(filtered);
    renderMobile(filtered);
  }

  function renderDesktop(list) {
    const tbody = document.getElementById('sa-rest-body-desktop');
    if (list.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-[var(--text-muted)]">No matching tenants found.</td></tr>';
      return;
    }

    tbody.innerHTML = list.map(r => `
      <tr class="hover:bg-[var(--bg-hover)] transition">
        <td class="p-3.5">
          <p class="font-bold text-[var(--text-primary)]">${r.restaurantName || 'Unnamed'}</p>
          <p class="text-[10px] text-[var(--text-muted)]">@${r.username} • ${r.cuisine || 'Boutique'}</p>
        </td>
        <td class="p-3.5">
          <span class="px-2 py-0.5 rounded text-[10px] font-bold ${
            r.businessType === 'HOTEL' ? 'bg-amber-500/10 text-amber-600' :
            (r.businessType === 'BOTH' ? 'bg-purple-500/10 text-purple-600' : 'bg-emerald-500/10 text-emerald-600')
          }">
            ${r.businessType === 'HOTEL' ? '🏨 Hotel' : (r.businessType === 'BOTH' ? '🏨🍽️ Combo' : '🍽️ Dining')}
          </span>
        </td>
        <td class="p-3.5 text-[var(--text-secondary)]">
          <p class="font-medium">${r.ownerName || '-'}</p>
          <p class="text-[10px] text-[var(--text-muted)]">${r.email}</p>
        </td>
        <td class="p-3.5">
          <select onchange="updatePlan('${r.id}', this.value)" class="px-2.5 py-1 text-xs font-bold rounded bg-[var(--bg-base)] border border-[var(--border)] focus:border-[var(--accent)]">
            <option value="FREE" ${r.plan?.tier === 'FREE' ? 'selected' : ''}>FREE Tier</option>
            <option value="PRO" ${r.plan?.tier === 'PRO' ? 'selected' : ''}>PRO Tier</option>
            <option value="PREMIUM" ${r.plan?.tier === 'PREMIUM' ? 'selected' : ''}>PREMIUM Tier</option>
          </select>
        </td>
        <td class="p-3.5 font-mono text-[11px]">
          ${r.businessType === 'HOTEL' || r.businessType === 'BOTH' ? `<span>${r._count?.rooms || 0} rooms</span> • ` : ''}
          <span>${r._count?.tables || 0} tables</span> • <span>${r._count?.orders || 0} orders</span>
        </td>
        <td class="p-3.5 text-center">
          <label class="inline-flex items-center gap-1.5 cursor-pointer">
            <input type="checkbox" onchange="toggleLanding('${r.id}', this.checked)" ${r.showOnLanding ? 'checked' : ''} class="rounded border-[var(--border)] text-[var(--accent)]">
          </label>
        </td>
        <td class="p-3.5 text-right">
          <button onclick="deleteTenant('${r.id}', '${r.restaurantName}')" class="p-1.5 rounded text-red-600 hover:bg-red-500/10 transition" title="Delete Tenant">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </td>
      </tr>
    `).join('');
    lucide.createIcons();
  }

  function renderMobile(list) {
    const container = document.getElementById('sa-rest-body-mobile');
    if (list.length === 0) {
      container.innerHTML = '<div class="p-8 text-center text-xs text-[var(--text-muted)]">No matching tenants found.</div>';
      return;
    }

    container.innerHTML = list.map(r => `
      <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)] space-y-3">
        <div class="flex items-start justify-between gap-2">
          <div>
            <div class="flex items-center gap-2">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold ${
                r.businessType === 'HOTEL' ? 'bg-amber-500/10 text-amber-600' :
                (r.businessType === 'BOTH' ? 'bg-purple-500/10 text-purple-600' : 'bg-emerald-500/10 text-emerald-600')
              }">
                ${r.businessType === 'HOTEL' ? '🏨 Hotel' : (r.businessType === 'BOTH' ? '🏨🍽️ Combo' : '🍽️ Dining')}
              </span>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">${r.restaurantName || 'Unnamed'}</h4>
            </div>
            <p class="text-[11px] text-[var(--text-muted)] mt-0.5">@${r.username} • ${r.email}</p>
          </div>
          <button onclick="deleteTenant('${r.id}', '${r.restaurantName}')" class="p-1.5 text-red-600 hover:bg-red-500/10 rounded">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </div>

        <div class="grid grid-cols-2 gap-2 text-xs py-1 border-y border-[var(--border)]">
          <div>
            <span class="text-[10px] text-[var(--text-muted)] block">Capacity</span>
            <span class="font-bold text-[var(--text-primary)]">
              ${r.businessType === 'HOTEL' || r.businessType === 'BOTH' ? `${r._count?.rooms || 0} Rooms • ` : ''}
              ${r._count?.tables || 0} Tables
            </span>
          </div>
          <div>
            <span class="text-[10px] text-[var(--text-muted)] block">Orders Total</span>
            <span class="font-bold text-[var(--accent)]">${r._count?.orders || 0} orders</span>
          </div>
        </div>

        <div class="flex items-center justify-between gap-2 pt-1">
          <select onchange="updatePlan('${r.id}', this.value)" class="flex-1 px-2 py-1.5 text-xs font-bold rounded bg-[var(--bg-surface)] border border-[var(--border)]">
            <option value="FREE" ${r.plan?.tier === 'FREE' ? 'selected' : ''}>FREE Tier</option>
            <option value="PRO" ${r.plan?.tier === 'PRO' ? 'selected' : ''}>PRO Tier</option>
            <option value="PREMIUM" ${r.plan?.tier === 'PREMIUM' ? 'selected' : ''}>PREMIUM Tier</option>
          </select>
          <label class="flex items-center gap-1.5 text-xs text-[var(--text-secondary)]">
            <input type="checkbox" onchange="toggleLanding('${r.id}', this.checked)" ${r.showOnLanding ? 'checked' : ''} class="rounded border-[var(--border)] text-[var(--accent)]">
            <span class="text-[11px]">Landing</span>
          </label>
        </div>
      </div>
    `).join('');
    lucide.createIcons();
  }

  function openOnboardModal() {
    document.getElementById('onboard-modal').classList.remove('hidden');
  }

  function closeOnboardModal() {
    document.getElementById('onboard-modal').classList.add('hidden');
  }

  async function submitOnboard(e) {
    e.preventDefault();
    const btn = document.getElementById('onb-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Creating...';

    const payload = {
      business_type: document.getElementById('onb-btype').value,
      restaurant_name: document.getElementById('onb-restname').value.trim(),
      username: document.getElementById('onb-username').value.trim(),
      password: document.getElementById('onb-password').value,
      email: document.getElementById('onb-email').value.trim(),
      owner_name: document.getElementById('onb-ownername').value.trim(),
      phone: document.getElementById('onb-phone').value.trim(),
      plan_id: document.getElementById('onb-plan').value || null,
    };

    try {
      const res = await fetch('/api/superadmin/tenants', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (data.success) {
        closeOnboardModal();
        window.showToast('✅ Tenant onboarded successfully!');
        loadRestaurants();
      } else {
        alert(data.error?.message || 'Failed to onboard tenant');
      }
    } catch(err) {
      alert('Error creating tenant');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Complete Onboarding';
    }
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

  async function deleteTenant(id, name) {
    if (!confirm(`Are you sure you want to completely remove tenant "${name}"? This action cannot be undone.`)) return;
    try {
      const res = await fetch('/api/superadmin/restaurants/' + id, {
        method: 'DELETE',
        headers: window.getAuthHeader(),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Tenant removed');
        loadRestaurants();
      } else {
        alert(data.error?.message || 'Failed to remove tenant');
      }
    } catch(e) {
      alert('Error deleting tenant');
    }
  }
</script>
@endsection
