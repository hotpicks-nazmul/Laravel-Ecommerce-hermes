@extends('admin.layouts.app')

@section('title', 'All Staffs')

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Staffs</div>
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
                <div class="text-muted small text-uppercase">Banned</div>
                <div class="h4 mb-0 text-danger" id="stat-banned">{{ $stats['banned'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">All Staffs</h4>
    <a href="{{ route('admin.staffs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Staff
    </a>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Search -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Name, email, designation..." value="{{ request('search') }}">
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
                        <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                    </select>
                </div>

                <!-- Warehouse -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Warehouse</label>
                    <select name="warehouse_id" id="filterWarehouse" class="form-select form-select-sm">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Per Page -->
                <div class="col-lg-1 col-md-2 col-sm-4">
                    <label class="form-label small text-muted">Per Page</label>
                    <select name="per_page" id="perPage" class="form-select form-select-sm">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="col-lg-1 col-md-2 col-sm-4">
                    <a href="{{ route('admin.staffs.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions -->
<div class="card border-0 shadow-sm mb-3" id="bulkActionsCard" style="display: none;">
    <div class="card-body py-2">
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small" id="selectedCount">0 selected</span>
            <select id="bulkActionSelect" class="form-select form-select-sm" style="width: auto;">
                <option value="">Bulk Actions</option>
                <option value="activate">Set Active</option>
                <option value="deactivate">Set Inactive</option>
                <option value="delete">Delete Selected</option>
            </select>
            <button type="button" id="applyBulkAction" class="btn btn-sm btn-primary">
                Apply
            </button>
        </div>
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
                        <th>Staff Member</th>
                        <th>Designation</th>
                        <th>Warehouse</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @include('admin.staffs.partials.table-rows')
                </tbody>
            </table>
        </div>

        <!-- Pagination inside card-body -->
        @if($staffs->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $staffs->firstItem() }} - {{ $staffs->lastItem() }} of {{ $staffs->total() }} staffs
            </div>
            <div>
                {{ $staffs->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Bulk Action Form -->
<form id="bulkActionForm" method="POST" action="{{ route('admin.staffs.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <input type="hidden" name="staff_ids" id="bulkIdsInput">
</form>
@endsection

@push('styles')
<style>
    .staff-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>
@endpush

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

            fetch('{{ route("admin.staffs.index") }}?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = data.html;

                // Update stats
                document.getElementById('stat-total').textContent = data.stats.total;
                document.getElementById('stat-active').textContent = data.stats.active;
                document.getElementById('stat-inactive').textContent = data.stats.inactive;
                document.getElementById('stat-banned').textContent = data.stats.banned;

                // Update pagination
                const paginationContainer = document.querySelector('.card-footer');
                if (paginationContainer && data.pagination) {
                    paginationContainer.innerHTML = `
                        <div class="text-muted small">
                            Showing {{ $staffs->firstItem() }} - {{ $staffs->lastItem() }} of {{ $staffs->total() }} staffs
                        </div>
                        <div>${data.pagination}</div>
                    `;
                }

                // Reinitialize checkbox listeners
                initCheckboxListeners();

                // Update URL
                const newUrl = '{{ route("admin.staffs.index") }}?' + params.toString();
                window.history.pushState({}, '', newUrl);

                searchSpinner.style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                searchSpinner.style.display = 'none';
            });
        }, 300);
    }

    // Search input listener
    searchInput.addEventListener('input', fetchResults);

    // Filter listeners
    document.getElementById('filterStatus').addEventListener('change', fetchResults);
    document.getElementById('filterWarehouse').addEventListener('change', fetchResults);
    document.getElementById('perPage').addEventListener('change', fetchResults);

    // Bulk Actions
    const selectedItems = new Set();

    function updateBulkActions() {
        const count = selectedItems.size;
        document.getElementById('selectedCount').textContent = count + ' selected';
        document.getElementById('bulkActionsCard').style.display = count > 0 ? 'block' : 'none';
    }

    function initCheckboxListeners() {
        // Select all
        document.getElementById('selectAllCheckbox').addEventListener('change', function() {
            const checkboxes = tableBody.querySelectorAll('.staff-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                if (this.checked) {
                    selectedItems.add(checkbox.value);
                } else {
                    selectedItems.delete(checkbox.value);
                }
            });
            updateBulkActions();
        });

        // Individual checkboxes
        tableBody.querySelectorAll('.staff-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    selectedItems.add(this.value);
                } else {
                    selectedItems.delete(this.value);
                }
                
                // Update select all checkbox
                const allCheckboxes = tableBody.querySelectorAll('.staff-checkbox');
                const checkedCount = tableBody.querySelectorAll('.staff-checkbox:checked').length;
                document.getElementById('selectAllCheckbox').checked = checkedCount === allCheckboxes.length;
                
                updateBulkActions();
            });
        });
    }

    // Bulk action apply
    document.getElementById('applyBulkAction').addEventListener('click', function() {
        const action = document.getElementById('bulkActionSelect').value;
        if (!action) {
            alert('Please select an action');
            return;
        }
        if (selectedItems.size === 0) {
            alert('Please select at least one staff member');
            return;
        }

        const confirmMessages = {
            'activate': 'Are you sure you want to activate the selected staff members?',
            'deactivate': 'Are you sure you want to deactivate the selected staff members?',
            'delete': 'Are you sure you want to delete the selected staff members? This action cannot be undone.'
        };

        if (!confirm(confirmMessages[action])) return;

        document.getElementById('bulkActionInput').value = action;
        document.getElementById('bulkIdsInput').value = JSON.stringify(Array.from(selectedItems));
        document.getElementById('bulkActionForm').submit();
    });

    // Initialize checkboxes on page load
    document.addEventListener('DOMContentLoaded', initCheckboxListeners);
</script>
@endpush
