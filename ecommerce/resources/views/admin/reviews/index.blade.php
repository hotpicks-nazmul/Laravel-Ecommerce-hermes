@extends('admin.layouts.app')

@section('title', 'Reviews Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Reviews Management</h4>
        <p class="text-muted mb-0 small">Manage customer reviews and ratings</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-chat-square-text text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ \App\Models\Review::count() }}</h5>
                        <small class="text-muted">Total Reviews</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="bi bi-clock text-warning fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ \App\Models\Review::where('status', 'pending')->count() }}</h5>
                        <small class="text-muted">Pending Reviews</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ \App\Models\Review::where('status', 'approved')->count() }}</h5>
                        <small class="text-muted">Approved Reviews</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="bi bi-star-fill text-info fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ number_format(\App\Models\Review::avg('rating') ?? 0, 1) }}</h5>
                        <small class="text-muted">Average Rating</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Reviews</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Rating</label>
                <select name="rating" class="form-select">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by product or customer name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions Form -->
<form id="bulkActionForm" action="{{ route('admin.reviews.bulk-action') }}" method="POST">
    @csrf
    <input type="hidden" name="action" id="bulkAction" value="">
    <input type="hidden" name="ids" id="bulkIds" value="">

    <!-- Reviews Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label small" for="selectAll">Select All</label>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="submitBulkAction('approve')">
                        <i class="bi bi-check-lg me-1"></i> Approve Selected
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="submitBulkAction('delete')">
                        <i class="bi bi-trash me-1"></i> Delete Selected
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40"></th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr>
                            <td>
                                <input class="form-check-input review-checkbox" type="checkbox" value="{{ $review->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($review->product)
                                        <div class="bg-light rounded p-2 me-2" style="width: 50px; height: 50px;">
                                            @if($review->product->image)
                                                <img src="{{ $review->product->image }}" alt="{{ $review->product->name }}" class="w-100 h-100 object-fit-cover rounded">
                                            @else
                                                <i class="bi bi-box text-muted"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" class="text-decoration-none">
                                                <span class="fw-medium">{{ Str::limit($review->product->name, 30) }}</span>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">Product deleted</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium">{{ $review->user->name ?? 'Guest' }}</span>
                                        <br>
                                        <small class="text-muted">{{ $review->user->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ $review->rating }}/5</small>
                                </div>
                            </td>
                            <td>
                                @if($review->title)
                                    <strong>{{ $review->title }}</strong><br>
                                @endif
                                <span class="text-muted">{{ Str::limit($review->comment, 80) }}</span>
                                @if(strlen($review->comment) > 80)
                                    <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $review->id }}">
                                        Read more
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if($review->status === 'approved')
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>Approved
                                    </span>
                                @elseif($review->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-x-circle me-1"></i>Rejected
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="bi bi-clock me-1"></i>Pending
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $review->created_at->format('d M, Y') }}</small>
                                <br>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($review->status !== 'approved')
                                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="View" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $review->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Review Detail Modal -->
                        <div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Review Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="bi bi-person text-primary fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $review->user->name ?? 'Guest' }}</h6>
                                                <small class="text-muted">{{ $review->created_at->format('F d, Y \a\t h:i A') }}</small>
                                            </div>
                                            <div class="ms-auto text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        @if($review->product)
                                            <div class="bg-light p-3 rounded mb-3">
                                                <small class="text-muted">Product:</small>
                                                <p class="mb-0 fw-medium">{{ $review->product->name }}</p>
                                            </div>
                                        @endif
                                        @if($review->title)
                                            <h6 class="fw-bold">{{ $review->title }}</h6>
                                        @endif
                                        <p class="text-muted mb-0">{{ $review->comment }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        @if($review->status !== 'approved')
                                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-lg me-1"></i> Approve
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-chat-square-text text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">No reviews found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($reviews->hasPages())
        <div class="card-footer bg-white">
            {{ $reviews->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</form>

@push('scripts')
<script>
// Select All Checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});

// Bulk Action Submit
function submitBulkAction(action) {
    const checkboxes = document.querySelectorAll('.review-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one review.');
        return;
    }
    
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (action === 'delete') {
        if (!confirm(`Are you sure you want to delete ${ids.length} review(s)?`)) {
            return;
        }
    }
    
    document.getElementById('bulkAction').value = action;
    document.getElementById('bulkIds').value = JSON.stringify(ids);
    document.getElementById('bulkActionForm').submit();
}
</script>
@endpush
@endsection
