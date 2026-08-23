@extends('layouts.app')

@section('content')
<div class="flex min-h-screen relative bg-[var(--bg-base)]" id="superadmin-wrapper">
  <!-- Mobile Sidebar Backdrop Overlay -->
  <div id="mobile-overlay" class="fixed inset-0 bg-black/60 z-40 md:hidden hidden transition-opacity duration-300 backdrop-blur-sm" onclick="toggleMobileSidebar()"></div>

  <!-- Sidebar Navigation -->
  <aside id="main-sidebar" class="fixed md:sticky top-0 left-0 h-screen z-50 flex flex-col justify-between bg-[var(--bg-surface)] border-r border-[var(--border)] transition-transform duration-300 -translate-x-full md:translate-x-0 w-[260px] shadow-2xl md:shadow-none">
    <div class="flex flex-col h-full overflow-y-auto">
      <!-- Brand Logo Header -->
      <div class="p-5 border-b border-[var(--border)] flex items-center justify-between">
        <a href="/superadmin" class="flex items-center gap-2.5 font-bold font-serif text-xl text-[var(--text-primary)]">
          <div class="w-8 h-8 rounded-lg bg-[var(--accent)] text-white flex items-center justify-center font-bold font-serif shadow-sm">V</div>
          <span class="tracking-tight">Vyora Admin</span>
        </a>
        <button onclick="toggleMobileSidebar()" class="md:hidden p-1.5 rounded-lg hover:bg-[var(--bg-hover)] text-[var(--text-muted)] hover:text-[var(--text-primary)] transition">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="p-3 space-y-1.5 flex-1">
        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Platform Management</div>
        
        <a href="/superadmin" class="flex items-center gap-3 px-3 py-2.5 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('superadmin') ? 'bg-[var(--accent)] text-white font-semibold shadow-sm' : 'text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] hover:text-[var(--text-primary)]' }}">
          <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
          <span>Overview</span>
        </a>

        <a href="/superadmin/restaurants" class="flex items-center gap-3 px-3 py-2.5 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('superadmin/restaurants*') ? 'bg-[var(--accent)] text-white font-semibold shadow-sm' : 'text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] hover:text-[var(--text-primary)]' }}">
          <i data-lucide="store" class="w-4 h-4 shrink-0"></i>
          <span>Tenants & Venues</span>
        </a>

        <a href="/superadmin/settings" class="flex items-center gap-3 px-3 py-2.5 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('superadmin/settings*') ? 'bg-[var(--accent)] text-white font-semibold shadow-sm' : 'text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] hover:text-[var(--text-primary)]' }}">
          <i data-lucide="sliders" class="w-4 h-4 shrink-0"></i>
          <span>SaaS Customizations</span>
        </a>

        <div class="pt-4 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Live Operations</div>
        
        <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-[var(--radius-md)] text-xs font-medium text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] hover:text-[var(--text-primary)] transition">
          <i data-lucide="external-link" class="w-3.5 h-3.5 shrink-0 text-[var(--accent)]"></i>
          <span>Go to Venue Portal</span>
        </a>
      </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-[var(--border)]">
      <button onclick="handleLogout()" class="w-full flex items-center gap-2 px-3 py-2.5 rounded-[var(--radius-sm)] text-xs text-red-600 hover:bg-red-500/10 transition font-semibold">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        <span>Sign Out</span>
      </button>
    </div>
  </aside>

  <!-- Main Content Area -->
  <div class="flex-1 flex flex-col min-w-0 pb-20 md:pb-8">
    <!-- Topbar Header -->
    <header class="sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 md:px-8 py-3.5 bg-[var(--bg-surface)]/90 backdrop-blur-md border-b border-[var(--border)]">
      <div class="flex items-center gap-3">
        <button onclick="toggleMobileSidebar()" class="md:hidden p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] transition" title="Open Menu">
          <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
        <div>
          <h1 class="text-base sm:text-lg md:text-xl font-bold font-serif text-[var(--text-primary)] tracking-tight">
            @yield('page_title', 'Platform Overview')
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-2 sm:gap-3">
        <!-- Theme Toggle -->
        <button onclick="toggleTheme()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Toggle Theme">
          <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
          <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
        </button>

        <!-- Admin Badge -->
        <div class="px-2.5 py-1 bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-[10px] sm:text-xs rounded-full border border-[var(--accent)]/20 uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-[var(--accent)]"></span>
          <span>Super Admin</span>
        </div>
      </div>
    </header>

    <!-- Main View Body -->
    <main class="flex-1 p-3.5 sm:p-6 md:p-8 max-w-7xl w-full mx-auto">
      @yield('superadmin_content')
    </main>
  </div>

  <!-- Mobile Bottom Navigation Bar -->
  <div class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-[var(--bg-surface)]/95 backdrop-blur-md border-t border-[var(--border)] h-16 flex items-center justify-around px-2 shadow-lg">
    <a href="/superadmin" class="flex flex-col items-center gap-1 py-1 px-3 text-[10px] font-medium transition {{ request()->is('superadmin') ? 'text-[var(--accent)] font-bold' : 'text-[var(--text-muted)]' }}">
      <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
      <span>Overview</span>
    </a>
    <a href="/superadmin/restaurants" class="flex flex-col items-center gap-1 py-1 px-3 text-[10px] font-medium transition {{ request()->is('superadmin/restaurants*') ? 'text-[var(--accent)] font-bold' : 'text-[var(--text-muted)]' }}">
      <i data-lucide="store" class="w-4 h-4"></i>
      <span>Tenants</span>
    </a>
    <a href="/superadmin/settings" class="flex flex-col items-center gap-1 py-1 px-3 text-[10px] font-medium transition {{ request()->is('superadmin/settings*') ? 'text-[var(--accent)] font-bold' : 'text-[var(--text-muted)]' }}">
      <i data-lucide="sliders" class="w-4 h-4"></i>
      <span>SaaS Settings</span>
    </a>
    <button onclick="toggleMobileSidebar()" class="flex flex-col items-center gap-1 py-1 px-3 text-[10px] font-medium text-[var(--text-muted)]">
      <i data-lucide="menu" class="w-4 h-4"></i>
      <span>More</span>
    </button>
  </div>
</div>

<script>
  (function initSuperAdminAuth() {
    const token = localStorage.getItem('token');
    const ownerStr = localStorage.getItem('owner');
    if (!token || !ownerStr) {
      window.location.href = '/auth/login';
      return;
    }
    try {
      const owner = JSON.parse(ownerStr);
      if (owner.role !== 'SUPER_ADMIN') {
        window.location.href = '/dashboard';
      }
    } catch (e) {
      window.location.href = '/auth/login';
    }
  })();

  function toggleMobileSidebar() {
    const sb = document.getElementById('main-sidebar');
    const ov = document.getElementById('mobile-overlay');
    sb.classList.toggle('-translate-x-full');
    ov.classList.toggle('hidden');
  }

  function toggleTheme() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    if (isDark) {
      document.documentElement.removeAttribute('data-theme');
      localStorage.setItem('theme', 'light');
    } else {
      document.documentElement.setAttribute('data-theme', 'dark');
      localStorage.setItem('theme', 'dark');
    }
  }

  function handleLogout() {
    localStorage.removeItem('token');
    localStorage.removeItem('owner');
    window.location.href = '/auth/login';
  }
</script>
@endsection
