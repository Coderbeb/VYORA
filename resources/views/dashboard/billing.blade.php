@extends('layouts.dashboard')

@section('title', 'Table Billing & Receipts - Vyora')
@section('page_title', 'Table Billing & Settlement')

@section('dashboard_content')
<div class="space-y-6">
  <!-- Top Filter & Search Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <div class="flex flex-wrap items-center gap-2">
      <button onclick="setFilter('active')" id="filter-btn-active" class="px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm">
        Active (<span id="count-active">0</span>)
      </button>
      <button onclick="setFilter('unpaid')" id="filter-btn-unpaid" class="px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        Unpaid (<span id="count-unpaid">0</span>)
      </button>
      <button onclick="setFilter('idle')" id="filter-btn-idle" class="px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        Idle (<span id="count-idle">0</span>)
      </button>
      <button onclick="setFilter('all')" id="filter-btn-all" class="px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]">
        All (<span id="count-all">0</span>)
      </button>
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto">
      <div class="relative flex-1 sm:w-48">
        <input type="text" id="billing-search" oninput="applySearch()" placeholder="Search table #..." class="w-full pl-8 pr-3 py-1.5 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        <i data-lucide="search" class="w-3.5 h-3.5 text-[var(--text-muted)] absolute left-2.5 top-1/2 -translate-y-1/2"></i>
      </div>
      <button onclick="loadBillingData()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)] transition" title="Refresh">
        <i data-lucide="refresh-cw" id="billing-refresh-icon" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <!-- Tables Billing Grid (Minimum 2 per row) -->
  <div id="billing-grid" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
    <div class="col-span-full p-8 text-center text-[var(--text-muted)]">Loading table billing statuses...</div>
  </div>
</div>

<!-- Receipt & Settlement Modal -->
<div id="receipt-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-md p-6 relative max-h-[90vh] flex flex-col justify-between">
    <div class="overflow-y-auto pr-1">
      <div class="flex justify-between items-center pb-3 border-b border-[var(--border)] mb-4">
        <div>
          <h3 id="receipt-modal-title" class="font-serif font-bold text-lg text-[var(--text-primary)]">Table # Session Bill</h3>
          <p id="receipt-modal-time" class="text-xs text-[var(--text-muted)]">Session details</p>
        </div>
        <button onclick="closeReceiptModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Itemized Table List -->
      <div class="space-y-3 mb-6">
        <div id="receipt-items-list" class="divide-y divide-[var(--border)] text-xs">
          <!-- Populated by JS -->
        </div>

        <div class="pt-3 border-t-2 border-[var(--border)] flex justify-between items-center text-sm font-bold text-[var(--text-primary)]">
          <span>Grand Total</span>
          <span id="receipt-grand-total" class="font-mono text-base text-[var(--accent)]">₹0.00</span>
        </div>
      </div>
    </div>

    <!-- Settlement Actions -->
    <div class="pt-4 border-t border-[var(--border)] space-y-2">
      <div class="flex items-center gap-2">
        <button onclick="printReceipt()" class="flex-1 py-2.5 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-xs font-bold text-[var(--text-primary)] transition flex items-center justify-center gap-1.5">
          <i data-lucide="printer" class="w-4 h-4"></i>
          <span>Print Receipt</span>
        </button>
        <button onclick="settleTableBill()" id="settle-btn" class="flex-1 py-2.5 rounded-[var(--radius-sm)] bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5">
          <i data-lucide="check-circle" class="w-4 h-4"></i>
          <span>Settle & Reset</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  let tablesData = [];
  let currentFilter = 'active';
  let activeSelectedTable = null;

  document.addEventListener('DOMContentLoaded', () => {
    loadBillingData();

    window.addEventListener('qrestro:order:new', () => loadBillingData());
    window.addEventListener('qrestro:order:updated', () => loadBillingData());
    window.addEventListener('qrestro:table:reset', () => loadBillingData());
  });

  async function loadBillingData() {
    const icon = document.getElementById('billing-refresh-icon');
    if (icon) icon.classList.add('animate-spin');

    try {
      const res = await fetch('/api/billing', { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        tablesData = data.data;
        updateCounts();
        renderBillingGrid();
      }
    } catch(e) {
      console.error(e);
    } finally {
      if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 400);
    }
  }

  function updateCounts() {
    const active = tablesData.filter(t => t.status !== 'idle').length;
    const unpaid = tablesData.filter(t => t.status === 'completed_unpaid').length;
    const idle = tablesData.filter(t => t.status === 'idle').length;
    const all = tablesData.length;

    document.getElementById('count-active').textContent = active;
    document.getElementById('count-unpaid').textContent = unpaid;
    document.getElementById('count-idle').textContent = idle;
    document.getElementById('count-all').textContent = all;
  }

  function setFilter(f) {
    currentFilter = f;
    ['active', 'unpaid', 'idle', 'all'].forEach(k => {
      const btn = document.getElementById(`filter-btn-${k}`);
      if (k === f) {
        btn.className = 'px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm';
      } else {
        btn.className = 'px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-semibold transition border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]';
      }
    });
    renderBillingGrid();
  }

  function applySearch() {
    renderBillingGrid();
  }

  function renderBillingGrid() {
    const grid = document.getElementById('billing-grid');
    const q = document.getElementById('billing-search').value.toLowerCase().trim();

    let filtered = tablesData;
    if (currentFilter === 'active') filtered = filtered.filter(t => t.status !== 'idle');
    else if (currentFilter === 'unpaid') filtered = filtered.filter(t => t.status === 'completed_unpaid');
    else if (currentFilter === 'idle') filtered = filtered.filter(t => t.status === 'idle');

    if (q) {
      filtered = filtered.filter(t => String(t.tableNumber).includes(q));
    }

    if (filtered.length === 0) {
      grid.innerHTML = '<div class="col-span-full p-8 text-center text-xs text-[var(--text-muted)]">No tables found matching criteria.</div>';
      return;
    }

    grid.innerHTML = '';
    filtered.forEach(table => {
      const card = document.createElement('div');
      card.className = 'p-5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between space-y-4 transition hover:shadow-md';

      let statusBadge = '';
      let btnAction = '';

      if (table.status === 'idle') {
        statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-slate-500/10 text-slate-600 font-bold text-[11px]">Idle</span>';
        btnAction = `<button disabled class="w-full py-2 rounded-[var(--radius-sm)] bg-[var(--bg-base)] text-[var(--text-muted)] text-xs font-medium cursor-not-allowed">No Active Bill</button>`;
      } else if (table.status === 'active') {
        statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 font-bold text-[11px] animate-pulse">Dining / Cooking</span>';
        btnAction = `<button onclick="openReceiptModal(${table.tableNumber})" class="w-full py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white text-xs font-bold shadow-sm transition">View Bill (₹${parseFloat(table.session.totalAmount).toFixed(2)})</button>`;
      } else {
        statusBadge = '<span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 font-bold text-[11px]">Served / Ready to Pay</span>';
        btnAction = `<button onclick="openReceiptModal(${table.tableNumber})" class="w-full py-2 rounded-[var(--radius-sm)] bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">Settle Bill (₹${parseFloat(table.session.totalAmount).toFixed(2)})</button>`;
      }

      const itemCount = table.session ? table.session.items.reduce((sum, i) => sum + i.quantity, 0) : 0;
      const orderCount = table.session ? table.session.orders.length : 0;

      card.innerHTML = `
        <div>
          <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
            <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Table #${table.tableNumber}</h3>
            ${statusBadge}
          </div>
          <div class="py-3 space-y-1.5 text-xs text-[var(--text-secondary)]">
            <div class="flex justify-between">
              <span>Orders Placed:</span>
              <span class="font-semibold text-[var(--text-primary)]">${orderCount} ticket(s)</span>
            </div>
            <div class="flex justify-between">
              <span>Items Ordered:</span>
              <span class="font-semibold text-[var(--text-primary)]">${itemCount} dish(es)</span>
            </div>
            <div class="flex justify-between font-bold text-[var(--text-primary)] pt-1 border-t border-[var(--border)]">
              <span>Total Accrued:</span>
              <span class="font-mono text-sm text-[var(--accent)]">₹${table.session ? parseFloat(table.session.totalAmount).toFixed(2) : '0.00'}</span>
            </div>
          </div>
        </div>
        <div>
          ${btnAction}
        </div>
      `;

      grid.appendChild(card);
    });

    lucide.createIcons();
  }

  function openReceiptModal(tableNumber) {
    const table = tablesData.find(t => t.tableNumber === tableNumber);
    if (!table || !table.session) return;
    activeSelectedTable = table;

    document.getElementById('receipt-modal-title').textContent = `Table #${table.tableNumber} Session Bill`;
    const started = new Date(table.session.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    document.getElementById('receipt-modal-time').textContent = `Session started at ${started}`;

    const itemsContainer = document.getElementById('receipt-items-list');
    itemsContainer.innerHTML = '';

    if (table.session.items.length === 0) {
      itemsContainer.innerHTML = `
        <div class="p-4 text-center text-xs font-bold text-amber-800 bg-amber-50 border border-amber-200 rounded-lg">
          No items served yet. Items are only added to billing once served at the table.
        </div>
      `;
    } else {
      table.session.items.forEach(item => {
        const row = document.createElement('div');
        row.className = 'flex justify-between items-center py-2';
        row.innerHTML = `
          <div>
            <p class="font-bold text-[var(--text-primary)]"><span class="text-[var(--accent)] font-extrabold mr-1">${item.quantity}x</span> ${item.menuItemName}</p>
            <p class="text-[10px] text-[var(--text-muted)]">₹${parseFloat(item.price).toFixed(2)} each • <span class="text-emerald-700 font-bold">Served</span></p>
          </div>
          <span class="font-mono font-bold text-[var(--text-primary)]">₹${(item.price * item.quantity).toFixed(2)}</span>
        `;
        itemsContainer.appendChild(row);
      });
    }

    // Display unserved notice if items are still in kitchen
    if (table.session.unservedOrdersCount > 0) {
      const notice = document.createElement('div');
      notice.className = 'mt-3 p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-900 font-medium flex items-center gap-2';
      notice.innerHTML = `
        <i data-lucide="clock" class="w-4 h-4 text-amber-600 shrink-0"></i>
        <span><strong>${table.session.unservedOrdersCount} active ticket(s)</strong> still cooking in kitchen. Only served dishes are billed.</span>
      `;
      itemsContainer.appendChild(notice);
    }

    document.getElementById('receipt-grand-total').textContent = `₹${parseFloat(table.session.totalAmount).toFixed(2)}`;
    document.getElementById('receipt-modal').classList.remove('hidden');
    lucide.createIcons();
  }

  function closeReceiptModal() {
    activeSelectedTable = null;
    document.getElementById('receipt-modal').classList.add('hidden');
  }

  async function settleTableBill() {
    if (!activeSelectedTable) return;
    const btn = document.getElementById('settle-btn');
    btn.disabled = true;
    btn.textContent = 'Settling...';

    try {
      const res = await fetch('/api/billing', {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ tableNumber: activeSelectedTable.tableNumber }),
      });
      const data = await res.json();
      if (data.success) {
        window.showToast(`Table #${activeSelectedTable.tableNumber} bill settled and session reset!`);
        closeReceiptModal();
        loadBillingData();
      } else {
        alert(data.error?.message || 'Failed to settle bill');
      }
    } catch(e) {
      console.error(e);
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i><span>Settle & Reset</span>';
      lucide.createIcons();
    }
  }

  function printReceipt() {
    if (!activeSelectedTable || !activeSelectedTable.session) return;
    const t = activeSelectedTable;
    const itemsHtml = t.session.items.map(i => `
      <tr>
        <td style="padding: 4px 0; vertical-align: top;">${i.quantity}x ${i.menuItemName}</td>
        <td style="text-align: right; padding: 4px 0; font-family: monospace; vertical-align: top;">₹${(i.price * i.quantity).toFixed(2)}</td>
      </tr>
    `).join('');

    const printContent = `<!DOCTYPE html>
      <html>
      <head>
        <meta charset="utf-8">
        <title>Receipt Table #${t.tableNumber}</title>
        <style>
          @page { size: 80mm auto; margin: 4mm; }
          body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, monospace;
            padding: 10px;
            font-size: 13px;
            color: #000;
            background: #fff;
            width: 72mm;
            margin: 0 auto;
          }
          .center { text-align: center; }
          .bold { font-weight: bold; }
          hr { border: none; border-top: 1px dashed #444; margin: 8px 0; }
          table { width: 100%; border-collapse: collapse; font-size: 12px; }
          @media print {
            body { width: 100%; margin: 0; padding: 0; }
          }
        </style>
      </head>
      <body>
        <div class="center bold" style="font-size: 16px; letter-spacing: 1px;">VYORA</div>
        <div class="center" style="font-size: 11px; margin-top: 2px;">Table #${t.tableNumber} Dining Receipt</div>
        <div class="center" style="font-size: 10px; color: #555; margin-top: 2px;">${new Date().toLocaleString()}</div>
        <hr/>
        <table>
          <tbody>
            ${itemsHtml}
          </tbody>
        </table>
        <hr/>
        <div style="display: flex; justify-content: space-between; font-size: 14px;" class="bold">
          <span>TOTAL AMOUNT:</span>
          <span style="font-family: monospace;">₹${parseFloat(t.session.totalAmount).toFixed(2)}</span>
        </div>
        <hr/>
        <div class="center" style="font-size: 11px; margin-top: 6px;">Thank you for dining with us!</div>
      </body>
      </html>`;

    let printFrame = document.getElementById('receipt-print-frame');
    if (printFrame) printFrame.remove();

    printFrame = document.createElement('iframe');
    printFrame.id = 'receipt-print-frame';
    printFrame.style.position = 'fixed';
    printFrame.style.right = '0';
    printFrame.style.bottom = '0';
    printFrame.style.width = '0';
    printFrame.style.height = '0';
    printFrame.style.border = '0';
    document.body.appendChild(printFrame);

    const doc = printFrame.contentWindow.document;
    doc.open();
    doc.write(printContent);
    doc.close();

    printFrame.contentWindow.focus();
    setTimeout(() => {
      printFrame.contentWindow.print();
    }, 250);
  }
</script>
@endsection
