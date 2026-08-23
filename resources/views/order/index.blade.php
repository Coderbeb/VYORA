@extends('layouts.app')

@section('title', 'Digital Menu - Vyora')

@section('content')
<div class="min-h-screen pb-32 bg-[var(--bg-base)] text-[var(--text-primary)]">
  <!-- Top Bar -->
  <header class="sticky top-0 z-30 bg-[var(--bg-surface)]/90 backdrop-blur-md border-b border-[var(--border)] px-4 py-3 shadow-sm">
    <div class="max-w-2xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-[var(--accent)] text-white flex items-center justify-center font-bold text-base">
          Q
        </div>
        <div>
          <h1 id="restaurant-name-header" class="font-serif font-bold text-base tracking-tight truncate max-w-[180px] sm:max-w-xs">Loading Menu...</h1>
          <span class="inline-block px-2 py-0.5 rounded-full bg-[var(--accent-glow)] text-[var(--accent)] font-bold text-[10px] uppercase tracking-wide">
            Table #{{ $tableNumber }}
          </span>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button id="tracking-toggle-btn" onclick="toggleTrackingSheet()" class="hidden px-2.5 py-1.5 rounded-[var(--radius-sm)] bg-amber-500/10 text-amber-600 border border-amber-500/20 font-bold text-xs flex items-center gap-1 animate-pulse">
          <i data-lucide="clock" class="w-3.5 h-3.5"></i>
          <span>Track Order</span>
        </button>
        <button onclick="toggleTheme()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">
          <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
          <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Main Container -->
  <main class="max-w-2xl mx-auto px-4 pt-4 space-y-4">
    <!-- Service Request Quick Bar (Call Waiter / Water) -->
    <div class="grid grid-cols-2 gap-2">
      <button onclick="requestService('waiter')" id="btn-waiter" class="py-2.5 px-3 rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border)] hover:border-[var(--accent)] text-xs font-bold text-[var(--text-primary)] transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95">
        <i data-lucide="bell" class="w-4 h-4 text-[var(--accent)]"></i>
        <span>Call Waiter</span>
      </button>
      <button onclick="requestService('water')" id="btn-water" class="py-2.5 px-3 rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border)] hover:border-blue-500 text-xs font-bold text-[var(--text-primary)] transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95">
        <i data-lucide="droplet" class="w-4 h-4 text-blue-500"></i>
        <span>Request Water</span>
      </button>
    </div>

    <!-- Search Input -->
    <div class="relative">
      <input type="text" id="cust-search" oninput="filterCustomerDishes()" placeholder="Search dishes & drinks..." class="w-full pl-9 pr-4 py-2.5 text-xs bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)] shadow-sm">
      <i data-lucide="search" class="w-4 h-4 text-[var(--text-muted)] absolute left-3 top-1/2 -translate-y-1/2"></i>
    </div>

    <!-- Category Tabs Filter -->
    <div id="cust-category-tabs" class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
      <button onclick="selectCustomerCategory('all')" id="cust-tab-all" class="px-4 py-1.5 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm shrink-0">
        All Items
      </button>
    </div>

    <!-- Dish List Container -->
    <div id="cust-dishes-container" class="space-y-3 pt-2">
      <div class="p-12 text-center text-xs text-[var(--text-muted)]">
        <span class="animate-spin inline-block mr-2">⏳</span> Loading digital menu...
      </div>
    </div>
  </main>

  <!-- Sticky Bottom Cart Bar -->
  <div id="bottom-cart-bar" class="fixed bottom-0 inset-x-0 p-4 bg-[var(--bg-surface)]/95 backdrop-blur-md border-t border-[var(--border)] z-40 hidden shadow-2xl transition-all">
    <div class="max-w-2xl mx-auto flex items-center justify-between">
      <div>
        <span id="cart-item-count" class="text-xs font-bold text-[var(--text-primary)]">0 items</span>
        <h4 id="cart-total-price" class="font-serif font-bold text-lg text-[var(--accent)]">$0.00</h4>
      </div>
      <button onclick="openCartModal()" class="px-6 py-3 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-md transition flex items-center gap-2 active:scale-95">
        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
        <span>View Order</span>
      </button>
    </div>
  </div>

  <!-- Cart & Checkout Modal / Bottom Sheet -->
  <div id="cart-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-t-[var(--radius-xl)] sm:rounded-[var(--radius-xl)] shadow-2xl w-full max-w-lg p-6 max-h-[85vh] flex flex-col justify-between">
      <div class="overflow-y-auto pr-1">
        <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
          <div class="flex items-center gap-2">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-[var(--accent)]"></i>
            <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Your Table Order</h3>
          </div>
          <button onclick="closeCartModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
            <i data-lucide="x" class="w-5 h-5"></i>
          </button>
        </div>

        <!-- Cart Items Container -->
        <div id="cart-modal-items" class="divide-y divide-[var(--border)] text-xs mb-4">
          <!-- Populated by JS -->
        </div>

        <!-- Dietary Instructions / Notes -->
        <div class="mb-4">
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Cooking Instructions / Dietary Notes</label>
          <textarea id="order-notes" rows="2" placeholder="e.g. Less spicy, dressing on the side..." class="w-full px-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]"></textarea>
        </div>

        <!-- Price Summary -->
        <div class="pt-3 border-t-2 border-[var(--border)] flex justify-between items-center text-sm font-bold text-[var(--text-primary)]">
          <span>Order Total</span>
          <span id="modal-grand-total" class="font-mono text-base text-[var(--accent)]">$0.00</span>
        </div>
      </div>

      <!-- Place Order Submit Button -->
      <div class="pt-4 border-t border-[var(--border)]">
        <button onclick="placeOrder()" id="place-order-btn" class="w-full py-3.5 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2 active:scale-95">
          <span>Submit Order to Kitchen</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Live Order Tracking Sheet Modal -->
  <div id="tracking-modal" class="fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-t-[var(--radius-xl)] sm:rounded-[var(--radius-xl)] shadow-2xl w-full max-w-lg p-6 max-h-[85vh] overflow-y-auto">
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Live Order Status</h3>
        <button onclick="toggleTrackingSheet()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <div id="tracking-orders-list" class="space-y-4">
        <!-- Injected by JS -->
      </div>
    </div>
  </div>
</div>

<script>
  const OWNER_ID = "{{ $ownerId }}";
  const TABLE_NUMBER = parseInt("{{ $tableNumber }}", 10);
  const SESSION_KEY = `sessionToken_${OWNER_ID}_${TABLE_NUMBER}`;

  let allMenuItems = [];
  let allCategories = [];
  let cart = new Map(); // id -> { item, quantity }
  let activeCatFilter = 'all';
  let activeLiveOrders = [];

  document.addEventListener('DOMContentLoaded', () => {
    loadMenu();
    loadLiveOrders();
    initPusherListener();
  });

  function initPusherListener() {
    if (window.PUSHER_KEY && OWNER_ID) {
      const pusher = new Pusher(window.PUSHER_KEY, { cluster: window.PUSHER_CLUSTER || 'mt1' });
      const channel = pusher.subscribe('restaurant.' + OWNER_ID);

      channel.bind('order:updated', function(data) {
        if (data.tableNumber === TABLE_NUMBER) {
          loadLiveOrders();
        }
      });

      channel.bind('table:reset', function(data) {
        if (data.tableNumber === TABLE_NUMBER) {
          sessionStorage.removeItem(SESSION_KEY);
          activeLiveOrders = [];
          document.getElementById('tracking-toggle-btn').classList.add('hidden');
          document.getElementById('tracking-modal').classList.add('hidden');
          window.showToast('Your session has ended. Thank you for dining with us!');
        }
      });
    }
  }

  async function loadMenu() {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code') || '';
    const storedSession = sessionStorage.getItem(SESSION_KEY) || '';

    try {
      const res = await fetch(`/api/public/menu?ownerId=${OWNER_ID}&tableNumber=${TABLE_NUMBER}&code=${code}&sessionToken=${storedSession}`);
      const data = await res.json();

      if (data.success && data.data) {
        document.getElementById('restaurant-name-header').textContent = data.data.restaurant?.restaurantName || 'Vyora Dining';
        
        if (data.data.sessionToken) {
          sessionStorage.setItem(SESSION_KEY, data.data.sessionToken);
        }

        allMenuItems = data.data.items || [];
        allCategories = data.data.categories || [];

        renderCategoryTabs();
        renderDishes();
      } else {
        document.getElementById('cust-dishes-container').innerHTML = `
          <div class="p-8 rounded-[var(--radius-xl)] bg-red-500/10 border border-red-500/20 text-center text-xs text-red-600">
            <h4 class="font-bold text-sm mb-1">${data.error?.code === 'SESSION_EXPIRED' ? 'Session Expired' : 'Access Denied'}</h4>
            <p>${data.error?.message || 'Please scan the QR code on your table to view the menu.'}</p>
          </div>
        `;
      }
    } catch(err) {
      console.error(err);
    }
  }

  function renderCategoryTabs() {
    const container = document.getElementById('cust-category-tabs');
    container.innerHTML = `
      <button onclick="selectCustomerCategory('all')" id="cust-tab-all" class="px-4 py-1.5 rounded-full text-xs font-bold transition ${activeCatFilter === 'all' ? 'bg-[var(--accent)] text-white shadow-sm' : 'border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]'} shrink-0">
        All Items
      </button>
    `;

    allCategories.forEach(cat => {
      const btn = document.createElement('button');
      btn.onclick = () => selectCustomerCategory(cat.id);
      btn.className = `px-4 py-1.5 rounded-full text-xs font-bold transition shrink-0 ${activeCatFilter === cat.id ? 'bg-[var(--accent)] text-white shadow-sm' : 'border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]'}`;
      btn.textContent = cat.name;
      container.appendChild(btn);
    });
  }

  function selectCustomerCategory(catId) {
    activeCatFilter = catId;
    renderCategoryTabs();
    renderDishes();
  }

  function filterCustomerDishes() {
    renderDishes();
  }

  function renderDishes() {
    const container = document.getElementById('cust-dishes-container');
    const q = document.getElementById('cust-search').value.toLowerCase().trim();

    let filtered = allMenuItems.filter(i => i.isAvailable);
    if (activeCatFilter !== 'all') {
      filtered = filtered.filter(i => i.categoryId === activeCatFilter);
    }
    if (q) {
      filtered = filtered.filter(i => i.name.toLowerCase().includes(q) || (i.description && i.description.toLowerCase().includes(q)));
    }

    if (filtered.length === 0) {
      container.innerHTML = '<div class="p-8 text-center text-xs text-[var(--text-muted)]">No menu items match your search.</div>';
      return;
    }

    container.innerHTML = '';
    filtered.forEach(dish => {
      const card = document.createElement('div');
      card.className = 'p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex items-center justify-between gap-4 transition hover:border-[var(--accent)]';

      const qty = cart.get(dish.id)?.quantity || 0;

      let actionControl = '';
      if (qty === 0) {
        actionControl = `
          <button onclick="addToCart('${dish.id}')" class="px-4 py-2 rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition active:scale-95 shrink-0">
            + Add
          </button>
        `;
      } else {
        actionControl = `
          <div class="flex items-center gap-2 bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] p-1 shrink-0">
            <button onclick="removeFromCart('${dish.id}')" class="w-6 h-6 rounded bg-[var(--bg-surface)] text-[var(--text-primary)] font-bold text-xs flex items-center justify-center hover:bg-red-500 hover:text-white transition">-</button>
            <span class="font-bold text-xs text-[var(--accent)] w-4 text-center font-mono">${qty}</span>
            <button onclick="addToCart('${dish.id}')" class="w-6 h-6 rounded bg-[var(--accent)] text-white font-bold text-xs flex items-center justify-center hover:bg-[var(--accent-hover)] transition">+</button>
          </div>
        `;
      }

      const imgHtml = dish.imageUrl ? `
        <img src="${dish.imageUrl}" alt="${dish.name}" class="w-20 h-20 rounded-[var(--radius-md)] object-cover border border-[var(--border)] shrink-0">
      ` : '';

      card.innerHTML = `
        <div class="flex items-center gap-3 overflow-hidden">
          ${imgHtml}
          <div class="overflow-hidden">
            <h4 class="font-serif font-bold text-sm text-[var(--text-primary)] truncate">${dish.name}</h4>
            <p class="text-[11px] text-[var(--text-secondary)] line-clamp-2 mt-0.5">${dish.description || ''}</p>
            <div class="flex items-center gap-2 mt-1.5">
              <span class="font-mono font-bold text-xs text-[var(--accent)]">$${parseFloat(dish.price).toFixed(2)}</span>
              <span class="text-[10px] text-[var(--text-muted)]">⏱️ ${dish.preparationTime}m</span>
            </div>
          </div>
        </div>
        <div>
          ${actionControl}
        </div>
      `;

      container.appendChild(card);
    });

    lucide.createIcons();
    updateCartBar();
  }

  function addToCart(dishId) {
    const dish = allMenuItems.find(i => i.id === dishId);
    if (!dish) return;
    const existing = cart.get(dishId);
    if (existing) {
      existing.quantity++;
    } else {
      cart.set(dishId, { item: dish, quantity: 1 });
    }
    renderDishes();
  }

  function removeFromCart(dishId) {
    const existing = cart.get(dishId);
    if (!existing) return;
    if (existing.quantity > 1) {
      existing.quantity--;
    } else {
      cart.delete(dishId);
    }
    renderDishes();
    if (cart.size === 0) closeCartModal();
  }

  function updateCartBar() {
    const bar = document.getElementById('bottom-cart-bar');
    let count = 0;
    let total = 0;

    cart.forEach(c => {
      count += c.quantity;
      total += c.quantity * c.item.price;
    });

    if (count > 0) {
      bar.classList.remove('hidden');
      document.getElementById('cart-item-count').textContent = `${count} ${count === 1 ? 'dish' : 'dishes'} in cart`;
      document.getElementById('cart-total-price').textContent = `$${total.toFixed(2)}`;
    } else {
      bar.classList.add('hidden');
    }
  }

  function openCartModal() {
    const container = document.getElementById('cart-modal-items');
    container.innerHTML = '';
    let total = 0;

    cart.forEach(({ item, quantity }) => {
      total += quantity * item.price;
      const row = document.createElement('div');
      row.className = 'flex justify-between items-center py-2.5';
      row.innerHTML = `
        <div>
          <p class="font-bold text-[var(--text-primary)] text-xs">${item.name}</p>
          <span class="text-[10px] text-[var(--text-muted)] font-mono">$${item.price.toFixed(2)} each</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-1.5 bg-[var(--bg-base)] border border-[var(--border)] rounded p-0.5">
            <button onclick="removeFromCart('${item.id}'); openCartModal();" class="w-5 h-5 rounded text-xs font-bold hover:bg-red-500 hover:text-white">-</button>
            <span class="w-4 text-center font-mono font-bold text-[var(--accent)] text-xs">${quantity}</span>
            <button onclick="addToCart('${item.id}'); openCartModal();" class="w-5 h-5 rounded text-xs font-bold bg-[var(--accent)] text-white hover:bg-[var(--accent-hover)]">+</button>
          </div>
          <span class="font-mono font-bold text-xs text-[var(--text-primary)] w-12 text-right">$${(item.price * quantity).toFixed(2)}</span>
        </div>
      `;
      container.appendChild(row);
    });

    document.getElementById('modal-grand-total').textContent = `$${total.toFixed(2)}`;
    document.getElementById('cart-modal').classList.remove('hidden');
    lucide.createIcons();
  }

  function closeCartModal() {
    document.getElementById('cart-modal').classList.add('hidden');
  }

  async function placeOrder() {
    const btn = document.getElementById('place-order-btn');
    const sessionToken = sessionStorage.getItem(SESSION_KEY);
    const notes = document.getElementById('order-notes').value.trim();

    if (cart.size === 0) return;
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Transmitting to Kitchen...';

    const items = [];
    cart.forEach(({ item, quantity }) => {
      items.push({ menuItemId: item.id, quantity });
    });

    try {
      const res = await fetch('/api/public/orders', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          ownerId: OWNER_ID,
          tableNumber: TABLE_NUMBER,
          items,
          sessionToken,
          notes: notes || null,
        })
      });
      const data = await res.json();
      if (data.success) {
        window.playNotificationSound();
        window.showToast('🎉 Order placed successfully!');
        cart.clear();
        closeCartModal();
        renderDishes();
        loadLiveOrders();
        toggleTrackingSheet();
      } else {
        alert(data.error?.message || 'Failed to place order.');
      }
    } catch (err) {
      console.error(err);
      alert('Network error. Please try again.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<span>Submit Order to Kitchen</span><i data-lucide="arrow-right" class="w-4 h-4"></i>';
      lucide.createIcons();
    }
  }

  async function requestService(type) {
    const sessionToken = sessionStorage.getItem(SESSION_KEY);
    const btn = document.getElementById(`btn-${type}`);
    btn.disabled = true;

    try {
      const res = await fetch('/api/service-request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
          ownerId: OWNER_ID,
          tableNumber: TABLE_NUMBER,
          type,
          sessionToken,
        })
      });
      const data = await res.json();
      if (data.success) {
        window.showToast(type === 'water' ? '💧 Water order placed!' : '🛎️ Waiter notified!');
        if (type === 'water') loadLiveOrders();
      } else {
        alert(data.error?.message || 'Request failed');
      }
    } catch (err) {
      console.error(err);
    } finally {
      setTimeout(() => { btn.disabled = false; }, 30000); // 30s cooldown
    }
  }

  async function loadLiveOrders() {
    const sessionToken = sessionStorage.getItem(SESSION_KEY);
    if (!sessionToken) return;

    try {
      const res = await fetch(`/api/public/orders?ownerId=${OWNER_ID}&tableNumber=${TABLE_NUMBER}&sessionToken=${sessionToken}`);
      const data = await res.json();
      if (data.success && data.data && data.data.length > 0) {
        activeLiveOrders = data.data.filter(o => o.status !== 'cancelled');
        if (activeLiveOrders.length > 0) {
          document.getElementById('tracking-toggle-btn').classList.remove('hidden');
          renderTrackingOrders();
        }
      }
    } catch(err) {
      console.error(err);
    }
  }

  function toggleTrackingSheet() {
    const modal = document.getElementById('tracking-modal');
    modal.classList.toggle('hidden');
    renderTrackingOrders();
  }

  function renderTrackingOrders() {
    const container = document.getElementById('tracking-orders-list');
    container.innerHTML = '';

    if (activeLiveOrders.length === 0) {
      container.innerHTML = '<div class="p-8 text-center text-xs text-[var(--text-muted)]">No active orders placed in this session.</div>';
      return;
    }

    const steps = ['pending', 'preparing', 'ready', 'completed'];

    activeLiveOrders.forEach(order => {
      const card = document.createElement('div');
      card.className = 'p-4 rounded-[var(--radius-lg)] bg-[var(--bg-base)] border border-[var(--border)] space-y-3';

      const currentStepIdx = steps.indexOf(order.status);
      const itemsList = order.items.map(i => `${i.quantity}x ${i.menuItemName}`).join(', ');

      let statusMsg = 'Kitchen received your ticket';
      if (order.status === 'preparing') statusMsg = 'Chef is preparing your meal 🔥';
      else if (order.status === 'ready') statusMsg = 'Plated & Ready to serve! 🍽️';
      else if (order.status === 'completed') statusMsg = 'Served at table ✅';

      card.innerHTML = `
        <div class="flex justify-between items-center">
          <div>
            <span class="font-bold text-xs text-[var(--text-primary)]">Order Ticket</span>
            <p class="text-[11px] text-[var(--text-muted)] truncate max-w-xs">${itemsList}</p>
          </div>
          <span class="font-mono font-bold text-xs text-[var(--accent)]">$${parseFloat(order.totalAmount).toFixed(2)}</span>
        </div>

        <!-- Progress Timeline -->
        <div class="py-2">
          <div class="flex items-center justify-between relative mb-2">
            <div class="absolute top-1/2 left-0 right-0 h-1 bg-[var(--border)] -translate-y-1/2 z-0"></div>
            <div class="w-6 h-6 rounded-full ${currentStepIdx >= 0 ? 'bg-[var(--accent)] text-white' : 'bg-gray-200 text-gray-400'} flex items-center justify-center font-bold text-[10px] z-10">1</div>
            <div class="w-6 h-6 rounded-full ${currentStepIdx >= 1 ? 'bg-[var(--accent)] text-white' : 'bg-gray-200 text-gray-400'} flex items-center justify-center font-bold text-[10px] z-10">2</div>
            <div class="w-6 h-6 rounded-full ${currentStepIdx >= 2 ? 'bg-[var(--accent)] text-white' : 'bg-gray-200 text-gray-400'} flex items-center justify-center font-bold text-[10px] z-10">3</div>
            <div class="w-6 h-6 rounded-full ${currentStepIdx >= 3 ? 'bg-[var(--accent)] text-white' : 'bg-gray-200 text-gray-400'} flex items-center justify-center font-bold text-[10px] z-10">4</div>
          </div>
          <p class="text-center text-xs font-bold text-[var(--accent)]">${statusMsg}</p>
        </div>
      `;
      container.appendChild(card);
    });

    lucide.createIcons();
  }
</script>
@endsection
