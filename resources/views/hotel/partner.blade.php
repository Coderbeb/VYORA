@extends('layouts.dashboard')

@section('title', 'Partner Dining Network - Vyora')
@section('page_title', 'Cross-Business Partner Dining Network')

@section('dashboard_content')
<div class="space-y-6 max-w-4xl">
  <!-- Explanation Banner -->
  <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-3">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold">
        <i data-lucide="utensils-crossed" class="w-5 h-5"></i>
      </div>
      <div>
        <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Multi-Hotel & Multi-Restaurant Partner Network</h3>
        <p class="text-xs text-[var(--text-secondary)]">Hotels can partner with multiple nearby restaurants, and restaurants can fulfill orders from multiple partner hotels simultaneously.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 text-xs">
      <div class="p-4 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] space-y-1">
        <h4 class="font-bold text-[var(--text-primary)] flex items-center gap-1.5">
          <i data-lucide="hotel" class="w-4 h-4 text-purple-600"></i>
          <span>For Hotels (Guest Choice)</span>
        </h4>
        <p class="text-[var(--text-secondary)]">Link 1 or more partner restaurants below. Guests in their room can switch between partner restaurant menus. Orders dispatch straight to the partner kitchen!</p>
      </div>
      <div class="p-4 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] space-y-1">
        <h4 class="font-bold text-[var(--text-primary)] flex items-center gap-1.5">
          <i data-lucide="utensils" class="w-4 h-4 text-[var(--accent)]"></i>
          <span>For Restaurants (Incoming Room Tickets)</span>
        </h4>
        <p class="text-[var(--text-secondary)]">When partner hotel guests order, your Kanban Board and Kitchen KDS will clearly display: <strong class="text-purple-600">Hotel Name • Room #[Number]</strong>.</p>
      </div>
    </div>
  </div>

  <!-- Currently Linked Partners Summary -->
  <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
      <div>
        <h3 class="font-serif font-bold text-base text-[var(--text-primary)]">Active Food Partnerships</h3>
        <p class="text-xs text-[var(--text-secondary)]">Restaurants currently fulfilling in-room guest orders for your property.</p>
      </div>
      <span id="active-partners-count" class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-600 font-bold text-xs">
        0 Partners Linked
      </span>
    </div>

    <div id="linked-partners-list" class="space-y-2 py-2">
      <!-- Populated by JS -->
    </div>
  </div>

  <!-- Available Restaurants Directory -->
  <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-4">
    <h3 class="font-serif font-bold text-base text-[var(--text-primary)]">Available Nearby Vyora Restaurants</h3>
    <p class="text-xs text-[var(--text-secondary)]">Click to link or unlink partner restaurants for your in-room guest portal.</p>

    <div id="restaurants-list" class="space-y-3 pt-2">
      <div class="p-8 text-center text-xs text-[var(--text-muted)]">Loading available restaurants...</div>
    </div>
  </div>
</div>

<script>
  let partnerData = null;

  document.addEventListener('DOMContentLoaded', () => {
    loadPartnerSettings();
  });

  async function loadPartnerSettings() {
    try {
      const res = await fetch('/api/hotel/partner', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        partnerData = data.data;
        renderPartnerView(partnerData);
      }
    } catch(err) {
      console.error(err);
    }
  }

  function renderPartnerView(d) {
    const countBadge = document.getElementById('active-partners-count');
    const linkedList = document.getElementById('linked-partners-list');

    const linked = d.linkedPartners || [];
    countBadge.textContent = `${linked.length} Partner${linked.length === 1 ? '' : 's'} Linked`;

    if (linked.length > 0) {
      linkedList.innerHTML = '';
      linked.forEach(p => {
        const item = document.createElement('div');
        item.className = 'flex items-center justify-between p-3.5 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)]';
        item.innerHTML = `
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center font-bold">
              <i data-lucide="utensils" class="w-4 h-4"></i>
            </div>
            <div>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">${p.name}</h4>
              <p class="text-xs text-[var(--text-secondary)] flex items-center gap-1.5 mt-0.5">
                <span>${p.cuisine || 'Restaurant'}</span>
                <span>•</span>
                <i data-lucide="phone" class="w-3 h-3 text-[var(--text-muted)]"></i>
                <span>${p.phone || 'No phone'}</span>
              </p>
            </div>
          </div>
          <button onclick="togglePartnerLink('${p.id}', false)" class="px-3 py-1.5 rounded-[var(--radius-sm)] border border-red-500/20 text-red-500 hover:bg-red-500/10 text-xs font-semibold">
            Unlink
          </button>
        `;
        linkedList.appendChild(item);
      });
    } else {
      linkedList.innerHTML = `
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)] text-xs text-[var(--text-secondary)]">
          <p class="font-semibold text-[var(--text-primary)]">In-House Kitchen Active</p>
          <p>No external restaurants linked. Dishes from your <a href="/dashboard/menu" class="text-[var(--accent)] underline font-bold">Menu Catalog</a> are served to rooms and billed to the guest's folio.</p>
        </div>
      `;
    }

    const restList = document.getElementById('restaurants-list');
    restList.innerHTML = '';

    if (!d.availableRestaurants || d.availableRestaurants.length === 0) {
      restList.innerHTML = '<div class="p-8 text-center text-xs text-[var(--text-muted)]">No other restaurants available on the platform yet.</div>';
      return;
    }

    d.availableRestaurants.forEach(r => {
      const card = document.createElement('div');
      card.className = `p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)] flex items-center justify-between gap-4 transition hover:border-[var(--accent)] ${r.isLinked ? 'border-purple-500' : ''}`;

      card.innerHTML = `
        <div>
          <div class="flex items-center gap-2">
            <h4 class="font-bold text-sm text-[var(--text-primary)]">${r.name}</h4>
            ${r.isLinked ? '<span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-600 font-bold text-[10px]">Active Partner</span>' : ''}
          </div>
          <p class="text-xs text-[var(--text-secondary)] flex items-center gap-1.5 mt-0.5">
            <span>${r.cuisine || 'General Dining'}</span>
            <span>•</span>
            <span>${r.itemCount || 0} menu dishes</span>
            <span>•</span>
            <i data-lucide="user" class="w-3 h-3 text-[var(--text-muted)]"></i>
            <span>${r.ownerName}</span>
          </p>
        </div>
        <div>
          ${r.isLinked ? `
            <button onclick="togglePartnerLink('${r.id}', false)" class="px-3.5 py-2 rounded-[var(--radius-sm)] border border-red-500/20 text-red-500 hover:bg-red-500/10 text-xs font-semibold transition">
              Unlink Partner
            </button>
          ` : `
            <button onclick="togglePartnerLink('${r.id}', true)" class="px-4 py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition">
              + Link Partner
            </button>
          `}
        </div>
      `;
      restList.appendChild(card);
    });

    lucide.createIcons();
  }

  async function togglePartnerLink(partnerId, link) {
    const verb = link ? 'Link' : 'Unlink';
    if (!confirm(`${verb} this restaurant partnership?`)) return;

    try {
      const res = await fetch('/api/hotel/partner', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ restaurantId: partnerId, link: link })
      });
      const data = await res.json();
      if (data.success) {
        window.showToast(data.message || 'Partnership updated!');
        loadPartnerSettings();
      } else {
        alert(data.error?.message || 'Action failed');
      }
    } catch(err) {
      console.error(err);
    }
  }
</script>
@endsection
