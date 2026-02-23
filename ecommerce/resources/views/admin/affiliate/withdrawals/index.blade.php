@extends('admin.layouts.app')

@section('title', 'Affiliate Withdrawals')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Affiliate Withdrawals</h1>
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
            <h5 class="mb-0">All Withdrawal Requests</h5>
        </div>
        <div class="card-body">
            @if($withdrawals->count() > 0)
            <table class="table table-striped" id="affiliateWithdrawalsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Affiliate</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Account Details</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $withdrawal)
                    <tr>
                        <td>{{ $withdrawal->id }}</td>
                        <td>{{ $withdrawal->affiliate->user->name ?? '-' }}</td>
                        <td>${{ number_format($withdrawal->amount, 2) }}</td>
                        <td>{{ ucfirst($withdrawal->payment_method) }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $withdrawal->id }}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                        <td>
                            @if($withdrawal->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($withdrawal->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                            @elseif($withdrawal->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                            @else
                            <span class="badge bg-info">Paid</span>
                            @endif
                        </td>
                        <td>{{ $withdrawal->requested_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.affiliate.withdrawals.show', $withdrawal->id) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($withdrawal->status === 'pending')
                            <form action="{{ route('admin.affiliate.withdrawals.approve', $withdrawal->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve" onclick="return confirm('Are you sure you want to approve this withdrawal?')">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.affiliate.withdrawals.reject', $withdrawal->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject" onclick="return confirm('Are you sure you want to reject this withdrawal?')">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $withdrawals->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-wallet2 text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">No withdrawals found</h5>
                <p class="text-muted">Withdrawal requests will appear here once affiliates request payouts.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Payment Details Modals --}}
@foreach($withdrawals ?? [] as $withdrawal)
<div class="modal fade" id="detailsModal{{ $withdrawal->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre class="bg-light p-3 rounded">{{ $withdrawal->payment_details }}</pre>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#affiliateWithdrawalsTable').DataTable({
            pageLength: 15,
            order: [[6, 'desc']],
            columnDefs: [
                { orderable: false, targets: [4, 7] }
            ]
        });
    });
</script>
@endpush
