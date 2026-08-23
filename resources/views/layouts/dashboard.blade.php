@extends('layouts.app')

@section('content')
<div class="flex min-h-screen relative" id="dashboard-wrapper">
  <!-- Mobile Sidebar Backdrop Overlay -->
  <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 md:hidden hidden transition-opacity duration-300" onclick="toggleMobileSidebar()"></div>

  <!-- Sidebar Navigation -->
  <aside id="main-sidebar" class="fixed md:sticky top-0 left-0 h-screen z-50 flex flex-col justify-between bg-[var(--bg-sidebar)] text-[var(--text-sidebar-primary)] transition-all duration-300 -translate-x-full md:translate-x-0 w-[240px] md:w-[var(--sidebar-width)] shadow-xl md:shadow-none border-r border-[var(--border-sidebar)]">
    <div class="flex flex-col h-full overflow-y-auto overflow-x-hidden">
      <!-- Brand Logo Header -->
      <div class="flex items-center justify-between p-4 border-b border-[var(--border-sidebar)]">
        <a href="/dashboard" class="flex items-center gap-2.5 text-white text-xl font-bold tracking-tight font-serif">
          <div class="w-8 h-8 rounded-lg bg-[var(--accent)] text-white flex items-center justify-center font-black text-lg shadow-sm font-serif">V</div>
          <span class="sidebar-label transition-opacity duration-200 font-bold tracking-tight">Vyora</span>
        </a>
        <button onclick="toggleSidebarCollapse()" class="hidden md:flex p-1.5 rounded-lg hover:bg-white/10 text-white/70 hover:text-white transition" title="Toggle Sidebar">
          <i data-lucide="chevron-left" id="collapse-icon" class="w-4 h-4 transition-transform duration-200"></i>
        </button>
        <button onclick="toggleMobileSidebar()" class="md:hidden p-1.5 rounded-lg hover:bg-white/10 text-white transition">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Navigation Menu Items -->
      <nav class="flex-1 px-3 py-4 space-y-6">
        <!-- Overview Link -->
        <div>
          <a href="/dashboard" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
            <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
            <span class="sidebar-label">Overview</span>
          </a>
        </div>

        <!-- Hotel Management Section (Visible for Hotel & Both) -->
        <div id="nav-hotel-section" class="hotel-only-nav">
          <div class="sidebar-section-title px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-amber-400/80">Hotel PMS</div>
          <div class="space-y-1">
            <a href="/dashboard/hotel/rooms" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/hotel/rooms*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="bed-double" class="w-4 h-4 shrink-0 text-amber-400"></i>
              <span class="sidebar-label">Rooms & Grid</span>
            </a>
            <a href="/dashboard/hotel/guests" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/hotel/guests*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="users" class="w-4 h-4 shrink-0 text-amber-400"></i>
              <span class="sidebar-label">Guests & Stays</span>
            </a>
            <a href="/dashboard/hotel/services" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/hotel/services*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="bell" class="w-4 h-4 shrink-0 text-amber-400"></i>
              <span class="sidebar-label">Housekeeping</span>
            </a>
            <a href="/dashboard/hotel/partner" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/hotel/partner*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="link" class="w-4 h-4 shrink-0 text-amber-400"></i>
              <span class="sidebar-label">Partner Dining</span>
            </a>
          </div>
        </div>

        <!-- Dining & Kitchen Operations (Visible for Restaurant & Both) -->
        <div id="nav-restaurant-section" class="restaurant-only-nav">
          <div class="sidebar-section-title px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-white/40">Dining & Kitchen</div>
          <div class="space-y-1">
            <a href="/dashboard/orders" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/orders*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="shopping-bag" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label">Live Orders</span>
            </a>
            <a href="/dashboard/kds" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/kds*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="chef-hat" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label">Kitchen KDS</span>
            </a>
            <a href="/dashboard/billing" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/billing*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="credit-card" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label">Table Billing</span>
            </a>
            <a href="/dashboard/menu" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/menu*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="utensils-crossed" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label">Menu Catalog</span>
            </a>
            <a href="/dashboard/tables" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/tables*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="qr-code" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label">Tables & QR</span>
            </a>
          </div>
        </div>

        <!-- Analytics & Settings -->
        <div>
          <div class="sidebar-section-title px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-white/40">Analytics & Admin</div>
          <div class="space-y-1">
            <a href="/dashboard/reports" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/reports*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="trending-up" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label">Reports</span>
            </a>
            <a href="/dashboard/staff" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/staff*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label">Team & Staff</span>
            </a>
            <a href="/dashboard/settings" class="nav-link flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('dashboard/settings*') ? 'bg-[var(--bg-sidebar-active)] text-[var(--text-sidebar-active)] font-semibold' : 'text-white/80 hover:bg-[var(--bg-sidebar-hover)] hover:text-white' }}">
              <i data-lucide="settings" class="w-4 h-4 shrink-0"></i>
              <span class="sidebar-label">Settings</span>
            </a>
          </div>
        </div>
      </nav>

      <!-- Venue Info Footer -->
      <div class="p-4 border-t border-[var(--border-sidebar)] text-xs text-white/60 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-[var(--accent-2)]/20 text-[var(--accent-2)] flex items-center justify-center font-bold shrink-0">
          <span id="sidebar-avatar-initial">Q</span>
        </div>
        <div class="overflow-hidden sidebar-label">
          <p id="sidebar-restaurant-name" class="font-semibold text-white truncate">My Venue</p>
          <p id="sidebar-username" class="text-[10px] text-white/50 truncate">@owner</p>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Content Body -->
  <div class="flex-1 flex flex-col min-w-0 bg-[var(--bg-base)]">
    <!-- Topbar Header -->
    <header class="sticky top-0 z-30 flex items-center justify-between px-4 md:px-8 py-3 bg-[var(--bg-topbar)] backdrop-blur-md border-b border-[var(--border)] transition-colors">
      <div class="flex items-center gap-3">
        <button onclick="toggleMobileSidebar()" class="md:hidden p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] transition">
          <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
        <div>
          <h1 class="font-serif font-bold text-xl md:text-2xl text-[var(--text-primary)] tracking-tight">
            @yield('page_title', 'Dashboard')
          </h1>
          <span id="business-type-badge" class="hidden sm:inline-block text-[10px] font-bold text-[var(--accent)] uppercase tracking-wider">RESTAURANT</span>
        </div>
      </div>

      <!-- Topbar Actions -->
      <div class="flex items-center gap-3">
        <!-- Theme Toggle -->
        <button onclick="toggleTheme()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Toggle Theme">
          <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
          <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
        </button>

        <!-- Sound Alert Indicator -->
        <button onclick="window.playNotificationSound()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Test Notification Chime">
          <i data-lucide="volume-2" class="w-4 h-4"></i>
        </button>

        <!-- User Profile Dropdown -->
        <div class="relative" id="profile-dropdown-container">
          <button onclick="toggleProfileDropdown()" class="flex items-center gap-2 p-1.5 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] transition">
            <div class="w-7 h-7 rounded-full bg-[var(--accent)] text-white flex items-center justify-center text-xs font-bold font-serif">
              <span id="topbar-avatar-initial">Q</span>
            </div>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
          </button>

          <!-- Dropdown Menu -->
          <div id="profile-dropdown" class="absolute right-0 mt-2 w-56 bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-lg)] shadow-[var(--shadow-lg)] py-2 hidden z-50">
            <div class="px-4 py-2 border-b border-[var(--border)]">
              <p class="text-xs font-bold text-[var(--text-primary)]" id="dropdown-owner-name">Owner</p>
              <p class="text-[10px] text-[var(--text-muted)] truncate" id="dropdown-email">owner@example.com</p>
            </div>
            <a href="/dashboard/settings" class="flex items-center gap-2 px-4 py-2 text-xs text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] hover:text-[var(--text-primary)]">
              <i data-lucide="settings" class="w-3.5 h-3.5"></i>
              <span>Venue Settings</span>
            </a>
            <button onclick="openChangePasswordModal()" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] hover:text-[var(--text-primary)]">
              <i data-lucide="lock" class="w-3.5 h-3.5"></i>
              <span>Change Password</span>
            </button>
            <div class="border-t border-[var(--border)] my-1"></div>
            <button onclick="logoutOwner()" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-red-600 hover:bg-red-500/10">
              <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
              <span>Sign Out</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Dashboard Body Content -->
    <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto">
      @yield('dashboard_content')
    </main>
  </div>
</div>

<!-- Change Password Modal -->
<div id="password-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-md p-6">
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
      <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Change Password</h3>
      <button onclick="closeChangePasswordModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <form onsubmit="submitChangePassword(event)" class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Current Password</label>
        <input type="password" id="modal-curr-pass" required class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
      </div>
      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">New Password (min 6 characters)</label>
        <input type="password" id="modal-new-pass" required minlength="6" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
      </div>
      <div id="modal-pass-error" class="text-xs text-red-500 hidden"></div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeChangePasswordModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Cancel</button>
        <button type="submit" id="modal-pass-btn" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm">Update Password</button>
      </div>
    </form>
  </div>
</div>

<script>
  let isSidebarCollapsed = false;
  let activeOwnerId = null;

  document.addEventListener('DOMContentLoaded', async () => {
    const token = window.getAuthToken();
    if (!token) {
      window.location.href = '/auth/login';
      return;
    }

    try {
      const res = await fetch('/api/auth/profile', {
        headers: { 'Authorization': 'Bearer ' + token }
      });
      const data = await res.json();
      if (!data.success || !data.data) {
        localStorage.removeItem('qrestro_token');
        window.location.href = '/auth/login';
        return;
      }

      const u = data.data;
      activeOwnerId = u.id;

      // Update Topbar & Sidebar info
      document.getElementById('sidebar-restaurant-name').textContent = u.restaurantName || 'Vyora Property';
      document.getElementById('sidebar-username').textContent = '@' + u.username;
      document.getElementById('dropdown-owner-name').textContent = u.ownerName || u.restaurantName;
      document.getElementById('dropdown-email').textContent = u.email;

      const initial = (u.restaurantName || 'Q').charAt(0).toUpperCase();
      document.getElementById('sidebar-avatar-initial').textContent = initial;
      document.getElementById('topbar-avatar-initial').textContent = initial;

      const badge = document.getElementById('business-type-badge');
      if (badge) {
        badge.textContent = (u.businessType || 'RESTAURANT') + ' PMS';
      }

      // Adaptive Navigation based on Business Type
      const bType = u.businessType || 'RESTAURANT';
      const hotelSection = document.getElementById('nav-hotel-section');
      const restSection = document.getElementById('nav-restaurant-section');

      if (bType === 'HOTEL') {
        if (restSection) restSection.style.display = 'none';
        if (hotelSection) hotelSection.style.display = 'block';
      } else if (bType === 'RESTAURANT') {
        if (hotelSection) hotelSection.style.display = 'none';
        if (restSection) restSection.style.display = 'block';
      } else {
        // BOTH
        if (hotelSection) hotelSection.style.display = 'block';
        if (restSection) restSection.style.display = 'block';
      }

      // Init Pusher Broadcast Subscription for Restaurant / Hotel Room events
      initPusherForOwner(u.id);

    } catch (err) {
      console.error(err);
    }
  });

  function initPusherForOwner(ownerId) {
    if (window.PUSHER_KEY && ownerId) {
      const pusher = new Pusher(window.PUSHER_KEY, {
        cluster: window.PUSHER_CLUSTER || 'mt1',
      });
      const channel = pusher.subscribe('restaurant.' + ownerId);

      channel.bind('order:new', function(data) {
        window.playNotificationSound();
        window.showToast(`🔔 New ${data.orderType === 'room_service' ? 'Room #' + data.roomNumber : 'Table #' + data.tableNumber} Order!`);
        window.dispatchEvent(new CustomEvent('qrestro:order:new', { detail: data }));
      });

      channel.bind('order:updated', function(data) {
        window.dispatchEvent(new CustomEvent('qrestro:order:updated', { detail: data }));
      });

      channel.bind('service:request', function(data) {
        window.playNotificationSound();
        const loc = data.roomNumber ? `Room #${data.roomNumber}` : `Table #${data.tableNumber}`;
        window.showToast(`🛎️ ${loc}: ${data.serviceName || data.type}`);
        window.dispatchEvent(new CustomEvent('qrestro:service:request', { detail: data }));
      });

      channel.bind('room:status', function(data) {
        window.dispatchEvent(new CustomEvent('qrestro:room:status', { detail: data }));
      });

      channel.bind('table:reset', function(data) {
        window.dispatchEvent(new CustomEvent('qrestro:table:reset', { detail: data }));
      });
    }
  }

  function toggleMobileSidebar() {
    const sb = document.getElementById('main-sidebar');
    const ov = document.getElementById('mobile-overlay');
    sb.classList.toggle('-translate-x-full');
    ov.classList.toggle('hidden');
  }

  function toggleSidebarCollapse() {
    isSidebarCollapsed = !isSidebarCollapsed;
    const sb = document.getElementById('main-sidebar');
    const labels = document.querySelectorAll('.sidebar-label');
    const icon = document.getElementById('collapse-icon');

    if (isSidebarCollapsed) {
      sb.style.width = '70px';
      labels.forEach(l => l.style.display = 'none');
      if (icon) icon.style.transform = 'rotate(180deg)';
    } else {
      sb.style.width = 'var(--sidebar-width)';
      labels.forEach(l => l.style.display = '');
      if (icon) icon.style.transform = 'rotate(0deg)';
    }
  }

  function toggleProfileDropdown() {
    const dd = document.getElementById('profile-dropdown');
    dd.classList.toggle('hidden');
  }

  document.addEventListener('click', (e) => {
    const container = document.getElementById('profile-dropdown-container');
    const dd = document.getElementById('profile-dropdown');
    if (container && !container.contains(e.target) && dd) {
      dd.classList.add('hidden');
    }
  });

  function logoutOwner() {
    localStorage.removeItem('qrestro_token');
    window.location.href = '/auth/login';
  }

  function openChangePasswordModal() {
    document.getElementById('password-modal').classList.remove('hidden');
  }

  function closeChangePasswordModal() {
    document.getElementById('password-modal').classList.add('hidden');
    document.getElementById('modal-curr-pass').value = '';
    document.getElementById('modal-new-pass').value = '';
    document.getElementById('modal-pass-error').classList.add('hidden');
  }

  async function submitChangePassword(e) {
    e.preventDefault();
    const curr = document.getElementById('modal-curr-pass').value;
    const next = document.getElementById('modal-new-pass').value;
    const btn = document.getElementById('modal-pass-btn');
    const err = document.getElementById('modal-pass-error');

    btn.disabled = true;
    err.classList.add('hidden');

    try {
      const res = await fetch('/api/auth/change-password', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ currentPassword: curr, newPassword: next })
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Password updated successfully!');
        closeChangePasswordModal();
      } else {
        err.textContent = data.error?.message || 'Failed to update password';
        err.classList.remove('hidden');
      }
    } catch(e) {
      err.textContent = 'Network error. Please try again.';
      err.classList.remove('hidden');
    } finally {
      btn.disabled = false;
    }
  }
</script>
@endsection
