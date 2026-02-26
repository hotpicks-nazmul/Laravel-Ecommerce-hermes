@extends('admin.layouts.app')

@section('title', 'Stock Alerts')

@section('content')
@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
    .alert-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Critical (0)</div>
                <div class="h4 mb-0 text-danger">{{ $stats['critical'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Warning (1-5)</div>
                <div class="h4 mb-0 text-warning">{{ $stats['warning'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Notice (6-10)</div>
                <div class="h4 mb-0 text-info">{{ $stats['notice'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" 
                               placeholder="Product name or SKU..." value="{{ request('search') }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm"></div>
                        </span>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Alert Level</label>
                    <select name="alert_type" id="filterAlertType" class="form-select form-select-sm">
                        <option value="">All Low Stock</option>
                        <option value="critical" {{ request('alert_type') === 'critical' ? 'selected' : '' }}>Critical (0)</option>
                        <option value="warning" {{ request('alert_type') === 'warning' ? 'selected' : '' }}>Warning (1-5)</option>
                        <option value="notice" {{ request('alert_type') === 'notice' ? 'selected' : '' }}>Notice (6-10)</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Sort</label>
                    <select name="sort" id="filterSort" class="form-select form-select-sm">
                        <option value="quantity" {{ request('sort', 'quantity') === 'quantity' ? 'selected' : '' }}>Stock Level</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="low_stock_threshold" {{ request('sort') === 'low_stock_threshold' ? 'selected' : '' }}>Threshold</option>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <a href="{{ route('admin.inventory.stock-alerts') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Alert Products Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Low Stock Products</h6>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-boxes me-1"></i> View All Inventory
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th class="text-center">Current Stock</th>
                        <th class="text-center">Threshold</th>
                        <th class="text-center">Alert Level</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @include('admin.inventory.partials.alert-table-rows', ['products' => $products])
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} entries
            </div>
            <div>
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Quick Restock Modal -->
<div class="modal fade" id="restockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Restock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="restockForm">
                <div class="modal-body">
                    <input type="hidden" id="restockProductId" name="product_id">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" id="restockProductName" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" id="restockCurrentStock" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity to Add <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="restockQuantity" class="form-control" min="1" value="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Update Threshold</label>
                        <input type="number" name="low_stock_threshold" id="restockThreshold" class="form-control" min="0">
                        <small class="text-muted">Leave empty to keep current threshold</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g., Received shipment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Restock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const searchSpinner = document.getElementById('searchSpinner');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();
        searchSpinner.style.display = 'block';
        searchTimeout = setTimeout(() => performSearch(searchTerm), 300);
    });

    ['filterAlertType', 'filterSort'].forEach(id => {
        const select = document.getElementById(id);
        if (select) {
            select.addEventListener('change', () => performSearch(searchInput.value.trim()));
        }
    });

    function performSearch(searchTerm) {
        const params = new URLSearchParams();
        if (searchTerm) params.set('search', searchTerm);
        
        const alertType = document.getElementById('filterAlertType').value;
        if (alertType) params.set('alert_type', alertType);

        const sort = document.getElementById('filterSort').value;
        if (sort) params.set('sort', sort);

        fetch(`{{ route('admin.inventory.stock-alerts') }}?${params.toString()}&ajax=1`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            searchSpinner.style.display = 'none';
            if (data.html) {
                document.querySelector('#tableBody').innerHTML = data.html;
                window.history.pushState({}, '', `{{ route('admin.inventory.stock-alerts') }}?${params.toString()}`);
            }
        })
        .catch(() => { searchSpinner.style.display = 'none'; document.getElementById('filterForm').submit(); });
    }

    function showRestockModal(productId) {
        fetch(`{{ route('admin.inventory.product', ':id') }}`.replace(':id', productId))
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('restockProductId').value = data.product.id;
                    document.getElementById('restockProductName').value = data.product.name;
                    document.getElementById('restockCurrentStock').value = data.product.quantity;
                    document.getElementById('restockThreshold').value = data.product.low_stock_threshold;
                    document.getElementById('restockQuantity').value = 10;
                    new bootstrap.Modal(document.getElementById('restockModal')).show();
                }
            });
    }

    document.getElementById('restockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Handle threshold separately
        const threshold = document.getElementById('restockThreshold').value;
        if (threshold) {
            fetch('{{ route('admin.inventory.threshold') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: new FormData(document.getElementById('restockForm'))
            });
        }
        
        formData.set('adjustment_type', 'add');
        
        fetch('{{ route('admin.inventory.adjust') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('restockModal')).hide();
                toastr.success('Product restocked successfully');
                setTimeout(() => window.location.reload(), 500);
            }
        })
        .catch(err => toastr.error('Failed to restock product'));
    });
</script>
@endpush
@endsection
