@extends('admin.layouts.app')

@section('title', 'Blog Tags')

@section('content')
<div class="content-area">
    <div class="container-fluid pt-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Blog Tags</h4>
            <a href="{{ route('admin.blog-tags.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add New Tag
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase">Total Tags</div>
                        <div class="h4 mb-0 text-primary">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase">Active</div>
                        <div class="h4 mb-0 text-success">{{ $stats['active'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase">Inactive</div>
                        <div class="h4 mb-0 text-secondary">{{ $stats['inactive'] ?? 0 }}</div>
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
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <label class="form-label small text-muted">Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" id="liveSearch" class="form-control" 
                                       placeholder="Search tags..." value="{{ request('search') }}">
                                <span class="input-group-text" id="searchSpinner" style="display: none;">
                                    <div class="spinner-border spinner-border-sm"></div>
                                </span>
                            </div>
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
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-sm btn-outline-secondary">
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
                        <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </div>
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
                                <th>Tag</th>
                                <th style="width: 100px;">Blogs</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @include('admin.blog-tags.partials.table-rows')
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($tags->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        Showing {{ $tags->firstItem() }} - {{ $tags->lastItem() }} of {{ $tags->total() }} tags
                    </div>
                    <div>
                        {{ $tags->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Form -->
<form id="bulkActionForm" method="POST" action="{{ route('admin.blog-tags.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <input type="hidden" name="ids" id="bulkIdsInput">
</form>
@endsection

@push('scripts')
<script>
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
    document.getElementById('filterStatus').addEventListener('change', function() {
        performLiveSearch(searchInput.value.trim());
    });

    // Live search function
    function performLiveSearch(searchTerm) {
        const params = new URLSearchParams();
        
        if (searchTerm) params.set('search', searchTerm);
        
        // Add filter values
        const status = document.getElementById('filterStatus').value;
        if (status) params.set('status', status);
        
        // Keep existing sort and per_page
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('per_page')) params.set('per_page', urlParams.get('per_page'));
        
        // AJAX request
        fetch(`{{ route('admin.blog-tags.index') }}?${params.toString()}`, {
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
        })
        .catch(error => {
            searchSpinner.style.display = 'none';
            console.error('Search error:', error);
        });
    }

    // Bulk selection
    let selectedItems = new Set();

    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            if (this.checked) {
                selectedItems.add(parseInt(checkbox.value));
            } else {
                selectedItems.delete(parseInt(checkbox.value));
            }
        });
        updateBulkActions();
    });

    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedItems.add(parseInt(this.value));
            } else {
                selectedItems.delete(parseInt(this.value));
            }
            updateBulkActions();
        });
    });

    function updateBulkActions() {
        const count = selectedItems.size;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('bulkActionsBar').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        const totalCheckboxes = document.querySelectorAll('.item-checkbox').length;
        const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked').length;
        document.getElementById('selectAllCheckbox').checked = totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes;
    }

    function clearSelection() {
        selectedItems.clear();
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('selectAllCheckbox').checked = false;
        updateBulkActions();
    }

    function bulkAction(action) {
        if (selectedItems.size === 0) {
            alert('Please select at least one tag.');
            return;
        }
        
        if (!confirm(`Are you sure you want to ${action} ${selectedItems.size} tag(s)?`)) return;
        
        document.getElementById('bulkActionInput').value = action;
        document.getElementById('bulkIdsInput').value = JSON.stringify(Array.from(selectedItems));
        document.getElementById('bulkActionForm').submit();
    }

    // Toggle status via AJAX
    function toggleStatus(url, id) {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update the badge
                const badge = document.querySelector(`.status-badge[data-id="${id}"]`);
                if (badge) {
                    badge.outerHTML = data.badge;
                    // Update the data-id for new element
                    document.querySelector(`.status-badge`).setAttribute('data-id', id);
                }
                // Update the button icon
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    const btn = row.querySelector('button[onclick^="toggleStatus"]');
                    if (btn) {
                        const isActive = data.status === 'active';
                        btn.className = `btn btn-sm btn-outline-${isActive ? 'warning' : 'success'}`;
                        btn.innerHTML = `<i class="bi bi-${isActive ? 'eye-slash' : 'eye'}"></i>`;
                        btn.setAttribute('onclick', `toggleStatus('${url}', ${id})`);
                        btn.title = isActive ? 'Deactivate' : 'Activate';
                    }
                }
                // Show success message
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function showToast(message, type) {
        // Simple toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed top-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Delete tag
    function deleteTag(url) {
        if (!confirm('Are you sure you want to delete this tag?')) return;
        
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Tag deleted successfully', 'success');
                // Reload the page or fetch new data
                performLiveSearch(searchInput.value.trim());
            } else {
                showToast(data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
</script>
@endpush
