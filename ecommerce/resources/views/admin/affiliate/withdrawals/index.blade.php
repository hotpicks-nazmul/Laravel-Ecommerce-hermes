@extends('admin.layouts.app')

@section('title', 'Affiliate Withdrawals')

@section('content')
<!-- Statistics Cards -->
<div class="stat-card-row mb-4">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon"><i class="bi bi-cash-stack"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total Withdrawals</span>
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
    <div class="stat-card stat-card-info">
        <div class="stat-card-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total Paid</span>
            <span class="stat-card-value">${{ number_format($stats['total_amount'] ?? 0, 2) }}</span>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Affiliate Withdrawals</h4>
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

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Search by name..." value="{{ $search ?? '' }}">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions Bar -->
<div class="card border-0 shadow-sm mb-3" id="bulkActionsBar" style="display: none;">
    <div class="card-body py-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted"><span id="selectedCount">0</span> selected</span>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="clearSelection()">
                    Clear Selection
                </button>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('approve')">
                    <i class="bi bi-check-circle me-1"></i> Approve
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('reject')">
                    <i class="bi bi-x-circle me-1"></i> Reject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($withdrawals->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="affiliateWithdrawalsTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                        </th>
                        <th>ID</th>
                        <th>Affiliate</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Account Details</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $withdrawal)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $withdrawal->id }}">
                        </td>
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
        </div>
        
        @if($withdrawals->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $withdrawals->firstItem() }} - {{ $withdrawals->lastItem() }} of {{ $withdrawals->total() }} withdrawals
            </div>
            <div>
                {{ $withdrawals->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
        @else
        <table class="table table-hover align-middle mb-0">
            <tbody>
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-wallet2 text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mb-2 mt-2">No withdrawals found</p>
                        <p class="text-muted small">Withdrawal requests will appear here once affiliates request payouts.</p>
                    </td>
                </tr>
            </tbody>
        </table>
        @endif
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
        // Initialize DataTable
        $('#affiliateWithdrawalsTable').DataTable({
            pageLength: 15,
            order: [[7, 'desc']], // Sort by Requested At column
            columnDefs: [
                { orderable: false, targets: [0, 5, 8] } // Checkbox, Account Details, Actions
            ]
        });
        
        // Live search with debounce
        let searchTimeout;
        $('#liveSearch').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                $('#filterForm').submit();
            }, 300);
        });
        
        // Handle select all checkbox
        $('#selectAllCheckbox').on('change', function() {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkActionsBar();
        });
        
        // Handle individual row checkbox
        $(document).on('change', '.row-checkbox', function() {
            updateBulkActionsBar();
        });
        
        function updateBulkActionsBar() {
            const selectedCount = $('.row-checkbox:checked').length;
            if (selectedCount > 0) {
                $('#bulkActionsBar').show();
                $('#selectedCount').text(selectedCount);
            } else {
                $('#bulkActionsBar').hide();
            }
        }
        
        // Clear selection function
        window.clearSelection = function() {
            $('.row-checkbox').prop('checked', false);
            $('#selectAllCheckbox').prop('checked', false);
            $('#bulkActionsBar').hide();
        };
        
        // Bulk action function
        window.bulkAction = function(action) {
            const selectedIds = $('.row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (selectedIds.length === 0) {
                alert('Please select at least one withdrawal');
                return;
            }
            
            const confirmMessage = action === 'approve' 
                ? 'Are you sure you want to approve ' + selectedIds.length + ' withdrawal(s)?'
                : 'Are you sure you want to reject ' + selectedIds.length + ' withdrawal(s)?';
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            // Create and submit a form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.affiliate.withdrawals.bulk') }}';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            
            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'ids';
            idsInput.value = JSON.stringify(selectedIds);
            form.appendChild(idsInput);
            
            document.body.appendChild(form);
            form.submit();
        };
    });
</script>
@endpush
