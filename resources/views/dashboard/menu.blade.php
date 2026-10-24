@extends('layouts.dashboard')

@section('title', 'Menu Catalog & Categories - Vyora')
@section('page_title', 'Menu Catalog Management')

@push('styles')
<style>
  /* Apple HIG Smooth Micro-Interactions */
  .dish-card-transition {
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
  }
  .dish-card-transition:hover {
    transform: translateY(-2px);
  }
  /* Category tabs horizontal scrolling without ugly scrollbar */
  .category-scroll-container::-webkit-scrollbar {
    height: 4px;
  }
  .category-scroll-container::-webkit-scrollbar-thumb {
    background: rgba(3, 77, 55, 0.2);
    border-radius: 9999px;
  }
</style>
@endpush

@section('dashboard_content')
<div class="space-y-6">

  <!-- 1. OPERATIONAL CATALOG HUD (High Visibility Metrics) -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <!-- Metric 1: Total Catalog Dishes -->
    <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col justify-between relative overflow-hidden transition hover:shadow-md">
      <div class="flex items-center justify-between pb-2 border-b border-[var(--border)]">
        <span class="text-xs font-bold uppercase tracking-wider text-[var(--accent)] flex items-center gap-1.5">
          <i data-lucide="utensils" class="w-3.5 h-3.5 text-[var(--accent)]"></i>
          <span>Total Dishes</span>
        </span>
        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Catalog</span>
      </div>
      <div class="pt-2 flex items-baseline justify-between">
        <h3 id="stat-total-dishes" class="font-serif font-black text-2xl sm:text-3xl text-slate-900 dark:text-slate-50">0</h3>
        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">items</span>
      </div>
      <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Live active menu directory</p>
    </div>

    <!-- Metric 2: In Stock (Available on QR) -->
    <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col justify-between relative overflow-hidden transition hover:shadow-md">
      <div class="flex items-center justify-between pb-2 border-b border-[var(--border)]">
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
          <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
          <span>In Stock</span>
        </span>
        <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-500/10 px-1.5 py-0.5 rounded">Active</span>
      </div>
      <div class="pt-2 flex items-baseline justify-between">
        <h3 id="stat-instock-dishes" class="font-serif font-black text-2xl sm:text-3xl text-emerald-700 dark:text-emerald-300">0</h3>
        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">orderable</span>
      </div>
      <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Visible on guest QR table menu</p>
    </div>

    <!-- Metric 3: Out of Stock (86'd) -->
    <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col justify-between relative overflow-hidden transition hover:shadow-md">
      <div class="flex items-center justify-between pb-2 border-b border-[var(--border)]">
        <span class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
          <i data-lucide="slash" class="w-3.5 h-3.5 text-amber-600"></i>
          <span>86'd / Sold Out</span>
        </span>
        <span class="text-[10px] font-bold text-amber-700 dark:text-amber-300 bg-amber-500/10 px-1.5 py-0.5 rounded">Paused</span>
      </div>
      <div class="pt-2 flex items-baseline justify-between">
        <h3 id="stat-soldout-dishes" class="font-serif font-black text-2xl sm:text-3xl text-amber-700 dark:text-amber-300">0</h3>
        <span class="text-xs font-bold text-amber-700 dark:text-amber-400">temporarily out</span>
      </div>
      <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Hidden from ordering flow</p>
    </div>

    <!-- Metric 4: Menu Categories -->
    <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs flex flex-col justify-between relative overflow-hidden transition hover:shadow-md">
      <div class="flex items-center justify-between pb-2 border-b border-[var(--border)]">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
          <i data-lucide="folders" class="w-3.5 h-3.5 text-slate-600 dark:text-slate-400"></i>
          <span>Categories</span>
        </span>
        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300">Sections</span>
      </div>
      <div class="pt-2 flex items-baseline justify-between">
        <h3 id="stat-total-categories" class="font-serif font-black text-2xl sm:text-3xl text-slate-900 dark:text-slate-50">0</h3>
        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">groups</span>
      </div>
      <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Menu sections configured</p>
    </div>
  </div>

  <!-- 2. CONTROL TOOLBAR & FILTER CONSOLE (High Contrast & Clear Typography) -->
  <div class="p-4 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-xs space-y-3">
    <!-- Top Row: Actions & Search -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
      <!-- Search Box with Clear Button -->
      <div class="relative flex-1 max-w-md">
        <input type="text" id="menu-search" oninput="filterDishes()" placeholder="Search dishes by name, ingredients, category..." class="w-full pl-9 pr-9 py-2.5 text-xs font-medium bg-[var(--bg-base)] border border-slate-300 dark:border-zinc-700 rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-zinc-500 shadow-xs transition">
        <i data-lucide="search" class="w-4 h-4 text-slate-500 dark:text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        <button id="search-clear-btn" onclick="clearSearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
          <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center gap-2 justify-end shrink-0">
        <!-- Manage Categories Button -->
        <button onclick="openCategoryModal()" class="px-3.5 py-2 text-xs font-bold rounded-[var(--radius-md)] border-2 border-slate-300 dark:border-zinc-700 hover:border-[var(--accent)] bg-[var(--bg-surface)] text-slate-800 dark:text-slate-100 hover:text-[var(--accent)] shadow-xs transition flex items-center gap-1.5 active:scale-95">
          <i data-lucide="folder-plus" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
          <span>Categories</span>
        </button>

        <!-- Add Dish Primary CTA -->
        <button onclick="openAddDishModal()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm transition flex items-center gap-1.5 active:scale-95">
          <i data-lucide="plus" class="w-4 h-4 stroke-[3]"></i>
          <span>Add Dish</span>
        </button>
      </div>
    </div>

    <!-- Bottom Row: Filter Pills (Dietary & Availability) -->
    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-[var(--border)]">
      <!-- Dietary Filters -->
      <div class="flex items-center gap-1.5">
        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mr-1">Diet:</span>
        <button onclick="setDietFilter('all')" id="filter-diet-all" class="px-2.5 py-1 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-xs">
          All
        </button>
        <button onclick="setDietFilter('veg')" id="filter-diet-veg" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-200 hover:bg-emerald-500/10 hover:border-emerald-500/30 transition flex items-center gap-1">
          <svg class="w-2.5 h-2.5 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3" stroke="#059669" stroke-width="2" fill="#ECFDF5"/><circle cx="8" cy="8" r="3.5" fill="#059669"/></svg>
          <span>Veg</span>
        </button>
        <button onclick="setDietFilter('nonveg')" id="filter-diet-nonveg" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-200 hover:bg-orange-500/10 hover:border-orange-500/30 transition flex items-center gap-1">
          <svg class="w-2.5 h-2.5 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3" stroke="#DC2626" stroke-width="2" fill="#FEF2F2"/><polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/></svg>
          <span>Non-Veg</span>
        </button>
      </div>

      <!-- Stock Availability Filters -->
      <div class="flex items-center gap-1.5">
        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mr-1">Stock:</span>
        <button onclick="setStockFilter('all')" id="filter-stock-all" class="px-2.5 py-1 rounded-full text-xs font-bold transition bg-slate-800 dark:bg-slate-200 text-white dark:text-slate-900 shadow-xs">
          All
        </button>
        <button onclick="setStockFilter('available')" id="filter-stock-available" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-200 hover:bg-[var(--bg-hover)] transition">
          In Stock
        </button>
        <button onclick="setStockFilter('unavailable')" id="filter-stock-unavailable" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-200 hover:bg-[var(--bg-hover)] transition">
          86'd (Out)
        </button>
      </div>
    </div>
  </div>

  <!-- 3. CATEGORY HORIZONTAL CAROUSEL TABS -->
  <div class="relative">
    <div id="category-tabs" class="category-scroll-container flex items-center gap-2 overflow-x-auto pb-1.5 scrollbar-thin">
      <button onclick="selectCategory('all')" id="tab-all" class="px-4 py-2 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-xs shrink-0 flex items-center gap-1.5">
        <span>All Items</span>
        <span id="tab-count-all" class="px-1.5 py-0.2 rounded-full bg-white/20 text-[10px] font-bold">0</span>
      </button>
      <!-- Dynamic Category Tabs injected here with item counts -->
    </div>
  </div>

  <!-- 4. MENU DISHES GRID -->
  <div id="dishes-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <div class="col-span-full p-12 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 flex flex-col items-center justify-center gap-2">
      <div class="w-6 h-6 border-2 border-[var(--accent)] border-t-transparent rounded-full animate-spin"></div>
      <span>Loading catalog items...</span>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- 5. ADD / EDIT DISH MODAL (High-Contrast)  -->
<!-- ========================================== -->
<div id="dish-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border-2 border-slate-300 dark:border-zinc-700 rounded-[var(--radius-xl)] shadow-2xl w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
    <!-- Modal Header -->
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-[var(--accent)] text-white flex items-center justify-center shadow-xs">
          <i data-lucide="utensils" class="w-4 h-4"></i>
        </div>
        <h3 id="dish-modal-title" class="font-serif font-black text-xl text-slate-900 dark:text-slate-50">Add New Dish</h3>
      </div>
      <button onclick="closeDishModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Dynamic Error Alert Banner (Revealed on error) -->
    <div id="dish-error-banner" class="hidden mb-4 p-3 rounded-[var(--radius-md)] bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-300 text-xs font-semibold flex items-start gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5 text-red-500"></i>
      <div id="dish-error-text" class="flex-1"></div>
    </div>

    <!-- Dish Form -->
    <form onsubmit="submitDish(event)" class="space-y-4">
      <input type="hidden" id="dish-id">

      <!-- Dish Name -->
      <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1">
          Dish Name <span class="text-red-500">*</span>
        </label>
        <input type="text" id="dish-name" required placeholder="e.g. Butter Chicken Special" class="w-full px-3.5 py-2.5 text-sm font-semibold bg-[var(--bg-base)] border border-slate-300 dark:border-zinc-700 rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-slate-900 dark:text-slate-100 placeholder-slate-400 transition">
      </div>

      <!-- Price & Preparation Time -->
      <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1">
            Price (₹) <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500 dark:text-slate-400">₹</span>
            <input type="number" step="0.01" min="0" id="dish-price" required placeholder="299.00" class="w-full pl-8 pr-3 py-2.5 text-sm font-mono font-bold bg-[var(--bg-base)] border border-slate-300 dark:border-zinc-700 rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-slate-900 dark:text-slate-100 placeholder-slate-400 transition">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1">
            Prep Time (mins)
          </label>
          <div class="relative">
            <input type="number" min="1" max="180" id="dish-prep-time" value="15" class="w-full pl-3 pr-10 py-2.5 text-sm font-mono font-bold bg-[var(--bg-base)] border border-slate-300 dark:border-zinc-700 rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-slate-900 dark:text-slate-100 transition">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">min</span>
          </div>
        </div>
      </div>

      <!-- Category Select -->
      <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1">
          Menu Category
        </label>
        <select id="dish-category" class="w-full px-3.5 py-2.5 text-sm font-semibold bg-[var(--bg-base)] border border-slate-300 dark:border-zinc-700 rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-slate-900 dark:text-slate-100 transition">
          <option value="">Uncategorized</option>
        </select>
      </div>

      <!-- Dietary Selection (Visual Radio Tiles) -->
      <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1.5">
          Dietary Classification <span class="text-red-500">*</span>
        </label>
        <div class="grid grid-cols-2 gap-3">
          <!-- Vegetarian Tile -->
          <label class="relative flex items-center gap-3 p-3 rounded-[var(--radius-md)] border-2 border-emerald-500/40 bg-emerald-500/5 cursor-pointer hover:bg-emerald-500/10 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-500/15">
            <input type="radio" name="dish-diet-type" id="dish-type-veg" value="1" checked class="text-emerald-600 focus:ring-emerald-500">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="1" width="14" height="14" rx="3" stroke="#059669" stroke-width="2" fill="#ECFDF5"/>
                <circle cx="8" cy="8" r="3.5" fill="#059669"/>
              </svg>
              <div>
                <span class="block text-xs font-bold text-emerald-800 dark:text-emerald-300">Vegetarian</span>
                <span class="text-[10px] text-emerald-700/80 dark:text-emerald-400/80">Green emblem</span>
              </div>
            </div>
          </label>

          <!-- Non-Vegetarian Tile -->
          <label class="relative flex items-center gap-3 p-3 rounded-[var(--radius-md)] border-2 border-orange-500/40 bg-orange-500/5 cursor-pointer hover:bg-orange-500/10 transition has-[:checked]:border-orange-600 has-[:checked]:bg-orange-500/15">
            <input type="radio" name="dish-diet-type" id="dish-type-nonveg" value="0" class="text-orange-600 focus:ring-orange-500">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="1" width="14" height="14" rx="3" stroke="#DC2626" stroke-width="2" fill="#FEF2F2"/>
                <polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/>
              </svg>
              <div>
                <span class="block text-xs font-bold text-orange-800 dark:text-orange-300">Non-Veg</span>
                <span class="text-[10px] text-orange-700/80 dark:text-orange-400/80">Red/Orange card</span>
              </div>
            </div>
          </label>
        </div>
      </div>

      <!-- Description -->
      <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1">
          Culinary Description
        </label>
        <textarea id="dish-desc" rows="2" placeholder="Brief description of flavours, spices, and preparation..." class="w-full px-3.5 py-2.5 text-xs font-medium bg-[var(--bg-base)] border border-slate-300 dark:border-zinc-700 rounded-[var(--radius-md)] focus:outline-none focus:border-[var(--accent)] text-slate-900 dark:text-slate-100 placeholder-slate-400 transition"></textarea>
      </div>

      <!-- Dish Image & Preview -->
      <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1">
          Dish Photo
        </label>
        <!-- Preview Box -->
        <div id="dish-image-preview-wrapper" class="hidden mb-2 relative w-full h-32 rounded-[var(--radius-md)] overflow-hidden border border-slate-300 dark:border-zinc-700 bg-slate-100 dark:bg-zinc-800">
          <img id="dish-image-preview" src="" alt="Dish Preview" class="w-full h-full object-cover">
          <button type="button" onclick="clearDishImage()" class="absolute top-2 right-2 p-1.5 rounded-full bg-black/60 text-white hover:bg-black transition" title="Remove photo">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
          </button>
        </div>
        <div class="space-y-2">
          <input type="file" id="dish-image-file" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewSelectedFile(event)" class="w-full text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-2 file:px-3.5 file:rounded-[var(--radius-sm)] file:border-0 file:text-xs file:font-bold file:bg-[var(--accent)] file:text-white hover:file:opacity-90 transition cursor-pointer">
          <div class="flex items-center gap-2">
            <span class="text-[10px] font-semibold text-slate-500 uppercase">OR Image URL:</span>
            <input type="url" id="dish-image-url" placeholder="https://..." oninput="previewUrlInput(this.value)" class="flex-1 px-2.5 py-1 text-xs font-mono bg-[var(--bg-base)] border border-slate-300 dark:border-zinc-700 rounded-[var(--radius-sm)] text-slate-900 dark:text-slate-100">
          </div>
        </div>
      </div>

      <!-- Availability Switch -->
      <div class="flex items-center justify-between p-3 rounded-[var(--radius-md)] bg-slate-50 dark:bg-zinc-800/60 border border-slate-200 dark:border-zinc-700">
        <div>
          <span class="block text-xs font-bold text-slate-900 dark:text-slate-100">Order Availability (In Stock)</span>
          <span class="text-[10px] text-slate-500 dark:text-slate-400">When toggled off, customers cannot order this item</span>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" id="dish-available" checked class="sr-only peer">
          <div class="w-11 h-6 bg-slate-300 dark:bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[var(--accent)]"></div>
        </label>
      </div>

      <!-- Action Footer -->
      <div class="flex items-center justify-end gap-3 pt-4 border-t border-[var(--border)]">
        <button type="button" onclick="closeDishModal()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-md)] border border-slate-300 dark:border-zinc-700 hover:bg-slate-100 dark:hover:bg-zinc-800 text-slate-700 dark:text-slate-300 transition">
          Cancel
        </button>
        <button type="submit" id="dish-submit-btn" class="px-5 py-2 text-xs font-black rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-md transition flex items-center gap-1.5 active:scale-95">
          <i data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
          <span id="dish-submit-btn-text">Save Dish</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================== -->
<!-- 6. CATEGORY MANAGEMENT MODAL (Add / Edit)      -->
<!-- ============================================== -->
<div id="category-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border-2 border-slate-300 dark:border-zinc-700 rounded-[var(--radius-xl)] shadow-2xl w-full max-w-md p-6 relative max-h-[85vh] flex flex-col justify-between">
    <div>
      <!-- Header -->
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-[var(--accent)] text-white flex items-center justify-center shadow-xs">
            <i data-lucide="folder-plus" class="w-4 h-4"></i>
          </div>
          <div>
            <h3 class="font-serif font-black text-xl text-slate-900 dark:text-slate-50">Menu Categories</h3>
            <p class="text-[10px] text-slate-500 dark:text-slate-400">Organize dishes into customer menu tabs</p>
          </div>
        </div>
        <button onclick="closeCategoryModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Add Category Form -->
      <form onsubmit="submitCategory(event)" class="space-y-2 mb-4 p-3 rounded-[var(--radius-md)] bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700">
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
          Create New Category
        </label>
        <div class="flex gap-2">
          <input type="text" id="cat-name-input" required placeholder="e.g. Starters, Tandoor, Mocktails" class="flex-1 px-3 py-2 text-xs font-semibold bg-[var(--bg-base)] border border-slate-300 dark:border-zinc-700 rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-slate-900 dark:text-slate-100 placeholder-slate-400">
          <button type="submit" id="cat-submit-btn" class="px-4 py-2 bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs rounded-[var(--radius-sm)] shadow-xs transition active:scale-95">
            Add
          </button>
        </div>
      </form>

      <!-- Category List -->
      <div class="space-y-1">
        <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Existing Categories:</span>
        <div id="category-list" class="space-y-1.5 max-h-64 overflow-y-auto pr-1">
          <!-- Injected by JS -->
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="pt-4 border-t border-[var(--border)] text-right">
      <button onclick="closeCategoryModal()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-md)] border border-slate-300 dark:border-zinc-700 hover:bg-slate-100 dark:hover:bg-zinc-800 text-slate-700 dark:text-slate-300 transition">
        Done
      </button>
    </div>
  </div>
</div>

<!-- Category Edit Prompt Dialog (Small Inline Modal) -->
<div id="cat-edit-modal" class="fixed inset-0 bg-black/70 z-60 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border-2 border-slate-300 dark:border-zinc-700 rounded-[var(--radius-xl)] shadow-2xl w-full max-w-sm p-5 space-y-4">
    <h4 class="font-serif font-black text-lg text-slate-900 dark:text-slate-50">Rename Category</h4>
    <input type="hidden" id="edit-cat-id">
    <div>
      <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">Category Name</label>
      <input type="text" id="edit-cat-name" required class="w-full px-3 py-2 text-sm font-semibold bg-[var(--bg-base)] border border-slate-300 dark:border-zinc-700 rounded-[var(--radius-md)] text-slate-900 dark:text-slate-100 focus:outline-none focus:border-[var(--accent)]">
    </div>
    <div class="flex justify-end gap-2 pt-2 border-t border-[var(--border)]">
      <button onclick="closeCatEditModal()" class="px-3.5 py-1.5 text-xs font-semibold rounded-[var(--radius-md)] border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-300">Cancel</button>
      <button onclick="saveCategoryRename()" class="px-4 py-1.5 text-xs font-bold rounded-[var(--radius-md)] bg-[var(--accent)] text-white">Save</button>
    </div>
  </div>
</div>

<script>
  let categories = [];
  let menuItems = [];
  let selectedCatId = 'all';
  let activeDietFilter = 'all'; // 'all' | 'veg' | 'nonveg'
  let activeStockFilter = 'all'; // 'all' | 'available' | 'unavailable'

  document.addEventListener('DOMContentLoaded', () => {
    loadMenuData();
  });

  // Central data loader
  async function loadMenuData() {
    try {
      const [catRes, itemRes] = await Promise.all([
        fetch('/api/categories', { headers: window.getAuthHeader() }),
        fetch('/api/menu', { headers: window.getAuthHeader() })
      ]);
      const catData = await catRes.json();
      const itemData = await itemRes.json();

      if (catData.success) categories = catData.data || [];
      if (itemData.success) menuItems = itemData.data || [];

      updateKpis();
      renderCategories();
      renderDishes();
    } catch (err) {
      console.error('Failed to load menu data:', err);
      window.showToast?.('Failed to load menu items', 'error');
    }
  }

  // Update top operational HUD
  function updateKpis() {
    const total = menuItems.length;
    const inStock = menuItems.filter(i => i.isAvailable).length;
    const soldOut = total - inStock;

    document.getElementById('stat-total-dishes').textContent = total;
    document.getElementById('stat-instock-dishes').textContent = inStock;
    document.getElementById('stat-soldout-dishes').textContent = soldOut;
    document.getElementById('stat-total-categories').textContent = categories.length;
    document.getElementById('tab-count-all').textContent = total;
  }

  // Render category carousel tabs and dropdowns
  function renderCategories() {
    const tabsContainer = document.getElementById('category-tabs');
    const selectEl = document.getElementById('dish-category');
    const catList = document.getElementById('category-list');

    // Retain "All Items" tab
    tabsContainer.innerHTML = `
      <button onclick="selectCategory('all')" id="tab-all" class="px-4 py-2 rounded-full text-xs font-bold transition shrink-0 flex items-center gap-1.5 ${selectedCatId === 'all' ? 'bg-[var(--accent)] text-white shadow-xs' : 'bg-[var(--bg-surface)] border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-200 hover:border-[var(--accent)]'}">
        <span>All Items</span>
        <span id="tab-count-all" class="px-1.5 py-0.2 rounded-full ${selectedCatId === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-zinc-800 text-slate-700 dark:text-slate-300'} text-[10px] font-bold">${menuItems.length}</span>
      </button>
    `;

    selectEl.innerHTML = '<option value="">Uncategorized</option>';
    catList.innerHTML = '';

    categories.forEach(cat => {
      // Calculate item count per category
      const count = menuItems.filter(i => i.categoryId === cat.id).length;

      // 1. Tab Button
      const isSelected = selectedCatId === cat.id;
      const tabBtn = document.createElement('button');
      tabBtn.onclick = () => selectCategory(cat.id);
      tabBtn.className = `px-4 py-2 rounded-full text-xs font-bold transition shrink-0 flex items-center gap-1.5 ${isSelected ? 'bg-[var(--accent)] text-white shadow-xs' : 'bg-[var(--bg-surface)] border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-200 hover:border-[var(--accent)]'}`;
      tabBtn.innerHTML = `
        <span>${escapeHtml(cat.name)}</span>
        <span class="px-1.5 py-0.2 rounded-full ${isSelected ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-zinc-800 text-slate-700 dark:text-slate-300'} text-[10px] font-bold">${count}</span>
      `;
      tabsContainer.appendChild(tabBtn);

      // 2. Dish Modal Dropdown Option
      const opt = document.createElement('option');
      opt.value = cat.id;
      opt.textContent = cat.name;
      selectEl.appendChild(opt);

      // 3. Management List Item (with Edit + Delete)
      const item = document.createElement('div');
      item.className = 'flex items-center justify-between p-2.5 rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-slate-200 dark:border-zinc-700 text-xs';
      item.innerHTML = `
        <div class="flex items-center gap-2">
          <i data-lucide="folder" class="w-4 h-4 text-[var(--accent)]"></i>
          <span class="font-bold text-slate-900 dark:text-slate-100">${escapeHtml(cat.name)}</span>
          <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded">${count} dishes</span>
        </div>
        <div class="flex items-center gap-1">
          <button onclick="openCatEditModal('${cat.id}', '${escapeJs(cat.name)}')" class="p-1 rounded text-slate-500 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-zinc-800" title="Rename Category">
            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
          </button>
          <button onclick="deleteCategory('${cat.id}')" class="p-1 rounded text-red-500 hover:text-red-700 hover:bg-red-500/10" title="Delete Category">
            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
          </button>
        </div>
      `;
      catList.appendChild(item);
    });

    if (categories.length === 0) {
      catList.innerHTML = '<div class="p-4 text-center text-xs text-slate-400">No categories created yet. Create one above!</div>';
    }

    if (window.lucide) window.lucide.createIcons();
  }

  function selectCategory(id) {
    selectedCatId = id;
    renderCategories();
    renderDishes();
  }

  function setDietFilter(filter) {
    activeDietFilter = filter;
    ['all', 'veg', 'nonveg'].forEach(f => {
      const btn = document.getElementById(`filter-diet-${f}`);
      if (!btn) return;
      if (f === filter) {
        btn.className = 'px-2.5 py-1 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-xs';
      } else {
        btn.className = 'px-2.5 py-1 rounded-full text-xs font-semibold border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-200 hover:bg-[var(--bg-hover)] transition flex items-center gap-1';
      }
    });
    renderDishes();
  }

  function setStockFilter(filter) {
    activeStockFilter = filter;
    ['all', 'available', 'unavailable'].forEach(f => {
      const btn = document.getElementById(`filter-stock-${f}`);
      if (!btn) return;
      if (f === filter) {
        btn.className = 'px-2.5 py-1 rounded-full text-xs font-bold transition bg-slate-800 dark:bg-slate-200 text-white dark:text-slate-900 shadow-xs';
      } else {
        btn.className = 'px-2.5 py-1 rounded-full text-xs font-semibold border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-200 hover:bg-[var(--bg-hover)] transition';
      }
    });
    renderDishes();
  }

  function filterDishes() {
    const q = document.getElementById('menu-search').value.trim();
    const clearBtn = document.getElementById('search-clear-btn');
    if (q) clearBtn.classList.remove('hidden');
    else clearBtn.classList.add('hidden');
    renderDishes();
  }

  function clearSearch() {
    document.getElementById('menu-search').value = '';
    document.getElementById('search-clear-btn').classList.add('hidden');
    renderDishes();
  }

  // Render dish cards with high contrast and luxury styling
  function renderDishes() {
    const grid = document.getElementById('dishes-grid');
    const q = document.getElementById('menu-search').value.toLowerCase().trim();

    let filtered = menuItems;

    // Filter by Category
    if (selectedCatId !== 'all') {
      filtered = filtered.filter(i => i.categoryId === selectedCatId);
    }

    // Filter by Diet
    if (activeDietFilter === 'veg') {
      filtered = filtered.filter(i => i.isVeg === true || i.is_veg === true || i.isVeg === 1 || i.is_veg === 1);
    } else if (activeDietFilter === 'nonveg') {
      filtered = filtered.filter(i => i.isVeg === false || i.is_veg === false || i.isVeg === 0 || i.is_veg === 0);
    }

    // Filter by Stock
    if (activeStockFilter === 'available') {
      filtered = filtered.filter(i => i.isAvailable);
    } else if (activeStockFilter === 'unavailable') {
      filtered = filtered.filter(i => !i.isAvailable);
    }

    // Filter by Search Query
    if (q) {
      filtered = filtered.filter(i =>
        i.name.toLowerCase().includes(q) ||
        (i.description && i.description.toLowerCase().includes(q)) ||
        (i.category?.name && i.category.name.toLowerCase().includes(q))
      );
    }

    if (filtered.length === 0) {
      grid.innerHTML = `
        <div class="col-span-full p-12 text-center rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-dashed border-slate-300 dark:border-zinc-700 flex flex-col items-center justify-center gap-3">
          <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-400">
            <i data-lucide="utensils" class="w-6 h-6"></i>
          </div>
          <h4 class="font-serif font-bold text-base text-slate-800 dark:text-slate-200">No matching dishes found</h4>
          <p class="text-xs text-slate-500 max-w-sm">Try clearing your filters or click "+ Add Dish" above to create a new culinary creation.</p>
          <button onclick="openAddDishModal()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-md)] bg-[var(--accent)] text-white shadow-xs">
            + Add First Dish
          </button>
        </div>
      `;
      if (window.lucide) window.lucide.createIcons();
      return;
    }

    grid.innerHTML = '';
    filtered.forEach(dish => {
      const isVeg = dish.isVeg !== false && dish.is_veg !== false && dish.isVeg !== 0 && dish.is_veg !== 0;

      // Card Base styling: Warm reddish-orange gradient styling for non-veg
      const card = document.createElement('div');
      const nonVegClass = !isVeg
        ? 'border-t-4 border-t-orange-500 bg-linear-to-b from-orange-500/5 to-transparent'
        : 'border-t-4 border-t-emerald-600';
      const opacityClass = !dish.isAvailable ? 'opacity-70 grayscale-[20%]' : '';

      card.className = `p-4 sm:p-5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-slate-200 dark:border-zinc-700 shadow-xs dish-card-transition flex flex-col justify-between space-y-3.5 relative ${nonVegClass} ${opacityClass}`;

      // Image
      const imgHtml = dish.imageUrl ? `
        <div class="relative w-full h-36 rounded-[var(--radius-lg)] overflow-hidden mb-3 border border-slate-200 dark:border-zinc-700">
          <img src="${dish.imageUrl}" alt="${escapeHtml(dish.name)}" class="w-full h-full object-cover" onerror="this.parentElement.style.display='none'">
        </div>
      ` : '';

      // Authentic FSSAI Emblem Badge
      const dietBadge = isVeg
        ? `<span class="inline-flex items-center gap-1.5 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-full text-[11px] font-bold text-emerald-800 dark:text-emerald-300">
             <svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3" stroke="#059669" stroke-width="2" fill="#ECFDF5"/><circle cx="8" cy="8" r="3.5" fill="#059669"/></svg>
             <span>Veg</span>
           </span>`
        : `<span class="inline-flex items-center gap-1.5 bg-orange-500/10 border border-orange-500/30 px-2 py-0.5 rounded-full text-[11px] font-bold text-orange-800 dark:text-orange-300">
             <svg class="w-3 h-3 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3" stroke="#DC2626" stroke-width="2" fill="#FEF2F2"/><polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/></svg>
             <span>Non-Veg</span>
           </span>`;

      // Status pill
      const stockBadge = dish.isAvailable
        ? `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded">In Stock</span>`
        : `<span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 dark:text-amber-300 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded">86'd (Out)</span>`;

      card.innerHTML = `
        <div>
          ${imgHtml}
          <!-- Badges & Category Row -->
          <div class="flex items-center justify-between gap-1.5 mb-2">
            <div class="flex items-center gap-1.5 flex-wrap">
              ${dietBadge}
              <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded border border-slate-200 dark:border-zinc-700">${escapeHtml(dish.category?.name || 'Uncategorized')}</span>
            </div>
            ${stockBadge}
          </div>

          <!-- Dish Title & Price -->
          <div class="flex items-start justify-between gap-2 mb-1.5">
            <h4 class="font-serif font-black text-lg text-slate-900 dark:text-slate-50 leading-tight">${escapeHtml(dish.name)}</h4>
            <span class="font-mono font-black text-base text-[var(--accent)] dark:text-emerald-400 shrink-0">₹${parseFloat(dish.price).toFixed(2)}</span>
          </div>

          <!-- Description -->
          <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 mb-3 leading-relaxed">${escapeHtml(dish.description || 'No detailed recipe description provided.')}</p>

          <!-- Prep Time metadata -->
          <div class="flex items-center gap-1 text-[11px] font-bold text-slate-500 dark:text-slate-400">
            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
            <span>Prep: ${dish.preparationTime} mins</span>
          </div>
        </div>

        <!-- Card Footer Actions: Switch + Edit + Delete -->
        <div class="pt-3 border-t border-[var(--border)] flex items-center justify-between gap-2">
          <!-- Toggle In-Stock Switch -->
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" onchange="toggleAvailability('${dish.id}', this.checked)" ${dish.isAvailable ? 'checked' : ''} class="w-4 h-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">${dish.isAvailable ? 'Available' : 'Disabled'}</span>
          </label>

          <!-- Edit & Delete Buttons -->
          <div class="flex items-center gap-1">
            <button onclick="openEditDishModal('${dish.id}')" class="p-2 rounded-[var(--radius-md)] border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-zinc-800 hover:text-[var(--accent)] transition" title="Edit dish details">
              <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
            </button>
            <button onclick="deleteDish('${dish.id}')" class="p-2 rounded-[var(--radius-md)] border border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-500/10 transition" title="Delete dish">
              <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
            </button>
          </div>
        </div>
      `;
      grid.appendChild(card);
    });

    if (window.lucide) window.lucide.createIcons();
  }

  // Open modal for Adding
  function openAddDishModal() {
    clearDishError();
    document.getElementById('dish-modal-title').textContent = 'Add New Dish';
    document.getElementById('dish-id').value = '';
    document.getElementById('dish-name').value = '';
    document.getElementById('dish-price').value = '';
    document.getElementById('dish-prep-time').value = '15';
    document.getElementById('dish-category').value = '';
    document.getElementById('dish-type-veg').checked = true;
    document.getElementById('dish-desc').value = '';
    document.getElementById('dish-image-file').value = '';
    document.getElementById('dish-image-url').value = '';
    document.getElementById('dish-available').checked = true;
    document.getElementById('dish-image-preview-wrapper').classList.add('hidden');
    document.getElementById('dish-submit-btn-text').textContent = 'Save Dish';
    document.getElementById('dish-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('dish-name').focus(), 50);
  }

  // Open modal for Editing
  function openEditDishModal(id) {
    clearDishError();
    const dish = menuItems.find(i => i.id === id);
    if (!dish) return;

    document.getElementById('dish-modal-title').textContent = `Edit Dish: ${dish.name}`;
    document.getElementById('dish-id').value = dish.id;
    document.getElementById('dish-name').value = dish.name;
    document.getElementById('dish-price').value = dish.price;
    document.getElementById('dish-prep-time').value = dish.preparationTime || 15;
    document.getElementById('dish-category').value = dish.categoryId || '';

    const isVeg = dish.isVeg !== false && dish.is_veg !== false && dish.isVeg !== 0 && dish.is_veg !== 0;
    document.getElementById('dish-type-veg').checked = isVeg;
    document.getElementById('dish-type-nonveg').checked = !isVeg;
    document.getElementById('dish-desc').value = dish.description || '';
    document.getElementById('dish-image-file').value = '';
    document.getElementById('dish-image-url').value = dish.imageUrl || '';
    document.getElementById('dish-available').checked = dish.isAvailable;

    // Show image preview if exists
    if (dish.imageUrl) {
      document.getElementById('dish-image-preview').src = dish.imageUrl;
      document.getElementById('dish-image-preview-wrapper').classList.remove('hidden');
    } else {
      document.getElementById('dish-image-preview-wrapper').classList.add('hidden');
    }

    document.getElementById('dish-submit-btn-text').textContent = 'Update Dish';
    document.getElementById('dish-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('dish-name').focus(), 50);
  }

  function closeDishModal() {
    document.getElementById('dish-modal').classList.add('hidden');
    clearDishError();
  }

  function clearDishError() {
    const banner = document.getElementById('dish-error-banner');
    banner.classList.add('hidden');
    document.getElementById('dish-error-text').textContent = '';
  }

  function showDishError(msg) {
    const banner = document.getElementById('dish-error-banner');
    document.getElementById('dish-error-text').textContent = msg;
    banner.classList.remove('hidden');
  }

  // Live Image preview for file input
  function previewSelectedFile(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('dish-image-preview').src = e.target.result;
        document.getElementById('dish-image-preview-wrapper').classList.remove('hidden');
      };
      reader.readAsDataURL(file);
    }
  }

  function previewUrlInput(url) {
    if (url && url.startsWith('http')) {
      document.getElementById('dish-image-preview').src = url;
      document.getElementById('dish-image-preview-wrapper').classList.remove('hidden');
    }
  }

  function clearDishImage() {
    document.getElementById('dish-image-file').value = '';
    document.getElementById('dish-image-url').value = '';
    document.getElementById('dish-image-preview').src = '';
    document.getElementById('dish-image-preview-wrapper').classList.add('hidden');
  }

  // Submit Dish (Add or Edit) with 100% crash-proof error handling
  async function submitDish(e) {
    e.preventDefault();
    clearDishError();

    const btn = document.getElementById('dish-submit-btn');
    const btnText = document.getElementById('dish-submit-btn-text');
    const originalText = btnText.textContent;
    btn.disabled = true;
    btnText.textContent = 'Saving...';

    const id = document.getElementById('dish-id').value;
    const fileInput = document.getElementById('dish-image-file');
    let imageUrl = document.getElementById('dish-image-url').value.trim();

    // 1. Upload image file if user selected one
    if (fileInput.files.length > 0) {
      const formData = new FormData();
      formData.append('file', fileInput.files[0]);
      try {
        const uploadRes = await fetch('/api/upload', {
          method: 'POST',
          headers: { 'Authorization': 'Bearer ' + window.getAuthToken() },
          body: formData
        });
        const uploadData = await uploadRes.json();
        if (uploadData.success && uploadData.url) {
          imageUrl = uploadData.url;
        } else {
          console.warn('Image upload error:', uploadData);
        }
      } catch (err) {
        console.warn('Image upload network exception:', err);
      }
    }

    // 2. Validate inputs
    const name = document.getElementById('dish-name').value.trim();
    const price = parseFloat(document.getElementById('dish-price').value);
    const prepTime = parseInt(document.getElementById('dish-prep-time').value) || 15;
    const catVal = document.getElementById('dish-category').value.trim();
    const isVeg = document.getElementById('dish-type-veg').checked;
    const description = document.getElementById('dish-desc').value.trim() || null;
    const isAvailable = document.getElementById('dish-available').checked;

    if (!name) {
      showDishError('Dish Name is required.');
      btn.disabled = false;
      btnText.textContent = originalText;
      return;
    }
    if (isNaN(price) || price < 0) {
      showDishError('Please provide a valid price (greater than or equal to 0).');
      btn.disabled = false;
      btnText.textContent = originalText;
      return;
    }

    // Build payload
    const payload = {
      name,
      price,
      preparationTime: prepTime,
      categoryId: catVal !== '' ? catVal : null,
      isVeg,
      description,
      imageUrl: imageUrl || null,
      isAvailable,
    };

    try {
      const url = id ? `/api/menu/${id}` : '/api/menu';
      const method = id ? 'PUT' : 'POST';

      const res = await fetch(url, {
        method,
        headers: window.getAuthHeader(),
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (res.ok && data.success) {
        window.showToast?.(`Dish "${name}" ${id ? 'updated' : 'added'} successfully!`);
        closeDishModal();
        loadMenuData();
      } else {
        // Detailed error extraction
        let errorMsg = 'Failed to save dish';
        if (data.error && data.error.message) {
          errorMsg = data.error.message;
        } else if (data.message) {
          errorMsg = data.message;
        } else if (data.errors) {
          errorMsg = Object.values(data.errors).flat().join(', ');
        }
        showDishError(errorMsg);
        window.showToast?.(errorMsg, 'error');
      }
    } catch(err) {
      console.error('Submit dish error:', err);
      showDishError('Network error while saving dish. Please check connection.');
      window.showToast?.('Network error while saving dish', 'error');
    } finally {
      btn.disabled = false;
      btnText.textContent = originalText;
    }
  }

  // Instant Availability Toggle
  async function toggleAvailability(id, isAvailable) {
    try {
      const res = await fetch(`/api/menu/${id}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ isAvailable }),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        window.showToast?.(`Dish marked as ${isAvailable ? 'Available' : '86\'d (Sold Out)'}`);
        const item = menuItems.find(i => i.id === id);
        if (item) item.isAvailable = isAvailable;
        updateKpis();
        renderDishes();
      } else {
        window.showToast?.(data.error?.message || 'Failed to update availability', 'error');
        loadMenuData();
      }
    } catch (err) {
      console.error(err);
      window.showToast?.('Failed to toggle status', 'error');
      loadMenuData();
    }
  }

  // Delete Dish with Confirmation
  async function deleteDish(id) {
    const dish = menuItems.find(i => i.id === id);
    const dishName = dish ? dish.name : 'this dish';
    if (!confirm(`Are you sure you want to permanently delete "${dishName}" from your menu?`)) return;

    try {
      const res = await fetch(`/api/menu/${id}`, {
        method: 'DELETE',
        headers: window.getAuthHeader(),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        window.showToast?.(`Dish "${dishName}" deleted successfully`);
        loadMenuData();
      } else {
        window.showToast?.(data.error?.message || 'Failed to delete dish', 'error');
      }
    } catch (err) {
      console.error(err);
      window.showToast?.('Network error deleting dish', 'error');
    }
  }

  // Category Modal Controls
  function openCategoryModal() {
    renderCategories();
    document.getElementById('cat-name-input').value = '';
    document.getElementById('category-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('cat-name-input').focus(), 50);
  }

  function closeCategoryModal() {
    document.getElementById('category-modal').classList.add('hidden');
  }

  // Submit new category
  async function submitCategory(e) {
    e.preventDefault();
    const input = document.getElementById('cat-name-input');
    const name = input.value.trim();
    if (!name) return;

    const btn = document.getElementById('cat-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Adding...';

    try {
      const res = await fetch('/api/categories', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ name }),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        input.value = '';
        window.showToast?.(`Category "${name}" created!`);
        await loadMenuData();
      } else {
        alert(data.error?.message || data.message || 'Failed to create category');
      }
    } catch(err) {
      console.error(err);
      alert('Network error creating category');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Add';
    }
  }

  // Open Category Rename Modal
  function openCatEditModal(id, currentName) {
    document.getElementById('edit-cat-id').value = id;
    document.getElementById('edit-cat-name').value = currentName;
    document.getElementById('cat-edit-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('edit-cat-name').focus(), 50);
  }

  function closeCatEditModal() {
    document.getElementById('cat-edit-modal').classList.add('hidden');
  }

  async function saveCategoryRename() {
    const id = document.getElementById('edit-cat-id').value;
    const name = document.getElementById('edit-cat-name').value.trim();
    if (!name) return;

    try {
      const res = await fetch(`/api/categories/${id}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ name }),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        closeCatEditModal();
        window.showToast?.(`Category renamed to "${name}"`);
        await loadMenuData();
      } else {
        alert(data.error?.message || data.message || 'Failed to rename category');
      }
    } catch(err) {
      console.error(err);
      alert('Network error renaming category');
    }
  }

  // Delete Category
  async function deleteCategory(id) {
    const cat = categories.find(c => c.id === id);
    const catName = cat ? cat.name : 'this category';
    if (!confirm(`Delete category "${catName}"? Dishes in this category will remain safe as Uncategorized.`)) return;

    try {
      const res = await fetch(`/api/categories/${id}`, {
        method: 'DELETE',
        headers: window.getAuthHeader(),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        window.showToast?.(`Category "${catName}" deleted`);
        await loadMenuData();
      } else {
        alert(data.error?.message || data.message || 'Failed to delete category');
      }
    } catch (err) {
      console.error(err);
      alert('Network error deleting category');
    }
  }

  // Helper sanitizers
  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function escapeJs(str) {
    if (!str) return '';
    return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
  }
</script>
@endsection
