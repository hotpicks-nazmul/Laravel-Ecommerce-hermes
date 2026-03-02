@extends('admin.layouts.app')

@section('title', 'Customer Groups')

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Groups</div>
                <div class="h4 mb-0 text-primary" id="stat-total">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Active</div>
                <div class="h4 mb-0 text-success" id="stat-active">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Inactive</div>
                <div class="h4 mb-0 text-secondary" id="stat-inactive">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">With Discount</div>
                <div class="h4 mb-0 text-info" id="stat-with-discount">{{ $stats['with_discount'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Customer Groups</h4>
    <a href="{{ route('admin.customers.groups.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Group
    </a>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Search -->
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Name, slug..." value="{{ request('search') }}">
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

                <!-- Per Page -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Per Page</label>
                    <select name="per_page" id="perPage" class="form-select form-select-sm">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <a href="{{ route('admin.customers.groups.index') }}" class="btn btn-outline-secondary btn-sm w-100">
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
                        <th>Group Name</th>
                        <th>Slug</th>
                        <th>Discount</th>
                        <th>Customers</th>
                        <th>Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @include('admin.customers.groups.partials.table-rows')
                </tbody>
            </table>
        </div>
        
        <!-- Pagination inside card-body -->
        @if($customerGroups->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $customerGroups->firstItem() }} - {{ $customerGroups->lastItem() }} of {{ $customerGroups->total() }} groups
            </div>
            <div>
                {{ $customerGroups->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live search with debounce
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const searchSpinner = document.getElementById('searchSpinner');
    const filterForm = document.getElementById('filterForm');
    const tableBody = document.getElementById('tableBody');

    function fetchResults() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchSpinner.style.display = 'block';
            
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            
            fetch('{{ route("admin.customers.groups.index") }}?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = data.html;
                document.getElementById('stat-total').textContent = data.stats.total;
                document.getElementById('stat-active').textContent = data.stats.active;
                document.getElementById('stat-inactive').textContent = data.stats.inactive;
                document.getElementById('stat-with-discount').textContent = data.stats.with_discount;
                
                // Update pagination
                const paginationContainer = document.querySelector('.card-footer');
                if (paginationContainer) {
                    paginationContainer.innerHTML = `
                        <div class="text-muted small">
                            Showing ${data.total > 0 ? '{{ $customerGroups->firstItem() }}' : 0} - ${data.total > 0 ? '{{ $customerGroups->lastItem() }}' : 0} of ${data.total} groups
                        </div>
                        <div>${data.pagination}</div>
                    `;
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                searchSpinner.style.display = 'none';
            });
        }, 300);
    }

    // Search input event
    if (searchInput) {
        searchInput.addEventListener('input', fetchResults);
    }

    // Filter change events
    document.getElementById('filterStatus')?.addEventListener('change', fetchResults);
    document.getElementById('perPage')?.addEventListener('change', () => {
        filterForm.submit();
    });

    // Delete confirmation
    function confirmDelete(formId) {
        if (confirm('Are you sure you want to delete this customer group?')) {
            document.getElementById(formId).submit();
        }
    }
</script>
@endpush
