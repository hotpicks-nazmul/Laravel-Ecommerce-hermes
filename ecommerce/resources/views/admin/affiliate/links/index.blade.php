@extends('admin.layouts.app')

@section('title', 'Affiliate Links')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Affiliate Links</h4>
    <a href="{{ route('admin.affiliate.links.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Link
    </a>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Search links..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" id="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.affiliate.links.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
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

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Link Name</th>
                        <th>Affiliate</th>
                        <th>Product</th>
                        <th style="width: 200px;">Code</th>
                        <th style="width: 80px;">Clicks</th>
                        <th style="width: 100px;">Conversions</th>
                        <th style="width: 90px;">Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @if($links->count() > 0)
                        @foreach($links as $link)
                        <tr>
                            <td>{{ $link->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $link->name }}</div>
                                @if($link->description)
                                <small class="text-muted">{{ Str::limit($link->description, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $link->affiliate->user->name ?? '-' }}</td>
                            <td>{{ $link->product->name ?? '-' }}</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm" value="{{ $link->full_url }}" readonly id="link{{ $link->id }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyLink('link{{ $link->id }}')">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="text-center">{{ number_format($link->clicks) }}</td>
                            <td class="text-center">{{ number_format($link->conversions) }}</td>
                            <td>
                                @if($link->status === 'active')
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.affiliate.links.edit', $link->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.affiliate.links.destroy', $link->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this link?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-link text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-2 mt-2">No affiliate links found</p>
                            <a href="{{ route('admin.affiliate.links.create') }}" class="btn btn-sm btn-primary mt-1">
                                <i class="bi bi-plus-lg me-1"></i> Add First Link
                            </a>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if($links->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $links->firstItem() ?? 0 }} - {{ $links->lastItem() ?? 0 }} of {{ $links->total() }} links
            </div>
            <div>
                {{ $links->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyLink(inputId) {
        var copyText = document.getElementById(inputId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = '<div class="toast show" role="alert"><div class="toast-body"><i class="bi bi-check-circle text-success me-2"></i>Link copied to clipboard!</div></div>';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // Live search functionality
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const filterForm = document.getElementById('filterForm');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                filterForm.submit();
            }, 300);
        });
    }
</script>
@endpush
