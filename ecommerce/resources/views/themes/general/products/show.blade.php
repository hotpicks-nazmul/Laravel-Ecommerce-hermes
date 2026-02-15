@extends('themes.general.layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">
            <!-- Product Image -->
            <div class="relative">
                @php
                    $imageUrl = $product->image ?? 'https://via.placeholder.com/500x500?text=No+Image';
                    if (str_starts_with($imageUrl, '/uploads/')) {
                        $imageUrl = asset($imageUrl);
                    } elseif (!str_starts_with($imageUrl, 'http')) {
                        $imageUrl = asset('storage/' . $imageUrl);
                    }
                @endphp
                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-lg">
                
                @if($product->sale_price)
                <span class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                    -{{ $product->discount_percentage }}% OFF
                </span>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                @if($product->category)
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-halal-green hover:underline">
                    {{ $product->category->name }}
                </a>
                @endif
                
                <h1 class="text-3xl font-bold text-gray-800 mt-2">{{ $product->name }}</h1>
                
                <!-- Rating -->
                <div class="flex items-center mt-3">
                    <div class="flex text-halal-gold">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star-fill"></i>
                        @endfor
                    </div>
                    <span class="text-gray-500 ml-2">({{ $product->reviews_count ?? 0 }} reviews)</span>
                </div>

                <!-- Price -->
                <div class="mt-4">
                    @if($product->sale_price)
                        <span class="text-3xl font-bold text-halal-green">৳{{ number_format($product->sale_price) }}</span>
                        <span class="text-xl text-gray-400 line-through ml-2">৳{{ number_format($product->price) }}</span>
                    @else
                        <span class="text-3xl font-bold text-halal-green">৳{{ number_format($product->price) }}</span>
                    @endif
                </div>

                <!-- Description -->
                <p class="text-gray-600 mt-4">{{ $product->short_description }}</p>

                <!-- Stock Status -->
                <div class="mt-4">
                    @if($product->quantity > 0)
                        <span class="text-green-600"><i class="bi bi-check-circle-fill mr-1"></i>In Stock ({{ $product->quantity }} available)</span>
                    @else
                        <span class="text-red-600"><i class="bi bi-x-circle-fill mr-1"></i>Out of Stock</span>
                    @endif
                </div>

                <!-- Add to Cart -->
                <div class="mt-6 flex gap-4">
                    <input type="number" id="quantity" value="1" min="1" max="{{ $product->quantity }}" 
                           class="w-20 border rounded-lg px-3 py-2 text-center">
                    
                    @if($product->quantity > 0)
                    <button onclick="addToCart({{ $product->id }}, document.getElementById('quantity').value)" 
                            class="flex-1 bg-halal-green text-white py-3 rounded-lg hover:bg-halal-dark transition-colors font-medium">
                        <i class="bi bi-cart-plus mr-2"></i>Add to Cart
                    </button>
                    @else
                    <button disabled class="flex-1 bg-gray-400 text-white py-3 rounded-lg cursor-not-allowed font-medium">
                        Out of Stock
                    </button>
                    @endif
                    
                    <button onclick="addToWishlist({{ $product->id }})" 
                            class="bg-gray-200 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>

                <!-- Long Description -->
                @if($product->long_description)
                <div class="mt-8 border-t pt-6">
                    <h3 class="font-bold text-lg mb-3">Description</h3>
                    <div class="text-gray-600 prose">
                        {{ $product->long_description }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Related Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
                @include('themes.general.partials.product-card', ['product' => $relatedProduct])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
