@extends('admin.layouts.app')

@section('title', 'Warehouse Staffs')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Warehouse Staffs</h4>
    @if(auth()->user()->role !== 'staff')
    <a href="{{ route('admin.staffs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Staff
    </a>
    @else
    <button type="button" class="btn btn-primary" onclick="showAccessDenied()">
        <i class="bi bi-plus-lg me-1"></i> Add New Staff
    </button>
    @endif
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
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Name, email..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Warehouse -->
                <div class="col-lg-3 col-md-4">
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

                <!-- Reset -->
                <div class="col-lg-2 col-md-4">
                    <a href="{{ route('admin.staffs.warehouse') }}" class="btn btn-outline-secondary btn-sm w-100">
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
                        <th>Staff Member</th>
                        <th>Designation</th>
                        <th>Warehouse</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $staff)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php
                                        $avatarUrl = $staff->avatar;
                                        if ($avatarUrl && !str_starts_with($avatarUrl, '/storage/') && !str_starts_with($avatarUrl, 'http')) {
                                            $avatarUrl = '/storage/' . $avatarUrl;
                                        }
                                    @endphp
                                    @if($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $staff->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $staff->name }}</div>
                                        <div class="small text-muted">{{ $staff->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $staff->designation ?? 'N/A' }}</td>
                            <td>
                                @if($staff->warehouse)
                                    <span class="badge bg-info">{{ $staff->warehouse->name }}</span>
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($staff->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($staff->status === 'inactive')
                                    <span class="badge bg-secondary">Inactive</span>
                                @else
                                    <span class="badge bg-danger">Banned</span>
                                @endif
                            </td>
                            <td>{{ $staff->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.staffs.edit', $staff->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-2 mt-2">No warehouse staff found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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

<!-- Access Denied Modal -->
<div class="modal fade" id="accessDeniedModal" tabindex="-1" aria-labelledby="accessDeniedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accessDeniedModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>Access Denied
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-shield-lock text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">Staff members cannot create other staff members.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show access denied modal for staff users
    function showAccessDenied() {
        var modal = new bootstrap.Modal(document.getElementById('accessDeniedModal'));
        modal.show();
    }
</script>
@endpush
