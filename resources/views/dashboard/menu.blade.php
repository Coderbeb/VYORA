@extends('layouts.dashboard')

@section('title', 'Menu & Categories - Vyora')
@section('page_title', 'Menu Management')

@section('dashboard_content')
<div class="space-y-6">
  <!-- Top Action Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-[var(--radius-lg)] bg-[var(--bg-surface)] border border-[var(--border)]">
    <div class="flex items-center gap-2">
      <button onclick="openAddDishModal()" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm transition flex items-center gap-1.5">
        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
        <span>Add Dish</span>
      </button>
      <button onclick="openCategoryModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] transition flex items-center gap-1.5">
        <i data-lucide="folder-plus" class="w-3.5 h-3.5"></i>
        <span>Manage Categories</span>
      </button>
    </div>

    <div class="relative w-full sm:w-64">
      <input type="text" id="menu-search" oninput="filterDishes()" placeholder="Search dishes..." class="w-full pl-8 pr-3 py-2 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
      <i data-lucide="search" class="w-3.5 h-3.5 text-[var(--text-muted)] absolute left-2.5 top-1/2 -translate-y-1/2"></i>
    </div>
  </div>

  <!-- Categories Filter Tabs -->
  <div id="category-tabs" class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
    <button onclick="selectCategory('all')" id="tab-all" class="px-4 py-2 rounded-full text-xs font-bold transition bg-[var(--accent)] text-white shadow-sm shrink-0">
      All Items
    </button>
    <!-- Dynamic Category Tabs injected here -->
  </div>

  <!-- Menu Dishes Grid -->
  <div id="dishes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">Loading menu items...</div>
  </div>
</div>

<!-- Add / Edit Dish Modal -->
<div id="dish-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
      <h3 id="dish-modal-title" class="font-serif font-bold text-lg text-[var(--text-primary)]">Add New Dish</h3>
      <button onclick="closeDishModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form onsubmit="submitDish(event)" class="space-y-4">
      <input type="hidden" id="dish-id">

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Dish Name *</label>
        <input type="text" id="dish-name" required placeholder="e.g. Paneer Butter Masala" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Price ($) *</label>
          <input type="number" step="0.01" min="0" id="dish-price" required placeholder="14.99" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
        <div>
          <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Prep Time (mins)</label>
          <input type="number" min="1" id="dish-prep-time" value="15" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Category</label>
        <select id="dish-category" class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
          <option value="">Uncategorized</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Description</label>
        <textarea id="dish-desc" rows="2" placeholder="Brief culinary description of ingredients and spices..." class="w-full px-3 py-2 text-sm bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]"></textarea>
      </div>

      <div>
        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Dish Image</label>
        <input type="file" id="dish-image-file" accept="image/*" class="w-full text-xs text-[var(--text-muted)] file:mr-3 file:py-1.5 file:px-3 file:rounded-[var(--radius-sm)] file:border-0 file:text-xs file:font-semibold file:bg-[var(--accent-glow)] file:text-[var(--accent)] hover:file:bg-[var(--accent)] hover:file:text-white transition">
        <input type="hidden" id="dish-image-url">
      </div>

      <div class="flex items-center gap-2 pt-2">
        <input type="checkbox" id="dish-available" checked class="rounded border-[var(--border)] text-[var(--accent)] focus:ring-[var(--accent)]">
        <label for="dish-available" class="text-xs font-semibold text-[var(--text-primary)]">Available on QR Menu (In Stock)</label>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-[var(--border)]">
        <button type="button" onclick="closeDishModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Cancel</button>
        <button type="submit" id="dish-submit-btn" class="px-4 py-2 text-xs font-bold rounded-[var(--radius-sm)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white shadow-sm">Save Dish</button>
      </div>
    </form>
  </div>
</div>

<!-- Category Management Modal -->
<div id="category-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
  <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-[var(--radius-xl)] shadow-[var(--shadow-lg)] w-full max-w-md p-6 relative max-h-[85vh] flex flex-col justify-between">
    <div>
      <div class="flex items-center justify-between pb-3 border-b border-[var(--border)] mb-4">
        <h3 class="font-serif font-bold text-lg text-[var(--text-primary)]">Categories</h3>
        <button onclick="closeCategoryModal()" class="p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-primary)]">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Add Category Form -->
      <form onsubmit="submitCategory(event)" class="flex gap-2 mb-4">
        <input type="text" id="cat-name-input" required placeholder="New Category (e.g. Desserts)" class="flex-1 px-3 py-1.5 text-xs bg-[var(--bg-base)] border border-[var(--border)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--accent)] text-[var(--text-primary)]">
        <button type="submit" class="px-3 py-1.5 bg-[var(--accent)] text-white font-bold text-xs rounded-[var(--radius-sm)] hover:bg-[var(--accent-hover)]">Add</button>
      </form>

      <!-- Category List -->
      <div id="category-list" class="space-y-2 max-h-60 overflow-y-auto divide-y divide-[var(--border)]">
        <!-- Injected by JS -->
      </div>
    </div>

    <div class="pt-4 border-t border-[var(--border)] text-right">
      <button onclick="closeCategoryModal()" class="px-4 py-2 text-xs font-semibold rounded-[var(--radius-sm)] border border-[var(--border)] hover:bg-[var(--bg-hover)] text-[var(--text-secondary)]">Close</button>
    </div>
  </div>
</div>

<script>
  let categories = [];
  let menuItems = [];
  let selectedCatId = 'all';

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

      renderCategories();
      renderDishes();
    } catch (err) {
      console.error(err);
    }
  }

  function renderCategories() {
    const tabsContainer = document.getElementById('category-tabs');
    const selectEl = document.getElementById('dish-category');
    const catList = document.getElementById('category-list');

    // Retain "All Items"
    tabsContainer.innerHTML = `
      <button onclick="selectCategory('all')" id="tab-all" class="px-4 py-2 rounded-full text-xs font-bold transition ${selectedCatId === 'all' ? 'bg-[var(--accent)] text-white shadow-sm' : 'border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]'} shrink-0">
        All Items
      </button>
    `;

    selectEl.innerHTML = '<option value="">Uncategorized</option>';
    catList.innerHTML = '';

    categories.forEach(cat => {
      // Tab
      const tabBtn = document.createElement('button');
      tabBtn.onclick = () => selectCategory(cat.id);
      tabBtn.className = `px-4 py-2 rounded-full text-xs font-bold transition shrink-0 ${selectedCatId === cat.id ? 'bg-[var(--accent)] text-white shadow-sm' : 'border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]'}`;
      tabBtn.textContent = cat.name;
      tabsContainer.appendChild(tabBtn);

      // Select Dropdown
      const opt = document.createElement('option');
      opt.value = cat.id;
      opt.textContent = cat.name;
      selectEl.appendChild(opt);

      // Management list
      const item = document.createElement('div');
      item.className = 'flex items-center justify-between py-2 text-xs';
      item.innerHTML = `
        <span class="font-semibold text-[var(--text-primary)]">${cat.name} (${cat._count?.items ?? 0})</span>
        <button onclick="deleteCategory('${cat.id}')" class="p-1 text-red-500 hover:text-red-700" title="Delete Category">
          <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
        </button>
      `;
      catList.appendChild(item);
    });

    lucide.createIcons();
  }

  function selectCategory(id) {
    selectedCatId = id;
    renderCategories();
    renderDishes();
  }

  function filterDishes() {
    renderDishes();
  }

  function renderDishes() {
    const grid = document.getElementById('dishes-grid');
    const q = document.getElementById('menu-search').value.toLowerCase().trim();

    let filtered = menuItems;
    if (selectedCatId !== 'all') {
      filtered = filtered.filter(i => i.categoryId === selectedCatId);
    }
    if (q) {
      filtered = filtered.filter(i => i.name.toLowerCase().includes(q) || (i.description && i.description.toLowerCase().includes(q)));
    }

    if (filtered.length === 0) {
      grid.innerHTML = '<div class="col-span-full p-12 text-center text-xs text-[var(--text-muted)]">No dishes found. Click "+ Add Dish" to create your first item!</div>';
      return;
    }

    grid.innerHTML = '';
    filtered.forEach(dish => {
      const card = document.createElement('div');
      card.className = `p-5 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-sm flex flex-col justify-between space-y-4 transition hover:shadow-md ${!dish.isAvailable ? 'opacity-60 bg-gray-50 dark:bg-zinc-900' : ''}`;

      const imgHtml = dish.imageUrl ? `
        <img src="${dish.imageUrl}" alt="${dish.name}" class="w-full h-36 object-cover rounded-[var(--radius-lg)] mb-3 border border-[var(--border)]">
      ` : '';

      card.innerHTML = `
        <div>
          ${imgHtml}
          <div class="flex items-start justify-between gap-2 mb-1">
            <h4 class="font-serif font-bold text-base text-[var(--text-primary)]">${dish.name}</h4>
            <span class="font-mono font-bold text-sm text-[var(--accent)] shrink-0">$${parseFloat(dish.price).toFixed(2)}</span>
          </div>
          <p class="text-xs text-[var(--text-secondary)] line-clamp-2 mb-2">${dish.description || 'No description provided.'}</p>
          <div class="flex items-center gap-2 text-[10px] text-[var(--text-muted)]">
            <span class="px-2 py-0.5 rounded bg-[var(--bg-base)] border border-[var(--border)] font-medium">${dish.category?.name || 'Uncategorized'}</span>
            <span>⏱️ ${dish.preparationTime} mins</span>
          </div>
        </div>
        <div class="pt-3 border-t border-[var(--border)] flex items-center justify-between">
          <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-[var(--text-secondary)]">
            <input type="checkbox" onchange="toggleAvailability('${dish.id}', this.checked)" ${dish.isAvailable ? 'checked' : ''} class="rounded border-[var(--border)] text-[var(--accent)]">
            <span>${dish.isAvailable ? 'Available' : '86\'d (Out)'}</span>
          </label>
          <div class="flex items-center gap-1">
            <button onclick="openEditDishModal('${dish.id}')" class="p-1.5 rounded-[var(--radius-sm)] border border-[var(--border)] text-[var(--text-secondary)] hover:bg-[var(--bg-hover)]" title="Edit">
              <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
            </button>
            <button onclick="deleteDish('${dish.id}')" class="p-1.5 rounded-[var(--radius-sm)] border border-red-500/20 text-red-500 hover:bg-red-500/10" title="Delete">
              <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
            </button>
          </div>
        </div>
      `;
      grid.appendChild(card);
    });

    lucide.createIcons();
  }

  function openAddDishModal() {
    document.getElementById('dish-modal-title').textContent = 'Add New Dish';
    document.getElementById('dish-id').value = '';
    document.getElementById('dish-name').value = '';
    document.getElementById('dish-price').value = '';
    document.getElementById('dish-prep-time').value = '15';
    document.getElementById('dish-category').value = '';
    document.getElementById('dish-desc').value = '';
    document.getElementById('dish-image-file').value = '';
    document.getElementById('dish-image-url').value = '';
    document.getElementById('dish-available').checked = true;
    document.getElementById('dish-modal').classList.remove('hidden');
  }

  function openEditDishModal(id) {
    const dish = menuItems.find(i => i.id === id);
    if (!dish) return;
    document.getElementById('dish-modal-title').textContent = 'Edit Dish';
    document.getElementById('dish-id').value = dish.id;
    document.getElementById('dish-name').value = dish.name;
    document.getElementById('dish-price').value = dish.price;
    document.getElementById('dish-prep-time').value = dish.preparationTime;
    document.getElementById('dish-category').value = dish.categoryId || '';
    document.getElementById('dish-desc').value = dish.description || '';
    document.getElementById('dish-image-file').value = '';
    document.getElementById('dish-image-url').value = dish.imageUrl || '';
    document.getElementById('dish-available').checked = dish.isAvailable;
    document.getElementById('dish-modal').classList.remove('hidden');
  }

  function closeDishModal() {
    document.getElementById('dish-modal').classList.add('hidden');
  }

  async function submitDish(e) {
    e.preventDefault();
    const btn = document.getElementById('dish-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const id = document.getElementById('dish-id').value;
    const fileInput = document.getElementById('dish-image-file');
    let imageUrl = document.getElementById('dish-image-url').value;

    // Handle file upload if provided
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
        if (uploadData.success) {
          imageUrl = uploadData.url;
        }
      } catch (err) {
        console.warn('Image upload failed:', err);
      }
    }

    const payload = {
      name: document.getElementById('dish-name').value.trim(),
      price: parseFloat(document.getElementById('dish-price').value),
      preparationTime: parseInt(document.getElementById('dish-prep-time').value) || 15,
      categoryId: document.getElementById('dish-category').value || null,
      description: document.getElementById('dish-desc').value.trim() || null,
      imageUrl: imageUrl || null,
      isAvailable: document.getElementById('dish-available').checked,
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
      if (data.success) {
        window.showToast(`Dish ${id ? 'updated' : 'added'} successfully!`);
        closeDishModal();
        loadMenuData();
      } else {
        alert(data.error?.message || 'Failed to save dish');
      }
    } catch(err) {
      console.error(err);
    } finally {
      btn.disabled = false;
      btn.textContent = 'Save Dish';
    }
  }

  async function toggleAvailability(id, isAvailable) {
    try {
      await fetch(`/api/menu/${id}`, {
        method: 'PUT',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ isAvailable }),
      });
      window.showToast('Item availability updated');
      const item = menuItems.find(i => i.id === id);
      if (item) item.isAvailable = isAvailable;
      renderDishes();
    } catch (err) {
      console.error(err);
    }
  }

  async function deleteDish(id) {
    if (!confirm('Are you sure you want to delete this dish?')) return;
    try {
      await fetch(`/api/menu/${id}`, {
        method: 'DELETE',
        headers: window.getAuthHeader(),
      });
      window.showToast('Dish deleted');
      loadMenuData();
    } catch (err) {
      console.error(err);
    }
  }

  function openCategoryModal() {
    document.getElementById('category-modal').classList.remove('hidden');
  }

  function closeCategoryModal() {
    document.getElementById('category-modal').classList.add('hidden');
  }

  async function submitCategory(e) {
    e.preventDefault();
    const input = document.getElementById('cat-name-input');
    const name = input.value.trim();
    if (!name) return;

    try {
      const res = await fetch('/api/categories', {
        method: 'POST',
        headers: window.getAuthHeader(),
        body: JSON.stringify({ name }),
      });
      const data = await res.json();
      if (data.success) {
        input.value = '';
        window.showToast('Category created!');
        loadMenuData();
      }
    } catch(e) {
      console.error(e);
    }
  }

  async function deleteCategory(id) {
    if (!confirm('Delete this category? Items under this category will become uncategorized.')) return;
    try {
      await fetch(`/api/categories/${id}`, {
        method: 'DELETE',
        headers: window.getAuthHeader(),
      });
      window.showToast('Category deleted');
      loadMenuData();
    } catch (err) {
      console.error(err);
    }
  }
</script>
@endsection
