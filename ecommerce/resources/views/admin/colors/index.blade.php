@extends('admin.layouts.app')

@section('title', 'Colors')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-palette me-2"></i>Colors</h4>
        <p class="text-muted mb-0">Manage product colors for variations</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.colors.export') }}" class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i> Export
        </a>
        <a href="{{ route('admin.colors.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Color
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stat-card-row mb-4">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon"><i class="bi bi-palette"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total</span>
            <span class="stat-card-value" id="statTotal" data-stat="total">{{ $stats['total'] }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-success">
        <div class="stat-card-icon"><i class="bi bi-check-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Active</span>
            <span class="stat-card-value" id="statActive" data-stat="active">{{ $stats['active'] }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-danger">
        <div class="stat-card-icon"><i class="bi bi-x-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Inactive</span>
            <span class="stat-card-value" id="statInactive" data-stat="inactive">{{ $stats['inactive'] }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-info">
        <div class="stat-card-icon"><i class="bi bi-box"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">In Use</span>
            <span class="stat-card-value" id="statProducts" data-stat="products">{{ $stats['products'] }}</span>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" 
                               placeholder="Name, Code..." value="{{ request('search') }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm"></div>
                        </span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" id="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <label class="form-label small text-muted">Per Page</label>
                    <select name="per_page" id="filterPerPage" class="form-select form-select-sm">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <a href="{{ route('admin.colors.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions Bar -->
<div class="card border-0 shadow-sm mb-3" id="bulkActionsBar" style="display: none;">
    <div class="card-body py-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted"><span id="selectedCount">0</span> selected</span>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="clearSelection()">
                    Clear Selection
                </button>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('activate')">
                    <i class="bi bi-check-circle me-1"></i> Activate
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('deactivate')">
                    <i class="bi bi-x-circle me-1"></i> Deactivate
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Colors Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th width="60">Color</th>
                        <th>
                            <a href="{{ route('admin.colors.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc', 'per_page' => request('per_page')])) }}" class="text-decoration-none text-dark">
                                Name <i class="bi bi-arrow-down-up"></i>
                            </a>
                        </th>
                        <th>Code</th>
                        <th>Hex Code</th>
                        <th>
                            <a href="{{ route('admin.colors.index', array_merge(request()->all(), ['sort' => 'display_order', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc', 'per_page' => request('per_page')])) }}" class="text-decoration-none text-dark">
                                Order <i class="bi bi-arrow-down-up"></i>
                            </a>
                        </th>
                        <th>Products</th>
                        <th>Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($colors as $color)
                    @php
                        $search = request('search');
                        $isMatch = $search && (
                            stripos($color->name, $search) !== false || 
                            stripos($color->code, $search) !== false
                        );
                    @endphp
                    <tr data-id="{{ $color->id }}" class="{{ $isMatch ? 'table-warning' : '' }}">
                        <td>
                            <input type="checkbox" class="form-check-input item-checkbox" value="{{ $color->id }}">
                        </td>
                        <td>
                            <span class="color-swatch" style="background-color: {{ $color->hex_code }};" title="{{ $color->name }}"></span>
                        </td>
                        <td>
                            <a href="{{ route('admin.colors.edit', $color->id) }}" class="text-decoration-none fw-medium">
                                {{ $color->name }}
                            </a>
                            @if($color->description)
                            <br><small class="text-muted">{{ Str::limit($color->description, 40) }}</small>
                            @endif
                        </td>
                        <td><code>{{ $color->code }}</code></td>
                        <td>
                            <span class="badge font-monospace" style="background-color: {{ $color->hex_code }}; color: {{ $color->contrast_color }};">
                                {{ $color->hex_code }}
                            </span>
                        </td>
                        <td>{{ $color->display_order }}</td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $color->products_count }} products</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm {{ $color->is_active ? 'btn-success' : 'btn-outline-secondary' }}" 
                                    onclick="toggleStatus({{ $color->id }})" title="Toggle Status">
                                <i class="bi {{ $color->is_active ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                {{ $color->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.colors.edit', $color->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteItem({{ $color->id }})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-palette text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No colors found</p>
                            <a href="{{ route('admin.colors.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-lg me-1"></i> Add First Color
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($colors->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
            Showing {{ $colors->firstItem() }} to {{ $colors->lastItem() }} of {{ $colors->total() }} colors
        </div>
        <div id="pagination">
            {{ $colors->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Bulk Action Form -->
<form id="bulkActionForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <input type="hidden" name="ids" id="bulkIdsInput">
</form>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .color-swatch {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-block;
        vertical-align: middle;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #ddd;
    }
    
    /* Force Bootstrap Icons to display - SAME AS REFERENCE PAGE */
    .stat-card-icon i,
    .stat-card-icon i::before,
    .bi::before {
        display: inline-block !important;
    }
    
    /* Override icon colors for stat cards */
    .stat-card-primary .stat-card-icon i::before { color: #0d6efd !important; }
    .stat-card-success .stat-card-icon i::before { color: #198754 !important; }
    .stat-card-info .stat-card-icon i::before { color: #0dcaf0 !important; }
    .stat-card-warning .stat-card-icon i::before { color: #ffc107 !important; }
    .stat-card-danger .stat-card-icon i::before { color: #dc3545 !important; }
    .stat-card-secondary .stat-card-icon i::before { color: #6c757d !important; }
    
    /* Make the whole icon colored */
    .stat-card-icon i { color: inherit !important; }
</style>
@endpush

@push('scripts')
<script>
    let selectedItems = new Set();

    // Live search
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const searchSpinner = document.getElementById('searchSpinner');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();
        
        searchSpinner.style.display = 'block';
        
        searchTimeout = setTimeout(() => {
            performLiveSearch(searchTerm);
        }, 300);
    });

    // Filter dropdowns
    ['filterStatus', 'filterPerPage'].forEach(id => {
        const select = document.getElementById(id);
        if (select) {
            select.addEventListener('change', function() {
                performLiveSearch(searchInput.value.trim());
            });
        }
    });

    function performLiveSearch(searchTerm) {
        const params = new URLSearchParams();
        
        if (searchTerm) params.set('search', searchTerm);
        
        const status = document.getElementById('filterStatus').value;
        if (status) params.set('status', status);
        
        const perPage = document.getElementById('filterPerPage').value;
        if (perPage) params.set('per_page', perPage);
        
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('sort')) params.set('sort', urlParams.get('sort'));
        if (urlParams.get('direction')) params.set('direction', urlParams.get('direction'));
        
        fetch(`{{ route('admin.colors.index') }}?${params.toString()}&ajax=1`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            searchSpinner.style.display = 'none';
            
            if (data.html) {
                document.querySelector('#tableBody').innerHTML = data.html;
                
                if (data.stats) {
                    updateStats(data.stats);
                }
                
                if (data.pagination) {
                    document.querySelector('#pagination').innerHTML = data.pagination;
                }
                
                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
            }
        });
    }

    function updateStats(stats) {
        document.querySelector('[data-stat="total"]').textContent = stats.total;
        document.querySelector('[data-stat="active"]').textContent = stats.active;
        document.querySelector('[data-stat="inactive"]').textContent = stats.inactive;
        document.querySelector('[data-stat="products"]').textContent = stats.products;
    }

    // Select all
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            if (this.checked) {
                selectedItems.add(parseInt(cb.value));
            } else {
                selectedItems.delete(parseInt(cb.value));
            }
        });
        updateBulkActions();
    });

    // Individual checkbox
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-checkbox')) {
            const id = parseInt(e.target.value);
            if (e.target.checked) {
                selectedItems.add(id);
            } else {
                selectedItems.delete(id);
            }
            updateBulkActions();
        }
    });

    function updateBulkActions() {
        const count = selectedItems.size;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('bulkActionsBar').style.display = count > 0 ? 'block' : 'none';
    }

    function clearSelection() {
        selectedItems.clear();
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkActions();
    }

    function bulkAction(action) {
        if (selectedItems.size === 0) {
            alert('Please select at least one color.');
            return;
        }
        
        if (!confirm(`Are you sure you want to ${action} ${selectedItems.size} color(s)?`)) return;
        
        document.getElementById('bulkActionInput').value = action;
        document.getElementById('bulkIdsInput').value = JSON.stringify(Array.from(selectedItems));
        document.getElementById('bulkActionForm').action = '{{ route('admin.colors.bulk-action') }}';
        document.getElementById('bulkActionForm').submit();
    }

    function toggleStatus(id) {
        fetch(`{{ route('admin.colors.toggle-status', ['color' => 'ID']) }}`.replace('ID', id), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function deleteItem(id) {
        if (!confirm('Are you sure you want to delete this color?')) return;
        
        const form = document.getElementById('deleteForm');
        form.action = `{{ route('admin.colors.destroy', ['color' => 'ID']) }}`.replace('ID', id);
        form.submit();
    }
</script>
@endpush
