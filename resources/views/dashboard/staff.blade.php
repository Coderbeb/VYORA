@extends('layouts.dashboard')

@section('title', 'Team & Staff Management - Vyora')
@section('page_title', 'Staff & Employee Directory')

@section('dashboard_content')
<div class="space-y-6">
  <!-- Key Metrics (Minimum 2 per row on mobile) -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Total Staff</span>
        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-[var(--accent-glow)] text-[var(--accent)] flex items-center justify-center">
          <i data-lucide="users" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="stat-total-staff" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--text-primary)]">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-0.5">Registered employees</p>
      </div>
    </div>

    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Active Today</span>
        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <i data-lucide="user-check" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="stat-active-staff" class="font-serif text-2xl sm:text-3xl font-bold text-emerald-600">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-0.5">Enabled access status</p>
      </div>
    </div>

    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Front Desk & Service</span>
        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
          <i data-lucide="bell" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="stat-service-staff" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--text-primary)]">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-0.5">Front desk & Waiters</p>
      </div>
    </div>

    <div class="p-4 sm:p-5 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[10px] sm:text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">Kitchen & Housekeeping</span>
        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <i data-lucide="chef-hat" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
        </div>
      </div>
      <div>
        <h3 id="stat-kitchen-staff" class="font-serif text-2xl sm:text-3xl font-bold text-[var(--text-primary)]">0</h3>
        <p class="text-[10px] text-[var(--text-muted)] mt-0.5">Chefs & Cleaners</p>
      </div>
    </div>
  </div>

  <!-- Controls Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm">
    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
      <div class="relative flex-1 sm:w-64">
        <input type="text" id="staff-search" oninput="filterStaff()" placeholder="Search staff by name, user, phone..." class="w-full pl-9 pr-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        <i data-lucide="search" class="w-4 h-4 text-[var(--text-muted)] absolute left-3 top-1/2 -translate-y-1/2"></i>
      </div>
      <select id="staff-role-filter" onchange="filterStaff()" class="px-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none text-[var(--text-primary)]">
        <option value="ALL">All Roles</option>
        <option value="FRONT_DESK">🛎️ Front Desk / Reception</option>
        <option value="HOUSEKEEPING">🧹 Housekeeping</option>
        <option value="CHEF">👨‍🍳 Chef / Kitchen</option>
        <option value="WAITER">🍽️ Waiter / Captain</option>
        <option value="CASHIER">💳 Cashier / Billing</option>
        <option value="MANAGER">👔 Store / Floor Manager</option>
      </select>
    </div>

    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
      <button onclick="openAddStaffModal()" class="flex-1 sm:flex-none px-4 py-2 bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs rounded-[var(--radius-sm)] transition flex items-center justify-center gap-1.5 shadow-sm">
        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
        <span>Add Employee</span>
      </button>
      <button onclick="loadStaff()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Refresh">
        <i data-lucide="refresh-cw" id="staff-ref-icon" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <!-- Staff Cards Grid (Minimum 2 per row on mobile) -->
  <div id="staff-grid" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
    <div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">Loading staff directory...</div>
  </div>
</div>

<!-- Add / Edit Staff Modal -->
<div id="staff-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-2xl w-full max-w-md p-6 relative max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
      <div>
        <h3 id="staff-modal-title" class="font-serif font-bold text-lg text-[var(--text-primary)]">Add Employee</h3>
        <p class="text-xs text-[var(--text-secondary)]">Create login credentials and role assignment.</p>
      </div>
      <button onclick="closeStaffModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form onsubmit="submitStaffForm(event)" class="space-y-4">
      <input type="hidden" id="staff-form-id">

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Full Name *</label>
        <input type="text" id="staff-form-name" required placeholder="e.g. Rahul Sharma" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Username / Staff ID *</label>
          <input type="text" id="staff-form-username" required placeholder="e.g. rahul_waiter" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Assigned Role *</label>
          <select id="staff-form-role" required class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
            <option value="FRONT_DESK">🛎️ Front Desk / Reception</option>
            <option value="HOUSEKEEPING">🧹 Housekeeping</option>
            <option value="CHEF">👨‍🍳 Chef / Kitchen</option>
            <option value="WAITER">🍽️ Waiter / Captain</option>
            <option value="CASHIER">💳 Cashier / Billing</option>
            <option value="MANAGER">👔 Store Manager</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Phone Number</label>
          <input type="text" id="staff-form-phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Email Address</label>
          <input type="email" id="staff-form-email" placeholder="rahul@venue.com" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Password</label>
          <input type="password" id="staff-form-password" placeholder="Leave blank to keep" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Quick POS 4-digit PIN</label>
          <input type="text" maxlength="6" id="staff-form-pin" placeholder="e.g. 1234" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
      </div>

      <div class="flex items-center justify-between p-3 rounded-[var(--radius-sm)] bg-[var(--bg-base)] border border-[var(--border)]">
        <div>
          <span class="text-xs font-bold text-[var(--text-primary)]">Active Status</span>
          <p class="text-[10px] text-[var(--text-secondary)]">Disable to temporarily revoke system access</p>
        </div>
        <input type="checkbox" id="staff-form-active" checked class="w-4 h-4 text-[var(--accent)] rounded border-[var(--border)]">
      </div>

      <div class="flex justify-end gap-3 pt-3 border-t border-[var(--border)]">
        <button type="button" onclick="closeStaffModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Cancel</button>
        <button type="submit" id="staff-submit-btn" class="px-5 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm">Save Employee</button>
      </div>
    </form>
  </div>
</div>

<script>
  let allStaff = [];

  const ROLE_LABELS = {
    FRONT_DESK: { name: 'Front Desk', icon: 'bell', badge: 'bg-blue-500/10 text-blue-600' },
    HOUSEKEEPING: { name: 'Housekeeping', icon: 'sparkles', badge: 'bg-amber-500/10 text-amber-600' },
    CHEF: { name: 'Chef / Kitchen', icon: 'chef-hat', badge: 'bg-orange-500/10 text-orange-600' },
    WAITER: { name: 'Waiter / Captain', icon: 'utensils', badge: 'bg-emerald-500/10 text-emerald-600' },
    CASHIER: { name: 'Cashier / Billing', icon: 'credit-card', badge: 'bg-purple-500/10 text-purple-600' },
    MANAGER: { name: 'Floor Manager', icon: 'briefcase', badge: 'bg-[var(--accent-glow)] text-[var(--accent)]' },
  };

  document.addEventListener('DOMContentLoaded', () => {
    loadStaff();
  });

  async function loadStaff() {
    const icon = document.getElementById('staff-ref-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/staff', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        allStaff = data.data;
        updateMetrics();
        filterStaff();
      }
    } catch(err) {
      console.error(err);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function updateMetrics() {
    document.getElementById('stat-total-staff').textContent = allStaff.length;
    document.getElementById('stat-active-staff').textContent = allStaff.filter(s => s.is_active).length;
    document.getElementById('stat-service-staff').textContent = allStaff.filter(s => ['FRONT_DESK', 'WAITER'].includes(s.role)).length;
    document.getElementById('stat-kitchen-staff').textContent = allStaff.filter(s => ['CHEF', 'HOUSEKEEPING'].includes(s.role)).length;
  }

  function filterStaff() {
    const q = document.getElementById('staff-search').value.toLowerCase().trim();
    const role = document.getElementById('staff-role-filter').value;

    const filtered = allStaff.filter(s => {
      const matchRole = (role === 'ALL' || s.role === role);
      const matchSearch = !q ||
        (s.name && s.name.toLowerCase().includes(q)) ||
        (s.username && s.username.toLowerCase().includes(q)) ||
        (s.phone && s.phone.toLowerCase().includes(q)) ||
        (s.email && s.email.toLowerCase().includes(q));
      return matchRole && matchSearch;
    });

    renderStaffGrid(filtered);
  }

  function renderStaffGrid(list) {
    const grid = document.getElementById('staff-grid');
    if (list.length === 0) {
      grid.innerHTML = '<div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">No employees found. Click "+ Add Employee" to onboard team members.</div>';
      return;
    }

    grid.innerHTML = list.map(s => {
      const r = ROLE_LABELS[s.role] || { name: s.role, icon: 'user', badge: 'bg-gray-500/10 text-gray-600' };
      const initial = (s.name || 'S').charAt(0).toUpperCase();

      return `
        <div class="p-4 sm:p-5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition">
          <div>
            <div class="flex items-start justify-between gap-2 pb-3 border-b border-[var(--border)]">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[var(--accent)] text-white font-bold text-sm flex items-center justify-center shrink-0 font-serif">
                  ${initial}
                </div>
                <div class="overflow-hidden">
                  <h4 class="font-bold text-sm text-[var(--text-primary)] truncate">${s.name}</h4>
                  <p class="text-[10px] text-[var(--text-muted)] truncate">@${s.username}</p>
                </div>
              </div>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${r.badge} shrink-0">
                ${r.name}
              </span>
            </div>

            <div class="py-2.5 space-y-1 text-xs text-[var(--text-secondary)]">
              <p class="flex items-center gap-1.5 truncate">
                <i data-lucide="phone" class="w-3 h-3 text-[var(--text-muted)] shrink-0"></i>
                <span>${s.phone || 'No phone set'}</span>
              </p>
              <p class="flex items-center gap-1.5 truncate">
                <i data-lucide="mail" class="w-3 h-3 text-[var(--text-muted)] shrink-0"></i>
                <span class="truncate">${s.email || 'No email set'}</span>
              </p>
              ${s.pin ? `
                <p class="flex items-center gap-1.5 font-mono text-[10px] text-[var(--accent)]">
                  <i data-lucide="key" class="w-3 h-3 text-[var(--accent)] shrink-0"></i>
                  <span>Quick PIN: ••••</span>
                </p>
              ` : ''}
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-[var(--border)]">
            <span class="inline-flex items-center gap-1 text-[10px] font-bold ${s.is_active ? 'text-emerald-600' : 'text-red-500'}">
              <span class="w-1.5 h-1.5 rounded-full ${s.is_active ? 'bg-emerald-500' : 'bg-red-500'}"></span>
              ${s.is_active ? 'Active' : 'Suspended'}
            </span>

            <div class="flex items-center gap-1">
              <button onclick='openEditStaff(${JSON.stringify(s)})' class="p-1.5 rounded border border-[var(--border)] hover:bg-[var(--bg-hover)] text-xs text-[var(--text-primary)]" title="Edit">
                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
              </button>
              <button onclick="deleteStaff('${s.id}', '${s.name}')" class="p-1.5 rounded text-red-600 hover:bg-red-500/10 transition" title="Delete">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
              </button>
            </div>
          </div>
        </div>
      `;
    }).join('');

    lucide.createIcons();
  }

  function openAddStaffModal() {
    document.getElementById('staff-modal-title').textContent = 'Add Employee';
    document.getElementById('staff-form-id').value = '';
    document.getElementById('staff-form-name').value = '';
    document.getElementById('staff-form-username').value = '';
    document.getElementById('staff-form-email').value = '';
    document.getElementById('staff-form-phone').value = '';
    document.getElementById('staff-form-password').value = '';
    document.getElementById('staff-form-pin').value = '';
    document.getElementById('staff-form-role').value = 'WAITER';
    document.getElementById('staff-form-active').checked = true;
    document.getElementById('staff-modal').classList.remove('hidden');
  }

  function openEditStaff(s) {
    document.getElementById('staff-modal-title').textContent = 'Edit Employee';
    document.getElementById('staff-form-id').value = s.id;
    document.getElementById('staff-form-name').value = s.name;
    document.getElementById('staff-form-username').value = s.username;
    document.getElementById('staff-form-email').value = s.email || '';
    document.getElementById('staff-form-phone').value = s.phone || '';
    document.getElementById('staff-form-password').value = '';
    document.getElementById('staff-form-pin').value = s.pin || '';
    document.getElementById('staff-form-role').value = s.role;
    document.getElementById('staff-form-active').checked = s.is_active;
    document.getElementById('staff-modal').classList.remove('hidden');
  }

  function closeStaffModal() {
    document.getElementById('staff-modal').classList.add('hidden');
  }

  async function submitStaffForm(e) {
    e.preventDefault();
    const btn = document.getElementById('staff-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const id = document.getElementById('staff-form-id').value;
    const payload = {
      name: document.getElementById('staff-form-name').value.trim(),
      username: document.getElementById('staff-form-username').value.trim(),
      role: document.getElementById('staff-form-role').value,
      email: document.getElementById('staff-form-email').value.trim() || null,
      phone: document.getElementById('staff-form-phone').value.trim() || null,
      pin: document.getElementById('staff-form-pin').value.trim() || null,
      is_active: document.getElementById('staff-form-active').checked,
    };

    const pass = document.getElementById('staff-form-password').value;
    if (pass) payload.password = pass;

    try {
      const url = id ? `/api/staff/${id}` : '/api/staff';
      const method = id ? 'PUT' : 'POST';

      const res = await fetch(url, {
        method,
        headers: window.getAuthHeader(),
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (data.success) {
        closeStaffModal();
        window.showToast(`✅ Employee ${id ? 'updated' : 'added'} successfully!`);
        loadStaff();
      } else {
        alert(data.error?.message || 'Failed to save staff member');
      }
    } catch(err) {
      alert('Error communicating with server');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Save Employee';
    }
  }

  async function deleteStaff(id, name) {
    if (!confirm(`Are you sure you want to remove employee "${name}"?`)) return;
    try {
      const res = await fetch(`/api/staff/${id}`, {
        method: 'DELETE',
        headers: window.getAuthHeader(),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Employee removed successfully');
        loadStaff();
      } else {
        alert(data.error?.message || 'Failed to remove employee');
      }
    } catch(err) {
      alert('Server error deleting staff');
    }
  }
</script>
@endsection
