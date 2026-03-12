@extends('admin.layouts.app')

@section('title', 'All Warehouses')

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4" id="statsCards">
    <div class="col-md-4 col-sm-4 col-12 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Warehouses</div>
                <div class="h4 mb-0 text-primary">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-12 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Active</div>
                <div class="h4 mb-0 text-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-12 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Inactive</div>
                <div class="h4 mb-0 text-secondary">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-building me-2"></i>All Warehouses</h4>
    <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Warehouse
    </a>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Search -->
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Name, Code, City, Phone..." value="{{ request('search') }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                    </div>
                </div>
                
                <!-- City Filter -->
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">City</label>
                    <select name="city" id="filterCity" class="form-select form-select-sm">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Status Filter -->
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" id="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <!-- Reset Button -->
                <div class="col-lg-2 col-md-2 col-sm-4">
                    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-sm btn-outline-secondary w-100" id="resetFilters">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Warehouses Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Warehouse</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @include('admin.warehouses.partials.table-rows', ['warehouses' => $warehouses])
                </tbody>
            </table>
        </div>
        
        <!-- Pagination & Per Page -->
        @if(isset($warehouses) && method_exists($warehouses, 'hasPages') && $warehouses->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Show:</span>
                <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 || !request('per_page') ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select class="text-muted small">per page</span>
>
                <span            </div>
            <div id="paginationLinks">
                {{ $warehouses->appends(request()->query())->links() }}
            </div>
            <div class="text-muted small">
                Showing {{ $warehouses->firstItem() }} - {{ $warehouses->lastItem() }} of {{ $warehouses->total() }} warehouses
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
let searchTimeout;

// Live search with debouncing
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
const filterSelects = ['filterCity', 'filterStatus'];
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
    const city = document.getElementById('filterCity').value;
    if (city) params.set('city', city);
    
    const status = document.getElementById('filterStatus').value;
    if (status) params.set('status', status);
    
    // Keep existing sort and per_page
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('sort')) params.set('sort', urlParams.get('sort'));
    if (urlParams.get('direction')) params.set('direction', urlParams.get('direction'));
    if (urlParams.get('per_page')) params.set('per_page', urlParams.get('per_page'));
    
    // AJAX request
    fetch(`{{ route('admin.warehouses.index') }}?${params.toString()}`, {
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
            
            // Update stats
            if (data.stats) {
                updateStats(data.stats);
            }
            
            // Update pagination if available
            if (data.pagination) {
                document.getElementById('paginationLinks').innerHTML = data.pagination;
            }
            
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

// Update statistics cards
function updateStats(stats) {
    const statCards = document.querySelectorAll('#statsCards .h4');
    if (statCards[0]) statCards[0].textContent = stats.total ?? 0;
    if (statCards[1]) statCards[1].textContent = stats.active ?? 0;
    if (statCards[2]) statCards[2].textContent = stats.inactive ?? 0;
}

// Change per page
function changePerPage(perPage) {
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', perPage);
    window.location.href = `${window.location.pathname}?${params.toString()}`;
}
</script>
@endpush
