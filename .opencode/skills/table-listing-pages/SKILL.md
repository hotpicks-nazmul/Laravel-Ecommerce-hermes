---
name: table-listing-pages
description: Standard table structure for admin index pages with responsive wrapper, hover effects, select all checkbox, and pagination.
---

# Table Listing Pages

**Standard Table Structure:**

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Page Title</h4>
    <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Item
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="form-check-input" id="selectAllCheckbox"></th>
                        <th>Column Header</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Table rows -->
                </tbody>
            </table>
        </div>
        
        @if($items->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $items->firstItem() }} - {{ $items->lastItem() }} of {{ $items->total() }} items
            </div>
            <div>{{ $items->appends(request()->query())->links() }}</div>
        </div>
        @endif
    </div>
</div>

**Key Elements:**
- Table wrapper: `<div class="table-responsive">`
- Table: `<table class="table table-hover align-middle mb-0">`
- Checkbox column: `style="width: 40px;"`
- Actions column: `style="width: 120px;"`
- Card body: `<div class="card-body p-0">`
- Pagination: Inside `card-body`, wrapped in `card-footer`

**Empty State:**

<tr>
    <td colspan="8" class="text-center py-5">
        <i class="bi bi-folder text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mb-2 mt-2">No items found</p>
        <a href="{{ route('admin.items.create') }}" class="btn btn-sm btn-primary mt-1">
            <i class="bi bi-plus-lg me-1"></i> Add First Item
        </a>
    </td>
</tr>