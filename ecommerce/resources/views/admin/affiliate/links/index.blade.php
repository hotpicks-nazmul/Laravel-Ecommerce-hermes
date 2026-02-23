@extends('admin.layouts.app')

@section('title', 'Affiliate Links')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Affiliate Links</h1>
        <a href="{{ route('admin.affiliate.links.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New Link
        </a>
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
            <h5 class="mb-0">All Affiliate Links</h5>
        </div>
        <div class="card-body">
            @if($links->count() > 0)
            <table class="table table-striped" id="affiliateLinksTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Link Name</th>
                        <th>Affiliate</th>
                        <th>Product</th>
                        <th>Code</th>
                        <th>Clicks</th>
                        <th>Conversions</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($links as $link)
                    <tr>
                        <td>{{ $link->id }}</td>
                        <td>{{ $link->name }}</td>
                        <td>{{ $link->affiliate->user->name ?? '-' }}</td>
                        <td>{{ $link->product->name ?? '-' }}</td>
                        <td>
                            <div class="input-group input-group-sm" style="max-width: 200px;">
                                <input type="text" class="form-control form-control-sm" value="{{ $link->full_url }}" readonly id="link{{ $link->id }}">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyLink('link{{ $link->id }}')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </td>
                        <td>{{ number_format($link->clicks) }}</td>
                        <td>{{ number_format($link->conversions) }}</td>
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
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $links->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-link text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">No affiliate links found</h5>
                <p class="text-muted">Start by creating a new affiliate link.</p>
                <a href="{{ route('admin.affiliate.links.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add New Link
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#affiliateLinksTable').DataTable({
            pageLength: 15,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: [4, 8] }
            ]
        });
    });

    function copyLink(inputId) {
        var copyText = document.getElementById(inputId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        // Show toast
        alert('Link copied to clipboard!');
    }
</script>
@endpush
