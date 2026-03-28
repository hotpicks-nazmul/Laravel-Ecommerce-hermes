@extends('admin.layouts.app')

@section('title', 'Affiliate Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Affiliate Users</h4>
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
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Name, Email, Code..." value="{{ request('search') }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm"></div>
                        </span>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" id="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-8">
                    <a href="{{ route('admin.affiliate.users.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>User</th>
                        <th>Code</th>
                        <th>Commission</th>
                        <th>Balance</th>
                        <th>Total Earnings</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($affiliates as $affiliate)
                    <tr>
                        <td>{{ $affiliate->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                    <i class="bi bi-person text-muted"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $affiliate->user->name ?? '-' }}</div>
                                    <div class="small text-muted">{{ $affiliate->user->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><code class="small">{{ $affiliate->affiliate_code }}</code></td>
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
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-2 mt-2">No affiliate users found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($affiliates->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $affiliates->firstItem() }} - {{ $affiliates->lastItem() }} of {{ $affiliates->total() }} users
            </div>
            <div>
                {{ $affiliates->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const searchSpinner = document.getElementById('searchSpinner');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();
        
        searchSpinner.style.display = 'block';
        
        searchTimeout = setTimeout(() => {
            performLiveSearch(searchTerm);
        }, 300);
    });

    const filterStatus = document.getElementById('filterStatus');
    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            performLiveSearch(searchInput.value.trim());
        });
    }

    function performLiveSearch(searchTerm) {
        const params = new URLSearchParams();
        
        if (searchTerm) params.set('search', searchTerm);
        
        const status = document.getElementById('filterStatus').value;
        if (status) params.set('status', status);
        
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('per_page')) params.set('per_page', urlParams.get('per_page'));
        
        fetch(`{{ route('admin.affiliate.users.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            searchSpinner.style.display = 'none';
            
            if (data.html) {
                document.querySelector('#tableBody').innerHTML = data.html;
                
                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
            } else {
                window.location.reload();
            }
        })
        .catch(() => {
            searchSpinner.style.display = 'none';
            window.location.search = params.toString();
        });
    }
</script>
@endpush
