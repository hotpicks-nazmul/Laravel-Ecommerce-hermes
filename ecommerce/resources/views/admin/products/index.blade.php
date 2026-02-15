@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Products</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Product
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="productsTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products ?? [] as $product)
                    <tr>
                        <td>
                            @if($product->featured_image)
                            <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}" width="50" height="50" class="rounded">
                            @else
                            <div class="bg-secondary rounded" style="width:50px;height:50px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-image text-white"></i>
                            </div>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>
                            @if($product->sale_price)
                                <del class="text-muted">৳{{ number_format($product->price, 2) }}</del>
                                <span class="text-danger">৳{{ number_format($product->sale_price, 2) }}</span>
                            @else
                                ৳{{ number_format($product->price, 2) }}
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $product->quantity > 10 ? 'bg-success' : ($product->quantity > 0 ? 'bg-warning' : 'bg-danger') }}">
                                {{ $product->quantity }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No products found. <a href="{{ route('admin.products.create') }}">Add your first product</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($products) && method_exists($products, 'hasPages') && $products->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
        pageLength: 25,
        order: [[1, 'asc']]
    });
});
</script>
@endpush
