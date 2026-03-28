@extends('admin.layouts.app')

@section('title', 'Seller Commission')

@section('content')
<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Sellers</div>
                <div class="h4 mb-0 text-primary">{{ $stats['total_sellers'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Default Commission</div>
                <div class="h4 mb-0 text-info">{{ $defaultCommission ?? 0 }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Avg. Commission</div>
                <div class="h4 mb-0 text-success">{{ $stats['avg_commission'] ?? 0 }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Revenue</div>
                <div class="h4 mb-0 text-warning">৳{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Seller Commission</h4>
    <a href="{{ route('admin.sellers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Sellers
    </a>
</div>

<!-- Set Default Commission Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Default Commission Settings</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.sellers.commission.update') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-lg-4 col-md-6">
                <label for="default_commission" class="form-label">Set Default Commission Rate (%) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="commission_rate" id="default_commission" 
                           class="form-control @error('commission_rate') is-invalid @enderror" 
                           value="{{ $defaultCommission ?? 10 }}"
                           min="0" max="100" step="0.01" required>
                    <span class="input-group-text"><i class="bi bi-percent"></i></span>
                    @error('commission_rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-text">This will be applied to all sellers by default</div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Apply to All Sellers
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Search Input -->
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" 
                               placeholder="Seller name, shop name, email..." value="{{ request('search') }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm"></div>
                        </span>
                    </div>
                </div>
                
                <!-- Commission Range Filter -->
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Commission Range</label>
                    <select name="commission_range" id="filterCommission" class="form-select form-select-sm">
                        <option value="">All Ranges</option>
                        <option value="0-5" {{ request('commission_range') === '0-5' ? 'selected' : '' }}>0% - 5%</option>
                        <option value="5-10" {{ request('commission_range') === '5-10' ? 'selected' : '' }}>5% - 10%</option>
                        <option value="10-15" {{ request('commission_range') === '10-15' ? 'selected' : '' }}>10% - 15%</option>
                        <option value="15-20" {{ request('commission_range') === '15-20' ? 'selected' : '' }}>15% - 20%</option>
                        <option value="20+" {{ request('commission_range') === '20+' ? 'selected' : '' }}>20%+</option>
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
                <div class="col-lg-3 col-md-2 col-sm-6">
                    <a href="{{ route('admin.sellers.commission') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
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
                        <th style="width: 50px;">#</th>
                        <th>Seller</th>
                        <th>Shop Name</th>
                        <th>Email</th>
                        <th>Commission Rate</th>
                        <th>Wallet Balance</th>
                        <th>Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($sellers as $key => $seller)
                    <tr>
                        <td>{{ $sellers->firstItem() + $key }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-primary text-white me-2">
                                    {{ strtoupper(substr($seller->name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $seller->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $seller->shop_name ?? '-' }}</td>
                        <td>{{ $seller->email }}</td>
                        <td>
                            <span class="badge bg-info">{{ $seller->commission_rate ?? $defaultCommission }}%</span>
                        </td>
                        <td>৳{{ number_format($seller->wallet_balance ?? 0, 2) }}</td>
                        <td>
                            @if($seller->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCommissionModal"
                                    data-seller-id="{{ $seller->id }}"
                                    data-seller-name="{{ $seller->name }}"
                                    data-shop-name="{{ $seller->shop_name }}"
                                    data-commission-rate="{{ $seller->commission_rate ?? $defaultCommission }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-2 mt-2">No sellers found</p>
                            <a href="{{ route('admin.sellers.create') }}" class="btn btn-sm btn-primary mt-1">
                                <i class="bi bi-plus-lg me-1"></i> Add First Seller
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination inside card-body -->
        @if($sellers->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $sellers->firstItem() }} - {{ $sellers->lastItem() }} of {{ $sellers->total() }} sellers
            </div>
            <div>
                {{ $sellers->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Edit Commission Modal -->
<div class="modal fade" id="editCommissionModal" tabindex="-1" aria-labelledby="editCommissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCommissionModalLabel">Edit Commission Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.sellers.commission.update') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="seller_id" id="modalSellerId">
                    
                    <div class="mb-3">
                        <label class="form-label">Seller</label>
                        <input type="text" class="form-control" id="modalSellerName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="modalCommissionRate" class="form-label">Commission Rate (%) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="commission_rate" id="modalCommissionRate" 
                                   class="form-control" min="0" max="100" step="0.01" required>
                            <span class="input-group-text"><i class="bi bi-percent"></i></span>
                        </div>
                        <div class="form-text">Enter a value between 0 and 100</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Update Commission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Modal population
    document.getElementById('editCommissionModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const sellerId = button.getAttribute('data-seller-id');
        const sellerName = button.getAttribute('data-seller-name');
        const shopName = button.getAttribute('data-shop-name');
        const commissionRate = button.getAttribute('data-commission-rate');
        
        document.getElementById('modalSellerId').value = sellerId;
        document.getElementById('modalSellerName').value = shopName ? `${sellerName} (${shopName})` : sellerName;
        document.getElementById('modalCommissionRate').value = commissionRate;
    });

    // Debounced live search
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const searchSpinner = document.getElementById('searchSpinner');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();
        
        // Show spinner
        searchSpinner.style.display = 'block';
        
        // Debounce - wait 400ms after user stops typing
        searchTimeout = setTimeout(() => {
            performLiveSearch(searchTerm);
        }, 400);
    });

    // Filter dropdowns trigger search on change
    const filterSelects = ['filterCommission', 'filterStatus'];
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
        const commission = document.getElementById('filterCommission').value;
        if (commission) params.set('commission_range', commission);
        
        const status = document.getElementById('filterStatus').value;
        if (status) params.set('status', status);
        
        // Keep existing sort and per_page
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('per_page')) params.set('per_page', urlParams.get('per_page'));
        
        // Redirect with filters (non-AJAX for simplicity)
        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.location.href = newUrl;
    }
</script>
@endpush
