@extends('admin.layouts.app')

@section('title', 'Stock History')

@section('content')
@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Stock In</div>
                <div class="h4 mb-0 text-success">+{{ number_format($stats['total_in'] ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Stock Out</div>
                <div class="h4 mb-0 text-danger">{{ number_format($stats['total_out'] ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Adjustments</div>
                <div class="h4 mb-0 text-primary">{{ number_format($stats['adjustments'] ?? 0) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Product</label>
                    <select name="product_id" id="filterProduct" class="form-select form-select-sm select2">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Action Type</label>
                    <select name="action_type" id="filterActionType" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        <option value="stock_in" {{ request('action_type') === 'stock_in' ? 'selected' : '' }}>Stock In</option>
                        <option value="stock_out" {{ request('action_type') === 'stock_out' ? 'selected' : '' }}>Stock Out</option>
                        <option value="adjustment" {{ request('action_type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        <option value="order" {{ request('action_type') === 'order' ? 'selected' : '' }}>Order</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Date From</label>
                    <input type="date" name="date_from" id="filterDateFrom" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>

                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Date To</label>
                    <input type="date" name="date_to" id="filterDateTo" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('admin.inventory.stock-history') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- History Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Inventory History</h6>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-boxes me-1"></i> Back to Inventory
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Action</th>
                        <th class="text-center">Before</th>
                        <th class="text-center">Change</th>
                        <th class="text-center">After</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @include('admin.inventory.partials.history-table-rows', ['history' => $history])
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} of {{ $history->total() }} entries
            </div>
            <div>
                {{ $history->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            placeholder: 'Select product',
            allowClear: true
        });
    }

    // Auto-submit on filter change
    ['filterProduct', 'filterActionType', 'filterDateFrom', 'filterDateTo'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', () => document.getElementById('filterForm').submit());
        }
    });
</script>
@endpush
@endsection
