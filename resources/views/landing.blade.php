@extends('layouts.app')

@section('title', 'Vyora - Unified OS for Stays & Dining')

@section('content')
<div class="min-h-screen flex flex-col selection:bg-[var(--accent)] selection:text-white">
  <!-- Navbar -->
  <header class="sticky top-0 z-40 bg-[var(--bg-base)]/80 backdrop-blur-md border-b border-[var(--border)] transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="/" class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-[var(--accent)] text-white flex items-center justify-center font-bold text-lg shadow-sm font-serif">
            V
          </div>
          <span class="font-serif font-bold text-2xl tracking-tight text-[var(--text-primary)]">Vyora</span>
        </a>
      </div>

      <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-[var(--text-secondary)]">
        <a href="#features" class="hover:text-[var(--text-primary)] transition">Features</a>
        <a href="#partners" class="hover:text-[var(--text-primary)] transition">Restaurants</a>
        <a href="#pricing" class="hover:text-[var(--text-primary)] transition">Pricing</a>
      </nav>

      <div class="flex items-center gap-3">
        <a href="/auth/login" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] transition">
          Sign In
        </a>
        <a href="/auth/register" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm transition">
          Get Started
        </a>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="relative pt-12 pb-20 md:pt-20 md:pb-28 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[var(--accent-glow)] border border-[var(--accent)]/20 text-[var(--accent)] text-xs font-bold mb-6">
        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
        <span>The Modern Hospitality OS</span>
      </div>

      <h1 class="font-serif text-4xl sm:text-6xl md:text-7xl font-bold tracking-tight text-[var(--text-primary)] max-w-4xl mx-auto leading-[1.1] mb-6">
        Elevate Dining with <span class="italic font-normal text-[var(--accent)]">Effortless</span> QR Ordering.
      </h1>

      <p class="text-base sm:text-lg md:text-xl text-[var(--text-secondary)] max-w-2xl mx-auto mb-10 leading-relaxed">
        Empower your guests to scan, order, and pay directly from their tables. Synchronize kitchen tickets to KDS and settle bills instantly.
      </p>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto mb-16">
        <a href="/auth/register" class="w-full sm:w-auto px-8 py-3.5 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-sm shadow-[var(--shadow-md)] transition flex items-center justify-center gap-2">
          <span>Start Free Today</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
        <a href="#features" class="w-full sm:w-auto px-8 py-3.5 rounded-[var(--radius-md)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] font-semibold text-sm transition flex items-center justify-center gap-2">
          <span>Explore Features</span>
        </a>
      </div>

      <!-- Feature Highlights Bento -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto text-left">
        <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
          <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-3">
            <i data-lucide="qr-code" class="w-4 h-4"></i>
          </div>
          <h3 class="font-bold text-sm text-[var(--text-primary)] mb-1">Instant QR Menus</h3>
          <p class="text-xs text-[var(--text-secondary)]">No app downloads required for diners.</p>
        </div>
        <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
          <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center mb-3">
            <i data-lucide="chef-hat" class="w-4 h-4"></i>
          </div>
          <h3 class="font-bold text-sm text-[var(--text-primary)] mb-1">Kitchen KDS</h3>
          <p class="text-xs text-[var(--text-secondary)]">Real-time FIFO kitchen dispatch screen.</p>
        </div>
        <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
          <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center mb-3">
            <i data-lucide="credit-card" class="w-4 h-4"></i>
          </div>
          <h3 class="font-bold text-sm text-[var(--text-primary)] mb-1">Table Billing</h3>
          <p class="text-xs text-[var(--text-secondary)]">Session order grouping & thermal receipts.</p>
        </div>
        <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
          <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center mb-3">
            <i data-lucide="trending-up" class="w-4 h-4"></i>
          </div>
          <h3 class="font-bold text-sm text-[var(--text-primary)] mb-1">Live Analytics</h3>
          <p class="text-xs text-[var(--text-secondary)]">Ticket averages and sales performance.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Detailed Section -->
  <section id="features" class="py-20 bg-[var(--bg-surface)] border-y border-[var(--border)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[var(--text-primary)] mb-3">Engineered for Culinary Speed</h2>
        <p class="text-sm text-[var(--text-secondary)]">Every tool is tailored to cut wait times, eliminate order errors, and boost table turnaround.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-base)] border border-[var(--border)] flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-600 flex items-center justify-center mb-4 font-bold">
              <i data-lucide="shopping-bag" class="w-5 h-5"></i>
            </div>
            <h3 class="font-bold text-base text-[var(--text-primary)] mb-2">Kanban Order Processing</h3>
            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">
              Track order lifecycles seamlessly across Pending, Preparing, Ready, and Completed with audio alerts.
            </p>
          </div>
        </div>

        <!-- Feature 2 -->
        <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-base)] border border-[var(--border)] flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-4 font-bold">
              <i data-lucide="utensils-crossed" class="w-5 h-5"></i>
            </div>
            <h3 class="font-bold text-base text-[var(--text-primary)] mb-2">Dynamic Menu Control</h3>
            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">
              Update 86'd items, adjust prices, organize course categories, and upload appetizing food photography in seconds.
            </p>
          </div>
        </div>

        <!-- Feature 3 -->
        <div class="p-6 rounded-[var(--radius-xl)] bg-[var(--bg-base)] border border-[var(--border)] flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center mb-4 font-bold">
              <i data-lucide="bell" class="w-5 h-5"></i>
            </div>
            <h3 class="font-bold text-base text-[var(--text-primary)] mb-2">Instant Waiter & Water Calls</h3>
            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">
              Customers can request service or chilled water right from their smartphones with built-in cooldowns.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Partner Restaurants Showcase -->
  <section id="partners" class="py-20 bg-[var(--bg-base)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-12">
        <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[var(--text-primary)] mb-3">Trusted by Premier Venues</h2>
        <p class="text-sm text-[var(--text-secondary)]">Join hundreds of bustling boutique hotels, resorts, cafes, and restaurants powered by Vyora.</p>
      </div>

      <div id="partners-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
        <!-- Loaded dynamically or fallback -->
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] text-center">
          <div class="w-10 h-10 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-sm flex items-center justify-center mx-auto mb-2">S</div>
          <p class="font-bold text-xs text-[var(--text-primary)] truncate">SpiceHub</p>
          <p class="text-[10px] text-[var(--text-muted)] truncate">Spice & Tandoor</p>
        </div>
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] text-center">
          <div class="w-10 h-10 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-sm flex items-center justify-center mx-auto mb-2">T</div>
          <p class="font-bold text-xs text-[var(--text-primary)] truncate">Tandoor Tales</p>
          <p class="text-[10px] text-[var(--text-muted)] truncate">Indian Cuisine</p>
        </div>
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] text-center">
          <div class="w-10 h-10 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-sm flex items-center justify-center mx-auto mb-2">U</div>
          <p class="font-bold text-xs text-[var(--text-primary)] truncate">Urban Bites</p>
          <p class="text-[10px] text-[var(--text-muted)] truncate">Cafe & Kitchen</p>
        </div>
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] text-center">
          <div class="w-10 h-10 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-sm flex items-center justify-center mx-auto mb-2">C</div>
          <p class="font-bold text-xs text-[var(--text-primary)] truncate">Curry House</p>
          <p class="text-[10px] text-[var(--text-muted)] truncate">Traditional Curry</p>
        </div>
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] text-center">
          <div class="w-10 h-10 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-sm flex items-center justify-center mx-auto mb-2">F</div>
          <p class="font-bold text-xs text-[var(--text-primary)] truncate">Food Fiesta</p>
          <p class="text-[10px] text-[var(--text-muted)] truncate">Multi Cuisine</p>
        </div>
        <div class="p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] text-center">
          <div class="w-10 h-10 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-sm flex items-center justify-center mx-auto mb-2">G</div>
          <p class="font-bold text-xs text-[var(--text-primary)] truncate">The Grill Club</p>
          <p class="text-[10px] text-[var(--text-muted)] truncate">Steakhouse</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section id="pricing" class="py-20 bg-[var(--bg-surface)] border-t border-[var(--border)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[var(--text-primary)] mb-3">Transparent Plans for Every Stage</h2>
        <p class="text-sm text-[var(--text-secondary)]">Start free and scale effortlessly as your table count expands.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
        <!-- Free Plan -->
        <div class="p-8 rounded-[var(--radius-xl)] bg-[var(--bg-base)] border border-[var(--border)] flex flex-col justify-between">
          <div>
            <span class="px-2.5 py-1 rounded-md bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-xs uppercase">Free</span>
            <div class="mt-4 mb-2 flex items-baseline gap-1">
              <span class="font-serif text-4xl font-bold text-[var(--text-primary)]">₹0</span>
              <span class="text-xs text-[var(--text-muted)]">/month</span>
            </div>
            <p class="text-xs text-[var(--text-secondary)] mb-6">Perfect for small bistros & starter cafes.</p>
            <ul class="space-y-3 text-xs text-[var(--text-secondary)] mb-8">
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> Up to 5 Tables</li>
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> Basic QR Ordering</li>
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> Real-time Orders Kanban</li>
            </ul>
          </div>
          <a href="/auth/register" class="w-full py-2.5 text-center rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-xs font-bold text-[var(--text-primary)] transition">Get Started Free</a>
        </div>

        <!-- Pro Plan -->
        <div class="p-8 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border-2 border-[var(--accent)] shadow-[var(--shadow-lg)] relative flex flex-col justify-between">
          <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-[var(--accent)] text-white text-[10px] font-bold tracking-wide uppercase">Most Popular</div>
          <div>
            <span class="px-2.5 py-1 rounded-md bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-xs uppercase">Pro</span>
            <div class="mt-4 mb-2 flex items-baseline gap-1">
              <span class="font-serif text-4xl font-bold text-[var(--text-primary)]">₹499</span>
              <span class="text-xs text-[var(--text-muted)]">/month</span>
            </div>
            <p class="text-xs text-[var(--text-secondary)] mb-6">Ideal for busy high-volume restaurants.</p>
            <ul class="space-y-3 text-xs text-[var(--text-secondary)] mb-8">
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> Up to 25 Tables</li>
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> Kitchen Display System (KDS)</li>
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> Table Billing & Thermal Receipts</li>
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> Advanced Analytics & Reports</li>
            </ul>
          </div>
          <a href="/auth/register" class="w-full py-2.5 text-center rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-xs font-bold text-white shadow-sm transition">Upgrade to Pro</a>
        </div>

        <!-- Premium Plan -->
        <div class="p-8 rounded-[var(--radius-xl)] bg-[var(--bg-base)] border border-[var(--border)] flex flex-col justify-between">
          <div>
            <span class="px-2.5 py-1 rounded-md bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-xs uppercase">Premium</span>
            <div class="mt-4 mb-2 flex items-baseline gap-1">
              <span class="font-serif text-4xl font-bold text-[var(--text-primary)]">₹999</span>
              <span class="text-xs text-[var(--text-muted)]">/month</span>
            </div>
            <p class="text-xs text-[var(--text-secondary)] mb-6">Multi-floor venues & restaurant chains.</p>
            <ul class="space-y-3 text-xs text-[var(--text-secondary)] mb-8">
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> Up to 100 Tables</li>
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> White-Label Branding</li>
              <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-[var(--accent)]"></i> Dedicated Account Support</li>
            </ul>
          </div>
          <a href="/auth/register" class="w-full py-2.5 text-center rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-xs font-bold text-[var(--text-primary)] transition">Contact Sales</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="mt-auto py-10 bg-[var(--bg-base)] border-t border-[var(--border)] text-xs text-[var(--text-muted)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 rounded bg-[var(--accent)] text-white flex items-center justify-center font-bold text-xs font-serif">V</div>
        <span class="font-bold text-[var(--text-primary)] font-serif">Vyora</span>
        <span>© {{ date('Y') }} All rights reserved.</span>
      </div>
      <div class="flex items-center gap-6">
        <a href="/auth/login" class="hover:text-[var(--text-primary)] transition">Sign In</a>
        <a href="/auth/register" class="hover:text-[var(--text-primary)] transition">Register</a>
      </div>
    </div>
  </footer>
</div>

<script>
  // Redirect logged in sessions directly
  (function checkExistingAuth() {
    const token = localStorage.getItem('token');
    const ownerStr = localStorage.getItem('owner');
    if (token && ownerStr) {
      try {
        const owner = JSON.parse(ownerStr);
        if (owner.role === 'SUPER_ADMIN') {
          window.location.href = '/superadmin';
        } else {
          window.location.href = '/dashboard';
        }
      } catch(e) {}
    }
  })();

  // Fetch real featured partners
  (async function loadPartners() {
    try {
      const res = await fetch('/api/public/featured');
      const data = await res.json();
      if (data.success && data.data && data.data.length > 0) {
        const grid = document.getElementById('partners-grid');
        grid.innerHTML = '';
        data.data.forEach(p => {
          const initial = (p.restaurantName || 'R').charAt(0).toUpperCase();
          const card = document.createElement('div');
          card.className = 'p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] text-center';
          card.innerHTML = `
            <div class="w-10 h-10 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-sm flex items-center justify-center mx-auto mb-2">${initial}</div>
            <p class="font-bold text-xs text-[var(--text-primary)] truncate">${p.restaurantName || 'Restaurant'}</p>
            <p class="text-[10px] text-[var(--text-muted)] truncate">${p.cuisine || 'Cuisine'}</p>
          `;
          grid.appendChild(card);
        });
      }
    } catch(e) {}
  })();
</script>
@endsection
