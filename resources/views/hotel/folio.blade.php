@extends('layouts.dashboard')

@section('title', 'Guest Folio & Invoice - Vyora')
@section('page_title', 'Guest Folio & Settlement')

@section('dashboard_content')
<div class="space-y-6 max-w-4xl mx-auto">
  <!-- Actions Header -->
  <div class="flex items-center justify-between p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <a href="/dashboard/hotel/rooms" class="px-3 py-1.5 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-xs font-semibold text-[var(--text-secondary)] flex items-center gap-1.5 transition">
      <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
      <span>Back to Rooms</span>
    </a>
    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="px-4 py-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-xs font-bold text-[var(--text-primary)] flex items-center gap-1.5 transition">
        <i data-lucide="printer" class="w-4 h-4"></i>
        <span>Print Tax Invoice</span>
      </button>
      <button onclick="settleAndCheckout()" id="btn-checkout" class="px-4 py-2 rounded-[var(--radius-sm)] bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        <span>Settle & Check-Out</span>
      </button>
    </div>
  </div>

  <!-- Printable Invoice Document Card -->
  <div class="p-8 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-[var(--shadow-lg)] space-y-8" id="invoice-doc">
    <!-- Invoice Top Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b-2 border-[var(--border)]">
      <div>
        <h2 id="inv-hotel-name" class="font-serif font-bold text-2xl text-[var(--text-primary)]">Grand Horizon Hotel</h2>
        <p class="text-xs text-[var(--text-secondary)]">Hospitality & Guest Accommodations</p>
      </div>
      <div class="text-left sm:text-right">
        <span class="inline-block px-3 py-1 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-xs uppercase tracking-wider mb-1">
          GUEST TAX INVOICE
        </span>
        <p class="font-mono text-xs text-[var(--text-muted)]">Folio #<span id="inv-folio-id">{{ $bookingId }}</span></p>
      </div>
    </div>

    <!-- Guest & Stay Metadata Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
      <div class="p-4 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] space-y-1.5">
        <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-wider">Guest Information</span>
        <h3 id="inv-guest-name" class="font-bold text-sm text-[var(--text-primary)]">-</h3>
        <p id="inv-guest-phone" class="text-[var(--text-secondary)]">Phone: -</p>
        <p id="inv-guest-id" class="text-[var(--text-secondary)]">ID: -</p>
      </div>
      <div class="p-4 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] space-y-1.5">
        <span class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-wider">Room & Dates</span>
        <h3 id="inv-room-info" class="font-bold text-sm text-[var(--text-primary)]">Room -</h3>
        <p id="inv-dates" class="text-[var(--text-secondary)]">Check-In: -</p>
        <p id="inv-stay-length" class="text-[var(--text-secondary)] font-semibold text-[var(--accent)]">Duration: 0 Night(s)</p>
      </div>
    </div>

    <!-- Itemized Breakdown Table -->
    <div class="space-y-4">
      <h4 class="font-serif font-bold text-base text-[var(--text-primary)]">Itemized Charges Breakdown</h4>
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b-2 border-[var(--border)] text-[10px] uppercase text-[var(--text-muted)]">
            <th class="py-2.5">Description</th>
            <th class="py-2.5 text-center">Qty / Nights</th>
            <th class="py-2.5 text-right">Unit Rate</th>
            <th class="py-2.5 text-right">Total Amount</th>
          </tr>
        </thead>
        <tbody id="inv-items-tbody" class="divide-y divide-[var(--border)]">
          <!-- Populated by JS -->
        </tbody>
      </table>
    </div>

    <!-- Summary & Balance Row -->
    <div class="pt-4 border-t-2 border-[var(--border)] flex justify-end">
      <div class="w-full sm:w-72 space-y-2 text-xs">
        <div class="flex justify-between text-[var(--text-secondary)]">
          <span>Room Accommodation Subtotal:</span>
          <span id="inv-room-subtotal" class="font-mono font-bold text-[var(--text-primary)]">$0.00</span>
        </div>
        <div class="flex justify-between text-[var(--text-secondary)]">
          <span>In-House Food & Dining:</span>
          <span id="inv-food-subtotal" class="font-mono font-bold text-[var(--text-primary)]">$0.00</span>
        </div>
        <div class="flex justify-between text-emerald-600 font-semibold">
          <span>Less Advance Deposit:</span>
          <span id="inv-advance" class="font-mono font-bold">-$0.00</span>
        </div>
        <div class="pt-2 border-t border-[var(--border)] flex justify-between text-base font-bold text-[var(--text-primary)]">
          <span>Balance Due:</span>
          <span id="inv-balance-due" class="font-mono text-lg text-[var(--accent)]">$0.00</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const BOOKING_ID = "{{ $bookingId }}";
  let folioData = null;

  document.addEventListener('DOMContentLoaded', () => {
    loadFolio();
  });

  async function loadFolio() {
    try {
      const res = await fetch(`/api/hotel/folio/${BOOKING_ID}`, { headers: window.getAuthHeader() });
      const data = await res.json();
      if (data.success && data.data) {
        folioData = data.data;
        renderFolio(folioData);
      } else {
        alert('Failed to load folio.');
      }
    } catch(err) {
      console.error(err);
    }
  }

  function renderFolio(f) {
    document.getElementById('inv-guest-name').textContent = f.guest.name;
    document.getElementById('inv-guest-phone').textContent = `Phone: ${f.guest.phone || 'N/A'}`;
    document.getElementById('inv-guest-id').textContent = `${f.guest.idType || 'ID'}: ${f.guest.idNumber || 'N/A'}`;

    document.getElementById('inv-room-info').textContent = `Room ${f.room.number} (${f.room.category}) • Floor ${f.room.floor}`;
    const checkInStr = new Date(f.stay.checkIn).toLocaleString();
    const checkOutStr = f.stay.checkOut ? new Date(f.stay.checkOut).toLocaleString() : 'Currently Active';
    document.getElementById('inv-dates').textContent = `In: ${checkInStr} | Out: ${checkOutStr}`;
    document.getElementById('inv-stay-length').textContent = `Duration: ${f.stay.nights} Night(s) @ $${f.stay.nightlyRate.toFixed(2)}/nt`;

    const tbody = document.getElementById('inv-items-tbody');
    tbody.innerHTML = '';

    // Room Tariff Line
    const trRoom = document.createElement('tr');
    trRoom.className = 'py-2 font-medium';
    trRoom.innerHTML = `
      <td class="py-2.5 font-bold text-[var(--text-primary)]">Room Accommodation Charge (${f.room.category})</td>
      <td class="py-2.5 text-center font-mono">${f.stay.nights} night(s)</td>
      <td class="py-2.5 text-right font-mono">$${f.stay.nightlyRate.toFixed(2)}</td>
      <td class="py-2.5 text-right font-mono font-bold text-[var(--text-primary)]">$${f.stay.roomTotal.toFixed(2)}</td>
    `;
    tbody.appendChild(trRoom);

    // Food Orders Lines
    f.foodOrders.forEach(order => {
      order.items.forEach(item => {
        const trFood = document.createElement('tr');
        trFood.className = 'py-2 text-[var(--text-secondary)]';
        trFood.innerHTML = `
          <td class="py-2 text-[11px]"><span class="text-[var(--accent)] mr-1">🍽️</span> ${item.name}</td>
          <td class="py-2 text-center font-mono">${item.quantity}</td>
          <td class="py-2 text-right font-mono">$${item.price.toFixed(2)}</td>
          <td class="py-2 text-right font-mono font-bold text-[var(--text-primary)]">$${item.lineTotal.toFixed(2)}</td>
        `;
        tbody.appendChild(trFood);
      });
    });

    // Totals
    document.getElementById('inv-room-subtotal').textContent = `$${f.stay.roomTotal.toFixed(2)}`;
    document.getElementById('inv-food-subtotal').textContent = `$${f.foodTotal.toFixed(2)}`;
    document.getElementById('inv-advance').textContent = `-$${f.totals.advanceDeposit.toFixed(2)}`;
    document.getElementById('inv-balance-due').textContent = `$${f.totals.balanceDue.toFixed(2)}`;

    if (f.status === 'checked_out') {
      const btn = document.getElementById('btn-checkout');
      btn.disabled = true;
      btn.className = 'px-4 py-2 rounded-[var(--radius-sm)] bg-gray-400 text-white font-bold text-xs cursor-not-allowed';
      btn.textContent = 'Already Checked Out';
    }
  }

  async function settleAndCheckout() {
    if (!confirm('Confirm settling folio and checking out this guest? Room will be marked for housekeeping.')) return;
    const btn = document.getElementById('btn-checkout');
    btn.disabled = true;
    btn.textContent = 'Checking Out...';

    try {
      const res = await fetch('/api/hotel/check-out', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ booking_id: BOOKING_ID })
      });
      const data = await res.json();
      if (data.success) {
        window.showToast('Guest checked out successfully!');
        window.location.href = '/dashboard/hotel/rooms';
      } else {
        alert(data.error?.message || 'Checkout failed');
        btn.disabled = false;
        btn.textContent = 'Settle & Check-Out';
      }
    } catch(err) {
      console.error(err);
      btn.disabled = false;
    }
  }
</script>
@endsection
