@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Categories</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Category
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="categoriesTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories ?? [] as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                @php
                                    $imageUrl = $category->image;
                                    if (!str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/storage/')) {
                                        $imageUrl = '/storage/' . $imageUrl;
                                    }
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $category->name }}" width="50" height="50" class="rounded">
                            @else
                            <div class="bg-secondary rounded" style="width:50px;height:50px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-folder text-white"></i>
                            </div>
                            @endif
                        </td>
                        <td>{{ $category->name }}</td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td><span class="badge bg-info">{{ $category->products_count ?? 0 }}</span></td>
                        <td>
                            <span class="badge {{ $category->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No categories found. <a href="{{ route('admin.categories.create') }}">Add your first category</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#categoriesTable').DataTable({
        pageLength: 25,
        order: [[1, 'asc']]
    });
});
</script>
@endpush
