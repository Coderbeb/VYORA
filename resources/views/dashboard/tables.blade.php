@extends('layouts.dashboard')

@section('title', 'Tables & QR Codes - Vyora')
@section('page_title', 'Tables & QR Code Generator')

@section('dashboard_content')
<div class="space-y-6">
  <!-- Top Action Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <div>
      <h2 class="font-serif font-bold text-base text-[var(--text-primary)]">Floor Plan & Dining Tables</h2>
      <p class="text-xs text-[var(--text-secondary)]">Every table is cryptographically signed with HMAC-SHA256.</p>
    </div>
    <div class="flex items-center gap-3">
      <button onclick="openAddTableModal()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm transition flex items-center gap-1.5">
        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
        <span>Add Table</span>
      </button>
    </div>
  </div>

  <!-- Tables Grid -->
  <div id="tables-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">Loading dining tables...</div>
  </div>
</div>

<!-- Add Table Modal -->
<div id="add-table-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-sm p-6">
    <h3 class="font-serif font-bold text-lg text-[var(--text-primary)] mb-2">Add New Table</h3>
    <p class="text-xs text-[var(--text-secondary)] mb-4">Enter the new table number to generate a secure signed QR code.</p>
    <form onsubmit="submitAddTable(event)" class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Table Number</label>
        <input type="number" min="1" id="new-table-num" required class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
      </div>
      <div id="add-table-error" class="text-xs text-red-500 hidden"></div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeAddTableModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Cancel</button>
        <button type="submit" id="add-table-btn" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm">Generate Table</button>
      </div>
    </form>
  </div>
</div>

<!-- QR Code Preview & Download Modal -->
<div id="qr-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-sm p-6 text-center">
    <div class="flex justify-between items-center pb-3 border-b border-[var(--border)] mb-4">
      <h3 id="qr-modal-title" class="font-serif font-bold text-lg text-[var(--text-primary)]">Table # QR Code</h3>
      <button onclick="closeQrModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- QR Code Display Box -->
    <div class="p-6 rounded-[var(--radius-lg)] bg-white border border-[var(--border)] shadow-inner inline-block my-2">
      <img id="qr-modal-img" src="" alt="Table QR" class="w-52 h-52 object-contain mx-auto">
    </div>

    <p class="text-[11px] text-[var(--text-muted)] my-3 max-w-xs mx-auto truncate" id="qr-modal-url">http://...</p>

    <!-- Download & Copy Buttons -->
    <div class="space-y-2 pt-2">
      <div class="grid grid-cols-2 gap-2">
        <button onclick="downloadQrPng()" class="py-2.5 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
          <i data-lucide="download" class="w-3.5 h-3.5"></i>
          <span>Save PNG</span>
        </button>
        <button onclick="downloadQrSvg()" class="py-2.5 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] font-semibold text-xs transition flex items-center justify-center gap-1.5">
          <i data-lucide="file-code" class="w-3.5 h-3.5"></i>
          <span>Save SVG</span>
        </button>
      </div>
      <button onclick="copyOrderLink()" class="w-full py-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] text-xs font-semibold flex items-center justify-center gap-1.5 transition">
        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
        <span>Copy Order Link</span>
      </button>
    </div>
  </div>
</div>

<script>
  let tablesList = [];
  let selectedTableForQr = null;

  document.addEventListener('DOMContentLoaded', () => {
    loadTables();
  });

  async function loadTables() {
    try {
      const res = await fetch('/api/tables', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        tablesList = data.data;
        renderTables(tablesList);
      }
    } catch(e) {
      console.error(e);
    }
  }

  function renderTables(tables) {
    const grid = document.getElementById('tables-grid');
    if (tables.length === 0) {
      grid.innerHTML = '<div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">No tables created yet. Click "+ Add Table" above.</div>';
      return;
    }

    grid.innerHTML = '';
    tables.forEach(table => {
      const card = document.createElement('div');
      card.className = `p-5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between space-y-4 transition hover:shadow-md ${!table.isActive ? 'opacity-60 bg-gray-50 dark:bg-zinc-900' : ''}`;

      card.innerHTML = `
        <div>
          <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
            <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Table #${table.tableNumber}</h3>
            <span class="px-2.5 py-0.5 rounded-full ${table.isActive ? 'bg-emerald-500/10 text-emerald-600' : 'bg-gray-500/10 text-gray-500'} font-bold text-[11px]">
              ${table.isActive ? 'Active' : 'Disabled'}
            </span>
          </div>

          <!-- QR Thumbnail Preview -->
          <div class="py-4 text-center">
            <div class="w-32 h-32 p-2 rounded-[var(--radius-md)] bg-white border border-[var(--border)] shadow-sm inline-block cursor-pointer" onclick="openQrModal('${table.id}')">
              <img src="${table.qrCodeImageUrl}" alt="Table ${table.tableNumber} QR" class="w-full h-full object-contain">
            </div>
          </div>
        </div>

        <div class="pt-3 border-t border-[var(--border)] space-y-2">
          <button onclick="openQrModal('${table.id}')" class="w-full py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
            <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
            <span>View & Print QR</span>
          </button>
          <div class="flex items-center justify-between pt-1 text-xs">
            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-[var(--text-secondary)]">
              <input type="checkbox" onchange="toggleTableActive('${table.id}', this.checked)" ${table.isActive ? 'checked' : ''} class="rounded border-[var(--border)] text-[var(--accent)]">
              <span>Accept Orders</span>
            </label>
            <button onclick="deleteTable('${table.id}')" class="p-1 text-red-500 hover:text-red-700" title="Delete Table">
              <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
            </button>
          </div>
        </div>
      `;
      grid.appendChild(card);
    });

    lucide.createIcons();
  }

  function openAddTableModal() {
    document.getElementById('add-table-modal').classList.remove('hidden');
    document.getElementById('new-table-num').value = (tablesList.length + 1);
    document.getElementById('add-table-error').classList.add('hidden');
  }

  function closeAddTableModal() {
    document.getElementById('add-table-modal').classList.add('hidden');
  }

  async function submitAddTable(e) {
    e.preventDefault();
    const num = parseInt(document.getElementById('new-table-num').value, 10);
    const btn = document.getElementById('add-table-btn');
    const errDiv = document.getElementById('add-table-error');

    btn.disabled = true;
    btn.textContent = 'Generating...';
    errDiv.classList.add('hidden');

    try {
      const res = await fetch('/api/tables', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ tableNumber: num }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast(`Table #${num} created!`);
        closeAddTableModal();
        loadTables();
      } else {
        errDiv.textContent = data.error?.message || 'Failed to create table';
        errDiv.classList.remove('hidden');
      }
    } catch(err) {
      errDiv.textContent = 'Network error. Please try again.';
      errDiv.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Generate Table';
    }
  }

  function openQrModal(tableId) {
    const table = tablesList.find(t => t.id === tableId);
    if (!table) return;
    selectedTableForQr = table;

    document.getElementById('qr-modal-title').textContent = `Table #${table.tableNumber} QR Code`;
    document.getElementById('qr-modal-img').src = table.qrCodeImageUrl;
    document.getElementById('qr-modal-url').textContent = table.qrCodeData;
    document.getElementById('qr-modal').classList.remove('hidden');
    lucide.createIcons();
  }

  function closeQrModal() {
    selectedTableForQr = null;
    document.getElementById('qr-modal').classList.add('hidden');
  }

  function downloadQrPng() {
    if (!selectedTableForQr) return;
    const a = document.createElement('a');
    a.href = selectedTableForQr.qrCodeImageUrl;
    a.download = `table-${selectedTableForQr.tableNumber}-qr.svg`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  function downloadQrSvg() {
    downloadQrPng();
  }

  function copyOrderLink() {
    if (!selectedTableForQr) return;
    navigator.clipboard.writeText(selectedTableForQr.qrCodeData);
    window.showToast('Order link copied to clipboard!');
  }

  async function toggleTableActive(id, isActive) {
    try {
      await fetch(`/api/tables/${id}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ isActive }),
      });
      window.showToast('Table status updated');
      loadTables();
    } catch(err) {
      console.error(err);
    }
  }

  async function deleteTable(id) {
    if (!confirm('Are you sure you want to delete this table?')) return;
    try {
      await fetch(`/api/tables/${id}`, {
        method: 'DELETE',
        headers: window.getAuthHeader(),
      });
      window.showToast('Table deleted');
      loadTables();
    } catch(err) {
      console.error(err);
    }
  }
</script>
@endsection
