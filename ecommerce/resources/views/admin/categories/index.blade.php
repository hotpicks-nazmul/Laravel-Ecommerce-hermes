@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total</div>
                <div class="h4 mb-0 text-primary" id="stat-total">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Active</div>
                <div class="h4 mb-0 text-success" id="stat-active">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Inactive</div>
                <div class="h4 mb-0 text-secondary" id="stat-inactive">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Featured</div>
                <div class="h4 mb-0 text-info" id="stat-featured">{{ $stats['featured'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Parent</div>
                <div class="h4 mb-0 text-warning" id="stat-parents">{{ $stats['parents'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">With Products</div>
                <div class="h4 mb-0 text-dark" id="stat-with-products">{{ $stats['with_products'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Categories</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categories.export') }}" class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add New Category
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <!-- Search -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <label class="form-label small text-muted">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Search categories..." value="{{ request('search') }}">
                    <span class="input-group-text" id="searchSpinner" style="display: none;">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </span>
                </div>
            </div>
            
            <!-- Status -->
            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label small text-muted">Status</label>
                <select name="status" id="filterStatus" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <!-- Featured -->
            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label small text-muted">Featured</label>
                <select name="featured" id="filterFeatured" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="yes" {{ request('featured') === 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ request('featured') === 'no' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            
            <!-- View Mode -->
            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label small text-muted">View Mode</label>
                <select name="view" id="viewMode" class="form-select form-select-sm">
                    <option value="tree" {{ request('view', 'tree') === 'tree' ? 'selected' : '' }}>Tree View</option>
                    <option value="flat" {{ request('view') === 'flat' ? 'selected' : '' }}>Flat View</option>
                </select>
            </div>
            
            <!-- Buttons -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="d-flex gap-1">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary flex-grow-1" id="resetFilters">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar -->
<div class="card border-0 shadow-sm mb-3" id="bulkActionsBar" style="display: none;">
    <div class="card-body py-2">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted"><span id="selectedCount">0</span> selected</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                    Clear Selection
                </button>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('activate')">
                    <i class="bi bi-check-circle me-1"></i> Activate
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('deactivate')">
                    <i class="bi bi-pause-circle me-1"></i> Deactivate
                </button>
                <button type="button" class="btn btn-sm btn-info" onclick="bulkAction('feature')">
                    <i class="bi bi-star me-1"></i> Feature
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="bulkAction('unfeature')">
                    <i class="bi bi-star-fill me-1"></i> Unfeature
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAction('show_in_menu')">
                    <i class="bi bi-list me-1"></i> Show in Menu
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkAction('hide_from_menu')">
                    <i class="bi bi-list-nested me-1"></i> Hide from Menu
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Categories Table/Tree -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="categoriesTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                        </th>
                        <th style="width: 60px;">Image</th>
                        <th>Category Name</th>
                        <th style="width: 100px;">Products</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 80px;">Featured</th>
                        <th style="width: 80px;">Menu</th>
                        <th style="width: 80px;">Homepage</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    @if($viewMode === 'tree')
                        @forelse($categories as $category)
                            @include('admin.categories.partials.category-row', ['category' => $category, 'depth' => 0])
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="bi bi-folder text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No categories found.</p>
                                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add Your First Category
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    @else
                        @forelse($categories as $category)
                            <tr data-id="{{ $category->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input category-checkbox" value="{{ $category->id }}" onchange="updateBulkActions()">
                                </td>
                                <td>
                                    @if($category->image)
                                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-folder text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-decoration-none">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $category->products_count > 0 ? 'bg-info' : 'bg-light text-dark' }}">
                                        {{ $category->products_count }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm status-toggle {{ $category->status === 'active' ? 'btn-success' : 'btn-outline-secondary' }}" data-id="{{ $category->id }}">
                                        {{ ucfirst($category->status) }}
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm featured-toggle {{ $category->is_featured ? 'btn-info' : 'btn-outline-secondary' }}" data-id="{{ $category->id }}">
                                        <i class="bi {{ $category->is_featured ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm menu-toggle {{ $category->show_in_menu ? 'btn-success' : 'btn-outline-secondary' }}" data-id="{{ $category->id }}">
                                        <i class="bi {{ $category->show_in_menu ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm homepage-toggle {{ $category->show_in_homepage ? 'btn-success' : 'btn-outline-secondary' }}" data-id="{{ $category->id }}">
                                        <i class="bi {{ $category->show_in_homepage ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                    </button>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($category->canBeDeleted())
                                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary disabled" title="Cannot delete - has children or products" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="bi bi-folder text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No categories found.</p>
                                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add Your First Category
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Pagination for flat view -->
        @if($viewMode !== 'tree' && isset($categories) && method_exists($categories, 'hasPages') && $categories->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2" id="paginationContainer">
            <div class="text-muted small">
                Showing {{ $categories->firstItem() }} - {{ $categories->lastItem() }} of {{ $categories->total() }} categories
            </div>
            <div>
                {{ $categories->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Bulk Action Form -->
<form id="bulkActionForm" method="POST" action="{{ route('admin.categories.bulk-action') }}">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <input type="hidden" name="ids" id="bulkIdsInput">
</form>
@endsection

@push('styles')
<style>
.status-toggle, .featured-toggle, .menu-toggle, .homepage-toggle {
    min-width: 70px;
    transition: all 0.2s;
}
.status-toggle:hover, .featured-toggle:hover {
    transform: scale(1.05);
}
.table > :not(caption) > * > * {
    padding: 0.75rem 0.5rem;
}
.category-checkbox:checked + td img {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.3);
}
.tree-indent {
    display: inline-block;
    width: 24px;
}
.tree-toggle {
    cursor: pointer;
    width: 20px;
    display: inline-block;
    text-align: center;
}
.children-row {
    background-color: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script>
let selectedCategories = new Set();

// Toggle select all
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.category-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
        if (checkbox.checked) {
            selectedCategories.add(parseInt(cb.value));
        } else {
            selectedCategories.delete(parseInt(cb.value));
        }
    });
    updateBulkActions();
}

// Clear selection
function clearSelection() {
    selectedCategories.clear();
    const checkboxes = document.querySelectorAll('.category-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    document.getElementById('selectAllCheckbox').checked = false;
    updateBulkActions();
}

// Update bulk actions bar visibility
function updateBulkActions() {
    const count = selectedCategories.size;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkActionsBar').style.display = count > 0 ? 'block' : 'none';
    
    // Update select all checkbox state
    const checkboxes = document.querySelectorAll('.category-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    document.getElementById('selectAllCheckbox').checked = allChecked && checkboxes.length > 0;
}

// Perform bulk action
function bulkAction(action) {
    if (selectedCategories.size === 0) {
        alert('Please select at least one category.');
        return;
    }
    
    let confirmMsg = '';
    switch(action) {
        case 'delete':
            confirmMsg = `Are you sure you want to delete ${selectedCategories.size} category(s)? Categories with products or children cannot be deleted.`;
            break;
        case 'activate':
            confirmMsg = `Activate ${selectedCategories.size} category(s)?`;
            break;
        case 'deactivate':
            confirmMsg = `Deactivate ${selectedCategories.size} category(s)?`;
            break;
        case 'feature':
            confirmMsg = `Mark ${selectedCategories.size} category(s) as featured?`;
            break;
        case 'unfeature':
            confirmMsg = `Remove ${selectedCategories.size} category(s) from featured?`;
            break;
        default:
            confirmMsg = `Apply this action to ${selectedCategories.size} category(s)?`;
    }
    
    if (!confirm(confirmMsg)) return;
    
    document.getElementById('bulkActionInput').value = action;
    document.getElementById('bulkIdsInput').value = JSON.stringify(Array.from(selectedCategories));
    document.getElementById('bulkActionForm').submit();
}

// Toggle status via AJAX
function initStatusToggles() {
    document.querySelectorAll('.status-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`{{ route('admin.categories.toggle-status', ['category' => 'ID']) }}`.replace('ID', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.textContent = ucfirst(data.status);
                    this.classList.toggle('btn-success', data.status === 'active');
                    this.classList.toggle('btn-outline-secondary', data.status !== 'active');
                    showToast(data.message, 'success');
                }
            });
        });
    });
}

// Toggle featured via AJAX
function initFeaturedToggles() {
    document.querySelectorAll('.featured-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`{{ route('admin.categories.toggle-featured', ['category' => 'ID']) }}`.replace('ID', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-star', !data.is_featured);
                    icon.classList.toggle('bi-star-fill', data.is_featured);
                    this.classList.toggle('btn-info', data.is_featured);
                    this.classList.toggle('btn-outline-secondary', !data.is_featured);
                    showToast(data.message, 'success');
                }
            });
        });
    });
}

// Toggle menu visibility via AJAX
function initMenuToggles() {
    document.querySelectorAll('.menu-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`{{ route('admin.categories.toggle-menu', ['category' => 'ID']) }}`.replace('ID', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-check-circle', data.show_in_menu);
                    icon.classList.toggle('bi-x-circle', !data.show_in_menu);
                    this.classList.toggle('btn-success', data.show_in_menu);
                    this.classList.toggle('btn-outline-secondary', !data.show_in_menu);
                    showToast(data.message, 'success');
                }
            });
        });
    });
}

// Toggle homepage visibility via AJAX
function initHomepageToggles() {
    document.querySelectorAll('.homepage-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`{{ route('admin.categories.toggle-homepage', ['category' => 'ID']) }}`.replace('ID', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-check-circle', data.show_in_homepage);
                    icon.classList.toggle('bi-x-circle', !data.show_in_homepage);
                    this.classList.toggle('btn-success', data.show_in_homepage);
                    this.classList.toggle('btn-outline-secondary', !data.show_in_homepage);
                    showToast(data.message, 'success');
                }
            });
        });
    });
}

// Toggle children visibility in tree view
function toggleChildren(id) {
    const children = document.querySelectorAll(`tr[data-parent="${id}"]`);
    const toggle = document.querySelector(`tr[data-id="${id}"] .tree-toggle i`);
    
    children.forEach(child => {
        if (child.style.display === 'none') {
            child.style.display = '';
        } else {
            child.style.display = 'none';
            // Also hide nested children
            const childId = child.dataset.id;
            const nestedChildren = document.querySelectorAll(`tr[data-parent="${childId}"]`);
            nestedChildren.forEach(nc => nc.style.display = 'none');
        }
    });
    
    if (toggle) {
        toggle.classList.toggle('bi-chevron-down');
        toggle.classList.toggle('bi-chevron-right');
    }
}

// Helper functions
function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed`;
    toast.style.cssText = 'bottom: 20px; right: 20px; z-index: 9999;';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    setTimeout(() => toast.remove(), 5000);
}

// Live search function
function performLiveSearch() {
    const searchSpinner = document.getElementById('searchSpinner');
    searchSpinner.style.display = 'block';
    
    // Build query parameters
    const params = new URLSearchParams();
    
    const searchTerm = document.getElementById('liveSearch').value.trim();
    if (searchTerm) params.set('search', searchTerm);
    
    const status = document.getElementById('filterStatus').value;
    if (status) params.set('status', status);
    
    const featured = document.getElementById('filterFeatured').value;
    if (featured) params.set('featured', featured);
    
    const viewMode = document.getElementById('viewMode').value;
    params.set('view', viewMode);
    
    // Make AJAX request
    fetch(`{{ route('admin.categories.index') }}?${params.toString()}&ajax=1`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        searchSpinner.style.display = 'none';
        
        if (data.html) {
            // Update table body
            const tbody = document.querySelector('#categoriesTable tbody');
            tbody.innerHTML = data.html;
            
            // Update stats
            if (data.stats) {
                document.getElementById('stat-total').textContent = data.stats.total;
                document.getElementById('stat-active').textContent = data.stats.active;
                document.getElementById('stat-inactive').textContent = data.stats.inactive;
                document.getElementById('stat-featured').textContent = data.stats.featured;
                document.getElementById('stat-parents').textContent = data.stats.parents;
                document.getElementById('stat-with-products').textContent = data.stats.with_products;
            }
            
            // Update pagination
            if (viewMode !== 'tree') {
                const paginationContainer = document.getElementById('paginationContainer');
                if (paginationContainer && data.pagination) {
                    paginationContainer.outerHTML = data.pagination;
                }
            }
            
            // Reinitialize event listeners
            reinitializeEventListeners();
            
            // Clear selection
            clearSelection();
            
            // Update URL without reload
            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.pushState({}, '', newUrl);
        }
    })
    .catch(err => {
        searchSpinner.style.display = 'none';
        console.error('Search error:', err);
    });
}

// Reinitialize event listeners after AJAX update
function reinitializeEventListeners() {
    initStatusToggles();
    initFeaturedToggles();
    initMenuToggles();
    initHomepageToggles();
    
    // Product checkboxes
    document.querySelectorAll('.category-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    clearSelection();
    
    // Initialize toggle buttons
    initStatusToggles();
    initFeaturedToggles();
    initMenuToggles();
    initHomepageToggles();
    
    // Live search with debounce
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performLiveSearch();
        }, 300);
    });
    
    // Live filter for dropdowns
    const filterSelects = ['filterStatus', 'filterFeatured', 'viewMode'];
    filterSelects.forEach(id => {
        const select = document.getElementById(id);
        if (select) {
            select.addEventListener('change', function() {
                performLiveSearch();
            });
        }
    });
});
</script>
@endpush
