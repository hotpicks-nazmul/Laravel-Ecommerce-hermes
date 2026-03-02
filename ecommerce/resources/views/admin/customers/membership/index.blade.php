@extends('admin.layouts.app')

@section('content')
<div class="content-area">
    <div class="container-fluid pt-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Membership Plans</h4>
            <a href="{{ route('admin.customers.membership.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add New Plan
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase">Total Plans</div>
                        <div class="h4 mb-0 text-primary">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase">Active</div>
                        <div class="h4 mb-0 text-success">{{ number_format($stats['active']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase">Inactive</div>
                        <div class="h4 mb-0 text-secondary">{{ number_format($stats['inactive']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase">Featured</div>
                        <div class="h4 mb-0 text-warning">{{ number_format($stats['featured']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase">Total Members</div>
                        <div class="h4 mb-0">{{ number_format($stats['total_members']) }}</div>
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
                                       placeholder="Name, slug..." value="{{ request('search') }}">
                            </div>
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

                        <!-- Featured Filter -->
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <label class="form-label small text-muted">Featured</label>
                            <select name="featured" id="filterFeatured" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured Only</option>
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <label class="form-label small text-muted">Sort By</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="sort_order" {{ request('sort') == 'sort_order' ? 'selected' : '' }}>Sort Order</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price</option>
                                <option value="duration_days" {{ request('sort') == 'duration_days' ? 'selected' : '' }}>Duration</option>
                                <option value="discount_percentage" {{ request('sort') == 'discount_percentage' ? 'selected' : '' }}>Discount</option>
                                <option value="members_count" {{ request('sort') == 'members_count' ? 'selected' : '' }}>Members</option>
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                            </select>
                        </div>

                        <!-- Direction -->
                        <div class="col-lg-1 col-md-2 col-sm-4">
                            <label class="form-label small text-muted">Order</label>
                            <select name="direction" class="form-select form-select-sm">
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Asc</option>
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Desc</option>
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="col-lg-1 col-md-2 col-sm-4">
                            <a href="{{ route('admin.customers.membership.index') }}" class="btn btn-sm btn-outline-secondary w-100">
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
                                <th>Plan Name</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Duration</th>
                                <th class="text-center">Discount</th>
                                <th class="text-center">Members</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Featured</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse($plans as $index => $plan)
                                <tr>
                                    <td>{{ $plans->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($plan->icon)
                                                <div class="me-2" style="width: 40px; height: 40px; background: {{ $plan->color }}20; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="{{ $plan->icon }}" style="color: {{ $plan->color }};"></i>
                                                </div>
                                            @else
                                                <div class="avatar-circle me-2" style="background: {{ $plan->color }};">
                                                    {{ strtoupper(substr($plan->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $plan->name }}</div>
                                                <div class="small text-muted">{{ $plan->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-medium">{{ $plan->formatted_price }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted">{{ $plan->formatted_duration }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($plan->discount_percentage > 0)
                                            <span class="badge bg-success">{{ number_format($plan->discount_percentage, 0) }}%</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted">{{ number_format($plan->members_count) }}</span>
                                        @if($plan->max_members)
                                            <span class="text-muted">/ {{ number_format($plan->max_members) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($plan->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($plan->is_featured)
                                            <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Featured</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.customers.membership.edit', $plan->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.customers.membership.toggle-status', $plan->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $plan->is_active ? 'warning' : 'success' }}" title="{{ $plan->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="bi bi-{{ $plan->is_active ? 'pause' : 'play' }}-fill"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.customers.membership.toggle-featured', $plan->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $plan->is_featured ? 'warning' : 'secondary' }}" title="{{ $plan->is_featured ? 'Unfeature' : 'Feature' }}">
                                                    <i class="bi bi-star{{ $plan->is_featured ? '-fill' : '' }}"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $plan->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="deleteModal{{ $plan->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Membership Plan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete the membership plan <strong>{{ $plan->name }}</strong>?</p>
                                                @if($plan->members_count > 0)
                                                    <div class="alert alert-warning">
                                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                                        This plan has <strong>{{ number_format($plan->members_count) }}</strong> active members. Please reassign them before deleting.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.customers.membership.destroy', $plan->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete Plan</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bi bi-card-checklist text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mb-2 mt-2">No membership plans found</p>
                                        <a href="{{ route('admin.customers.membership.create') }}" class="btn btn-sm btn-primary mt-1">
                                            <i class="bi bi-plus-lg me-1"></i> Add First Plan
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($plans->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        Showing {{ $plans->firstItem() }} - {{ $plans->lastItem() }} of {{ $plans->total() }} plans
                    </div>
                    <div>
                        {{ $plans->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    document.querySelectorAll('#filterForm select').forEach(element => {
        element.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Live search with debounce
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            document.getElementById('filterForm').submit();
        }, 500);
    });
});
</script>
@endsection
