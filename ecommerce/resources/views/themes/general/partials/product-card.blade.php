<div class="bg-white rounded-xl shadow-md overflow-hidden product-card group hover:shadow-xl transition-all duration-300">
    <!-- Product Image -->
    <div class="relative overflow-hidden">
        <a href="{{ route('products.show', $product->slug) }}">
            @php
                $imagePath = $product->featured_image ?? $product->image ?? '';
                $imageUrl = 'https://placehold.co/300x300/e2e8f0/64748b?text=No+Image';
                if ($imagePath) {
                    $imagePath = ltrim($imagePath, '/');
                    if (str_starts_with($imagePath, 'http')) {
                        $imageUrl = $imagePath;
                    } elseif (str_starts_with($imagePath, 'storage/')) {
                        $imageUrl = '/' . $imagePath;
                    } elseif (str_starts_with($imagePath, 'uploads/')) {
                        $imageUrl = asset($imagePath);
                    } else {
                        $imageUrl = asset('storage/' . $imagePath);
                    }
                }
            @endphp
            <img src="{{ $imageUrl }}" 
                alt="{{ $product->name }}" 
                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
                onerror="this.src='https://placehold.co/300x300?text=No+Image'">
        </a>
        
        <!-- Badges -->
        <div class="absolute top-2 left-2 flex flex-col space-y-1">
            @if($product->is_new ?? false)
            <span class="bg-halal-green text-white text-xs px-2 py-1 rounded-full font-medium">
                <i class="bi bi-star-fill mr-1"></i>New
            </span>
            @endif
            
            @if(($product->discount_percent ?? 0) > 0)
            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                -{{ $product->discount_percent }}%
            </span>
            @endif
            
            @if($product->stock <= 0)
            <span class="bg-gray-700 text-white text-xs px-2 py-1 rounded-full font-medium">
                Out of Stock
            </span>
            @endif
        </div>
        
        <!-- Quick Actions -->
        <div class="absolute top-2 right-2 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button onclick="if(typeof addToWishlist==='function')addToWishlist({{ $product->id }})" class="w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-halal-green hover:text-white transition-colors" title="Add to Wishlist">
                <i class="bi bi-heart"></i>
            </button>
            <button onclick="if(typeof quickView==='function')quickView({{ $product->id }})" class="w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-halal-green hover:text-white transition-colors" title="Quick View">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        
        <!-- Add to Cart Button -->
        <div class="absolute bottom-0 left-0 right-0 p-2 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
            @if($product->stock > 0)
            <button onclick="if(typeof addToCart==='function')addToCart({{ $product->id }})" class="w-full bg-halal-green text-white py-2 rounded-lg hover:bg-halal-dark transition-colors font-medium flex items-center justify-center">
                <i class="bi bi-cart-plus mr-2"></i>Add to Cart
            </button>
            @else
            <button disabled class="w-full bg-gray-400 text-white py-2 rounded-lg cursor-not-allowed font-medium">
                <i class="bi bi-x-circle mr-2"></i>Out of Stock
            </button>
            @endif
        </div>
    </div>
    
    <!-- Product Info -->
    <div class="p-4">
        <!-- Category -->
        @if($product->category)
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-xs text-halal-green hover:underline">
            {{ $product->category->name }}
        </a>
        @endif
        
        <!-- Name -->
        <h3 class="font-poppins font-semibold text-gray-800 mt-1 hover:text-halal-green transition-colors">
            <a href="{{ route('products.show', $product->slug) }}">{{ Str::limit($product->name, 40) }}</a>
        </h3>
        
        <!-- Rating -->
        <div class="flex items-center mt-2">
            <div class="flex text-halal-gold">
                @php $avgRating = $product->average_rating ?? 0; @endphp
                @for($i = 1; $i <= 5; $i++)
                    @if($avgRating >= $i)
                        <i class="bi bi-star-fill text-sm"></i>
                    @elseif($avgRating >= $i - 0.5)
                        <i class="bi bi-star-half text-sm"></i>
                    @else
                        <i class="bi bi-star text-sm text-gray-300"></i>
                    @endif
                @endfor
            </div>
            <span class="text-xs text-gray-500 ml-2">({{ $product->approved_reviews_count ?? 0 }})</span>
        </div>
        
        <!-- Price -->
        <div class="mt-3 flex items-center justify-between">
            <div>
                @if($product->isOnSale())
                    <span class="text-lg font-bold text-halal-green">৳{{ number_format($product->sale_price) }}</span>
                    <span class="text-sm text-gray-400 line-through ml-1">৳{{ number_format($product->price) }}</span>
                @else
                    <span class="text-lg font-bold text-halal-green">৳{{ number_format($product->price) }}</span>
                @endif
            </div>
            
            <!-- Unit -->
            @if($product->unit)
            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                {{ $product->unit }}
            </span>
            @endif
        </div>
        
        <!-- Stock Status -->
        @if($product->stock > 0 && $product->stock <= 5)
        <div class="mt-2 text-xs text-orange-600 flex items-center">
            <i class="bi bi-exclamation-triangle-fill mr-1"></i>
            Only {{ $product->stock }} left in stock!
        </div>
        @endif
    </div>
</div>
