# UI Preferences & Guidelines

This document contains UI/UX preferences and guidelines for consistent styling across the admin panel.

---

## Table of Contents

1. **Floating Action Buttons** - Standard button style for create/edit pages
2. **Floating Buttons Overlap Issue** - Fix for floating buttons blocking content
3. **Icons** - Bootstrap Icons reference for common actions
4. **Cards** - Standard card styling patterns
5. **Form Controls** - Required fields, help text, validation errors
6. **Search & Filter Functionality** - Live search, filters, AJAX updates
7. **Bulk Actions** - Selection management and bulk operations
8. **Statistics Cards** - Summary statistics display for listing pages

---

## Floating Action Buttons

### Standard Style (Used in Create/Edit Pages)

All create and edit pages should use the **floating-save-container** style for action buttons. This style is already defined in `ecommerce/resources/views/admin/layouts/app.blade.php`.

#### HTML Structure

```html
<!-- Floating Save Button -->
<div class="floating-save-container">
    <a href="{{ route('cancel.route') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="formId" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Action Name
    </button>
</div>
```

#### CSS Classes

| Class | Purpose |
|-------|---------|
| `floating-save-container` | Container for floating buttons (fixed position at bottom-right) |
| `floating-save-btn` | Primary action button (gradient purple background) |
| `floating-reset-btn` | Secondary/Cancel button (gray background) |

#### Button Styling

| Button Type | CSS Classes | Icon |
|-------------|-------------|------|
| **Cancel** | `btn btn-secondary floating-reset-btn` | `bi-x-lg` |
| **Create** | `btn btn-primary floating-save-btn` | `bi-check-lg` |
| **Update** | `btn btn-primary floating-save-btn` | `bi-check-lg` |
| **Delete** | `btn btn-outline-danger floating-reset-btn` | `bi-trash` |

### Example - Create Page

```html
<div class="floating-save-container">
    <a href="{{ route('admin.items.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="itemForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create Item
    </button>
</div>
```

### Example - Edit Page (with Delete)

```html
<div class="floating-save-container">
    <a href="{{ route('admin.items.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <a href="{{ route('admin.items.destroy', $item->id) }}" 
       class="btn btn-outline-danger floating-reset-btn" 
       onclick="event.preventDefault(); if(confirm('Are you sure?')) { document.getElementById('deleteForm').submit(); }">
        <i class="bi bi-trash me-1"></i> Delete
    </a>
    <button type="submit" form="itemForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Update Item
    </button>
</div>
```

---

## Do NOT Use

### ❌ Incorrect - Custom Floating Actions

```html
<!-- DO NOT USE THIS STYLE -->
<div class="floating-actions">
    <div class="container-fluid">
        <div class="d-flex justify-content-end gap-2">
            <a href="..." class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </div>
</div>

<style>
.floating-actions {
    position: fixed;
    bottom: 0;
    /* ... custom CSS ... */
}
</style>
```

### ✅ Correct - Use Global Style

```html
<!-- USE THIS STYLE INSTEAD -->
<div class="floating-save-container">
    <a href="..." class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="formId" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create
    </button>
</div>
```

---

## Floating Buttons Overlap Issue

### Problem

When using floating action buttons on pages with forms or content that extends to the bottom of the page, the floating buttons can block/overlap with form elements or other components at the bottom.

**Affected Pages:**
- Bulk Import (`bulk-import.blade.php`)
- Bulk Export (`bulk-export.blade.php`)
- Bulk Discount (`bulk-discount.blade.php`)
- Any other pages with floating buttons

### Solution

Add bottom padding to the content area to create space for the floating buttons. Include this CSS in the `@push('styles')` section of each affected page:

```html
@push('styles')
<style>
    /* Add padding at bottom to prevent floating button overlap */
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush
```

### Why This Works

- The `.floating-save-container` is positioned `fixed` at `bottom: 20px`
- The floating buttons have a height of approximately 48px (12px padding + 24px content)
- Adding 100px padding-bottom ensures content doesn't extend behind the floating buttons
- The `!important` flag ensures this overrides any conflicting styles

### Alternative Approach (Global)

If you want to apply this globally for all pages with floating buttons, you can add the `has-floating-save` class to the content-area div in the layout file:

```html
<!-- In layouts/app.blade.php -->
<div class="content-area has-floating-save">
    @yield('content')
</div>
```

The CSS for this class is already defined in the layout:
```css
.content-area.has-floating-save {
    padding-bottom: 100px;
}
```

However, the per-page approach is preferred to avoid unnecessary padding on pages without floating buttons.

---

## Sidebar Navigation

### Menu Structure

The sidebar uses collapsible menu categories. Each category has:

1. **Header** - Clickable to expand/collapse
2. **Submenu items** - Navigation links

### Active State

Menu items are highlighted when the current route matches:

```php
<a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="...">
```

### Collapse State

Submenus stay open when any child route is active:

```php
<div class="collapse {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="menuProducts">
```

---

## Form Layout

### Standard Two-Column Layout

```html
<div class="row">
    <div class="col-lg-8">
        <!-- Main form content -->
        <form id="formId" method="POST" action="...">
            @csrf
            <!-- Form fields -->
        </form>
    </div>
    
    <div class="col-lg-4">
        <!-- Sidebar content -->
        <!-- Status, Actions, Tips, etc. -->
    </div>
</div>

<!-- Floating buttons at the bottom -->
<div class="floating-save-container">
    ...
</div>
```

---

## Icons

Use Bootstrap Icons (`bi bi-*`) consistently:

| Action | Icon |
|--------|------|
| Add/Create | `bi-plus-lg` or `bi-plus-circle` |
| Edit | `bi-pencil` |
| Delete | `bi-trash` |
| Save/Confirm | `bi-check-lg` |
| Cancel | `bi-x-lg` |
| Back | `bi-arrow-left` |
| View | `bi-eye` |
| Settings | `bi-gear` |
| Category | `bi-folder` |
| Digital | `bi-file-earmark-binary` |

---

## Cards

Standard card styling:

```html
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-icon me-2"></i>Card Title</h6>
    </div>
    <div class="card-body">
        <!-- Content -->
    </div>
</div>
```

---

## Form Controls

### Required Fields

```html
<label class="form-label">Field Name <span class="text-danger">*</span></label>
```

### Help Text

```html
<small class="text-muted">Helper text goes here</small>
```

### Validation Errors

```html
<input type="text" class="form-control @error('field') is-invalid @enderror" ...>
@error('field')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
```

---

## Search & Filter Functionality

The products index page (`/admin/products`) uses a live search and filter system with AJAX. This pattern should be followed for all listing pages.

### Filter Form Structure

```html
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Search Input -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" 
                               placeholder="Name, SKU..." value="{{ request('search') }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm"></div>
                        </span>
                    </div>
                </div>
                
                <!-- Filter Dropdowns -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Category</label>
                    <select name="category" id="filterCategory" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Status Filter -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" id="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <!-- Reset Button -->
                <div class="col-lg-2 col-md-4 col-sm-8">
                    <a href="{{ route('admin.items.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
```

### Live Search JavaScript

```javascript
// Debounced live search
let searchTimeout;
const searchInput = document.getElementById('liveSearch');
const searchSpinner = document.getElementById('searchSpinner');

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const searchTerm = this.value.trim();
    
    // Show spinner
    searchSpinner.style.display = 'block';
    
    // Debounce - wait 300ms after user stops typing
    searchTimeout = setTimeout(() => {
        performLiveSearch(searchTerm);
    }, 300);
});

// Filter dropdowns trigger search on change
const filterSelects = ['filterCategory', 'filterStatus', 'filterStock'];
filterSelects.forEach(id => {
    const select = document.getElementById(id);
    if (select) {
        select.addEventListener('change', function() {
            performLiveSearch(searchInput.value.trim());
        });
    }
});

// Live search function
function performLiveSearch(searchTerm) {
    const params = new URLSearchParams();
    
    if (searchTerm) params.set('search', searchTerm);
    
    // Add filter values
    const category = document.getElementById('filterCategory').value;
    if (category) params.set('category', category);
    
    // Keep existing sort and per_page
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('sort')) params.set('sort', urlParams.get('sort'));
    if (urlParams.get('per_page')) params.set('per_page', urlParams.get('per_page'));
    
    // AJAX request
    fetch(`{{ route('admin.items.index') }}?${params.toString()}&ajax=1`, {
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
            document.querySelector('#tableBody').innerHTML = data.html;
            
            // Update URL without reload
            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.pushState({}, '', newUrl);
        }
    });
}
```

### Controller Handling

```php
public function index(Request $request)
{
    $query = Model::query();
    
    // Search
    if ($request->search) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
              ->orWhere('sku', 'like', "%{$request->search}%");
        });
    }
    
    // Filters
    if ($request->category) {
        $query->where('category_id', $request->category);
    }
    
    if ($request->status) {
        $query->where('status', $request->status);
    }
    
    // Sorting
    $sort = $request->sort ?? 'created_at';
    $direction = $request->direction ?? 'desc';
    $query->orderBy($sort, $direction);
    
    // Pagination
    $perPage = $request->per_page ?? 25;
    $items = $query->paginate($perPage);
    
    // AJAX response
    if ($request->ajax()) {
        return response()->json([
            'html' => view('admin.items.partials.table-rows', compact('items'))->render(),
            'pagination' => $items->links()->toHtml(),
            'stats' => $this->getStats()
        ]);
    }
    
    return view('admin.items.index', compact('items'));
}
```

### Key Features

| Feature | Description |
|---------|-------------|
| **Debounced Search** | 300ms delay before triggering search |
| **Loading Spinner** | Shows spinner while AJAX request is in progress |
| **URL Update** | Updates browser URL without page reload |
| **Multiple Filters** | Category, Status, Stock, Featured filters |
| **Sorting** | Clickable column headers for sorting |
| **Per Page** | User can select items per page (10, 25, 50, 100) |
| **AJAX Updates** | Table updates without full page reload |

---

## Bulk Actions

### Bulk Actions Bar

Shows when items are selected:

```html
<div class="card border-0 shadow-sm mb-3" id="bulkActionsBar" style="display: none;">
    <div class="card-body py-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted"><span id="selectedCount">0</span> selected</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                    Clear Selection
                </button>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('activate')">
                    <i class="bi bi-check-circle me-1"></i> Activate
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>
```

### Bulk Action JavaScript

```javascript
let selectedItems = new Set();

function updateBulkActions() {
    const count = selectedItems.size;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkActionsBar').style.display = count > 0 ? 'block' : 'none';
}

function bulkAction(action) {
    if (selectedItems.size === 0) {
        alert('Please select at least one item.');
        return;
    }
    
    if (!confirm(`Are you sure you want to ${action} ${selectedItems.size} item(s)?`)) return;
    
    document.getElementById('bulkActionInput').value = action;
    document.getElementById('bulkIdsInput').value = JSON.stringify(Array.from(selectedItems));
    document.getElementById('bulkActionForm').submit();
}
```

---

## Statistics Cards

Display summary statistics at the top of listing pages:

```html
<div class="row mb-4">
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total</div>
                <div class="h4 mb-0 text-primary">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Active</div>
                <div class="h4 mb-0 text-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <!-- More stat cards... -->
</div>
```

---

*Last updated: February 2026*
