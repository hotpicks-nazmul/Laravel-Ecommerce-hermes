@extends('themes.general.layouts.app')

@section('title', 'Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">All Products</h1>
        <p class="text-gray-600 mt-2">Browse our premium quality halal products</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="lg:w-1/4">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="font-bold text-lg mb-4">Categories</h3>
                <div class="space-y-2">
                    @foreach($categories ?? [] as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                       class="block px-3 py-2 rounded-lg hover:bg-halal-cream {{ (request('category') == $category->slug) ? 'bg-halal-green text-white' : '' }}">
                        {{ $category->name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            @if(isset($products) && $products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    @include('themes.general.partials.product-card', ['product' => $product])
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
            @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i class="bi bi-box-seam text-6xl text-gray-300"></i>
                <h3 class="text-xl font-bold text-gray-600 mt-4">No Products Found</h3>
                <p class="text-gray-500 mt-2">Try adjusting your filters or search criteria.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
