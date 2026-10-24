@extends('layouts.dashboard')

@section('title', 'Menu Catalog & Categories - Vyora')
@section('page_title', 'Menu Catalog Management')

@push('styles')
<style>
  .dish-card-hover {
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
  }
  .dish-card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  }
  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }
  .no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
</style>
@endpush

@section('dashboard_content')
<div class="space-y-6">

  <!-- 1. TOP OPERATIONAL HUD METRICS (Pure Crisp High-Contrast) -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <!-- Total Dishes -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-slate-200 shadow-xs flex flex-col justify-between">
      <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
        <span class="text-xs font-black uppercase tracking-wider text-[#034d37] flex items-center gap-1.5">
          <span class="p-1.5 rounded-lg bg-emerald-50 text-[#034d37] border border-emerald-200">
            <i data-lucide="utensils" class="w-3.5 h-3.5 stroke-[2.5]"></i>
          </span>
          <span>Total Dishes</span>
        </span>
        <span class="text-xs font-extrabold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">All</span>
      </div>
      <div class="pt-3 flex items-baseline justify-between">
        <h3 id="stat-total-dishes" class="font-sans font-black text-3xl sm:text-4xl text-slate-900" style="color: #0f172a !important;">0</h3>
        <span class="text-xs font-bold text-slate-600">dishes</span>
      </div>
      <p class="text-xs font-semibold text-slate-600 mt-1">Full dish catalog</p>
    </div>

    <!-- In Stock -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-emerald-200 shadow-xs flex flex-col justify-between">
      <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
        <span class="text-xs font-black uppercase tracking-wider text-emerald-800 flex items-center gap-1.5">
          <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300">
            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 stroke-[2.5]"></i>
          </span>
          <span>In Stock</span>
        </span>
        <span class="text-xs font-extrabold text-emerald-800 bg-emerald-100 border border-emerald-300 px-2 py-0.5 rounded-md">Active</span>
      </div>
      <div class="pt-3 flex items-baseline justify-between">
        <h3 id="stat-instock-dishes" class="font-sans font-black text-3xl sm:text-4xl text-emerald-700" style="color: #047857 !important;">0</h3>
        <span class="text-xs font-bold text-emerald-700">orderable</span>
      </div>
      <p class="text-xs font-semibold text-slate-600 mt-1">Visible on guest QR menu</p>
    </div>

    <!-- 86'd / Sold Out -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-amber-200 shadow-xs flex flex-col justify-between">
      <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
        <span class="text-xs font-black uppercase tracking-wider text-amber-800 flex items-center gap-1.5">
          <span class="p-1.5 rounded-lg bg-amber-100 text-amber-800 border border-amber-300">
            <i data-lucide="slash" class="w-3.5 h-3.5 stroke-[2.5]"></i>
          </span>
          <span>86'd / Sold Out</span>
        </span>
        <span class="text-xs font-extrabold text-amber-800 bg-amber-100 border border-amber-300 px-2 py-0.5 rounded-md">Paused</span>
      </div>
      <div class="pt-3 flex items-baseline justify-between">
        <h3 id="stat-soldout-dishes" class="font-sans font-black text-3xl sm:text-4xl text-amber-700" style="color: #b45309 !important;">0</h3>
        <span class="text-xs font-bold text-amber-700">disabled</span>
      </div>
      <p class="text-xs font-semibold text-slate-600 mt-1">Hidden from ordering</p>
    </div>

    <!-- Categories -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-slate-200 shadow-xs flex flex-col justify-between">
      <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
        <span class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
          <span class="p-1.5 rounded-lg bg-slate-100 text-slate-800 border border-slate-300">
            <i data-lucide="folders" class="w-3.5 h-3.5 stroke-[2.5]"></i>
          </span>
          <span>Sections</span>
        </span>
        <span class="text-xs font-extrabold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">Groups</span>
      </div>
      <div class="pt-3 flex items-baseline justify-between">
        <h3 id="stat-total-categories" class="font-sans font-black text-3xl sm:text-4xl text-slate-900" style="color: #0f172a !important;">0</h3>
        <span class="text-xs font-bold text-slate-600">categories</span>
      </div>
      <p class="text-xs font-semibold text-slate-600 mt-1">Menu groupings configured</p>
    </div>
  </div>

  <!-- 2. CONTROL TOOLBAR & FILTERS -->
  <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-slate-200 shadow-xs space-y-4">
    <!-- Top Row: Search + Action Buttons -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
      <!-- Search Box -->
      <div class="relative flex-1 max-w-lg">
        <input type="text" id="menu-search" oninput="filterDishes()" placeholder="Search dishes by name, ingredients, category..." class="w-full pl-10 pr-9 py-2.5 text-sm font-semibold bg-slate-50 border-2 border-slate-200 hover:border-slate-300 focus:border-[#034d37] focus:bg-white text-slate-900 placeholder-slate-400 rounded-xl transition shadow-xs focus:outline-none" style="color: #0f172a !important;">
        <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        <button id="search-clear-btn" onclick="clearSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-700">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center gap-2.5 justify-end shrink-0">
        <!-- Manage Categories Button -->
        <button onclick="openCategoryModal()" class="px-4 py-2.5 text-xs font-bold rounded-xl border-2 border-slate-300 hover:border-[#034d37] bg-white text-slate-800 hover:text-[#034d37] shadow-xs transition flex items-center gap-2 active:scale-95 cursor-pointer">
          <i data-lucide="folder-plus" class="w-4 h-4 text-slate-600"></i>
          <span>Categories</span>
        </button>

        <!-- Add Dish Primary CTA -->
        <button onclick="openAddDishModal()" class="px-5 py-2.5 text-xs font-black rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white shadow-md transition flex items-center gap-2 active:scale-95 cursor-pointer">
          <i data-lucide="plus" class="w-4 h-4 stroke-[3]"></i>
          <span>Add Dish</span>
        </button>
      </div>
    </div>

    <!-- Filter Pills Row -->
    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200">
      <!-- Dietary Filters -->
      <div class="flex items-center gap-2 flex-wrap">
        <span class="text-xs font-black text-slate-700 uppercase tracking-wider mr-1">DIET:</span>
        <button onclick="setDietFilter('all')" id="filter-diet-all" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition bg-[#034d37] text-white shadow-xs cursor-pointer">
          All
        </button>
        <button onclick="setDietFilter('veg')" id="filter-diet-veg" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border-2 border-slate-200 bg-white text-emerald-900 hover:border-emerald-500 hover:bg-emerald-50 transition flex items-center gap-1.5 cursor-pointer">
          <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3" stroke="#059669" stroke-width="2.5" fill="#ECFDF5"/><circle cx="8" cy="8" r="3.5" fill="#059669"/></svg>
          <span>Veg Only</span>
        </button>
        <button onclick="setDietFilter('nonveg')" id="filter-diet-nonveg" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border-2 border-slate-200 bg-white text-orange-900 hover:border-orange-500 hover:bg-orange-50 transition flex items-center gap-1.5 cursor-pointer">
          <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3" stroke="#DC2626" stroke-width="2.5" fill="#FEF2F2"/><polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/></svg>
          <span>Non-Veg Only</span>
        </button>
      </div>

      <!-- Stock Availability Filters -->
      <div class="flex items-center gap-2 flex-wrap">
        <span class="text-xs font-black text-slate-700 uppercase tracking-wider mr-1">STOCK:</span>
        <button onclick="setStockFilter('all')" id="filter-stock-all" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition bg-[#034d37] text-white shadow-xs cursor-pointer">
          All Items
        </button>
        <button onclick="setStockFilter('available')" id="filter-stock-available" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border-2 border-slate-200 bg-white text-slate-800 hover:border-slate-400 hover:bg-slate-50 transition cursor-pointer">
          In Stock
        </button>
        <button onclick="setStockFilter('unavailable')" id="filter-stock-unavailable" class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border-2 border-slate-200 bg-white text-slate-800 hover:border-slate-400 hover:bg-slate-50 transition cursor-pointer">
          86'd (Sold Out)
        </button>
      </div>
    </div>
  </div>

  <!-- 3. CATEGORY TABS CAROUSEL (Never Faint, Crystal Clear) -->
  <div class="relative">
    <div id="category-tabs" class="flex items-center gap-2.5 overflow-x-auto pb-1.5 no-scrollbar">
      <button onclick="selectCategory('all')" id="tab-all" class="px-4 py-2 rounded-full text-xs font-extrabold transition shrink-0 flex items-center gap-2 bg-[#034d37] text-white shadow-xs cursor-pointer">
        <span>All Items</span>
        <span id="tab-count-all" class="px-2 py-0.5 rounded-full bg-white/25 text-white text-xs font-black">0</span>
      </button>
    </div>
  </div>

  <!-- 4. MENU DISHES GRID -->
  <div id="dishes-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    <div class="col-span-full p-12 text-center text-xs font-bold text-slate-600 flex flex-col items-center justify-center gap-3">
      <div class="w-8 h-8 border-3 border-[#034d37] border-t-transparent rounded-full animate-spin"></div>
      <span class="text-sm text-slate-700">Loading menu dishes...</span>
    </div>
  </div>
</div>

<!-- ============================================== -->
<!-- 5. ADD / EDIT DISH MODAL (Pure Light Surface)  -->
<!-- ============================================== -->
<div id="dish-modal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
  <div class="bg-white border-2 border-slate-200 rounded-2xl shadow-2xl w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
    <!-- Modal Header -->
    <div class="flex items-center justify-between pb-3.5 border-b border-slate-200 mb-4">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-[#034d37] text-white flex items-center justify-center shadow-xs">
          <i data-lucide="utensils" class="w-5 h-5"></i>
        </div>
        <h3 id="dish-modal-title" class="font-sans font-black text-xl text-slate-900" style="color: #0f172a !important;">Add New Dish</h3>
      </div>
      <button onclick="closeDishModal()" class="p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Error Alert Banner -->
    <div id="dish-error-banner" class="hidden mb-4 p-3 rounded-xl bg-red-50 border-2 border-red-200 text-red-800 text-xs font-bold flex items-start gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5 text-red-600"></i>
      <div id="dish-error-text" class="flex-1"></div>
    </div>

    <!-- Dish Form -->
    <form onsubmit="submitDish(event)" class="space-y-4">
      <input type="hidden" id="dish-id">

      <!-- Dish Name -->
      <div>
        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider mb-1.5">
          Dish Name <span class="text-red-500">*</span>
        </label>
        <input type="text" id="dish-name" required placeholder="e.g. Butter Chicken Special" class="w-full px-3.5 py-2.5 text-sm font-bold bg-slate-50 border-2 border-slate-300 rounded-xl focus:outline-none focus:border-[#034d37] focus:bg-white text-slate-900 placeholder-slate-400 transition" style="color: #0f172a !important;">
      </div>

      <!-- Price & Prep Time -->
      <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <div>
          <label class="block text-xs font-black text-slate-800 uppercase tracking-wider mb-1.5">
            Price (₹) <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-slate-600">₹</span>
            <input type="number" step="0.01" min="0" id="dish-price" required placeholder="299.00" class="w-full pl-9 pr-3 py-2.5 text-sm font-mono font-black bg-slate-50 border-2 border-slate-300 rounded-xl focus:outline-none focus:border-[#034d37] focus:bg-white text-slate-900 placeholder-slate-400 transition" style="color: #0f172a !important;">
          </div>
        </div>
        <div>
          <label class="block text-xs font-black text-slate-800 uppercase tracking-wider mb-1.5">
            Prep Time (mins)
          </label>
          <div class="relative">
            <input type="number" min="1" max="180" id="dish-prep-time" value="15" class="w-full pl-3.5 pr-12 py-2.5 text-sm font-mono font-bold bg-slate-50 border-2 border-slate-300 rounded-xl focus:outline-none focus:border-[#034d37] focus:bg-white text-slate-900 transition" style="color: #0f172a !important;">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500">mins</span>
          </div>
        </div>
      </div>

      <!-- Category Select -->
      <div>
        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider mb-1.5">
          Menu Category
        </label>
        <select id="dish-category" class="w-full px-3.5 py-2.5 text-sm font-bold bg-slate-50 border-2 border-slate-300 rounded-xl focus:outline-none focus:border-[#034d37] focus:bg-white text-slate-900 transition" style="color: #0f172a !important;">
          <option value="">Uncategorized</option>
        </select>
      </div>

      <!-- Dietary Radio Tiles -->
      <div>
        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider mb-1.5">
          Dietary Type <span class="text-red-500">*</span>
        </label>
        <div class="grid grid-cols-2 gap-3">
          <!-- Veg Tile -->
          <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 border-emerald-400 bg-emerald-50/60 cursor-pointer hover:bg-emerald-50 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-100">
            <input type="radio" name="dish-diet-type" id="dish-type-veg" value="1" checked class="text-emerald-700 focus:ring-emerald-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="1" width="14" height="14" rx="3" stroke="#059669" stroke-width="2.5" fill="#ECFDF5"/>
                <circle cx="8" cy="8" r="3.5" fill="#059669"/>
              </svg>
              <div>
                <span class="block text-xs font-black text-emerald-950">Vegetarian</span>
                <span class="text-[11px] font-bold text-emerald-800">Green mark</span>
              </div>
            </div>
          </label>

          <!-- Non-Veg Tile -->
          <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 border-orange-400 bg-orange-50/60 cursor-pointer hover:bg-orange-50 transition has-[:checked]:border-orange-600 has-[:checked]:bg-orange-100">
            <input type="radio" name="dish-diet-type" id="dish-type-nonveg" value="0" class="text-orange-700 focus:ring-orange-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="1" width="14" height="14" rx="3" stroke="#DC2626" stroke-width="2.5" fill="#FEF2F2"/>
                <polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/>
              </svg>
              <div>
                <span class="block text-xs font-black text-orange-950">Non-Veg</span>
                <span class="text-[11px] font-bold text-orange-800">Red mark</span>
              </div>
            </div>
          </label>
        </div>
      </div>

      <!-- Description -->
      <div>
        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider mb-1.5">
          Description
        </label>
        <textarea id="dish-desc" rows="2" placeholder="Brief description of flavours, spices, and ingredients..." class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 border-2 border-slate-300 rounded-xl focus:outline-none focus:border-[#034d37] focus:bg-white text-slate-900 placeholder-slate-400 transition" style="color: #0f172a !important;"></textarea>
      </div>

      <!-- Dish Image & Preview -->
      <div>
        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider mb-1.5">
          Dish Photo
        </label>
        <!-- Preview Box -->
        <div id="dish-image-preview-wrapper" class="hidden mb-2 relative w-full h-36 rounded-xl overflow-hidden border-2 border-slate-200 bg-slate-100">
          <img id="dish-image-preview" src="" alt="Dish Preview" class="w-full h-full object-cover">
          <button type="button" onclick="clearDishImage()" class="absolute top-2 right-2 p-1.5 rounded-full bg-slate-900/80 text-white hover:bg-slate-950 transition cursor-pointer" title="Remove photo">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        </div>
        <div class="space-y-2">
          <input type="file" id="dish-image-file" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewSelectedFile(event)" class="w-full text-xs text-slate-700 font-semibold file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#034d37] file:text-white hover:file:bg-[#023d2b] transition cursor-pointer">
          <div class="flex items-center gap-2">
            <span class="text-[11px] font-black text-slate-600 uppercase">OR URL:</span>
            <input type="url" id="dish-image-url" placeholder="https://images.unsplash.com/..." oninput="previewUrlInput(this.value)" class="flex-1 px-3 py-1.5 text-xs font-mono bg-slate-50 border-2 border-slate-300 rounded-lg text-slate-900 focus:bg-white focus:outline-none focus:border-[#034d37]" style="color: #0f172a !important;">
          </div>
        </div>
      </div>

      <!-- In Stock Toggle -->
      <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border-2 border-slate-200">
        <div>
          <span class="block text-xs font-black text-slate-900" style="color: #0f172a !important;">In Stock (Orderable)</span>
          <span class="text-[11px] font-semibold text-slate-600">When disabled, guests cannot order this item</span>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" id="dish-available" checked class="sr-only peer">
          <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#034d37]"></div>
        </label>
      </div>

      <!-- Footer Buttons -->
      <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
        <button type="button" onclick="closeDishModal()" class="px-4 py-2.5 text-xs font-bold rounded-xl border-2 border-slate-300 hover:bg-slate-100 text-slate-700 transition cursor-pointer">
          Cancel
        </button>
        <button type="submit" id="dish-submit-btn" class="px-5 py-2.5 text-xs font-black rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white shadow-md transition flex items-center gap-2 active:scale-95 cursor-pointer">
          <i data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
          <span id="dish-submit-btn-text">Save Dish</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================== -->
<!-- 6. CATEGORY MANAGEMENT MODAL                   -->
<!-- ============================================== -->
<div id="category-modal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
  <div class="bg-white border-2 border-slate-200 rounded-2xl shadow-2xl w-full max-w-md p-6 relative max-h-[85vh] flex flex-col justify-between">
    <div>
      <div class="flex items-center justify-between pb-3.5 border-b border-slate-200 mb-4">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-[#034d37] text-white flex items-center justify-center shadow-xs">
            <i data-lucide="folder-plus" class="w-5 h-5"></i>
          </div>
          <div>
            <h3 class="font-sans font-black text-xl text-slate-900" style="color: #0f172a !important;">Menu Categories</h3>
            <p class="text-xs font-semibold text-slate-600">Group dishes into menu sections</p>
          </div>
        </div>
        <button onclick="closeCategoryModal()" class="p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Add Category Form -->
      <form onsubmit="submitCategory(event)" class="space-y-2 mb-4 p-3.5 rounded-xl bg-slate-50 border-2 border-slate-200">
        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
          Create New Category
        </label>
        <div class="flex gap-2">
          <input type="text" id="cat-name-input" required placeholder="e.g. Starters, Tandoor, Desserts" class="flex-1 px-3 py-2 text-xs font-bold bg-white border-2 border-slate-300 rounded-lg focus:outline-none focus:border-[#034d37] text-slate-900 placeholder-slate-400" style="color: #0f172a !important;">
          <button type="submit" id="cat-submit-btn" class="px-4 py-2 bg-[#034d37] hover:bg-[#023d2b] text-white font-black text-xs rounded-lg shadow-sm transition active:scale-95 cursor-pointer">
            Add
          </button>
        </div>
      </form>

      <!-- Category List -->
      <div class="space-y-1">
        <span class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">Existing Categories:</span>
        <div id="category-list" class="space-y-2 max-h-60 overflow-y-auto pr-1"></div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="pt-4 border-t border-slate-200 text-right">
      <button onclick="closeCategoryModal()" class="px-5 py-2 text-xs font-bold rounded-xl border-2 border-slate-300 hover:bg-slate-100 text-slate-700 transition cursor-pointer">
        Done
      </button>
    </div>
  </div>
</div>

<!-- Category Rename Dialog -->
<div id="cat-edit-modal" class="fixed inset-0 bg-slate-900/60 z-60 flex items-center justify-center p-4 hidden backdrop-blur-xs">
  <div class="bg-white border-2 border-slate-200 rounded-2xl shadow-2xl w-full max-w-sm p-5 space-y-4">
    <h4 class="font-sans font-black text-lg text-slate-900" style="color: #0f172a !important;">Rename Category</h4>
    <input type="hidden" id="edit-cat-id">
    <div>
      <label class="block text-xs font-black text-slate-800 mb-1">Category Name</label>
      <input type="text" id="edit-cat-name" required class="w-full px-3 py-2 text-sm font-bold bg-slate-50 border-2 border-slate-300 rounded-lg text-slate-900 focus:bg-white focus:outline-none focus:border-[#034d37]" style="color: #0f172a !important;">
    </div>
    <div class="flex justify-end gap-2 pt-2 border-t border-slate-200">
      <button onclick="closeCatEditModal()" class="px-3.5 py-1.5 text-xs font-bold rounded-lg border-2 border-slate-300 text-slate-700 hover:bg-slate-100 cursor-pointer">Cancel</button>
      <button onclick="saveCategoryRename()" class="px-4 py-1.5 text-xs font-black rounded-lg bg-[#034d37] text-white cursor-pointer">Save</button>
    </div>
  </div>
</div>

<script>
  let categories = [];
  let menuItems = [];
  let selectedCatId = 'all';
  let activeDietFilter = 'all';
  let activeStockFilter = 'all';

  document.addEventListener('DOMContentLoaded', () => {
    loadMenuData();
  });

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
    }
  }

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

  function renderCategories() {
    const tabsContainer = document.getElementById('category-tabs');
    const selectEl = document.getElementById('dish-category');
    const catList = document.getElementById('category-list');

    tabsContainer.innerHTML = `
      <button onclick="selectCategory('all')" id="tab-all" class="px-4 py-2 rounded-full text-xs font-extrabold transition shrink-0 flex items-center gap-2 cursor-pointer ${selectedCatId === 'all' ? 'bg-[#034d37] text-white shadow-xs' : 'bg-white border-2 border-slate-200 text-slate-900 hover:border-[#034d37]'}">
        <span style="${selectedCatId === 'all' ? 'color:#ffffff;' : 'color:#0f172a;'}">All Items</span>
        <span id="tab-count-all" class="px-2 py-0.5 rounded-full ${selectedCatId === 'all' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-800'} text-xs font-black">${menuItems.length}</span>
      </button>
    `;

    selectEl.innerHTML = '<option value="">Uncategorized</option>';
    catList.innerHTML = '';

    categories.forEach(cat => {
      const count = menuItems.filter(i => i.categoryId === cat.id).length;
      const isSelected = selectedCatId === cat.id;

      // Tab button - High contrast text always visible
      const tabBtn = document.createElement('button');
      tabBtn.onclick = () => selectCategory(cat.id);
      tabBtn.className = `px-4 py-2 rounded-full text-xs font-extrabold transition shrink-0 flex items-center gap-2 cursor-pointer ${isSelected ? 'bg-[#034d37] text-white shadow-xs' : 'bg-white border-2 border-slate-200 text-slate-900 hover:border-[#034d37]'}`;
      tabBtn.innerHTML = `
        <span style="${isSelected ? 'color:#ffffff;' : 'color:#0f172a;'}">${escapeHtml(cat.name)}</span>
        <span class="px-2 py-0.5 rounded-full ${isSelected ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-800'} text-xs font-black">${count}</span>
      `;
      tabsContainer.appendChild(tabBtn);

      // Select option
      const opt = document.createElement('option');
      opt.value = cat.id;
      opt.textContent = cat.name;
      selectEl.appendChild(opt);

      // Management list item (Clean light row)
      const item = document.createElement('div');
      item.className = 'flex items-center justify-between p-3 rounded-xl bg-white border-2 border-slate-200 text-xs shadow-2xs';
      item.innerHTML = `
        <div class="flex items-center gap-2.5">
          <i data-lucide="folder" class="w-4 h-4 text-[#034d37]"></i>
          <span class="font-black text-slate-900 text-sm" style="color:#0f172a !important;">${escapeHtml(cat.name)}</span>
          <span class="text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">${count} dishes</span>
        </div>
        <div class="flex items-center gap-1.5">
          <button onclick="openCatEditModal('${cat.id}', '${escapeJs(cat.name)}')" class="p-1.5 rounded-lg border border-slate-200 text-slate-700 hover:text-slate-950 hover:bg-slate-100 cursor-pointer" title="Rename Category">
            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
          </button>
          <button onclick="deleteCategory('${cat.id}')" class="p-1.5 rounded-lg border border-red-200 text-red-600 hover:text-red-800 hover:bg-red-50 cursor-pointer" title="Delete Category">
            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
          </button>
        </div>
      `;
      catList.appendChild(item);
    });

    if (categories.length === 0) {
      catList.innerHTML = '<div class="p-4 text-center text-xs font-bold text-slate-500">No categories created yet. Create one above!</div>';
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
        btn.className = 'px-3.5 py-1.5 rounded-full text-xs font-extrabold transition bg-[#034d37] text-white shadow-xs cursor-pointer';
      } else {
        const textCol = f === 'veg' ? 'text-emerald-900 hover:bg-emerald-50' : (f === 'nonveg' ? 'text-orange-900 hover:bg-orange-50' : 'text-slate-800 hover:bg-slate-50');
        btn.className = `px-3.5 py-1.5 rounded-full text-xs font-extrabold border-2 border-slate-200 bg-white ${textCol} transition flex items-center gap-1.5 cursor-pointer`;
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
        btn.className = 'px-3.5 py-1.5 rounded-full text-xs font-extrabold transition bg-[#034d37] text-white shadow-xs cursor-pointer';
      } else {
        btn.className = 'px-3.5 py-1.5 rounded-full text-xs font-extrabold border-2 border-slate-200 bg-white text-slate-800 hover:border-slate-400 hover:bg-slate-50 transition cursor-pointer';
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

  function renderDishes() {
    const grid = document.getElementById('dishes-grid');
    const q = document.getElementById('menu-search').value.toLowerCase().trim();

    let filtered = menuItems;

    if (selectedCatId !== 'all') {
      filtered = filtered.filter(i => i.categoryId === selectedCatId);
    }

    if (activeDietFilter === 'veg') {
      filtered = filtered.filter(i => i.isVeg === true || i.is_veg === true || i.isVeg === 1 || i.is_veg === 1);
    } else if (activeDietFilter === 'nonveg') {
      filtered = filtered.filter(i => i.isVeg === false || i.is_veg === false || i.isVeg === 0 || i.is_veg === 0);
    }

    if (activeStockFilter === 'available') {
      filtered = filtered.filter(i => i.isAvailable);
    } else if (activeStockFilter === 'unavailable') {
      filtered = filtered.filter(i => !i.isAvailable);
    }

    if (q) {
      filtered = filtered.filter(i =>
        i.name.toLowerCase().includes(q) ||
        (i.description && i.description.toLowerCase().includes(q)) ||
        (i.category?.name && i.category.name.toLowerCase().includes(q))
      );
    }

    if (filtered.length === 0) {
      grid.innerHTML = `
        <div class="col-span-full p-12 text-center rounded-2xl bg-white border-2 border-dashed border-slate-300 flex flex-col items-center justify-center gap-3">
          <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-500">
            <i data-lucide="utensils" class="w-7 h-7"></i>
          </div>
          <h4 class="font-sans font-black text-lg text-slate-900" style="color: #0f172a !important;">No matching dishes found</h4>
          <p class="text-xs font-semibold text-slate-600 max-w-sm">Try clearing filters or click "+ Add Dish" to add a new menu item.</p>
          <button onclick="openAddDishModal()" class="px-5 py-2.5 text-xs font-black rounded-xl bg-[#034d37] hover:bg-[#023d2b] text-white shadow-md cursor-pointer">
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

      const card = document.createElement('div');
      const topStripe = isVeg ? 'border-t-4 border-t-emerald-600' : 'border-t-4 border-t-orange-600';
      const opacityClass = !dish.isAvailable ? 'opacity-80 bg-slate-50/70 border-slate-300' : 'bg-white border-slate-200';

      card.className = `p-4 sm:p-5 rounded-2xl border-2 ${topStripe} ${opacityClass} shadow-xs dish-card-hover flex flex-col justify-between space-y-4 relative`;

      const imgHtml = dish.imageUrl ? `
        <div class="relative w-full h-40 rounded-xl overflow-hidden mb-3 border border-slate-200 bg-slate-100">
          <img src="${dish.imageUrl}" alt="${escapeHtml(dish.name)}" class="w-full h-full object-cover" onerror="this.parentElement.style.display='none'">
        </div>
      ` : `
        <div class="w-full h-24 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-3">
          <i data-lucide="utensils" class="w-8 h-8 text-slate-300"></i>
        </div>
      `;

      const dietBadge = isVeg
        ? `<span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-300 px-2.5 py-1 rounded-md text-xs font-black text-emerald-900">
             <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3" stroke="#059669" stroke-width="2.5" fill="#ECFDF5"/><circle cx="8" cy="8" r="3.5" fill="#059669"/></svg>
             <span>Veg</span>
           </span>`
        : `<span class="inline-flex items-center gap-1.5 bg-orange-50 border border-orange-300 px-2.5 py-1 rounded-md text-xs font-black text-orange-900">
             <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="14" height="14" rx="3" stroke="#DC2626" stroke-width="2.5" fill="#FEF2F2"/><polygon points="8,4.2 12,11.5 4,11.5" fill="#DC2626"/></svg>
             <span>Non-Veg</span>
           </span>`;

      // Category Tag - Clean Slate Tag (NEVER pitch-black)
      const categoryTag = `<span class="text-xs font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-300">${escapeHtml(dish.category?.name || 'Uncategorized')}</span>`;

      // Stock Tag
      const stockBadge = dish.isAvailable
        ? `<span class="inline-flex items-center gap-1 text-xs font-black text-emerald-800 bg-emerald-50 border border-emerald-300 px-2.5 py-1 rounded-md">In Stock</span>`
        : `<span class="inline-flex items-center gap-1 text-xs font-black text-amber-800 bg-amber-50 border border-amber-300 px-2.5 py-1 rounded-md">86'd Out</span>`;

      card.innerHTML = `
        <div>
          ${imgHtml}
          <!-- Category & Badges Row -->
          <div class="flex items-center justify-between gap-1.5 mb-2.5 flex-wrap">
            <div class="flex items-center gap-1.5 flex-wrap">
              ${dietBadge}
              ${categoryTag}
            </div>
            ${stockBadge}
          </div>

          <!-- Title & Price (100% High Contrast, Deep Black Title, Emerald Price) -->
          <div class="flex items-start justify-between gap-2 mb-2">
            <h4 class="font-sans font-black text-lg text-slate-900 leading-snug tracking-tight" style="color: #0f172a !important;">${escapeHtml(dish.name)}</h4>
            <span class="font-mono font-black text-lg text-[#034d37] shrink-0" style="color: #034d37 !important;">₹${parseFloat(dish.price).toFixed(2)}</span>
          </div>

          <!-- Description (Dark Readable Slate 700) -->
          <p class="text-xs font-medium text-slate-700 line-clamp-2 mb-3 leading-relaxed" style="color: #334155 !important;">${escapeHtml(dish.description || 'No detailed culinary description provided.')}</p>

          <!-- Prep Time -->
          <div class="flex items-center gap-1.5 text-xs font-bold text-slate-600">
            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-500"></i>
            <span>Prep: ${dish.preparationTime || 15} mins</span>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-3.5 border-t border-slate-200 flex items-center justify-between gap-2">
          <!-- Availability iOS Toggle Switch -->
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" onchange="toggleAvailability('${dish.id}', this.checked)" ${dish.isAvailable ? 'checked' : ''} class="w-4 h-4 rounded border-slate-300 text-[#034d37] focus:ring-[#034d37] cursor-pointer">
            <span class="text-xs font-black text-slate-800" style="color: #0f172a !important;">${dish.isAvailable ? 'In Stock' : '86\'d Out'}</span>
          </label>

          <!-- Edit & Delete Buttons -->
          <div class="flex items-center gap-1.5">
            <button onclick="openEditDishModal('${dish.id}')" class="p-2 rounded-lg border-2 border-slate-200 hover:border-[#034d37] bg-white hover:bg-slate-50 text-slate-700 hover:text-[#034d37] transition shadow-2xs cursor-pointer" title="Edit dish">
              <i data-lucide="pencil" class="w-4 h-4"></i>
            </button>
            <button onclick="deleteDish('${dish.id}')" class="p-2 rounded-lg border-2 border-red-200 hover:border-red-500 bg-white hover:bg-red-50 text-red-600 transition shadow-2xs cursor-pointer" title="Delete dish">
              <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
          </div>
        </div>
      `;
      grid.appendChild(card);
    });

    if (window.lucide) window.lucide.createIcons();
  }

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
        }
      } catch (err) {
        console.warn('Image upload failed:', err);
      }
    }

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

    const payload = {
      name,
      price,
      preparationTime: prepTime,
      categoryId: catVal !== '' && catVal !== 'null' ? catVal : null,
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

  function openCategoryModal() {
    renderCategories();
    document.getElementById('cat-name-input').value = '';
    document.getElementById('category-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('cat-name-input').focus(), 50);
  }

  function closeCategoryModal() {
    document.getElementById('category-modal').classList.add('hidden');
  }

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
