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
                
                @if($product->sale_price)
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

    <!-- Reviews Section -->
    <div class="mt-12 bg-white rounded-xl shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Customer Reviews</h2>
        
        <!-- Rating Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 pb-8 border-b">
            <!-- Overall Rating -->
            <div class="text-center">
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
                            <div class="h-3 bg-halal-gold rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="w-10 text-sm text-gray-500">{{ $distribution[$i] }}</span>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Write Review Button -->
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
                <button type="button" class="btn btn-halal-green mb-6" data-bs-toggle="modal" data-bs-target="#reviewModal">
                    <i class="bi bi-pencil-square me-2"></i>Write a Review
                </button>
            @elseif($hasReviewed)
                <div class="alert alert-info mb-6">
                    <i class="bi bi-check-circle me-2"></i>You have already reviewed this product.
                </div>
            @else
                <div class="alert alert-warning mb-6">
                    <i class="bi bi-info-circle me-2"></i>You can only review products you have purchased and received.
                </div>
            @endif
        @else
            <div class="alert alert-info mb-6">
                <i class="bi bi-person me-2"></i>Please <a href="{{ route('login') }}" class="text-halal-green font-medium hover:underline">login</a> to write a review.
            </div>
        @endauth

        <!-- Reviews List -->
        @if(isset($reviews) && $reviews->count() > 0)
            <div class="space-y-6">
                @foreach($reviews as $review)
                    <div class="border-b pb-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-halal-green rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-800">{{ $review->user->name ?? 'Anonymous' }}</h4>
                                    <div class="flex items-center mt-1">
                                        <div class="flex text-halal-gold">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-sm"></i>
                                            @endfor
                                        </div>
                                        <span class="text-gray-400 text-sm ml-2">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            @if($review->user_id === auth()->id())
                                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                        @if($review->title)
                            <h5 class="font-medium text-gray-800 mt-3">{{ $review->title }}</h5>
                        @endif
                        <p class="text-gray-600 mt-2">{{ $review->comment }}</p>
                        
                        <!-- Helpful Voting -->
                        <div class="mt-4 flex items-center gap-4">
                            <span class="text-sm text-gray-500">Was this review helpful?</span>
                            @auth
                                @php
                                    $userVote = $review->getUserVote(auth()->id());
                                @endphp
                                <button type="button" 
                                        class="btn btn-sm {{ $userVote && $userVote->is_helpful ? 'btn-success' : 'btn-outline-secondary' }} vote-btn" 
                                        data-review-id="{{ $review->id }}" 
                                        data-is-helpful="1">
                                    <i class="bi bi-hand-thumbs-up{{ $userVote && $userVote->is_helpful ? '-fill' : '' }} me-1"></i>
                                    <span class="helpful-count">{{ $review->helpful_count }}</span>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm {{ $userVote && !$userVote->is_helpful ? 'btn-danger' : 'btn-outline-secondary' }} vote-btn" 
                                        data-review-id="{{ $review->id }}" 
                                        data-is-helpful="0">
                                    <i class="bi bi-hand-thumbs-down{{ $userVote && !$userVote->is_helpful ? '-fill' : '' }} me-1"></i>
                                    <span class="not-helpful-count">{{ $review->not_helpful_count }}</span>
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Login to vote">
                                    <i class="bi bi-hand-thumbs-up me-1"></i>{{ $review->helpful_count }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Login to vote">
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
            <div class="text-center py-8 text-gray-500">
                <i class="bi bi-chat-square-text text-4xl"></i>
                <p class="mt-2">No reviews yet. Be the first to review this product!</p>
            </div>
        @endif
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

<!-- Review Modal -->
@auth
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-body">
                    <!-- Star Rating -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Rating <span class="text-danger">*</span></label>
                        <div class="star-rating" id="starRating">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="btn star-btn p-0" data-rating="{{ $i }}">
                                    <i class="bi bi-star text-halal-gold fs-4"></i>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="" required>
                        @error('rating')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Review Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Summarize your review" required>
                        @error('title')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Comment -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Your Review <span class="text-danger">*</span></label>
                        <textarea name="comment" class="form-control" rows="4" placeholder="What did you like or dislike about this product?" required></textarea>
                        @error('comment')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-halal-green">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Star Rating Functionality
document.addEventListener('DOMContentLoaded', function() {
    const starButtons = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('ratingInput');
    
    starButtons.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            const rating = this.dataset.rating;
            ratingInput.value = rating;
            
            // Update star display
            starButtons.forEach((starBtn, starIndex) => {
                const icon = starBtn.querySelector('i');
                if (starIndex < rating) {
                    icon.classList.remove('bi-star');
                    icon.classList.add('bi-star-fill');
                } else {
                    icon.classList.remove('bi-star-fill');
                    icon.classList.add('bi-star');
                }
            });
        });
        
        // Hover effect
        btn.addEventListener('mouseenter', function() {
            const rating = this.dataset.rating;
            starButtons.forEach((starBtn, starIndex) => {
                const icon = starBtn.querySelector('i');
                if (starIndex < rating) {
                    icon.classList.remove('bi-star');
                    icon.classList.add('bi-star-fill');
                } else {
                    icon.classList.remove('bi-star-fill');
                    icon.classList.add('bi-star');
                }
            });
        });
    });
    
    // Reset on mouse leave
    document.getElementById('starRating').addEventListener('mouseleave', function() {
        const currentRating = parseInt(ratingInput.value) || 0;
        starButtons.forEach((starBtn, starIndex) => {
            const icon = starBtn.querySelector('i');
            if (starIndex < currentRating) {
                icon.classList.remove('bi-star');
                icon.classList.add('bi-star-fill');
            } else {
                icon.classList.remove('bi-star-fill');
                icon.classList.add('bi-star');
            }
        });
    });
});

// Review Voting Functionality
document.querySelectorAll('.vote-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const reviewId = this.dataset.reviewId;
        const isHelpful = this.dataset.isHelpful;
        const reviewCard = this.closest('.border-b');
        
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
                
                // Update button states
                const helpfulBtn = reviewCard.querySelector('[data-is-helpful="1"]');
                const notHelpfulBtn = reviewCard.querySelector('[data-is-helpful="0"]');
                
                if (isHelpful === '1') {
                    helpfulBtn.classList.remove('btn-outline-secondary');
                    helpfulBtn.classList.add('btn-success');
                    helpfulBtn.querySelector('i').classList.add('bi-hand-thumbs-up-fill');
                    helpfulBtn.querySelector('i').classList.remove('bi-hand-thumbs-up');
                    
                    notHelpfulBtn.classList.remove('btn-danger');
                    notHelpfulBtn.classList.add('btn-outline-secondary');
                    notHelpfulBtn.querySelector('i').classList.remove('bi-hand-thumbs-down-fill');
                    notHelpfulBtn.querySelector('i').classList.add('bi-hand-thumbs-down');
                } else {
                    notHelpfulBtn.classList.remove('btn-outline-secondary');
                    notHelpfulBtn.classList.add('btn-danger');
                    notHelpfulBtn.querySelector('i').classList.add('bi-hand-thumbs-down-fill');
                    notHelpfulBtn.querySelector('i').classList.remove('bi-hand-thumbs-down');
                    
                    helpfulBtn.classList.remove('btn-success');
                    helpfulBtn.classList.add('btn-outline-secondary');
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
</script>
@endpush
@endauth

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
</script>
@endpush
@endsection
