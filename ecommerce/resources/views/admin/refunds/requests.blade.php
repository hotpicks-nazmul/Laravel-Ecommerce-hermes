@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Refund Requests</h4>
    <a href="{{ route('admin.refunds.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> All Refunds
    </a>
</div>

<!-- Statistics Cards -->
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
                <div class="text-muted small text-uppercase">Pending</div>
                <div class="h4 mb-0 text-warning">{{ $stats['pending'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Approved</div>
                <div class="h4 mb-0 text-info">{{ $stats['approved'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Rejected</div>
                <div class="h4 mb-0 text-danger">{{ $stats['rejected'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Processed</div>
                <div class="h4 mb-0 text-success">{{ $stats['processed'] ?? 0 }}</div>
            </div>
        </div>
    </div>
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
                        <input type="text" name="search" id="liveSearch" class="form-control" 
                               placeholder="Refund #, Order #, Customer..." value="{{ request('search') }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm"></div>
                        </span>
                    </div>
                </div>
                
                <!-- Reason Filter -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Reason</label>
                    <select name="reason" id="filterReason" class="form-select form-select-sm">
                        <option value="">All Reasons</option>
                        @foreach(App\Models\Refund::getReasonOptions() as $value => $label)
                            <option value="{{ $value }}" {{ request('reason') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Per Page -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Per Page</label>
                    <select name="per_page" id="filterPerPage" class="form-select form-select-sm">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                
                <!-- Reset Button -->
                <div class="col-lg-2 col-md-4 col-sm-8">
                    <a href="{{ route('admin.refunds.requests') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Reset
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
                        <th>Refund Details</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Reason</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @include('admin.refunds.partials.table-rows', ['refunds' => $refunds])
                </tbody>
            </table>
        </div>
        
        <!-- Pagination inside card-body -->
        @if($refunds->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $refunds->firstItem() }} - {{ $refunds->lastItem() }} of {{ $refunds->total() }} items
            </div>
            <div>
                {{ $refunds->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Debounced live search
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const searchSpinner = document.getElementById('searchSpinner');

    if (searchInput) {
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
    }

    // Filter dropdowns trigger search on change
    const filterSelects = ['filterReason', 'filterPerPage'];
    filterSelects.forEach(id => {
        const select = document.getElementById(id);
        if (select) {
            select.addEventListener('change', function() {
                performLiveSearch(searchInput ? searchInput.value.trim() : '');
            });
        }
    });

    // Live search function
    function performLiveSearch(searchTerm) {
        const params = new URLSearchParams();
        
        if (searchTerm) params.set('search', searchTerm);
        
        // Add filter values
        const reason = document.getElementById('filterReason')?.value;
        if (reason) params.set('reason', reason);
        
        const perPage = document.getElementById('filterPerPage')?.value;
        if (perPage) params.set('per_page', perPage);
        
        // Keep existing sort
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('sort')) params.set('sort', urlParams.get('sort'));
        if (urlParams.get('direction')) params.set('direction', urlParams.get('direction'));
        
        // AJAX request
        fetch(`{{ route('admin.refunds.requests') }}?${params.toString()}&ajax=1`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (searchSpinner) searchSpinner.style.display = 'none';
            
            if (data.html) {
                // Update table body
                document.querySelector('#tableBody').innerHTML = data.html;
                
                // Update pagination if needed
                if (data.pagination) {
                    const paginationContainer = document.querySelector('.card-footer div:last-child');
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                }
                
                // Update URL without reload
                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
            }
        })
        .catch(error => {
            if (searchSpinner) searchSpinner.style.display = 'none';
            console.error('Search error:', error);
            // Fallback to regular form submission
            document.getElementById('filterForm').submit();
        });
    }
</script>
@endpush
@endsection
