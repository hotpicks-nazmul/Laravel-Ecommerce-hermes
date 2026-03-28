@extends('admin.layouts.app')

@section('title', 'Affiliate Categories')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Affiliate Categories</h4>
    <a href="{{ route('admin.affiliate.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add New Category
    </a>
</div>

<!-- Alert Messages -->
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

<!-- Categories Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-folder me-2"></i>All Affiliate Categories</h6>
    </div>
    <div class="card-body p-0">
        @if($categories->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="affiliateCategoriesTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 80px;">Image</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th style="width: 120px;">Commission</th>
                        <th style="width: 80px;">Products</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            @if($category->image)
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-folder text-muted"></i>
                            </div>
                            @endif
                        </td>
                        <td>{{ $category->name }}</td>
                        <td><code class="small">{{ $category->slug }}</code></td>
                        <td><span class="badge bg-info">{{ $category->commission_rate }}%</span></td>
                        <td><span class="badge bg-secondary">{{ $category->products_count }}</span></td>
                        <td>
                            @if($category->status === 'active')
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.affiliate.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.affiliate.categories.destroy', $category->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
        
        <div class="d-flex justify-content-center mt-4">
            {{ $categories->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-folder text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">No categories found</h5>
            <p class="text-muted">Start by creating a new affiliate category.</p>
            <a href="{{ route('admin.affiliate.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add New Category
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#affiliateCategoriesTable').DataTable({
            pageLength: 15,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: [1, 7] }
            ]
        });
    });
</script>
@endpush
