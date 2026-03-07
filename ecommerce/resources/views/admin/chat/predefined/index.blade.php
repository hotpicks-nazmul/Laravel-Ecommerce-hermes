@extends('admin.layouts.app')

@section('title', 'Predefined Messages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Predefined Messages</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Message
    </button>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Search messages..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <label class="form-label small text-muted">Category</label>
                    <select name="category" id="filterCategory" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" id="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <a href="{{ route('admin.chat.predefined.index') }}" class="btn btn-sm btn-outline-secondary w-100">
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
                        <th>Title</th>
                        <th>Message</th>
                        <th>Category</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $index => $message)
                    <tr>
                        <td>{{ $messages->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-medium">{{ $message->title }}</div>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 300px;" title="{{ $message->message }}">
                                {{ $message->message }}
                            </div>
                        </td>
                        <td>
                            @if($message->category)
                                <span class="badge bg-info">{{ $message->category }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                    {{ $message->is_active ? 'checked' : '' }} 
                                    onchange="toggleStatus({{ $message->id }})">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal"
                                    onclick="editMessage({{ $message->id }})"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" 
                                    onclick="deleteMessage({{ $message->id }})"
                                    title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-chat-text text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-2 mt-2">No predefined messages found</p>
                            <button class="btn btn-sm btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="bi bi-plus-lg me-1"></i> Add First Message
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($messages->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $messages->firstItem() }} - {{ $messages->lastItem() }} of {{ $messages->total() }} messages
            </div>
            <div>
                {{ $messages->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Predefined Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.chat.predefined.store') }}" id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g., Greeting">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="3" required placeholder="Type your quick reply message..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="e.g., General, Orders, Shipping">
                        <div class="form-text">Optional grouping for messages</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="createIsActive" value="1" checked>
                            <label class="form-check-label" for="createIsActive">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Predefined Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" id="editMessage" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" id="editCategory" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="editSortOrder" class="form-control" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" value="1">
                            <label class="form-check-label" for="editIsActive">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    // Form submission using AJAX for better UX
    document.getElementById('createForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';
        
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                return response.json().catch(() => {
                    throw new Error('Server error. Please try again.');
                });
            }
        })
        .then(data => {
            if (data && data.message) {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            alert(error.message || 'An error occurred. Please try again.');
        });
    });

    // Edit form submission using AJAX
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        // Add _method field for PUT request since HTML forms don't support PUT directly
        formData.append('_method', 'PUT');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                return response.json().catch(() => {
                    throw new Error('Server error. Please try again.');
                });
            }
        })
        .then(data => {
            if (data && data.message) {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            alert(error.message || 'An error occurred. Please try again.');
        });
    });
    // Live search and filters
    const searchInput = document.getElementById('liveSearch');
    const filterForm = document.getElementById('filterForm');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterForm.submit();
        }, 300);
    });

    document.getElementById('filterCategory').addEventListener('change', () => filterForm.submit());
    document.getElementById('filterStatus').addEventListener('change', () => filterForm.submit());

    // Toggle status
    function toggleStatus(id) {
        fetch(`{{ route('admin.chat.predefined.index') }}/toggle/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
            }
        })
        .catch(err => console.error(err));
    }

    // Edit message
    function editMessage(id) {
        fetch(`{{ route('admin.chat.predefined.index') }}/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('editTitle').value = data.title;
            document.getElementById('editMessage').value = data.message;
            document.getElementById('editCategory').value = data.category || '';
            document.getElementById('editSortOrder').value = data.sort_order || 0;
            document.getElementById('editIsActive').checked = data.is_active;
            document.getElementById('editForm').action = `{{ route('admin.chat.predefined.index') }}/${id}`;
        })
        .catch(err => console.error(err));
    }

    // Delete message
    function deleteMessage(id) {
        if (confirm('Are you sure you want to delete this message?')) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('admin.chat.predefined.index') }}/${id}`;
            form.submit();
        }
    }

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endpush
