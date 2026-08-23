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
      <form onsubmit="handleLogin(event)" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Username</label>
          <input type="text" id="login-username" required placeholder="e.g. spicehub or grandhorizon" class="w-full px-3.5 py-2.5 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)] transition">
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Password</label>
          <div class="relative">
            <input type="password" id="login-password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)] transition">
            <button type="button" onclick="togglePassVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)] hover:text-[var(--text-primary)]">
              <i data-lucide="eye" id="pass-eye-icon" class="w-4 h-4"></i>
            </button>
          </div>
        </div>

        <div id="login-error" class="p-3 rounded-[var(--radius-sm)] bg-red-500/10 border border-red-500/20 text-xs text-red-600 hidden"></div>

        <button type="submit" id="login-btn" class="w-full py-3 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-sm shadow-sm transition flex items-center justify-center gap-2">
          <span>Sign In</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
      </form>

      <!-- Demo Fill Quick Buttons -->
      <div class="mt-6 pt-6 border-t border-[var(--border)]">
        <p class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-wider text-center mb-3">Quick Demo Credentials</p>
        <div class="grid grid-cols-3 gap-2">
          <button onclick="fillDemo('spicehub', 'demo123')" class="px-2 py-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[11px] font-medium text-[var(--text-secondary)] transition text-center truncate">
            🍽️ Restaurant
          </button>
          <button onclick="fillDemo('grandhorizon', 'demo123')" class="px-2 py-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[11px] font-medium text-[var(--text-secondary)] transition text-center truncate">
            🏨 Hotel PMS
          </button>
          <button onclick="fillDemo('superadmin', 'admin123')" class="px-2 py-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[11px] font-medium text-[var(--text-secondary)] transition text-center truncate">
            🛡️ SuperAdmin
          </button>
        </div>
      </div>
    </div>

    <!-- Footer Register CTA -->
    <p class="text-center text-xs text-[var(--text-secondary)] mt-6">
      Don't have an account yet?
      <a href="/auth/register" class="font-bold text-[var(--accent)] hover:underline ml-1">Register now</a>
    </p>
  </div>
</div>

<script>
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

  function fillDemo(u, p) {
    document.getElementById('login-username').value = u;
    document.getElementById('login-password').value = p;
  }

  async function handleLogin(e) {
    e.preventDefault();
    const u = document.getElementById('login-username').value.trim();
    const p = document.getElementById('login-password').value;
    const btn = document.getElementById('login-btn');
    const errDiv = document.getElementById('login-error');

    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Signing in...';
    errDiv.classList.add('hidden');

    try {
      const res = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ username: u, password: p }),
      });
      const data = await res.json();

      if (data.success && data.data) {
        localStorage.setItem('token', data.data.token);
        localStorage.setItem('owner', JSON.stringify(data.data.owner));
        document.cookie = `token=${data.data.token}; path=/; max-age=86400; SameSite=Lax`;

        if (data.data.owner.role === 'SUPER_ADMIN') {
          window.location.href = '/superadmin';
        } else if (data.data.owner.businessType === 'HOTEL') {
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
      btn.innerHTML = '<span>Sign In</span><i data-lucide="arrow-right" class="w-4 h-4"></i>';
      lucide.createIcons();
    }
  }
</script>
@endsection
