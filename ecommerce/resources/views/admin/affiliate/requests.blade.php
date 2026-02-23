@extends('admin.layouts.app')

@section('title', 'Affiliate Requests')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Affiliate Requests</h1>
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

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Affiliate Registration Requests</h5>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
            <table class="table table-striped" id="affiliateRequestsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Website</th>
                        <th>Promotion Methods</th>
                        <th>Requested At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
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
            
            <div class="d-flex justify-content-center mt-4">
                {{ $requests->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">No requests found</h5>
                <p class="text-muted">Affiliate registration requests will appear here.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#affiliateRequestsTable').DataTable({
            pageLength: 15,
            order: [[5, 'desc']],
            columnDefs: [
                { orderable: false, targets: [7] }
            ]
        });
    });
</script>
@endpush
