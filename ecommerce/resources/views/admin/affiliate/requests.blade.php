@extends('admin.layouts.app')

@section('title', 'Affiliate Requests')

@section('content')
<!-- Statistics Cards -->
<div class="stat-card-row mb-4">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon"><i class="bi bi-inbox"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total Requests</span>
            <span class="stat-card-value">{{ number_format($stats['total'] ?? 0) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-warning">
        <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Pending</span>
            <span class="stat-card-value">{{ number_format($stats['pending'] ?? 0) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-success">
        <div class="stat-card-icon"><i class="bi bi-check-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Approved</span>
            <span class="stat-card-value">{{ number_format($stats['approved'] ?? 0) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-danger">
        <div class="stat-card-icon"><i class="bi bi-x-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Rejected</span>
            <span class="stat-card-value">{{ number_format($stats['rejected'] ?? 0) }}</span>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Affiliate Requests</h4>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-inbox me-2"></i>All Affiliate Registration Requests</h6>
    </div>
    <div class="card-body p-0">
        @if($requests->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="affiliateRequestsTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                        </th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Website</th>
                        <th>Promotion Methods</th>
                        <th>Requested At</th>
                        <th>Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $request->id }}">
                        </td>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->user->name ?? '-' }}</td>
                        <td>{{ $request->user->email ?? '-' }}</td>
                        <td>
                            @if($request->website)
                            <a href="{{ $request->website }}" target="_blank" class="text-decoration-none">
                                {{ Str::limit($request->website, 30) }}
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($request->promotion_methods, 50) ?? '-' }}</td>
                        <td>{{ $request->requested_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($request->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($request->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                            @else
                            <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            @if($request->status === 'pending')
                            <form action="{{ route('admin.affiliate.requests.approve', $request->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve" onclick="return confirm('Are you sure you want to approve this request?')">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.affiliate.requests.reject', $request->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject" onclick="return confirm('Are you sure you want to reject this request?')">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted">Processed</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($requests->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $requests->firstItem() }} - {{ $requests->lastItem() }} of {{ $requests->total() }} requests
            </div>
            <div>
                {{ $requests->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">No requests found</h5>
            <p class="text-muted">Affiliate registration requests will appear here.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize select all checkbox
        $('#selectAllCheckbox').on('change', function() {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Initialize DataTable
        $('#affiliateRequestsTable').DataTable({
            pageLength: 15,
            order: [[6, 'desc']],
            columnDefs: [
                { orderable: false, targets: [0, 8] }
            ]
        });
    });
</script>
@endpush
