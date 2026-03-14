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
9. **Important Implementation Rule** - Admin Panel + Frontend Integration Rule
10. **Route Conflict Prevention** - Avoid placeholder routes conflicting with actual implementations
11. **Sidebar Navigation State** - Keep menu expanded when child item is active
12. **Product Images** - Proper image path handling for admin tables
13. **404 Errors Due to Route Ordering** - Fix for routes not matching correctly
14. **Create/Edit Form Layout** - Proper form structure for multi-card forms
15. **Table Listing Pages** - Proper table structure and styling
16. **@push Directive Placement** - CSS/JS not loading when @push is before @section
17. **Tabbed Interface Pages** - Proper view rendering for tab-based admin pages
18. **Bootstrap Modal Popup** - Reusable modal for showing messages/info without page reload

---

## Create/Edit Form Layout

### Standard Two-Column Layout with Sidebar Cards

When creating forms that span multiple cards (e.g., Basic Info, Location, Settings), use a single form with the `id` attribute and connect fields from sidebar cards using the `form` attribute.

#### HTML Structure

```html
<div class="row">
    <div class="col-lg-8">
        <!-- Card 1: Basic Info -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-icon me-2"></i>Card Title</h6>
            </div>
            <div class="card-body">
                <form id="itemForm" method="POST" action="...">
                    @csrf
                    <!-- Form fields -->
                </form>
            </div>
        </div>
        
        <!-- Card 2: Additional Info -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-icon me-2"></i>Card Title</h6>
            </div>
            <div class="card-body">
                <!-- Fields with form attribute -->
                <div class="mb-3">
                    <label for="field_name" class="form-label">Field Label</label>
                    <input type="text" id="field_name" name="field_name" form="itemForm" class="form-control">
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Sidebar Card with form attribute -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Status</h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" form="itemForm">
                    <label class="form-check-label" for="is_active">
                        <i class="bi bi-check-circle text-success me-1"></i> Active
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Buttons -->
<div class="floating-save-container">
    <a href="{{ route('admin.items.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="itemForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create Item
    </button>
</div>
```

### Key Points

| Pattern | Usage |
|---------|-------|
| `id="itemForm"` | Add to main form element |
| `form="itemForm"` | Add to all input fields outside the main form |
| `id="field_name"` | Add to all inputs for label connection |
| `for="field_name"` | Add to labels to connect with inputs |
| `<div class="form-text">` | Use for help text instead of `<small>` |

### Form Controls Best Practices

```html
<!-- Required Field -->
<label for="name" class="form-label">Field Name <span class="text-danger">*</span></label>
<input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" required>
@error('name')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror

<!-- Help Text -->
<div class="form-text">Help text goes here</div>

<!-- Input with Icon -->
<div class="input-group">
    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
    <input type="text" class="form-control" id="slug" name="slug" placeholder="auto-generated">
</div>

<!-- Form Switch with Icon -->
<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" id="is_active" form="itemForm">
    <label class="form-check-label" for="is_active">
        <i class="bi bi-check-circle text-success me-1"></i> Active
    </label>
    <div class="form-text">Help text</div>
</div>
```

### Header with Back Button

```html
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Page Title</h4>
    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Items
    </a>
</div>
```

---

## Table Listing Pages

### Standard Table Structure

For index/listing pages, use the following structure:

```html
<!-- Header with Title and Add Button -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Page Title</h4>
    <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Item
    </a>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Search Input -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Search...">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                        </th>
                        <th>Column Header</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Table rows -->
                </tbody>
            </table>
        </div>
        
        <!-- Pagination inside card-body -->
        @if($items->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $items->firstItem() }} - {{ $items->lastItem() }} of {{ $items->total() }} items
            </div>
            <div>
                {{ $items->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
```

### Key Points

| Element | Class/Attribute |
|---------|----------------|
| Table wrapper | `<div class="table-responsive">` |
| Table | `<table class="table table-hover align-middle mb-0">` |
| Checkbox column | `style="width: 40px;"` |
| Actions column | `style="width: 120px;"` |
| Card body | `<div class="card-body p-0">` |
| Pagination | Inside `card-body`, wrapped in `card-footer` |
| Empty state | Use `<tr><td colspan="X" class="text-center py-5">` |

### Empty State

```html
<tr>
    <td colspan="8" class="text-center py-5">
        <i class="bi bi-folder text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mb-2 mt-2">No items found</p>
        <a href="{{ route('admin.items.create') }}" class="btn btn-sm btn-primary mt-1">
            <i class="bi bi-plus-lg me-1"></i> Add First Item
        </a>
    </td>
</tr>
```

---

## Floating Action Buttons

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

---

## Important Implementation Rule

**Admin Panel + Frontend Integration Rule:**

When implementing any admin panel functionality that affects the frontend display or user experience, ALWAYS implement the corresponding frontend adjustments as well. This includes but is not limited to:

1. **Product-related features** (attributes, colors, variants) - Must be displayed on product detail page and work with cart/checkout
2. **Category/Brand management** - Must be reflected in frontend filters and navigation
3. **Banner/Slider management** - Must display correctly on homepage
4. **Settings changes** - Must reflect in frontend layout, colors, logos, etc.
5. **SEO/Meta settings** - Must be applied to frontend pages
6. **Payment/Shipping settings** - Must work with frontend checkout process

**Rule:** `Admin Panel Functionality = Backend + Frontend Implementation`

Always ask: "Does this admin feature need frontend display or interaction?" If yes, implement both sides.

---

## Route Conflict Prevention

### Problem: Placeholder Routes vs Actual Implementation

When implementing new features, there may be placeholder routes in `routes/admin.php` that show "under development" messages. These placeholder routes can conflict with actual implementations.

**Example Issue:**
- Placeholder route: `admin/related-products` → Shows "under development" message
- Actual implementation: `admin/products/{product}/related` → Full functionality

**What Happens:**
- User clicks a menu item linking to the placeholder route
- User sees "This feature is currently under development" instead of the actual implementation
- Confusion ensues because the actual feature exists at a different URL

### Solution

1. **Check for placeholder routes** before implementing new features:
   ```bash
   php artisan route:list --name=feature-name
   ```

2. **Remove or update placeholder routes** when implementing the actual feature:
   - Delete placeholder routes from `routes/admin.php`
   - Ensure menu links point to the correct implementation URLs

3. **Use consistent route naming**:
   - Good: `admin.products.related` (nested under products)
   - Avoid: `admin.related-products` (separate top-level route)

4. **Clear route cache after changes**:
   ```bash
   php artisan route:clear
   ```

### Prevention Checklist

When implementing a new feature:
- [ ] Search for existing placeholder routes with similar names
- [ ] Remove any conflicting placeholder routes
- [ ] Update sidebar menu links to point to correct URLs
- [ ] Clear route cache after deployment

---

## Sidebar Navigation State

### Problem: Menu Collapses When Clicking Child Items

When a user clicks on a submenu item, the parent menu category collapses, causing the user to lose track of where they are in the navigation hierarchy.

**Why This Happens:**
- The menu uses Bootstrap collapse which toggles on click
- The `aria-expanded` state is not properly maintained across page navigation
- The page reloads and the menu state resets

### Solution: Keep Parent Menu Expanded for Active Routes

The sidebar menu should automatically expand and highlight the parent category when a child route is active.

**Implementation in `layouts/app.blade.php`:**

```html
<!-- Menu Category with proper active state detection -->
<div class="menu-category">
    <a class="menu-category-header {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" 
       data-bs-toggle="collapse" 
       href="#menuProducts" 
       role="button" 
       aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}">
        <div>
            <i class="bi bi-box menu-icon"></i>
            <span class="menu-category-title">Products</span>
        </div>
        <i class="bi bi-chevron-down arrow"></i>
    </a>
    <div class="collapse {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="menuProducts">
        <!-- Submenu items -->
    </div>
</div>
```

### Key Points

1. **`aria-expanded`**: Set to `'true'` when any child route is active
2. **`.show` class**: Add to collapse div to keep it expanded
3. **`.active` class**: Add to header for visual feedback
4. **`request()->routeIs('admin.products.*')`**: Matches all routes starting with `admin.products.`

### Route Naming Convention for Menu State

Use consistent route naming to make menu state detection easier:

| Feature | Route Name Pattern | Menu Detection |
|---------|-------------------|----------------|
| Products | `admin.products.*` | `routeIs('admin.products.*')` |
| Orders | `admin.orders.*` | `routeIs('admin.orders.*')` |
| Categories | `admin.categories.*` | `routeIs('admin.categories.*')` |
| Settings | `admin.settings.*` | `routeIs('admin.settings.*')` |

### Common Issues

1. **Menu collapses after clicking**: Ensure `aria-expanded` and `.show` class are set based on active route
2. **Wrong menu highlighted**: Check route naming matches the pattern in `routeIs()`
3. **Multiple menus expanded**: Each menu should have unique detection logic

---

## Product Images

### Problem: Images Not Displaying in Admin Tables

When displaying product images in admin listing tables, the images may not appear if the `featured_image` path doesn't include the `/storage/` prefix. This is a common issue because the database may store just the filename or a relative path.

### Solution: Proper Image Path Handling

Use this pattern in your Blade templates to properly handle product images:

```php
@php
    $imageUrl = $product->featured_image;
    if($imageUrl && !str_starts_with($imageUrl, '/storage/') && !str_starts_with($imageUrl, 'http')) {
        $imageUrl = '/storage/' . $imageUrl;
    }
@endphp

@if($imageUrl)
<img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
@else
<div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
    <i class="bi bi-image text-white"></i>
</div>
@endif
```

### Why This Works

| Scenario | Original Value | Processed Value |
|----------|---------------|----------------|
| Filename only | `products/image.jpg` | `/storage/products/image.jpg` |
| Already has prefix | `/storage/products/image.jpg` | `/storage/products/image.jpg` |
| External URL | `https://cdn.example.com/img.jpg` | `https://cdn.example.com/img.jpg` |
| Empty/Null | `null` | `null` (shows placeholder) |

### Key Points

1. **Check for `/storage/` prefix** - Don't add if already present
2. **Check for `http` prefix** - Don't modify external URLs
3. **Always provide fallback** - Show placeholder icon when no image
4. **Use consistent styling** - Match the image size with other table columns

### Example in Table Rows

```html
<td>
    @php
        $imageUrl = $product->featured_image;
        if($imageUrl && !str_starts_with($imageUrl, '/storage/') && !str_starts_with($imageUrl, 'http')) {
            $imageUrl = '/storage/' . $imageUrl;
        }
    @endphp
    @if($imageUrl)
    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
    @else
    <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
        <i class="bi bi-image text-white"></i>
    </div>
    @endif
    <div class="d-inline-block">
        <div class="fw-medium">{{ $product->name }}</div>
    </div>
</td>
```

---

## 404 Errors Due to Route Ordering

### Problem

When creating custom routes for specific order types (like `/admin/orders/in-house`), you may encounter 404 errors or unexpected behavior if the routes are defined in the wrong order.

**Example Issue:**
- `/admin/orders` works correctly
- `/admin/orders/in-house` returns 404 error

### Root Cause

This happens because the **resource route** (which includes a wildcard pattern `/{order}`) is defined before the specific route. Laravel matches routes in the order they are defined, so the wildcard route matches `/in-house` as an order ID instead of recognizing it as a separate route.

### Incorrect Route Order

```php
// ❌ INCORRECT - Resource route first
Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);

// This route will never be matched because the resource route's wildcard pattern matches first
Route::get('/orders/in-house', [OrderController::class, 'inHouse'])->name('orders.in-house');
Route::get('/orders/in-house/create', [OrderController::class, 'create'])->name('orders.in-house.create');
Route::post('/orders/in-house', [OrderController::class, 'store'])->name('orders.in-house.store');
Route::get('/orders/in-house/{order}', [OrderController::class, 'inHouseShow'])->name('orders.in-house.show');
```

### Solution

**Always define specific routes before resource routes.** This ensures that Laravel matches the specific route patterns first.

```php
// ✅ CORRECT - Specific routes first
Route::get('/orders/in-house', [OrderController::class, 'inHouse'])->name('orders.in-house');
Route::get('/orders/in-house/create', [OrderController::class, 'create'])->name('orders.in-house.create');
Route::post('/orders/in-house', [OrderController::class, 'store'])->name('orders.in-house.store');
Route::get('/orders/in-house/{order}', [OrderController::class, 'inHouseShow'])->name('orders.in-house.show');

// Resource route comes after specific routes
Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
```

### Best Practice

1. **Specific Routes First:** Define all custom routes with specific patterns before any resource or wildcard routes
2. **Resource Routes Last:** Always place resource routes at the end of the route group
3. **Order Matters:** Remember that Laravel matches routes in the order they are defined
4. **Test Routes:** Verify all routes are working after making changes to the route definitions

This pattern applies to all resource controllers, not just the orders controller.

---

## @push Directive Placement

### Problem

When creating admin panel pages (create/edit forms), CSS and JavaScript may not load correctly, causing layout issues where:
- Header appears at the bottom of the page
- Sidebar overlaps the main content
- Form elements appear displaced

### Root Cause

In Laravel Blade, the `@push` directive must be placed AFTER the `@section` declaration where the content will be injected. If `@push` is placed BEFORE `@section('content')`, the stacked content may not be properly rendered in the layout's `@stack` directive.

### Incorrect Code

```php
@extends('admin.layouts.app')

@section('title', 'Page Title')

@push('styles')  // ❌ WRONG - This is before @section('content')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush

@section('content')
<!-- Page content here -->
@endsection
```

### Correct Code

```php
@extends('admin.layouts.app')

@section('title', 'Page Title')

@section('content')
<!-- Page content here -->
@endsection

@push('styles')  // ✅ CORRECT - After @section('content')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // JavaScript code here
</script>
@endpush
```

### Best Practice

1. **Always place @push after @section:** Put `@push('styles')` and `@push('scripts')` AFTER your `@section('content')` ends
2. **Follow working examples:** Check how existing working pages like `brands/create.blade.php` are structured
3. **Keep styles and scripts together:** Group all `@push` directives at the end of the file, after all content sections

---

## Tabbed Interface Pages

### Problem

When creating admin pages with tabs (like API Keys & Integrations with API Keys and Webhooks tabs), accessing a tab directly via URL (e.g., `/admin/api-keys?tab=webhooks`) may result in missing CSS/styles if the controller renders a partial view instead of the main page view.

**Example Issue:**
- Controller returns a separate partial view for webhooks: `return view('admin.settings.api-keys.webhooks', [...])`
- The partial view doesn't extend the admin layout
- Result: Page renders without proper styling

### Solution

Always render tabbed pages through the main index view that extends the admin layout.

#### Correct Controller Implementation

```php
public function index(Request $request)
{
    $tab = $request->get('tab', 'api-keys');
    
    if ($tab === 'webhooks') {
        return $this->webhooksIndex(); // Should return main index view
    }
    
    return $this->apiKeysIndex();
}

protected function webhooksIndex()
{
    $webhooks = Webhook::orderBy('created_at', 'desc')->paginate(10);
    $events = Webhook::getEvents();
    $apiKeys = ApiKey::orderBy('created_at', 'desc')->paginate(10);
    $types = ApiKey::getTypes();
    
    // Always return the main index view with all variables
    return view('admin.settings.api-keys.index', [
        'apiKeys' => $apiKeys,
        'webhooks' => $webhooks,
        'types' => $types,
        'events' => $events,
        'activeTab' => 'webhooks',
    ]);
}
```

#### View Implementation

```php
@extends('admin.layouts.app')

@section('title', 'API Keys & Integrations')

@section('content')
@php
    $activeTab = $activeTab ?? 'api-keys';
    $apiKeys = $apiKeys ?? collect();
    $webhooks = $webhooks ?? collect();
    $types = $types ?? [];
    $events = $events ?? [];
@endphp

<!-- Tab Content -->
<div class="tab-content">
    @if($activeTab === 'api-keys')
        <!-- API Keys Tab Content -->
    @else
        <!-- Webhooks Tab Content -->
    @endif
</div>
@endsection
```

### Key Points

1. **Always use main layout view:** Don't return partial views that don't extend the admin layout
2. **Pass all required variables:** Ensure all variables needed by the view are passed, even if empty
3. **Use default values:** Use `@php` block to set default values for variables that may not be passed
4. **Single entry point:** Use query parameter (`?tab=xyz`) to switch between tabs, all through the same controller method

---

*Last updated: March 2026*

---

## Bootstrap Modal Popup

Use Bootstrap modal popups to show messages, confirmations, or information without requiring a page reload. This provides better UX than browser alerts or redirecting to error pages.

### Common Use Cases

- Show access denied messages instead of 403 error pages
- Display success/error messages after form submissions
- Confirm destructive actions (delete, etc.)
- Show information modals with detailed content

### Why Use Modal?
- Better user experience - no page reload or error page
- Users understand the message clearly
- Can easily dismiss and continue using the app

### Implementation Steps

#### 1. Check User Role in Controller (Optional)

If you want to block the actual action, add check in the controller methods:

```php
public function store(Request $request)
{
    $user = auth()->user();
    
    // Check if user has permission
    if ($user->role === 'staff') {
        abort(403, 'Unauthorized access.');
    }
    // ... rest of the code
}
```

#### 2. Show Modal via Button/Link Click

Conditionally show either a link or a button that triggers a modal:

```html
@if(auth()->user()->role !== 'staff')
<a href="{{ route('admin.staffs.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> Add New Staff
</a>
@else
<button type="button" class="btn btn-primary" onclick="showAccessDenied()">
    <i class="bi bi-plus-lg me-1"></i> Add New Staff
</button>
@endif
```

#### 3. Add JavaScript Function to Show Modal

Add this JavaScript in the @push('scripts') section:

```html
@push('scripts')
<script>
    // Show access denied modal for staff users
    function showAccessDenied() {
        var modal = new bootstrap.Modal(document.getElementById('accessDeniedModal'));
        modal.show();
    }
</script>
@endpush
```

#### 4. Add Modal HTML

Add the modal HTML at the end of the view file (before @endsection):

```html
<!-- Access Denied Modal -->
<div class="modal fade" id="accessDeniedModal" tabindex="-1" aria-labelledby="accessDeniedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accessDeniedModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>Access Denied
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-shield-lock text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">Staff members cannot create other staff members.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
```

### Key Points

1. **Use Bootstrap modal** - The admin panel already uses Bootstrap modals, so this integrates seamlessly
2. **Use JavaScript function** - Call a function like `showModal('modalId')` from onclick handler
3. **Unique modal ID** - Use a unique ID that doesn't conflict with other modals on the page
4. **User-friendly message** - Include icon and clear message explaining what's happening
5. **Reusable** - The same pattern can be used for any type of modal (info, warning, error, confirmation)

---

*Last updated: March 2026*
