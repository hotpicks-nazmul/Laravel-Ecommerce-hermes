@extends('admin.layouts.app')

@section('content')
<div class="content-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Customer Segmentation</h4>
                    <a href="{{ route('admin.customers.segmentation.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Create Segment
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Total Segments</p>
                                        <h4 class="mb-0">{{ $stats['total_segments'] }}</h4>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 rounded p-3">
                                        <i class="bi bi-diagram-3 text-primary" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Active Segments</p>
                                        <h4 class="mb-0">{{ $stats['active_segments'] }}</h4>
                                    </div>
                                    <div class="bg-success bg-opacity-10 rounded p-3">
                                        <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Total Customers Segmented</p>
                                        <h4 class="mb-0">{{ number_format($stats['total_customers_segmented']) }}</h4>
                                    </div>
                                    <div class="bg-info bg-opacity-10 rounded p-3">
                                        <i class="bi bi-people text-info" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <form method="GET" id="filterForm">
                            <div class="row g-2 align-items-end">
                                <div class="col-lg-4 col-md-4 col-sm-6">
                                    <label class="form-label small text-muted">Search</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Search segments..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                    <label class="form-label small text-muted">Status</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">All Status</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-funnel me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.customers.segmentation.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Segments Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Segment Name</th>
                                        <th>Description</th>
                                        <th>Customers</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th style="width: 150px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($segments as $segment)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.customers.segmentation.show', $segment->id) }}" class="text-decoration-none fw-semibold">
                                                {{ $segment->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ Str::limit($segment->description, 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                <i class="bi bi-people me-1"></i>
                                                {{ number_format($segment->customer_count) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($segment->is_active)
                                            <span class="badge bg-success">Active</span>
                                            @else
                                            <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                {{ $segment->created_at->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-gear"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.customers.segmentation.show', $segment->id) }}">
                                                            <i class="bi bi-eye me-2"></i> View Customers
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.customers.segmentation.edit', $segment->id) }}">
                                                            <i class="bi bi-pencil me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.customers.segmentation.export', $segment->id) }}">
                                                            <i class="bi bi-download me-2"></i> Export
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('admin.customers.segmentation.toggle-status', $segment->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item {{ $segment->is_active ? 'text-warning' : 'text-success' }}">
                                                                <i class="bi {{ $segment->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-2"></i>
                                                                {{ $segment->is_active ? 'Deactivate' : 'Activate' }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.customers.segmentation.destroy', $segment->id) }}" method="POST" class="delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this segment?')">
                                                                <i class="bi bi-trash me-2"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="bi bi-diagram-3 text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mb-2 mt-2">No segments found</p>
                                            <a href="{{ route('admin.customers.segmentation.create') }}" class="btn btn-sm btn-primary mt-1">
                                                <i class="bi bi-plus-lg me-1"></i> Create First Segment
                                            </a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($segments->hasPages())
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="text-muted small">
                                Showing {{ $segments->firstItem() }} - {{ $segments->lastItem() }} of {{ $segments->total() }} segments
                            </div>
                            <div>
                                {{ $segments->appends(request()->query())->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
