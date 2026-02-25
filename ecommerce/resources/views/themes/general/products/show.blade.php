@extends('themes.general.layouts.app')

@section('title', $product->name)

@push('styles')
<style>
/* Image Zoom Wrapper */
.image-zoom-wrapper {
    position: relative;
}

/* Main Image Container with Hover Zoom */
.image-zoom-container {
    position: relative;
    overflow: hidden;
    cursor: crosshair;
}

.image-zoom-container img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    transition: transform 0.1s ease;
}

/* Hover Zoom Effect */
.image-zoom-container:hover img {
    transform: scale(1.5);
}

/* Zoom Lens (shows area being magnified) */
.zoom-lens {
    position: absolute;
    width: 150px;
    height: 150px;
    background-color: rgba(45, 90, 39, 0.2);
    border: 2px solid #2D5A27;
    border-radius: 50%;
    cursor: crosshair;
    display: none;
    pointer-events: none;
    z-index: 10;
}

.image-zoom-container:hover .zoom-lens {
    display: block;
}

/* Zoom Result Window (side panel showing magnified view) */
.zoom-result-container {
    position: absolute;
    top: 0;
    left: calc(100% + 20px);
    width: 450px;
    height: 400px;
    background-repeat: no-repeat;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    display: none;
    z-index: 100;
    background-size: 200%;
}

.image-zoom-wrapper:hover .zoom-result-container {
    display: block;
}

/* Hide zoom result on mobile */
@media (max-width: 1200px) {
    .zoom-result-container {
        display: none !important;
    }
}

/* Lightbox */
.lightbox-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 9999;
    display: none;
    justify-content: center;
    align-items: center;
    cursor: zoom-out;
}

.lightbox-overlay.active {
    display: flex;
}

.lightbox-overlay img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    border-radius: 8px;
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: white;
    font-size: 40px;
    cursor: pointer;
    z-index: 10000;
}

.lightbox-controls {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 20px;
}

.lightbox-btn {
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}

.lightbox-btn:hover {
    background: rgba(255,255,255,0.3);
}

/* Zoom Controls */
.zoom-controls {
    position: absolute;
    bottom: 10px;
    right: 10px;
    display: flex;
    gap: 5px;
    z-index: 10;
}

.zoom-control-btn {
    background: rgba(255,255,255,0.9);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #333;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.zoom-control-btn:hover {
    background: white;
    transform: scale(1.1);
}

/* Gallery Thumbnails */
.gallery-thumbnails {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.gallery-thumb {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s;
    flex-shrink: 0;
}

.gallery-thumb:hover,
.gallery-thumb.active {
    border-color: #2D5A27;
    transform: scale(1.05);
}

/* Mobile Touch Zoom */
@media (max-width: 768px) {
    .zoom-result-container {
        display: none !important;
    }
    
    .image-zoom-container.zoomed img {
        transform: scale(2.5);
    }
}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">
            <!-- Product Image with Zoom -->
            <div class="relative">
                @php
                    $imagePath = $product->featured_image ?? $product->image ?? '';
                    $imageUrl = 'https://via.placeholder.com/500x500?text=No+Image';
                    if ($imagePath) {
                        if (str_starts_with($imagePath, 'http')) {
                            $imageUrl = $imagePath;
                        } elseif (str_starts_with($imagePath, '/storage/')) {
                            $imageUrl = $imagePath;
                        } elseif (str_starts_with($imagePath, '/uploads/')) {
                            $imageUrl = asset($imagePath);
                        } else {
                            $imageUrl = asset('storage/' . $imagePath);
                        }
                    }
                    
                    // Get gallery images
                    $galleryImages = [];
                    if ($product->gallery && is_array($product->gallery)) {
                        $galleryImages = $product->gallery;
                    } elseif ($product->gallery && is_string($product->gallery)) {
                        $decoded = json_decode($product->gallery, true);
                        $galleryImages = is_array($decoded) ? $decoded : [];
                    }
                @endphp
                
                <div class="image-zoom-wrapper">
                    <div class="image-zoom-container rounded-lg" id="mainImageContainer" onmousemove="handleMouseMove(event)" onmouseleave="handleMouseLeave()">
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="rounded-lg" id="mainProductImage" data-src="{{ $imageUrl }}">
                        <div class="zoom-lens" id="zoomLens"></div>
                        
                        <!-- Zoom Controls -->
                        <div class="zoom-controls">
                            <button class="zoom-control-btn" onclick="event.stopPropagation(); openLightbox()" title="Fullscreen">
                                <i class="bi bi-fullscreen"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Zoom Result Window (shows magnified view) -->
                    <div class="zoom-result-container rounded-lg" id="zoomResult"></div>
                </div>
                
                <!-- Gallery Thumbnails -->
                @if(!empty($galleryImages) || $imagePath)
                <div class="gallery-thumbnails">
                    @if($imagePath)
                    <img src="{{ $imageUrl }}" alt="Main Image" class="gallery-thumb active" onclick="changeImage(this, '{{ $imageUrl }}')">
                    @endif
                    @foreach($galleryImages as $index => $galleryImage)
                        @php
                            $galleryUrl = $galleryImage;
                            if (!str_starts_with($galleryImage, 'http') && !str_starts_with($galleryImage, '/storage/') && !str_starts_with($galleryImage, '/uploads/')) {
                                $galleryUrl = asset('storage/' . $galleryImage);
                            } elseif (str_starts_with($galleryImage, '/uploads/')) {
                                $galleryUrl = asset($galleryImage);
                            }
                        @endphp
                        <img src="{{ $galleryUrl }}" alt="Gallery Image {{ $index + 1 }}" class="gallery-thumb" onclick="changeImage(this, '{{ $galleryUrl }}')">
                    @endforeach
                </div>
                @endif
                
                @if($product->isOnSale())
                <span class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold z-10">
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
                        @php $avgRating = $product->average_rating; @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($avgRating >= $i)
                                <i class="bi bi-star-fill"></i>
                            @elseif($avgRating >= $i - 0.5)
                                <i class="bi bi-star-half"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-gray-500 ml-2">({{ $product->approved_reviews_count ?? 0 }} reviews)</span>
                </div>

                <!-- Price -->
                <div class="mt-4">
                    @if($product->isOnSale())
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

                <!-- Color Selection -->
                @if(isset($colors) && $colors->count() > 0)
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Color: <span id="selectedColorName">Select a color</span></h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($colors as $color)
                        <button type="button" 
                                class="color-option relative w-12 h-12 rounded-full border-2 transition-all hover:scale-110 {{ $loop->first ? 'ring-2 ring-halal-green ring-offset-2' : 'border-gray-300' }}"
                                style="background-color: {{ $color->hex_code }};"
                                data-color-id="{{ $color->id }}"
                                data-color-name="{{ $color->name }}"
                                data-color-hex="{{ $color->hex_code }}"
                                @if($color->pivot->image) data-color-image="{{ asset($color->pivot->image) }}" @endif
                                @if($color->pivot->quantity) data-color-stock="{{ $color->pivot->quantity }}" @endif
                                @if($color->pivot->price_adjustment) data-color-price="{{ $color->pivot->price_adjustment }}" @endif
                                title="{{ $color->name }}">
                            @if($color->pivot->quantity <= 0)
                            <span class="absolute inset-0 flex items-center justify-center">
                                <i class="bi bi-x-lg text-white drop-shadow-lg"></i>
                            </span>
                            @endif
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="selected_color" id="selectedColorId" value="">
                </div>
                @endif

                <!-- Attributes Selection -->
                @if(!empty($attributes))
                <div class="mt-6 space-y-4">
                    @foreach($attributes as $attributeName => $values)
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-3">{{ $attributeName }}: <span id="selected{{ Str::slug($attributeName) }}">Select</span></h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($values as $value)
                            <button type="button" 
                                    class="attribute-option px-4 py-2 border-2 rounded-lg transition-all hover:border-halal-green {{ $loop->first ? 'border-halal-green bg-halal-green/10' : 'border-gray-300' }}"
                                    data-attribute="{{ Str::slug($attributeName) }}"
                                    data-attribute-name="{{ $attributeName }}"
                                    data-value-id="{{ $value->id }}"
                                    data-value="{{ $value->value }}">
                                {{ $value->value }}
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="attribute_{{ Str::slug($attributeName) }}" id="attribute{{ Str::slug($attributeName) }}" value="">
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Add to Cart -->
                <div class="mt-6 flex gap-4">
                    <input type="number" id="quantity" value="1" min="1" max="{{ $product->quantity }}" 
                           class="w-20 border rounded-lg px-3 py-2 text-center">
                    
                    @if($product->quantity > 0)
                    <button onclick="addToCartWithVariants({{ $product->id }}, document.getElementById('quantity').value)" 
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

    <!-- Related Products Section (Moved Before Reviews) -->
    @if($relatedProducts->count() > 0)
    <div class="mt-12 bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="bi bi-grid-3x3-gap text-halal-green me-2"></i>
                Related Products
            </h2>
            <span class="text-sm text-gray-500">{{ $relatedProducts->count() }} products</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            @foreach($relatedProducts as $relatedProduct)
                @include('themes.general.partials.product-card', ['product' => $relatedProduct])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Ask a Question & Write a Review Section -->
    <div class="mt-12 bg-white rounded-xl shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Ask a Question Card -->
            <div class="group relative overflow-hidden rounded-xl border-2 border-gray-100 hover:border-halal-green/30 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-halal-green/5 to-emerald-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-halal-green to-halal-dark rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-halal-green/20">
                            <i class="bi bi-question-circle text-white text-2xl"></i>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Have a Question?</h3>
                            <p class="text-gray-500 text-sm mb-4">Get answers from our community and help other customers make informed decisions.</p>
                            <button type="button" 
                                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-halal-green to-halal-dark text-white rounded-lg hover:shadow-lg hover:shadow-halal-green/30 hover:-translate-y-0.5 transition-all duration-300 font-medium text-sm" 
                                data-bs-toggle="modal" data-bs-target="#askQuestionModal">
                                <i class="bi bi-plus-lg me-2"></i>Ask a Question
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Write a Review Card -->
            <div class="group relative overflow-hidden rounded-xl border-2 border-gray-100 hover:border-amber-300 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-yellow-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-500 via-halal-gold to-yellow-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-amber-300/30">
                            <i class="bi bi-pencil-square text-white text-2xl"></i>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Share Your Experience</h3>
                            <p class="text-gray-500 text-sm mb-4">Help others by sharing your honest review and rating for this product.</p>
                            @auth
                                @php
                                    $hasPurchased = auth()->user()->orders()
                                        ->whereHas('items', function ($q) use ($product) {
                                            $q->where('product_id', $product->id);
                                        })
                                        ->where('status', 'delivered')
                                        ->exists();
                                    $hasReviewed = \App\Models\Review::where('user_id', auth()->id())
                                        ->where('product_id', $product->id)
                                        ->exists();
                                @endphp
                                @if($hasPurchased && !$hasReviewed)
                                    <button type="button" 
                                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-amber-500 via-halal-gold to-yellow-500 text-white rounded-lg hover:shadow-lg hover:shadow-amber-300/30 hover:-translate-y-0.5 transition-all duration-300 font-medium text-sm" 
                                        data-bs-toggle="modal" data-bs-target="#reviewModal">
                                        <i class="bi bi-star me-2"></i>Write a Review
                                    </button>
                                @elseif($hasReviewed)
                                    <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
                                        <i class="bi bi-check-circle me-2"></i>Already Reviewed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm">
                                        <i class="bi bi-info-circle me-2"></i>Purchase required
                                    </span>
                                @endif
                            @else
                                <a href="{{ route('login') }}" 
                                    class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-amber-500 via-halal-gold to-yellow-500 text-white rounded-lg hover:shadow-lg hover:shadow-amber-300/30 hover:-translate-y-0.5 transition-all duration-300 font-medium text-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login to Review
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-12 bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="bi bi-chat-quote text-halal-green me-2"></i>
                Customer Reviews
            </h2>
            <span class="text-sm text-gray-500">{{ $product->approved_reviews_count }} reviews</span>
        </div>
        
        <!-- Rating Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 pb-8 border-b">
            <!-- Overall Rating -->
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <div class="text-5xl font-bold text-halal-green">{{ number_format($product->average_rating, 1) }}</div>
                <div class="flex justify-center mt-2">
                    @php $avgRating = $product->average_rating; @endphp
                    @for($i = 1; $i <= 5; $i++)
                        @if($avgRating >= $i)
                            <i class="bi bi-star-fill text-halal-gold text-xl"></i>
                        @elseif($avgRating >= $i - 0.5)
                            <i class="bi bi-star-half text-halal-gold text-xl"></i>
                        @else
                            <i class="bi bi-star text-halal-gold text-xl"></i>
                        @endif
                    @endfor
                </div>
                <p class="text-gray-500 mt-2">{{ $product->approved_reviews_count }} reviews</p>
            </div>
            
            <!-- Rating Distribution -->
            <div class="col-span-2">
                @php $distribution = $product->rating_distribution; $total = $product->approved_reviews_count; @endphp
                @for($i = 5; $i >= 1; $i--)
                    @php $percentage = $total > 0 ? ($distribution[$i] / $total) * 100 : 0; @endphp
                    <div class="flex items-center mb-2">
                        <span class="w-12 text-sm text-gray-600">{{ $i }} star</span>
                        <div class="flex-1 h-3 bg-gray-200 rounded-full mx-3">
                            <div class="h-3 bg-halal-gold rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="w-10 text-sm text-gray-500">{{ $distribution[$i] }}</span>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Review Status Messages -->
        @auth
            @if($hasReviewed)
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="bi bi-check-circle me-2 text-xl"></i>
                    <span>You have already reviewed this product.</span>
                </div>
            @elseif(!$hasPurchased)
                <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="bi bi-info-circle me-2 text-xl"></i>
                    <span>You can only review products you have purchased and received.</span>
                </div>
            @endif
        @endauth

        <!-- Reviews List -->
        @if(isset($reviews) && $reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="border rounded-xl p-5 border-gray-200 hover:border-gray-300 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-halal-green to-halal-dark rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $review->user->name ?? 'Anonymous' }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex text-halal-gold">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-sm"></i>
                                            @endfor
                                        </div>
                                        <span class="text-gray-400 text-sm">{{ $review->created_at->diffForHumans() }}</span>
                                        @if($review->verified_purchase)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="bi bi-patch-check-fill me-1"></i>Verified Purchase
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($review->user_id === auth()->id())
                                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                        @if($review->title)
                            <h5 class="font-semibold text-gray-800 mt-4">{{ $review->title }}</h5>
                        @endif
                        <p class="text-gray-600 mt-2 leading-relaxed">{{ $review->comment }}</p>
                        
                        <!-- Review Images -->
                        @if($review->images && count($review->images) > 0)
                            <div class="flex gap-2 mt-4 flex-wrap">
                                @foreach($review->images as $image)
                                    <a href="{{ asset($image) }}" target="_blank" class="block">
                                        <img src="{{ asset($image) }}" alt="Review image" class="w-20 h-20 object-cover rounded-lg border hover:opacity-80 transition-opacity">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Helpful Voting -->
                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3">
                            <span class="text-gray-500 text-sm">Was this review helpful?</span>
                            @auth
                                @php
                                    $userVote = $review->getUserVote(auth()->id());
                                @endphp
                                <button type="button" 
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm border {{ $userVote && $userVote->is_helpful ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-300 hover:bg-gray-100' }} transition-colors vote-btn" 
                                        data-review-id="{{ $review->id }}" 
                                        data-is-helpful="1">
                                    <i class="bi bi-hand-thumbs-up{{ $userVote && $userVote->is_helpful ? '-fill' : '' }} me-1"></i>
                                    <span class="helpful-count">{{ $review->helpful_count }}</span>
                                </button>
                                <button type="button" 
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm border {{ $userVote && !$userVote->is_helpful ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-300 hover:bg-gray-100' }} transition-colors vote-btn" 
                                        data-review-id="{{ $review->id }}" 
                                        data-is-helpful="0">
                                    <i class="bi bi-hand-thumbs-down{{ $userVote && !$userVote->is_helpful ? '-fill' : '' }} me-1"></i>
                                    <span class="not-helpful-count">{{ $review->not_helpful_count }}</span>
                                </button>
                            @else
                                <button type="button" class="inline-flex items-center px-3 py-1 rounded-full text-sm border border-gray-200 text-gray-400 cursor-not-allowed" disabled title="Login to vote">
                                    <i class="bi bi-hand-thumbs-up me-1"></i>{{ $review->helpful_count }}
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-1 rounded-full text-sm border border-gray-200 text-gray-400 cursor-not-allowed" disabled title="Login to vote">
                                    <i class="bi bi-hand-thumbs-down me-1"></i>{{ $review->not_helpful_count }}
                                </button>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if(isset($reviews) && $reviews->hasPages())
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-xl">
                <i class="bi bi-chat-square-text text-5xl text-gray-300"></i>
                <p class="mt-3 text-lg">No reviews yet. Be the first to review this product!</p>
            </div>
        @endif
    </div>

    <!-- Product Q&A Section -->
    <div class="mt-12 bg-white rounded-xl shadow-md p-6" id="qa-section">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="bi bi-question-circle text-halal-green me-2"></i>
                Questions & Answers
            </h2>
            @php
                $qaCount = \App\Models\ProductQA::where('product_id', $product->id)
                    ->where('status', 'published')
                    ->count();
            @endphp
            <span class="text-sm text-gray-500">{{ $qaCount }} questions</span>
        </div>
        
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="bi bi-check-circle me-2 text-xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif
        
        @php
            $qaEntries = \App\Models\ProductQA::where('product_id', $product->id)
                ->where('status', 'published')
                ->with(['user', 'answerer'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('helpful_count', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        @endphp
        
        @if($qaEntries->count() > 0)
            <div class="space-y-4">
                @foreach($qaEntries as $qa)
                    <div class="border rounded-xl p-5 {{ $qa->is_featured ? 'border-halal-green bg-green-50' : 'border-gray-200' }}">
                        <!-- Question -->
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-halal-green rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                <i class="bi bi-question-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-800">{{ $qa->questioner_name }}</span>
                                    @if($qa->is_featured)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-halal-green text-white">
                                            <i class="bi bi-star-fill me-1"></i>Featured
                                        </span>
                                    @endif
                                    <span class="text-gray-400 text-sm ml-auto">{{ $qa->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700">{{ $qa->question }}</p>
                            </div>
                        </div>
                        
                        <!-- Answer -->
                        @if($qa->answer)
                            <div class="flex items-start gap-3 mt-4 ml-4 pl-4 border-l-4 border-halal-green">
                                <div class="w-10 h-10 bg-halal-dark rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-800">{{ $qa->answerer?->name ?? 'Store Admin' }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Answered
                                        </span>
                                        <span class="text-gray-400 text-sm ml-auto">{{ $qa->answered_at?->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-600 mb-3">{{ $qa->answer }}</p>
                                    
                                    <!-- Helpful Voting -->
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-500 text-sm">Was this helpful?</span>
                                        <button type="button" class="inline-flex items-center px-3 py-1 rounded-full text-sm border border-gray-300 hover:bg-gray-100 transition-colors qa-vote-btn" data-qa-id="{{ $qa->id }}" data-is-helpful="1">
                                            <i class="bi bi-hand-thumbs-up me-1"></i>{{ $qa->helpful_count }}
                                        </button>
                                        <button type="button" class="inline-flex items-center px-3 py-1 rounded-full text-sm border border-gray-300 hover:bg-gray-100 transition-colors qa-vote-btn" data-qa-id="{{ $qa->id }}" data-is-helpful="0">
                                            <i class="bi bi-hand-thumbs-down me-1"></i>{{ $qa->not_helpful_count }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($qaEntries->hasPages())
            <div class="mt-6">
                {{ $qaEntries->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-xl">
                <i class="bi bi-chat-dots text-5xl text-gray-300"></i>
                <p class="mt-3 text-lg">No questions yet. Be the first to ask!</p>
            </div>
        @endif
    </div>
</div>

<!-- Ask Question Modal - Modern Design -->
<div class="modal fade" id="askQuestionModal" tabindex="-1" aria-labelledby="askQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden" style="max-width: 480px; margin: 1.75rem auto;">
            <!-- Header with gradient -->
            <div class="modal-header border-0 bg-gradient-to-r from-halal-green to-halal-dark px-5 py-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="bi bi-question-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white font-bold text-lg mb-0" id="askQuestionModalLabel">
                            Ask a Question
                        </h5>
                        <p class="text-white/70 text-sm mb-0">Get answers from our community</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('product-qa.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-body p-5">
                    <!-- Product Preview -->
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl mb-4">
                        <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200">
                            @if($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="bi bi-box text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Asking about</p>
                            <h6 class="font-semibold text-gray-800 mb-0 truncate">{{ $product->name }}</h6>
                        </div>
                    </div>
                    
                    <!-- Question Input -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Your Question <span class="text-red-500">*</span>
                        </label>
                        <textarea name="question" 
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-halal-green/20 focus:border-halal-green transition-all duration-300 resize-none text-gray-700 text-sm" 
                            rows="3" 
                            placeholder="What would you like to know about this product?" 
                            required></textarea>
                        <div class="flex items-center justify-between mt-1.5">
                            <p class="text-xs text-gray-400"><i class="bi bi-lightbulb me-1"></i>Be specific for better answers</p>
                            <span class="text-xs text-gray-400 question-char-count">0/500</span>
                        </div>
                    </div>
                    
                    @auth
                        <!-- Anonymous Toggle -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-halal-green/10 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-incognito text-halal-green"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700 text-sm mb-0">Post Anonymously</p>
                                    <p class="text-xs text-gray-500 mb-0">Your name won't be visible</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_anonymous" class="sr-only peer" id="isAnonymous" value="1">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-halal-green/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-halal-green"></div>
                            </label>
                        </div>
                    @else
                        <!-- Guest User Fields -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Your Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="questioner_name" 
                                    class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-halal-green/20 focus:border-halal-green transition-all duration-300 text-sm" 
                                    placeholder="Enter your name" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Email <span class="text-gray-400 text-xs font-normal">(optional)</span>
                                </label>
                                <input type="email" name="questioner_email" 
                                    class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-halal-green/20 focus:border-halal-green transition-all duration-300 text-sm" 
                                    placeholder="your@email.com">
                                <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                                    <i class="bi bi-bell-fill text-halal-gold"></i>
                                    We'll notify you when answered
                                </p>
                            </div>
                        </div>
                    @endauth
                </div>
                
                <!-- Footer -->
                <div class="modal-footer border-0 bg-gray-50 px-5 py-4">
                    <div class="flex items-center justify-end gap-3 w-full">
                        <button type="button" class="px-5 py-2.5 border-2 border-gray-300 text-gray-600 rounded-xl hover:bg-gray-100 transition-all duration-300 font-medium text-sm" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-halal-green to-halal-dark text-white rounded-xl hover:shadow-lg hover:shadow-halal-green/20 transition-all duration-300 font-semibold text-sm flex items-center gap-2">
                            <i class="bi bi-send"></i>
                            Submit Question
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Modal - Modern Design -->
@auth
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden" style="max-width: 480px; margin: 1.75rem auto;">
            <!-- Header with gradient -->
            <div class="modal-header border-0 bg-gradient-to-r from-amber-500 via-halal-gold to-yellow-500 px-5 py-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="bi bi-pencil-square text-white text-xl"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white font-bold text-lg mb-0" id="reviewModalLabel">
                            Write a Review
                        </h5>
                        <p class="text-white/70 text-sm mb-0">Share your experience with others</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-body p-5">
                    <!-- Product Preview -->
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl mb-4">
                        <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200">
                            @if($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="bi bi-box text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Reviewing</p>
                            <h6 class="font-semibold text-gray-800 mb-0 truncate">{{ $product->name }}</h6>
                        </div>
                    </div>
                    
                    <!-- Star Rating -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Your Rating <span class="text-red-500">*</span>
                        </label>
                        <div class="star-rating-container p-3 bg-amber-50 rounded-xl border border-amber-100">
                            <div class="star-rating flex justify-center gap-2" id="starRating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" class="star-btn w-10 h-10 rounded-lg border-2 border-gray-200 hover:border-halal-gold hover:bg-yellow-100 hover:scale-110 transition-all duration-300 flex items-center justify-center group" data-rating="{{ $i }}">
                                        <i class="bi bi-star text-gray-300 group-hover:text-halal-gold text-lg transition-colors"></i>
                                    </button>
                                @endfor
                            </div>
                            <p class="text-center text-sm text-gray-500 mt-2" id="ratingText">Click to rate</p>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="" required>
                        @error('rating')
                            <div class="text-red-500 text-sm mt-1.5 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Review Title -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Review Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" 
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-amber-200 focus:border-halal-gold transition-all duration-300 text-sm" 
                            placeholder="Summarize your experience" required>
                        @error('title')
                            <div class="text-red-500 text-sm mt-1.5 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Review Comment -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Your Review <span class="text-red-500">*</span>
                        </label>
                        <textarea name="comment" 
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-200 focus:border-halal-gold transition-all duration-300 resize-none text-sm" 
                            rows="3" 
                            placeholder="What did you like or dislike?" 
                            required></textarea>
                        <div class="flex items-center justify-between mt-1.5">
                            <p class="text-xs text-gray-400"><i class="bi bi-lightbulb me-1"></i>Detailed reviews help others</p>
                            <span class="text-xs text-gray-400 review-char-count">0/1000</span>
                        </div>
                        @error('comment')
                            <div class="text-red-500 text-sm mt-1.5 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <!-- Review Guidelines -->
                    <div class="p-3 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-xs font-medium text-blue-800 mb-1">
                            <i class="bi bi-info-circle me-1"></i>Guidelines
                        </p>
                        <ul class="text-xs text-blue-600 space-y-0.5 mb-0">
                            <li><i class="bi bi-check2 me-1"></i>Be honest and specific</li>
                            <li><i class="bi bi-check2 me-1"></i>Avoid offensive content</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="modal-footer border-0 bg-gray-50 px-5 py-4">
                    <div class="flex items-center justify-end gap-3 w-full">
                        <button type="button" class="px-5 py-2.5 border-2 border-gray-300 text-gray-600 rounded-xl hover:bg-gray-100 transition-all duration-300 font-medium text-sm" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 via-halal-gold to-yellow-500 text-white rounded-xl hover:shadow-lg hover:shadow-amber-300/20 transition-all duration-300 font-semibold text-sm flex items-center gap-2">
                            <i class="bi bi-send"></i>
                            Submit Review
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

@push('scripts')
<script>
// Star Rating Functionality - Enhanced
document.addEventListener('DOMContentLoaded', function() {
    const starButtons = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('ratingInput');
    const ratingText = document.getElementById('ratingText');
    
    const ratingTexts = {
        0: 'Click to rate',
        1: 'Poor - Not satisfied',
        2: 'Fair - Below expectations',
        3: 'Good - Met expectations',
        4: 'Very Good - Above expectations',
        5: 'Excellent - Highly recommend!'
    };
    
    function updateStars(rating, isHover = false) {
        starButtons.forEach((starBtn, starIndex) => {
            const icon = starBtn.querySelector('i');
            if (starIndex < rating) {
                icon.classList.remove('bi-star', 'text-gray-300');
                icon.classList.add('bi-star-fill', 'text-halal-gold');
                starBtn.classList.add('border-halal-gold', 'bg-yellow-50');
                starBtn.classList.remove('border-gray-200');
            } else {
                icon.classList.remove('bi-star-fill', 'text-halal-gold');
                icon.classList.add('bi-star', 'text-gray-300');
                starBtn.classList.remove('border-halal-gold', 'bg-yellow-50');
                starBtn.classList.add('border-gray-200');
            }
        });
        
        if (ratingText) {
            ratingText.textContent = ratingTexts[rating];
            if (rating > 0) {
                ratingText.classList.add('font-medium', 'text-halal-gold');
                ratingText.classList.remove('text-gray-500');
            } else {
                ratingText.classList.remove('font-medium', 'text-halal-gold');
                ratingText.classList.add('text-gray-500');
            }
        }
    }
    
    starButtons.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            updateStars(rating);
        });
        
        // Hover effect
        btn.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            updateStars(rating, true);
        });
    });
    
    // Reset on mouse leave
    const starRatingContainer = document.getElementById('starRating');
    if (starRatingContainer) {
        starRatingContainer.addEventListener('mouseleave', function() {
            const currentRating = parseInt(ratingInput.value) || 0;
            updateStars(currentRating);
        });
    }
    
    // Character count for question textarea
    const questionTextarea = document.querySelector('textarea[name="question"]');
    if (questionTextarea) {
        questionTextarea.addEventListener('input', function() {
            const count = this.value.length;
            const countElement = document.querySelector('.question-char-count');
            if (countElement) {
                countElement.textContent = `${count}/500`;
                if (count > 500) {
                    countElement.classList.add('text-red-500');
                } else {
                    countElement.classList.remove('text-red-500');
                }
            }
        });
    }
    
    // Character count for review textarea
    const reviewTextarea = document.querySelector('textarea[name="comment"]');
    if (reviewTextarea) {
        reviewTextarea.addEventListener('input', function() {
            const count = this.value.length;
            const countElement = document.querySelector('.review-char-count');
            if (countElement) {
                countElement.textContent = `${count}/1000`;
                if (count > 1000) {
                    countElement.classList.add('text-red-500');
                } else {
                    countElement.classList.remove('text-red-500');
                }
            }
        });
    }
});

// Review Voting Functionality
document.querySelectorAll('.vote-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const reviewId = this.dataset.reviewId;
        const isHelpful = this.dataset.isHelpful;
        const reviewCard = this.closest('.border.rounded-xl');
        
        fetch(`/reviews/${reviewId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_helpful: isHelpful === '1' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update counts
                reviewCard.querySelector('.helpful-count').textContent = data.helpful_count;
                reviewCard.querySelector('.not-helpful-count').textContent = data.not_helpful_count;
                
                // Update button states with Tailwind classes
                const helpfulBtn = reviewCard.querySelector('[data-is-helpful="1"]');
                const notHelpfulBtn = reviewCard.querySelector('[data-is-helpful="0"]');
                
                if (isHelpful === '1') {
                    // Helpful button selected
                    helpfulBtn.classList.remove('border-gray-300', 'hover:bg-gray-100');
                    helpfulBtn.classList.add('border-green-500', 'bg-green-50', 'text-green-700');
                    helpfulBtn.querySelector('i').classList.remove('bi-hand-thumbs-up');
                    helpfulBtn.querySelector('i').classList.add('bi-hand-thumbs-up-fill');
                    
                    // Reset not helpful button
                    notHelpfulBtn.classList.remove('border-red-500', 'bg-red-50', 'text-red-700');
                    notHelpfulBtn.classList.add('border-gray-300', 'hover:bg-gray-100');
                    notHelpfulBtn.querySelector('i').classList.remove('bi-hand-thumbs-down-fill');
                    notHelpfulBtn.querySelector('i').classList.add('bi-hand-thumbs-down');
                } else {
                    // Not helpful button selected
                    notHelpfulBtn.classList.remove('border-gray-300', 'hover:bg-gray-100');
                    notHelpfulBtn.classList.add('border-red-500', 'bg-red-50', 'text-red-700');
                    notHelpfulBtn.querySelector('i').classList.remove('bi-hand-thumbs-down');
                    notHelpfulBtn.querySelector('i').classList.add('bi-hand-thumbs-down-fill');
                    
                    // Reset helpful button
                    helpfulBtn.classList.remove('border-green-500', 'bg-green-50', 'text-green-700');
                    helpfulBtn.classList.add('border-gray-300', 'hover:bg-gray-100');
                    helpfulBtn.querySelector('i').classList.remove('bi-hand-thumbs-up-fill');
                    helpfulBtn.querySelector('i').classList.add('bi-hand-thumbs-up');
                }
                
                // Show toast notification
                showToast(data.message, 'success');
            }
        })
        .catch(error => {
            showToast('An error occurred. Please try again.', 'error');
        });
    });
});

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} shadow-lg z-50 animate-fade-in`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Q&A Voting Functionality
document.querySelectorAll('.qa-vote-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const qaId = this.dataset.qaId;
        const isHelpful = this.dataset.isHelpful;
        const qaCard = this.closest('.border.rounded-xl');
        
        fetch(`/product-qa/${qaId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_helpful: isHelpful === '1' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button text and states
                const helpfulBtn = qaCard.querySelector('[data-is-helpful="1"]');
                const notHelpfulBtn = qaCard.querySelector('[data-is-helpful="0"]');
                
                helpfulBtn.innerHTML = `<i class="bi bi-hand-thumbs-up${isHelpful === '1' ? '-fill' : ''} me-1"></i>${data.helpful_count}`;
                notHelpfulBtn.innerHTML = `<i class="bi bi-hand-thumbs-down${isHelpful === '0' ? '-fill' : ''} me-1"></i>${data.not_helpful_count}`;
                
                // Update button styles
                if (isHelpful === '1') {
                    helpfulBtn.classList.remove('border-gray-300', 'hover:bg-gray-100');
                    helpfulBtn.classList.add('border-green-500', 'bg-green-50', 'text-green-700');
                    notHelpfulBtn.classList.remove('border-red-500', 'bg-red-50', 'text-red-700');
                    notHelpfulBtn.classList.add('border-gray-300', 'hover:bg-gray-100');
                } else {
                    notHelpfulBtn.classList.remove('border-gray-300', 'hover:bg-gray-100');
                    notHelpfulBtn.classList.add('border-red-500', 'bg-red-50', 'text-red-700');
                    helpfulBtn.classList.remove('border-green-500', 'bg-green-50', 'text-green-700');
                    helpfulBtn.classList.add('border-gray-300', 'hover:bg-gray-100');
                }
                
                showToast('Thank you for your feedback!', 'success');
            }
        })
        .catch(error => {
            showToast('An error occurred. Please try again.', 'error');
        });
    });
});
</script>
@endpush

<!-- Lightbox Overlay -->
<div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img src="" alt="Product Image" id="lightboxImage">
    <div class="lightbox-controls">
        <button class="lightbox-btn" onclick="event.stopPropagation(); lightboxZoomIn()">
            <i class="bi bi-zoom-in"></i> Zoom In
        </button>
        <button class="lightbox-btn" onclick="event.stopPropagation(); lightboxZoomOut()">
            <i class="bi bi-zoom-out"></i> Zoom Out
        </button>
    </div>
</div>

@push('scripts')
<script>
let lightboxZoom = 1;

// Mouse move handler for hover zoom
function handleMouseMove(e) {
    const container = document.getElementById('mainImageContainer');
    const img = document.getElementById('mainProductImage');
    const lens = document.getElementById('zoomLens');
    const result = document.getElementById('zoomResult');
    
    const rect = container.getBoundingClientRect();
    
    // Calculate cursor position relative to the image
    let x = e.clientX - rect.left;
    let y = e.clientY - rect.top;
    
    // Calculate percentage position
    let xPercent = (x / rect.width) * 100;
    let yPercent = (y / rect.height) * 100;
    
    // Set transform origin to cursor position
    img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
    
    // Position the lens
    const lensSize = 150;
    let lensX = x - lensSize / 2;
    let lensY = y - lensSize / 2;
    
    // Keep lens within bounds
    lensX = Math.max(0, Math.min(lensX, rect.width - lensSize));
    lensY = Math.max(0, Math.min(lensY, rect.height - lensSize));
    
    lens.style.left = lensX + 'px';
    lens.style.top = lensY + 'px';
    
    // Update zoom result background position
    result.style.backgroundImage = `url(${img.src})`;
    result.style.backgroundPosition = `${xPercent}% ${yPercent}%`;
}

// Mouse leave handler
function handleMouseLeave() {
    const img = document.getElementById('mainProductImage');
    img.style.transformOrigin = 'center center';
}

// Change main image from gallery
function changeImage(thumb, imageUrl) {
    const mainImage = document.getElementById('mainProductImage');
    mainImage.src = imageUrl;
    mainImage.dataset.src = imageUrl;
    
    // Update active thumbnail
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

// Open lightbox
function openLightbox() {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightboxImage');
    const mainImage = document.getElementById('mainProductImage');
    
    lightboxImg.src = mainImage.dataset.src || mainImage.src;
    lightbox.classList.add('active');
    lightboxZoom = 1;
    lightboxImg.style.transform = `scale(${lightboxZoom})`;
    document.body.style.overflow = 'hidden';
}

// Close lightbox
function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
}

// Lightbox zoom in
function lightboxZoomIn() {
    const lightboxImg = document.getElementById('lightboxImage');
    if (lightboxZoom < 4) {
        lightboxZoom += 0.5;
        lightboxImg.style.transform = `scale(${lightboxZoom})`;
    }
}

// Lightbox zoom out
function lightboxZoomOut() {
    const lightboxImg = document.getElementById('lightboxImage');
    if (lightboxZoom > 1) {
        lightboxZoom -= 0.5;
        lightboxImg.style.transform = `scale(${lightboxZoom})`;
    }
}

// Keyboard controls
document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('lightbox');
    
    if (lightbox.classList.contains('active')) {
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === '+' || e.key === '=') {
            lightboxZoomIn();
        } else if (e.key === '-') {
            lightboxZoomOut();
        }
    }
});

// Color Selection
document.querySelectorAll('.color-option').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove selection from all colors
        document.querySelectorAll('.color-option').forEach(b => {
            b.classList.remove('ring-2', 'ring-halal-green', 'ring-offset-2');
        });
        
        // Add selection to clicked color
        this.classList.add('ring-2', 'ring-halal-green', 'ring-offset-2');
        
        // Update selected color
        const colorId = this.dataset.colorId;
        const colorName = this.dataset.colorName;
        document.getElementById('selectedColorId').value = colorId;
        document.getElementById('selectedColorName').textContent = colorName;
        
        // Update main image if color has specific image
        if (this.dataset.colorImage) {
            changeImage(this, this.dataset.colorImage);
        }
        
        // Update stock if color has specific stock
        if (this.dataset.colorStock !== undefined) {
            const stock = parseInt(this.dataset.colorStock);
            const stockElement = document.querySelector('.text-green-600, .text-red-600');
            if (stockElement) {
                if (stock > 0) {
                    stockElement.className = 'text-green-600';
                    stockElement.innerHTML = `<i class="bi bi-check-circle-fill mr-1"></i>In Stock (${stock} available)`;
                } else {
                    stockElement.className = 'text-red-600';
                    stockElement.innerHTML = `<i class="bi bi-x-circle-fill mr-1"></i>Out of Stock`;
                }
            }
        }
        
        // Update price if color has price adjustment
        if (this.dataset.colorPrice) {
            const adjustment = parseFloat(this.dataset.colorPrice);
            const basePrice = {{ $product->price }};
            const baseSalePrice = {{ $product->sale_price ?? $product->price }};
            
            const newPrice = basePrice + adjustment;
            const newSalePrice = baseSalePrice + adjustment;
            
            const priceContainer = document.querySelector('.mt-4 .text-3xl');
            if (priceContainer) {
                @if($product->isOnSale())
                priceContainer.textContent = '৳' + number_format(newSalePrice);
                const originalPrice = priceContainer.nextElementSibling;
                if (originalPrice && originalPrice.classList.contains('line-through')) {
                    originalPrice.textContent = '৳' + number_format(newPrice);
                }
                @else
                priceContainer.textContent = '৳' + number_format(newPrice);
                @endif
            }
        }
    });
});

// Attribute Selection
document.querySelectorAll('.attribute-option').forEach(btn => {
    btn.addEventListener('click', function() {
        const attribute = this.dataset.attribute;
        const attributeName = this.dataset.attributeName;
        const valueId = this.dataset.valueId;
        const value = this.dataset.value;
        
        // Remove selection from all options in this attribute group
        document.querySelectorAll(`.attribute-option[data-attribute="${attribute}"]`).forEach(b => {
            b.classList.remove('border-halal-green', 'bg-halal-green/10');
            b.classList.add('border-gray-300');
        });
        
        // Add selection to clicked option
        this.classList.remove('border-gray-300');
        this.classList.add('border-halal-green', 'bg-halal-green/10');
        
        // Update hidden input
        const hiddenInput = document.getElementById('attribute' + attribute.charAt(0).toUpperCase() + attribute.slice(1));
        if (hiddenInput) {
            hiddenInput.value = valueId;
        }
        
        // Update selected text
        const selectedText = document.getElementById('selected' + attribute.charAt(0).toUpperCase() + attribute.slice(1));
        if (selectedText) {
            selectedText.textContent = value;
        }
    });
});

// Enhanced addToCart with color and attributes
function addToCartWithVariants(productId, quantity) {
    const colorId = document.getElementById('selectedColorId')?.value || null;
    
    // Collect all selected attributes
    const attributes = {};
    document.querySelectorAll('[id^="attribute"][type="hidden"]').forEach(input => {
        if (input.value) {
            attributes[input.name] = input.value;
        }
    });
    
    // Call original addToCart with additional data
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity,
            color_id: colorId,
            attributes: attributes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product added to cart!', 'success');
            // Update cart count if exists
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
        } else {
            showToast(data.message || 'Error adding to cart', 'error');
        }
    })
    .catch(error => {
        showToast('An error occurred. Please try again.', 'error');
    });
}
</script>
@endpush
@endsection
