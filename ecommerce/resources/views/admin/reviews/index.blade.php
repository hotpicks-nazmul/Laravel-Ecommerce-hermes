@extends('admin.layouts.app')

@section('title', 'Reviews')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Reviews</h4>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="reviewsTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews ?? [] as $review)
                    <tr>
                        <td>{{ $review->product->name ?? 'N/A' }}</td>
                        <td>{{ $review->user->name ?? 'Guest' }}</td>
                        <td>
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : '' }}"></i>
                            @endfor
                        </td>
                        <td>{{ Str::limit($review->comment, 50) }}</td>
                        <td>
                            <span class="badge {{ $review->is_approved ? 'bg-success' : 'bg-warning' }}">
                                {{ $review->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </td>
                        <td>{{ $review->created_at->format('d M, Y') }}</td>
                        <td>
                            @if(!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No reviews found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#reviewsTable').DataTable({
        pageLength: 25
    });
});
</script>
@endpush
