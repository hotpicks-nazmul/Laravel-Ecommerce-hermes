@extends('admin.layouts.app')

@section('title', 'Affiliate Users')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Affiliate Users</h1>
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
            <h5 class="mb-0">All Affiliate Users</h5>
        </div>
        <div class="card-body">
            @if($affiliates->count() > 0)
            <table class="table table-striped" id="affiliateUsersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Code</th>
                        <th>Commission</th>
                        <th>Balance</th>
                        <th>Total Earnings</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($affiliates as $affiliate)
                    <tr>
                        <td>{{ $affiliate->id }}</td>
                        <td>{{ $affiliate->user->name ?? '-' }}</td>
                        <td>{{ $affiliate->user->email ?? '-' }}</td>
                        <td><code>{{ $affiliate->affiliate_code }}</code></td>
                        <td><span class="badge bg-info">{{ $affiliate->commission_rate }}%</span></td>
                        <td>${{ number_format($affiliate->balance, 2) }}</td>
                        <td>${{ number_format($affiliate->total_earnings, 2) }}</td>
                        <td>
                            @if($affiliate->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                            @elseif($affiliate->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($affiliate->status === 'suspended')
                            <span class="badge bg-danger">Suspended</span>
                            @else
                            <span class="badge bg-secondary">{{ ucfirst($affiliate->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.affiliate.users.show', $affiliate->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($affiliate->status === 'pending')
                            <form action="{{ route('admin.affiliate.users.approve', $affiliate->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            @endif
                            @if($affiliate->status === 'approved')
                            <form action="{{ route('admin.affiliate.users.suspend', $affiliate->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Suspend" onclick="return confirm('Are you sure you want to suspend this affiliate?')">
                                    <i class="bi bi-pause-circle"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.affiliate.users.destroy', $affiliate->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this affiliate?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $affiliates->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">No affiliate users found</h5>
                <p class="text-muted">Affiliate users will appear here once they register.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#affiliateUsersTable').DataTable({
            pageLength: 15,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: [8] }
            ]
        });
    });
</script>
@endpush
