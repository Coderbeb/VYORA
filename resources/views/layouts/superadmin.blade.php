@extends('layouts.app')

@section('content')
<div class="flex min-h-screen relative" id="superadmin-wrapper">
  <!-- Sidebar -->
  <aside class="w-[240px] bg-[var(--bg-surface)] border-r border-[var(--border)] flex flex-col justify-between h-screen sticky top-0">
    <div>
      <div class="p-5 border-b border-[var(--border)] flex items-center justify-between">
        <a href="/superadmin" class="flex items-center gap-2 font-bold font-serif text-lg text-[var(--accent)]">
          <div class="w-8 h-8 rounded-lg bg-[var(--accent)] text-white flex items-center justify-center font-bold font-serif">V</div>
          <span>Vyora Admin</span>
        </a>
      </div>
      <nav class="p-3 space-y-1">
        <a href="/superadmin" class="flex items-center gap-3 px-3 py-2.5 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('superadmin') ? 'bg-[var(--accent)] text-white font-semibold' : 'text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]' }}">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Overview</span>
        </a>
        <a href="/superadmin/restaurants" class="flex items-center gap-3 px-3 py-2.5 rounded-[var(--radius-md)] text-sm font-medium transition {{ request()->is('superadmin/restaurants*') ? 'bg-[var(--accent)] text-white font-semibold' : 'text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]' }}">
          <i data-lucide="store" class="w-4 h-4"></i>
          <span>Restaurants</span>
        </a>
      </nav>
    </div>

    <div class="p-4 border-t border-[var(--border)]">
      <button onclick="handleLogout()" class="w-full flex items-center gap-2 px-3 py-2 rounded-[var(--radius-sm)] text-xs text-red-600 hover:bg-red-500/10 transition font-semibold">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        <span>Sign Out</span>
      </button>
    </div>
  </aside>

  <!-- Main Area -->
  <div class="flex-1 flex flex-col min-w-0">
    <header class="sticky top-0 z-20 flex items-center justify-between px-8 py-4 bg-[var(--bg-surface)] border-b border-[var(--border)]">
      <h1 class="text-lg font-bold font-serif">@yield('page_title', 'Platform Overview')</h1>
      <div class="flex items-center gap-3">
        <button onclick="toggleTheme()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">
          <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
          <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
        </button>
        <div class="px-3 py-1 bg-[var(--accent-glow)] text-[var(--accent)] font-semibold text-xs rounded-full border border-[var(--accent)]/20">
          Root Admin
        </div>
      </div>
    </header>

    <main class="flex-1 p-8 max-w-7xl w-full mx-auto">
      @yield('superadmin_content')
    </main>
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
