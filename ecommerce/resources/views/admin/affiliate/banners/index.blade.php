@extends('admin.layouts.app')

@section('title', 'Affiliate Banners')

@section('content')
<!-- Statistics Cards -->
<div class="stat-card-row mb-4">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon"><i class="bi bi-images"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total Banners</span>
            <span class="stat-card-value">{{ number_format($stats['total'] ?? 0) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-success">
        <div class="stat-card-icon"><i class="bi bi-check-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Active</span>
            <span class="stat-card-value">{{ number_format($stats['active'] ?? 0) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-secondary">
        <div class="stat-card-icon"><i class="bi bi-x-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Inactive</span>
            <span class="stat-card-value">{{ number_format($stats['inactive'] ?? 0) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-info">
        <div class="stat-card-icon"><i class="bi bi-cursor"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total Clicks</span>
            <span class="stat-card-value">{{ number_format($stats['total_clicks'] ?? 0) }}</span>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Affiliate Banners</h4>
    <a href="{{ route('admin.affiliate.banners.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add New Banner
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-images me-2"></i>Banner List</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Preview</th>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Affiliate</th>
                        <th>Clicks</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr>
                        <td>
                            @php
                                $imageUrl = $banner->image;
                                if($imageUrl && !str_starts_with($imageUrl, '/storage/') && !str_starts_with($imageUrl, 'http')) {
                                    $imageUrl = '/storage/' . $imageUrl;
                                }
                            @endphp
                            @if($imageUrl)
                            <img src="{{ asset($imageUrl) }}" alt="{{ $banner->name }}" style="width: 80px; height: 50px; object-fit: cover;" class="rounded">
                            @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 50px;">
                                <i class="bi bi-image text-white"></i>
                            </div>
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
                                <form action="{{ route('admin.affiliate.banners.destroy', $banner->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this banner?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-2 mt-2">No banners found. Create your first banner to get started.</p>
                            <a href="{{ route('admin.affiliate.banners.create') }}" class="btn btn-sm btn-primary mt-1">
                                <i class="bi bi-plus-lg me-1"></i>Add First Banner
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($banners->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $banners->firstItem() }} - {{ $banners->lastItem() }} of {{ $banners->total() }} banners
            </div>
            <div>
                {{ $banners->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
