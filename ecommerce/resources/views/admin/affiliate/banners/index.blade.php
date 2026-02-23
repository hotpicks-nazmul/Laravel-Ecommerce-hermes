@extends('admin.layouts.app')

@section('title', 'Affiliate Banners')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Affiliate Banners</h1>
        <a href="{{ route('admin.affiliate.banners.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New Banner
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Banner List</h5>
        </div>
        <div class="card-body">
            @if($banners->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Affiliate</th>
                            <th>Clicks</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banners as $banner)
                        <tr>
                            <td>
                                @if($banner->image)
                                <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->name }}" style="max-width: 100px; max-height: 60px; object-fit: contain;">
                                @else
                                <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td>{{ $banner->name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $banner->width }}x{{ $banner->height }}</span>
                            </td>
                            <td>
                                @if($banner->affiliate)
                                <a href="{{ route('admin.affiliate.users.show', $banner->affiliate->id) }}">
                                    {{ $banner->affiliate->user->name ?? 'Unknown' }}
                                </a>
                                @else
                                <span class="text-muted">General</span>
                                @endif
                            </td>
                            <td>{{ number_format($banner->clicks) }}</td>
                            <td>
                                @if($banner->status === 'active')
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $banner->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.affiliate.banners.edit', $banner->id) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.affiliate.banners.destroy', $banner->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this banner?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No banners found. Create your first banner to get started.</p>
                <a href="{{ route('admin.affiliate.banners.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add New Banner
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
        $('.datatable').DataTable({
            order: [[6, 'desc']]
        });
    });
</script>
@endpush
