@extends('admin.layouts.app')

@section('title', 'Affiliate Payouts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Affiliate Payouts</h4>
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

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>All Payouts</h6>
    </div>
    <div class="card-body p-0">
        @if($withdrawals->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="affiliatePayoutsTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Affiliate</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th style="width: 120px;">Actions</th>
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
                            @if($withdrawal->status === 'pending')
                            <form action="{{ route('admin.affiliate.payouts.approve', $withdrawal->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve" onclick="return confirm('Are you sure you want to approve this payout?')">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.affiliate.payouts.reject', $withdrawal->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject" onclick="return confirm('Are you sure you want to reject this payout?')">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($withdrawals->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $withdrawals->firstItem() }} - {{ $withdrawals->lastItem() }} of {{ $withdrawals->total() }} payouts
            </div>
            <div>
                {{ $withdrawals->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-5">
            <i class="bi bi-cash-stack text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">No payouts found</h5>
            <p class="text-muted">Payout requests will appear here once affiliates request withdrawals.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Note: Using Laravel pagination, not DataTables
        // This provides server-side pagination which is more scalable
    });
</script>
@endpush
