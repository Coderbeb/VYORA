@extends('layouts.app')

@section('title', 'Sign In - Vyora')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center p-4 bg-[var(--bg-base)]">
  <div class="w-full max-w-md">
    <!-- Header Brand -->
    <div class="text-center mb-8">
      <a href="/" class="inline-flex items-center gap-2.5 mb-3">
        <div class="w-10 h-10 rounded-xl bg-[var(--accent)] text-white flex items-center justify-center font-bold text-xl shadow-sm font-serif">
          V
        </div>
        <span class="font-serif font-bold text-3xl tracking-tight text-[var(--text-primary)]">Vyora</span>
      </a>
      <p class="text-xs text-[var(--text-secondary)]">Sign in to manage your dining tables, hotel rooms, and orders.</p>
    </div>

    <!-- Login Card -->
    <div class="p-8 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-[var(--shadow-lg)]">
      <!-- Segmented Role Selector Tabs -->
      <div class="grid grid-cols-2 p-1 mb-5 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] text-xs font-semibold">
        <button type="button" id="tab-owner" onclick="switchLoginTab('owner')" class="py-2 px-3 rounded-[var(--radius-sm)] transition-all flex items-center justify-center gap-1.5 bg-[var(--bg-surface)] text-[var(--text-primary)] shadow-xs font-bold cursor-pointer">
          <i data-lucide="building-2" class="w-3.5 h-3.5 text-[var(--accent)]"></i>
          <span>Venue Owner</span>
        </button>
        <button type="button" id="tab-staff" onclick="switchLoginTab('staff')" class="py-2 px-3 rounded-[var(--radius-sm)] transition-all flex items-center justify-center gap-1.5 text-[var(--text-secondary)] hover:text-[var(--text-primary)] cursor-pointer">
          <i data-lucide="user-check" class="w-3.5 h-3.5 text-[var(--accent-2)]"></i>
          <span>Staff Station</span>
        </button>
      </div>

      <form onsubmit="handleLogin(event)" class="space-y-4">
        <!-- Venue ID container (displayed on Staff Station tab) -->
        <div id="staff-venue-container" class="hidden">
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-semibold text-[var(--text-secondary)]">Venue / Restaurant ID</label>
            <span class="text-[10px] text-[var(--text-muted)]">e.g. spicehub</span>
          </div>
          <input type="text" id="login-venue" placeholder="e.g. spicehub or grandhorizon" class="w-full px-3.5 py-2.5 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)] transition">
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5" id="username-label">Username</label>
          <input type="text" id="login-username" required placeholder="e.g. spicehub or grandhorizon" class="w-full px-3.5 py-2.5 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)] transition">
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-semibold text-[var(--text-secondary)]" id="password-label">Password</label>
            <span class="text-[10px] text-[var(--text-muted)]" id="password-hint"></span>
          </div>
          <div class="relative">
            <input type="password" id="login-password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)] transition">
            <button type="button" onclick="togglePassVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)] hover:text-[var(--text-primary)]">
              <i data-lucide="eye" id="pass-eye-icon" class="w-4 h-4"></i>
            </button>
          </div>
        </div>

        <div id="login-error" class="p-3 rounded-[var(--radius-sm)] bg-red-500/10 border border-red-500/20 text-xs text-red-600 hidden"></div>

        <button type="submit" id="login-btn" class="w-full py-3 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-sm shadow-sm transition flex items-center justify-center gap-2 cursor-pointer">
          <span id="login-btn-text">Sign In as Owner</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
      </form>
    </div>

    <!-- Footer Register CTA -->
    <p class="text-center text-xs text-[var(--text-secondary)] mt-6">
      Don't have an account yet?
      <a href="/auth/register" class="font-bold text-[var(--accent)] hover:underline ml-1">Register now</a>
    </p>
  </div>
</div>

<script>
  let currentLoginTab = 'owner';

  function switchLoginTab(tab) {
    currentLoginTab = tab;
    const tabOwner = document.getElementById('tab-owner');
    const tabStaff = document.getElementById('tab-staff');
    const venueField = document.getElementById('staff-venue-container');
    const venueInput = document.getElementById('login-venue');
    const userLabel = document.getElementById('username-label');
    const userInput = document.getElementById('login-username');
    const passLabel = document.getElementById('password-label');
    const passHint = document.getElementById('password-hint');
    const btnText = document.getElementById('login-btn-text');
    const errDiv = document.getElementById('login-error');
    errDiv.classList.add('hidden');

    if (tab === 'staff') {
      tabStaff.className = 'py-2 px-3 rounded-[var(--radius-sm)] transition-all flex items-center justify-center gap-1.5 bg-[var(--bg-surface)] text-[var(--text-primary)] shadow-xs font-bold cursor-pointer';
      tabOwner.className = 'py-2 px-3 rounded-[var(--radius-sm)] transition-all flex items-center justify-center gap-1.5 text-[var(--text-secondary)] hover:text-[var(--text-primary)] cursor-pointer';
      venueField.classList.remove('hidden');
      venueInput.required = true;
      userLabel.textContent = 'Staff Username or PIN';
      userInput.placeholder = 'e.g. waiter_rahul, chef_suresh, or 1234';
      passLabel.textContent = 'Staff PIN or Password';
      passHint.textContent = 'Default PIN: 1234';
      btnText.textContent = 'Sign In to Station';

      const savedVenue = localStorage.getItem('vyora_staff_venue');
      if (savedVenue) {
        venueInput.value = savedVenue;
      }
    } else {
      tabOwner.className = 'py-2 px-3 rounded-[var(--radius-sm)] transition-all flex items-center justify-center gap-1.5 bg-[var(--bg-surface)] text-[var(--text-primary)] shadow-xs font-bold cursor-pointer';
      tabStaff.className = 'py-2 px-3 rounded-[var(--radius-sm)] transition-all flex items-center justify-center gap-1.5 text-[var(--text-secondary)] hover:text-[var(--text-primary)] cursor-pointer';
      venueField.classList.add('hidden');
      venueInput.required = false;
      userLabel.textContent = 'Username';
      userInput.placeholder = 'e.g. spicehub or grandhorizon';
      passLabel.textContent = 'Password';
      passHint.textContent = '';
      btnText.textContent = 'Sign In as Owner';
    }
    lucide.createIcons();
  }

  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('tab') === 'staff') {
      switchLoginTab('staff');
    }
  });

  function togglePassVisibility() {
    const input = document.getElementById('login-password');
    const icon = document.getElementById('pass-eye-icon');
    if (input.type === 'password') {
      input.type = 'text';
      icon.setAttribute('data-lucide', 'eye-off');
    } else {
      input.type = 'password';
      icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
  }

  async function handleLogin(e) {
    e.preventDefault();
    const u = document.getElementById('login-username').value.trim();
    const p = document.getElementById('login-password').value;
    const venue = currentLoginTab === 'staff' ? document.getElementById('login-venue').value.trim() : '';
    const btn = document.getElementById('login-btn');
    const errDiv = document.getElementById('login-error');

    if (venue) {
      localStorage.setItem('vyora_staff_venue', venue);
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Signing in...';
    errDiv.classList.add('hidden');

    try {
      const res = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ username: u, password: p, venue: venue }),
      });
      const data = await res.json();

      if (data.success && data.data) {
        localStorage.setItem('token', data.data.token);
        localStorage.setItem('owner', JSON.stringify(data.data.owner));
        document.cookie = `token=${data.data.token}; path=/; max-age=86400; SameSite=Lax`;

        const role = (data.data.owner.role || '').toUpperCase();
        const bType = data.data.owner.businessType;

        if (role === 'SUPER_ADMIN') {
          window.location.href = '/superadmin';
        } else if (role === 'CHEF') {
          window.location.href = '/dashboard/kds';
        } else if (role === 'WAITER') {
          window.location.href = '/dashboard/orders';
        } else if (role === 'CASHIER') {
          window.location.href = '/dashboard/billing';
        } else if (role === 'HOUSEKEEPING') {
          window.location.href = '/dashboard/hotel/services';
        } else if (role === 'FRONT_DESK') {
          window.location.href = '/dashboard/hotel/rooms';
        } else if (bType === 'HOTEL') {
          window.location.href = '/dashboard/hotel/rooms';
        } else {
          window.location.href = '/dashboard';
        }
      } else {
        errDiv.textContent = data.error?.message || 'Invalid username or password';
        errDiv.classList.remove('hidden');
      }
    } catch (err) {
      errDiv.textContent = 'Server connection error. Please try again.';
      errDiv.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      const defaultText = currentLoginTab === 'staff' ? 'Sign In to Station' : 'Sign In as Owner';
      btn.innerHTML = `<span id="login-btn-text">${defaultText}</span><i data-lucide="arrow-right" class="w-4 h-4"></i>`;
      lucide.createIcons();
    }
  }
</script>
@endsection
