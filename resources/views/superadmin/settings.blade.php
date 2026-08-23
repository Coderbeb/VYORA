@extends('layouts.superadmin')

@section('title', 'SaaS Customizations & Settings - Vyora')
@section('page_title', 'SaaS Provider Customizations & Settings')

@section('superadmin_content')
<div class="space-y-8 max-w-5xl">
  <!-- Section Tabs -->
  <div class="flex items-center gap-2 border-b border-[var(--border)] pb-2 overflow-x-auto no-scrollbar">
    <button onclick="switchTab('branding')" id="tab-btn-branding" class="tab-btn px-4 py-2 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm flex items-center gap-1.5 shrink-0">
      <i data-lucide="palette" class="w-3.5 h-3.5"></i>
      <span>Branding & Identity</span>
    </button>
    <button onclick="switchTab('plans')" id="tab-btn-plans" class="tab-btn px-4 py-2 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] flex items-center gap-1.5 shrink-0">
      <i data-lucide="layers" class="w-3.5 h-3.5"></i>
      <span>Subscription Plans</span>
    </button>
    <button onclick="switchTab('gateways')" id="tab-btn-gateways" class="tab-btn px-4 py-2 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] flex items-center gap-1.5 shrink-0">
      <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
      <span>Billing & Gateways</span>
    </button>
    <button onclick="switchTab('realtime')" id="tab-btn-realtime" class="tab-btn px-4 py-2 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)] flex items-center gap-1.5 shrink-0">
      <i data-lucide="radio" class="w-3.5 h-3.5"></i>
      <span>Live Sync</span>
    </button>
  </div>

  <!-- 1. Branding & Identity Tab -->
  <div id="tab-pane-branding" class="tab-pane space-y-6">
    <div class="p-4 sm:p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-6">
      <div class="border-b border-[var(--border)] pb-3">
        <h3 class="font-serif font-bold text-base sm:text-lg text-[var(--text-primary)]">Platform Identity & White-labeling</h3>
        <p class="text-xs text-[var(--text-secondary)]">Customize your SaaS product brand, support channels, and currency format.</p>
      </div>

      <form onsubmit="saveSettings(event)" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Platform Brand Name *</label>
            <input type="text" id="set-platform-name" required placeholder="e.g. Vyora" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Tagline / Headline</label>
            <input type="text" id="set-tagline" placeholder="e.g. Unified OS for Stays & Dining" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Support Email Address</label>
            <input type="email" id="set-support-email" placeholder="support@vyora.com" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Support Phone / WhatsApp</label>
            <input type="text" id="set-support-phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Currency Symbol *</label>
            <input type="text" id="set-currency-symbol" required placeholder="₹ or $ or €" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Currency Code *</label>
            <input type="text" id="set-currency-code" required placeholder="INR or USD or EUR" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Trial Period (Days)</label>
            <input type="number" id="set-trial-days" value="14" min="0" max="365" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
        </div>

        <div class="p-4 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] flex items-center justify-between">
          <div>
            <h4 class="text-xs font-bold text-[var(--text-primary)]">Allow Public Tenant Self-Registration</h4>
            <p class="text-[10px] text-[var(--text-secondary)]">If disabled, new hotels and restaurants can only be created by Super Admin.</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="set-allow-registration" class="sr-only peer" checked>
            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[var(--accent)]"></div>
          </label>
        </div>

        <div class="flex justify-end pt-3">
          <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition">
            Save Branding Customizations
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- 2. Subscription Plans Manager Tab -->
  <div id="tab-pane-plans" class="tab-pane space-y-6 hidden">
    <div class="p-4 sm:p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[var(--border)] pb-3">
        <div>
          <h3 class="font-serif font-bold text-base sm:text-lg text-[var(--text-primary)]">SaaS Subscription Tiers</h3>
          <p class="text-xs text-[var(--text-secondary)]">Create and edit subscription packages for Restaurants, Hotels, and Combo properties.</p>
        </div>
        <button onclick="openPlanModal()" class="w-full sm:w-auto px-4 py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
          <i data-lucide="plus" class="w-3.5 h-3.5"></i>
          <span>Create New Tier</span>
        </button>
      </div>

      <div id="plans-container" class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <div class="col-span-full p-8 text-center text-xs text-[var(--text-muted)]">Loading subscription plans...</div>
      </div>
    </div>
  </div>

  <!-- 3. Billing & Gateways Tab -->
  <div id="tab-pane-gateways" class="tab-pane space-y-6 hidden">
    <div class="p-4 sm:p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-6">
      <div class="border-b border-[var(--border)] pb-3">
        <h3 class="font-serif font-bold text-base sm:text-lg text-[var(--text-primary)]">Payment Gateways & Commission Settings</h3>
        <p class="text-xs text-[var(--text-secondary)]">Configure online payment gateways for SaaS tenant billing and platform fee splits.</p>
      </div>

      <form onsubmit="saveSettings(event)" class="space-y-5">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Active Gateway Mode</label>
          <select id="set-gateway-mode" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
            <option value="manual">Manual / Offline Bank Transfer & Invoice</option>
            <option value="stripe">Stripe Connect (Global Cards & Apple Pay)</option>
            <option value="razorpay">Razorpay (India UPI, Cards, Netbanking)</option>
          </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Platform Service Commission (%)</label>
            <input type="number" step="0.1" id="set-commission-percent" placeholder="0.0" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Terms of Service URL</label>
            <input type="text" id="set-terms-url" placeholder="/terms" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
        </div>

        <div class="p-4 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] space-y-3">
          <h4 class="text-xs font-bold text-[var(--text-primary)]">Stripe Gateway Keys (Optional)</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-[var(--text-secondary)] mb-1">Stripe Publishable Key</label>
              <input type="text" id="set-stripe-pub" placeholder="pk_live_..." class="w-full px-3 py-1.5 text-xs bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none text-[var(--text-primary)]">
            </div>
            <div>
              <label class="block text-[11px] text-[var(--text-secondary)] mb-1">Stripe Secret Key</label>
              <input type="password" id="set-stripe-sec" placeholder="sk_live_..." class="w-full px-3 py-1.5 text-xs bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none text-[var(--text-primary)]">
            </div>
          </div>
        </div>

        <div class="p-4 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] space-y-3">
          <h4 class="text-xs font-bold text-[var(--text-primary)]">Razorpay Gateway Keys (Optional)</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-[var(--text-secondary)] mb-1">Razorpay Key ID</label>
              <input type="text" id="set-rzp-id" placeholder="rzp_live_..." class="w-full px-3 py-1.5 text-xs bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none text-[var(--text-primary)]">
            </div>
            <div>
              <label class="block text-[11px] text-[var(--text-secondary)] mb-1">Razorpay Key Secret</label>
              <input type="password" id="set-rzp-sec" placeholder="••••••••" class="w-full px-3 py-1.5 text-xs bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none text-[var(--text-primary)]">
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-3">
          <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition">
            Save Billing Settings
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- 4. Live Realtime Tab -->
  <div id="tab-pane-realtime" class="tab-pane space-y-6 hidden">
    <div class="p-4 sm:p-6 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm space-y-6">
      <div class="border-b border-[var(--border)] pb-3">
        <h3 class="font-serif font-bold text-base sm:text-lg text-[var(--text-primary)]">Live Real-time Configuration</h3>
        <p class="text-xs text-[var(--text-secondary)]">Manage Pusher channels for instant real-time kitchen tickets, room orders, and housekeeping dispatch.</p>
      </div>

      <form onsubmit="saveSettings(event)" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">App ID *</label>
            <input type="text" id="set-pusher-id" required placeholder="e.g. 2188435" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Cluster *</label>
            <input type="text" id="set-pusher-cluster" required placeholder="e.g. ap2 or mt1" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">App Key *</label>
            <input type="text" id="set-pusher-key" required placeholder="e.g. 339d3e48e2eb0e402d7c" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
          <div>
            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">App Secret *</label>
            <input type="password" id="set-pusher-secret" required placeholder="••••••••" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          </div>
        </div>

        <div class="flex justify-end pt-3">
          <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition">
            Save Real-time Configuration
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Plan Modal (Create / Edit Tier) -->
<div id="plan-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-2xl w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
      <h3 id="plan-modal-title" class="font-serif font-bold text-lg text-[var(--text-primary)]">Create Subscription Tier</h3>
      <button onclick="closePlanModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form onsubmit="submitPlanForm(event)" class="space-y-4">
      <input type="hidden" id="plan-form-id">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Tier Name *</label>
          <input type="text" id="plan-form-tier" required placeholder="e.g. PRO or ENTERPRISE" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Business Target *</label>
          <select id="plan-form-btype" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
            <option value="RESTAURANT">Dining Restaurant</option>
            <option value="HOTEL">Hotel PMS</option>
            <option value="COMBO">Combo (Hotel + Restaurant)</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Price / Month (₹)</label>
          <input type="number" step="0.01" id="plan-form-price" value="0.00" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Max Tables</label>
          <input type="number" id="plan-form-tables" value="10" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Max Rooms</label>
          <input type="number" id="plan-form-rooms" value="0" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Features (One feature per line)</label>
        <textarea id="plan-form-features" rows="4" placeholder="Unified Hotel PMS & Restaurant POS&#10;Up to 25 Tables & 30 Rooms&#10;Real-time KDS Kitchen Tickets" class="w-full px-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]"></textarea>
      </div>

      <div class="flex justify-end gap-3 pt-3 border-t border-[var(--border)]">
        <button type="button" onclick="closePlanModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Cancel</button>
        <button type="submit" class="px-5 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm">Save Tier</button>
      </div>
    </form>
  </div>
</div>

<script>
  let currentSettings = {};
  let currentPlans = [];

  document.addEventListener('DOMContentLoaded', () => {
    loadSettings();
    loadPlans();
  });

  function switchTab(tabId) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
      b.classList.remove('bg-[var(--accent)]', 'text-white', 'shadow-sm');
      b.classList.add('border', 'border-[var(--border)]', 'text-[var(--text-secondary)]');
    });

    const pane = document.getElementById('tab-pane-' + tabId);
    const btn = document.getElementById('tab-btn-' + tabId);
    if (pane) pane.classList.remove('hidden');
    if (btn) {
      btn.classList.add('bg-[var(--accent)]', 'text-white', 'shadow-sm');
      btn.classList.remove('border', 'border-[var(--border)]', 'text-[var(--text-secondary)]');
    }
  }

  async function loadSettings() {
    try {
      const res = await fetch('/api/superadmin/settings', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        currentSettings = data.data;
        document.getElementById('set-platform-name').value = currentSettings.platform_name || 'Vyora';
        document.getElementById('set-tagline').value = currentSettings.tagline || '';
        document.getElementById('set-support-email').value = currentSettings.support_email || '';
        document.getElementById('set-support-phone').value = currentSettings.support_phone || '';
        document.getElementById('set-currency-symbol').value = currentSettings.currency_symbol || '₹';
        document.getElementById('set-currency-code').value = currentSettings.currency_code || 'INR';
        document.getElementById('set-trial-days').value = currentSettings.default_trial_days || 14;
        document.getElementById('set-allow-registration').checked = !!currentSettings.allow_self_registration;

        document.getElementById('set-gateway-mode').value = currentSettings.payment_gateway_mode || 'manual';
        document.getElementById('set-commission-percent').value = currentSettings.commission_percent || 0.0;
        document.getElementById('set-terms-url').value = currentSettings.terms_url || '/terms';
        document.getElementById('set-stripe-pub').value = currentSettings.stripe_public_key || '';
        document.getElementById('set-stripe-sec').value = currentSettings.stripe_secret_key || '';
        document.getElementById('set-rzp-id').value = currentSettings.razorpay_key_id || '';
        document.getElementById('set-rzp-sec').value = currentSettings.razorpay_key_secret || '';

        document.getElementById('set-pusher-id').value = currentSettings.pusher_app_id || '';
        document.getElementById('set-pusher-cluster').value = currentSettings.pusher_app_cluster || 'ap2';
        document.getElementById('set-pusher-key').value = currentSettings.pusher_app_key || '';
        document.getElementById('set-pusher-secret').value = currentSettings.pusher_app_secret || '';
      }
    } catch(err) {
      console.error(err);
    }
  }

  async function saveSettings(e) {
    e.preventDefault();
    const payload = {
      platform_name: document.getElementById('set-platform-name').value.trim(),
      tagline: document.getElementById('set-tagline').value.trim(),
      support_email: document.getElementById('set-support-email').value.trim(),
      support_phone: document.getElementById('set-support-phone').value.trim(),
      currency_symbol: document.getElementById('set-currency-symbol').value.trim(),
      currency_code: document.getElementById('set-currency-code').value.trim(),
      default_trial_days: parseInt(document.getElementById('set-trial-days').value) || 14,
      allow_self_registration: document.getElementById('set-allow-registration').checked,
      payment_gateway_mode: document.getElementById('set-gateway-mode').value,
      commission_percent: parseFloat(document.getElementById('set-commission-percent').value) || 0.0,
      terms_url: document.getElementById('set-terms-url').value.trim(),
      stripe_public_key: document.getElementById('set-stripe-pub').value.trim(),
      stripe_secret_key: document.getElementById('set-stripe-sec').value.trim(),
      razorpay_key_id: document.getElementById('set-rzp-id').value.trim(),
      razorpay_key_secret: document.getElementById('set-rzp-sec').value.trim(),
      pusher_app_id: document.getElementById('set-pusher-id').value.trim(),
      pusher_app_cluster: document.getElementById('set-pusher-cluster').value.trim(),
      pusher_app_key: document.getElementById('set-pusher-key').value.trim(),
      pusher_app_secret: document.getElementById('set-pusher-secret').value.trim(),
    };

    try {
      const res = await fetch('/api/superadmin/settings', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('✅ Platform customizations saved successfully!');
      } else {
        alert(data.error?.message || 'Failed to save settings');
      }
    } catch(err) {
      alert('Error communicating with server');
    }
  }

  async function loadPlans() {
    try {
      const res = await fetch('/api/superadmin/plans', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success) {
        currentPlans = data.data || [];
        renderPlans();
      }
    } catch(err) {
      console.error(err);
    }
  }

  function renderPlans() {
    const container = document.getElementById('plans-container');
    if (currentPlans.length === 0) {
      container.innerHTML = '<div class="col-span-full p-8 text-center text-xs text-[var(--text-muted)]">No plans configured yet.</div>';
      return;
    }

    container.innerHTML = currentPlans.map(p => `
      <div class="p-5 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)] flex flex-col justify-between space-y-4">
        <div>
          <div class="flex items-center justify-between">
            <span class="px-2.5 py-0.5 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-[10px] uppercase tracking-wider">${p.tier}</span>
            <span class="text-[10px] font-semibold text-[var(--text-muted)] uppercase">${p.business_type}</span>
          </div>
          <div class="mt-3 flex items-baseline gap-1">
            <span class="font-serif text-2xl font-bold text-[var(--text-primary)]">₹${parseFloat(p.price).toFixed(2)}</span>
            <span class="text-[10px] text-[var(--text-muted)]">/month</span>
          </div>
          <div class="mt-2 text-xs text-[var(--text-secondary)] space-y-1">
            <p>📊 Max Tables: <strong>${p.max_tables > 0 ? p.max_tables : 'Unlimited'}</strong></p>
            <p>🏨 Max Rooms: <strong>${p.max_rooms > 0 ? p.max_rooms : 'Unlimited'}</strong></p>
          </div>
          ${Array.isArray(p.features) && p.features.length > 0 ? `
            <ul class="mt-3 text-[11px] text-[var(--text-secondary)] space-y-1 border-t border-[var(--border)] pt-2">
              ${p.features.map(f => `<li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> ${f}</li>`).join('')}
            </ul>
          ` : ''}
        </div>
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-[var(--border)]">
          <button onclick='openPlanModal(${JSON.stringify(p)})' class="px-3 py-1.5 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-xs font-semibold text-[var(--text-primary)]">Edit</button>
          <button onclick="deletePlan('${p.id}')" class="px-3 py-1.5 rounded-[var(--radius-sm)] bg-red-500/10 hover:bg-red-500/20 text-xs font-semibold text-red-600">Delete</button>
        </div>
      </div>
    `).join('');
    lucide.createIcons();
  }

  function openPlanModal(plan = null) {
    const modal = document.getElementById('plan-modal');
    document.getElementById('plan-form-id').value = plan ? plan.id : '';
    document.getElementById('plan-form-tier').value = plan ? plan.tier : '';
    document.getElementById('plan-form-btype').value = plan ? plan.business_type : 'RESTAURANT';
    document.getElementById('plan-form-price').value = plan ? plan.price : '0.00';
    document.getElementById('plan-form-tables').value = plan ? plan.max_tables : '10';
    document.getElementById('plan-form-rooms').value = plan ? plan.max_rooms : '0';
    document.getElementById('plan-form-features').value = plan && Array.isArray(plan.features) ? plan.features.join('\n') : '';
    document.getElementById('plan-modal-title').textContent = plan ? 'Edit Subscription Tier' : 'Create Subscription Tier';
    modal.classList.remove('hidden');
  }

  function closePlanModal() {
    document.getElementById('plan-modal').classList.add('hidden');
  }

  async function submitPlanForm(e) {
    e.preventDefault();
    const payload = {
      id: document.getElementById('plan-form-id').value || null,
      tier: document.getElementById('plan-form-tier').value.trim(),
      business_type: document.getElementById('plan-form-btype').value,
      price: parseFloat(document.getElementById('plan-form-price').value) || 0,
      max_tables: parseInt(document.getElementById('plan-form-tables').value) || 0,
      max_rooms: parseInt(document.getElementById('plan-form-rooms').value) || 0,
      features: document.getElementById('plan-form-features').value,
    };

    try {
      const res = await fetch('/api/superadmin/plans', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (data.success) {
        closePlanModal();
        window.showToast('✅ Subscription plan saved!');
        loadPlans();
      } else {
        alert(data.error?.message || 'Failed to save plan');
      }
    } catch(err) {
      alert('Server error saving plan');
    }
  }

  async function deletePlan(id) {
    if (!confirm('Are you sure you want to delete this subscription plan?')) return;
    try {
      const res = await fetch('/api/superadmin/plans/' + id, {
        method: 'DELETE',
        headers: window.getAuthHeader(),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Plan deleted');
        loadPlans();
      }
    } catch(err) {
      alert('Error deleting plan');
    }
  }
</script>
@endsection
