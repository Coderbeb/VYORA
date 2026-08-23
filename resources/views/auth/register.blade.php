@extends('layouts.app')

@section('title', 'Register Venue - Vyora')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center p-4 bg-[var(--bg-base)]">
  <div class="w-full max-w-xl my-8">
    <div class="text-center mb-6">
      <a href="/" class="inline-flex items-center gap-2.5 mb-2">
        <div class="w-10 h-10 rounded-xl bg-[var(--accent)] text-white flex items-center justify-center font-bold text-xl shadow-sm font-serif">
          V
        </div>
        <span class="font-serif font-bold text-3xl tracking-tight text-[var(--text-primary)]">Vyora</span>
      </a>
      <p class="text-xs text-[var(--text-secondary)]">Choose your hospitality model and launch your digital operations in seconds.</p>
    </div>

    <div class="p-8 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-[var(--shadow-lg)] space-y-5">
      <!-- Business Model Selector Tabs -->
      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-2">Select Business Model *</label>
        <div class="grid grid-cols-3 gap-2">
          <button type="button" onclick="selectBizType('RESTAURANT')" id="biz-btn-RESTAURANT" class="p-3 rounded-[var(--radius-md)] border-2 border-[var(--accent)] bg-[var(--accent)] text-white text-center transition">
            <span class="block text-base mb-0.5">🍽️</span>
            <span class="block font-bold text-xs">Restaurant</span>
          </button>
          <button type="button" onclick="selectBizType('HOTEL')" id="biz-btn-HOTEL" class="p-3 rounded-[var(--radius-md)] border-2 border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-[var(--accent)] text-center transition">
            <span class="block text-base mb-0.5">🏨</span>
            <span class="block font-bold text-xs">Hotel Only</span>
          </button>
          <button type="button" onclick="selectBizType('BOTH')" id="biz-btn-BOTH" class="p-3 rounded-[var(--radius-md)] border-2 border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-[var(--accent)] text-center transition">
            <span class="block text-base mb-0.5">🏨🍽️</span>
            <span class="block font-bold text-xs">Both (Combo)</span>
          </button>
        </div>
      </div>

      <form onsubmit="handleRegister(event)" class="space-y-4">
        <input type="hidden" id="reg-biz-type" value="RESTAURANT">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1" id="label-venue-name">Restaurant Name *</label>
            <input type="text" id="reg-rest-name" required placeholder="e.g. Grand Horizon" class="w-full px-3.5 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Owner / Manager Name *</label>
            <input type="text" id="reg-owner-name" required placeholder="e.g. Alex Vance" class="w-full px-3.5 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Email Address *</label>
            <input type="email" id="reg-email" required placeholder="alex@hospitality.com" class="w-full px-3.5 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Phone Number</label>
            <input type="tel" id="reg-phone" placeholder="+1 (555) 000-1234" class="w-full px-3.5 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Username (Login ID) *</label>
            <input type="text" id="reg-username" minlength="3" required placeholder="alexvance" class="w-full px-3.5 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div id="wrapper-counts">
            <div id="box-tables">
              <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Initial Tables (Max 5)</label>
              <input type="number" id="reg-table-count" min="1" max="5" value="5" class="w-full px-3.5 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
            </div>
            <div id="box-rooms" class="hidden">
              <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Initial Rooms (Max 10)</label>
              <input type="number" id="reg-room-count" min="1" max="10" value="5" class="w-full px-3.5 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Password * (Min 6 chars)</label>
          <input type="password" id="reg-password" minlength="6" required placeholder="••••••••" class="w-full px-3.5 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>

        <div id="register-error" class="p-3 rounded-[var(--radius-sm)] bg-red-500/10 border border-red-500/20 text-xs text-red-600 hidden"></div>

        <button type="submit" id="register-btn" class="w-full py-3.5 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-sm shadow-sm transition flex items-center justify-center gap-2">
          <span>Create Account & Setup Portal</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
      </form>
    </div>

    <p class="text-center text-xs text-[var(--text-secondary)] mt-6">
      Already registered?
      <a href="/auth/login" class="font-bold text-[var(--accent)] hover:underline ml-1">Sign In</a>
    </p>
  </div>
</div>

<script>
  let activeBizType = 'RESTAURANT';

  function selectBizType(t) {
    activeBizType = t;
    document.getElementById('reg-biz-type').value = t;

    ['RESTAURANT', 'HOTEL', 'BOTH'].forEach(k => {
      const btn = document.getElementById(`biz-btn-${k}`);
      if (k === t) {
        btn.className = 'p-3 rounded-[var(--radius-md)] border-2 border-[var(--accent)] bg-[var(--accent)] text-white text-center transition shadow-sm';
      } else {
        btn.className = 'p-3 rounded-[var(--radius-md)] border-2 border-[var(--border)] bg-[var(--bg-base)] text-[var(--text-secondary)] hover:border-[var(--accent)] text-center transition';
      }
    });

    const labelVenue = document.getElementById('label-venue-name');
    const boxTables = document.getElementById('box-tables');
    const boxRooms = document.getElementById('box-rooms');

    if (t === 'HOTEL') {
      labelVenue.textContent = 'Hotel / Resort Name *';
      boxTables.classList.add('hidden');
      boxRooms.classList.remove('hidden');
    } else if (t === 'RESTAURANT') {
      labelVenue.textContent = 'Restaurant / Cafe Name *';
      boxTables.classList.remove('hidden');
      boxRooms.classList.add('hidden');
    } else {
      labelVenue.textContent = 'Hotel & Restaurant Name *';
      boxTables.classList.remove('hidden');
      boxRooms.classList.remove('hidden');
    }
  }

  async function handleRegister(e) {
    e.preventDefault();
    const btn = document.getElementById('register-btn');
    const errDiv = document.getElementById('register-error');

    const payload = {
      restaurantName: document.getElementById('reg-rest-name').value.trim(),
      ownerName: document.getElementById('reg-owner-name').value.trim(),
      email: document.getElementById('reg-email').value.trim(),
      phone: document.getElementById('reg-phone').value.trim(),
      username: document.getElementById('reg-username').value.trim(),
      password: document.getElementById('reg-password').value,
      businessType: activeBizType,
      tableCount: activeBizType === 'HOTEL' ? 0 : parseInt(document.getElementById('reg-table-count').value || 5, 10),
      roomCount: activeBizType === 'RESTAURANT' ? 0 : parseInt(document.getElementById('reg-room-count').value || 5, 10),
    };

    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Setting up your platform...';
    errDiv.classList.add('hidden');

    try {
      const res = await fetch('/api/auth/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = await res.json();

      if (data.success) {
        window.showToast('Registration complete! Please sign in.');
        setTimeout(() => {
          window.location.href = '/auth/login';
        }, 1200);
      } else {
        errDiv.textContent = data.error?.message || 'Registration failed. Please review your input.';
        errDiv.classList.remove('hidden');
      }
    } catch (err) {
      errDiv.textContent = 'Server connection error. Please try again.';
      errDiv.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<span>Create Account & Setup Portal</span><i data-lucide="arrow-right" class="w-4 h-4"></i>';
      lucide.createIcons();
    }
  }
</script>
@endsection
