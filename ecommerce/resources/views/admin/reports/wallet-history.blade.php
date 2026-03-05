@extends('admin.layouts.app')

@section('title', 'Wallet Recharge History')

@section('content')
<div class="mb-4">
    <h4 class="mb-0"><i class="bi bi-wallet me-2"></i>Wallet Recharge History</h4>
    <p class="text-muted mb-0">Track all wallet recharge and debit transactions</p>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <i class="bi bi-plus-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted small mb-0">Total Recharges</p>
                        <h4 class="mb-0">{{ number_format($totalRecharges) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <i class="bi bi-currency-dollar text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted small mb-0">Recharge Amount</p>
                        <h4 class="mb-0">৳{{ number_format($totalRechargeAmount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-danger bg-opacity-10 rounded p-3">
                            <i class="bi bi-dash-circle text-danger fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted small mb-0">Total Debits</p>
                        <h4 class="mb-0">৳{{ number_format($totalDebitAmount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <i class="bi bi-wallet2 text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted small mb-0">Net Amount</p>
                        <h4 class="mb-0">৳{{ number_format($netAmount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-0">Unique Users</p>
                        <h4 class="mb-0">{{ number_format($totalUsers) }}</h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded p-3">
                        <i class="bi bi-people text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-0">Debit Transactions</p>
                        <h4 class="mb-0 text-danger">{{ number_format($totalDebits) }}</h4>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded p-3">
                        <i class="bi bi-arrow-down-circle text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-0">Sources</p>
                        <h4 class="mb-0">{{ $sourceCounts->count() }}</h4>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded p-3">
                        <i class="bi bi-collection text-info fs-4"></i>
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
                <!-- Search Input -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" 
                               placeholder="Search by name, email, phone..." value="{{ $search }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm"></div>
                        </span>
                    </div>
                </div>
                
                <!-- Date Range -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Date Range</label>
                    <input type="text" name="date_range" id="dateRange" class="form-control form-control-sm" 
                           placeholder="Select date range" value="{{ $dateRange }}">
                </div>
                
                <!-- Type Filter -->
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <label class="form-label small text-muted">Type</label>
                    <select name="type" id="filterType" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="credit" {{ $type === 'credit' ? 'selected' : '' }}>Credit (Recharge)</option>
                        <option value="debit" {{ $type === 'debit' ? 'selected' : '' }}>Debit</option>
                    </select>
                </div>
                
                <!-- Source Filter -->
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <label class="form-label small text-muted">Source</label>
                    <select name="source" id="filterSource" class="form-select form-select-sm">
                        <option value="">All Sources</option>
                        <option value="admin" {{ $source === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="order" {{ $source === 'order' ? 'selected' : '' }}>Order</option>
                        <option value="refund" {{ $source === 'refund' ? 'selected' : '' }}>Refund</option>
                        <option value="payment" {{ $source === 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="other" {{ $source === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <!-- Sort -->
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <label class="form-label small text-muted">Sort By</label>
                    <select name="sort" id="filterSort" class="form-select form-select-sm">
                        <option value="date_desc" {{ $sortBy === 'date_desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="date_asc" {{ $sortBy === 'date_asc' ? 'selected' : '' }}>Oldest First</option>
                        <option value="amount_desc" {{ $sortBy === 'amount_desc' ? 'selected' : '' }}>Highest Amount</option>
                        <option value="amount_asc" {{ $sortBy === 'amount_asc' ? 'selected' : '' }}>Lowest Amount</option>
                        <option value="balance_desc" {{ $sortBy === 'balance_desc' ? 'selected' : '' }}>Highest Balance</option>
                        <option value="balance_asc" {{ $sortBy === 'balance_asc' ? 'selected' : '' }}>Lowest Balance</option>
                    </select>
                </div>
                
                <!-- Filter Buttons -->
                <div class="col-lg-12 col-md-12 col-sm-12 mt-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.reports.wallet-history') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </a>
                    <a href="{{ route('admin.reports.wallet-history.export', request()->query()) }}" class="btn btn-sm btn-success float-end">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Transactions Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Source</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Balance After</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $transaction->user->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $transaction->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($transaction->type === 'credit')
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="bi bi-plus-circle me-1"></i>Credit
                            </span>
                            @else
                            <span class="badge bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-dash-circle me-1"></i>Debit
                            </span>
                            @endif
                        </td>
                        <td>
                            @php
                                $sourceColors = [
                                    'admin' => 'primary',
                                    'order' => 'info',
                                    'refund' => 'warning',
                                    'payment' => 'success',
                                    'other' => 'secondary'
                                ];
                                $color = $sourceColors[$transaction->source] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                {{ ucfirst($transaction->source) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="{{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }} fw-medium">
                                {{ $transaction->type === 'credit' ? '+' : '-' }}৳{{ number_format($transaction->amount, 2) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-medium">৳{{ number_format($transaction->balance_after, 2) }}</span>
                        </td>
                        <td>
                            @if($transaction->reference_id)
                            <span class="text-muted small">{{ $transaction->reference_id }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small">{{ Str::limit($transaction->description, 30) ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="small">{{ $transaction->created_at->format('d M Y') }}</div>
                            <div class="text-muted small">{{ $transaction->created_at->format('h:i A') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-wallet text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-2 mt-2">No wallet transactions found</p>
                            @if(request()->anyFilled(['search', 'date_range', 'type', 'source']))
                            <a href="{{ route('admin.reports.wallet-history') }}" class="btn btn-sm btn-primary mt-1">
                                <i class="bi bi-x-lg me-1"></i> Clear Filters
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
            </div>
            <div>
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Date range picker
    document.addEventListener('DOMContentLoaded', function() {
        const dateRangeInput = document.getElementById('dateRange');
        if (dateRangeInput && typeof flatpickr !== 'undefined') {
            flatpickr(dateRangeInput, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M Y',
                maxDate: 'today',
                static: true
            });
        }
        
        // Auto-submit form on filter change
        const filterSelects = ['filterType', 'filterSource', 'filterSort'];
        filterSelects.forEach(id => {
            const select = document.getElementById(id);
            if (select) {
                select.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
        });
        
        // Live search with debounce
        let searchTimeout;
        const searchInput = document.getElementById('liveSearch');
        const searchSpinner = document.getElementById('searchSpinner');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();
                
                if (searchTerm.length >= 3 || searchTerm.length === 0) {
                    searchSpinner.style.display = 'block';
                    searchTimeout = setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 500);
                }
            });
        }
    });
</script>
@endpush
