@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Product: {{ $product->name }}</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="product-form">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="2" maxlength="500">{{ old('short_description', $product->short_description) }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $product->long_description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Regular Price (৳) *</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_price" class="form-label">Sale Price (৳)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
                                @error('sale_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU *</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="product_code" class="form-label">Product Code</label>
                                <input type="text" class="form-control @error('product_code') is-invalid @enderror" id="product_code" name="product_code" value="{{ old('product_code', $product->product_code) }}" placeholder="e.g., PRD-001">
                                @error('product_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="Product barcode">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="Product brand">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">Purchase/Cost Price (৳)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" placeholder="Cost price">
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Used for profit calculation</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock Quantity *</label>
                                <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->quantity) }}" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="low_stock_threshold" class="form-label">Low Stock Alert</label>
                                <input type="number" min="0" class="form-control @error('low_stock_threshold') is-invalid @enderror" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 10) }}">
                                @error('low_stock_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Alert when stock falls below this</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Stock Value</label>
                                <div class="form-control-plaintext fw-semibold">
                                    ৳{{ number_format($product->stock_value, 0) }}
                                </div>
                                <small class="text-muted">Quantity × Cost Price</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Source</label>
                                <div class="form-control-plaintext">
                                    @if($product->isInHouse())
                                        <span class="badge bg-primary"><i class="bi bi-house-door me-1"></i> In-House Product</span>
                                    @else
                                        <span class="badge bg-info"><i class="bi bi-shop me-1"></i> Seller Product</span>
                                        @if($product->seller)
                                            <small class="text-muted ms-2">by {{ $product->seller->name }}</small>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Profit Margin</label>
                                <div class="form-control-plaintext">
                                    @if($product->profit_margin > 0)
                                        <span class="text-success fw-semibold">{{ $product->profit_margin }}%</span>
                                        <small class="text-muted">(৳{{ number_format($product->profit_amount, 0) }} per unit)</small>
                                    @else
                                        <span class="text-muted">Set purchase price to calculate</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category *</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ old('category_id', $product->category_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Featured Image</label>
                        @if($product->featured_image)
                            @php
                                $imageUrl = $product->featured_image;
                                // Handle different image path formats
                                if (!str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/storage/')) {
                                    $imageUrl = '/storage/' . $imageUrl;
                                }
                            @endphp
                            <div class="mb-2">
                                <img src="{{ $imageUrl }}" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="imagePreview" class="mt-2"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="images" class="form-label">Gallery Images</label>
                        @if($product->images && is_array(json_decode($product->images)))
                            <div class="mb-2 d-flex flex-wrap gap-2">
                                @foreach(json_decode($product->images) as $index => $img)
                                    @php
                                        $galleryUrl = $img;
                                        if (!str_starts_with($galleryUrl, 'http') && !str_starts_with($galleryUrl, '/storage/')) {
                                            $galleryUrl = '/storage/' . $galleryUrl;
                                        }
                                    @endphp
                                    <div class="position-relative">
                                        <img src="{{ $galleryUrl }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        <form action="{{ route('admin.products.images.delete', [$product->id, $index]) }}" method="POST" class="d-inline position-absolute top-0 start-100 translate-middle">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="badge bg-danger rounded-circle border-0" 
                                               style="font-size: 10px; width: 18px; height: 18px; line-height: 1;"
                                               onclick="return confirm('Delete this image?')">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Featured Product
                            </label>
                        </div>
                    </div>
                    
                    <!-- Related Products Quick Access -->
                    <div class="mb-3">
                        <label class="form-label">Related Products</label>
                        <div class="card border">
                            <div class="card-body py-2">
                                @php
                                    $relatedProducts = $product->relatedProducts()->limit(5)->get();
                                    $relatedCount = $product->relatedProducts()->count();
                                @endphp
                                @if($relatedCount > 0)
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-primary me-2">{{ $relatedCount }}</span>
                                        <span class="small text-muted">related product(s)</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        @foreach($relatedProducts as $rel)
                                            @php
                                                $relImages = is_string($rel->images) ? json_decode($rel->images, true) : $rel->images;
                                                $relImage = $rel->featured_image ?? ($relImages[0] ?? null);
                                            @endphp
                                            <img src="{{ $relImage ?? asset('images/placeholder.png') }}" 
                                                 alt="{{ $rel->name }}" 
                                                 class="rounded" 
                                                 style="width: 40px; height: 40px; object-fit: cover;"
                                                 title="{{ $rel->name }}">
                                        @endforeach
                                        @if($relatedCount > 5)
                                            <span class="badge bg-light text-dark" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                +{{ $relatedCount - 5 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-muted small mb-2">No related products added yet.</p>
                                @endif
                                <a href="{{ route('admin.products.related', $product->id) }}" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-diagram-3 me-1"></i> Manage Related Products
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Floating Buttons - Following Preference.md standard -->
<div class="floating-save-container">
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="product-form" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Update Product
    </button>
</div>

@push('styles')
<style>
    /* Add padding at bottom to prevent floating button overlap */
    .card-body {
        padding-bottom: 100px !important;
    }
</style>
@endpush

<!-- Note: Update button is now inside the form for reliable submission -->
@endsection

<!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alertModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4" id="alertModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Form validation for stock quantity
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('product-form');
    const stockInput = document.getElementById('stock');
    const lowStockInput = document.getElementById('low_stock_threshold');
    
    // Auto-scroll to first error field (using existing invalid-feedback)
    @if($errors->any())
        var firstErrorField = document.querySelector('.is-invalid');
        if (firstErrorField) {
            setTimeout(function() {
                firstErrorField.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                firstErrorField.focus();
            }, 100);
        }
    @endif
    
    if (form && stockInput && lowStockInput) {
        form.addEventListener('submit', function(e) {
            const stock = parseInt(stockInput.value) || 0;
            const lowStock = parseInt(lowStockInput.value) || 0;
            
            if (stock < lowStock) {
                e.preventDefault();
                
                // Add error class
                stockInput.classList.add('is-invalid');
                
                // Create or update invalid-feedback div
                let feedbackDiv = stockInput.parentElement.querySelector('.invalid-feedback');
                if (!feedbackDiv) {
                    feedbackDiv = document.createElement('div');
                    feedbackDiv.className = 'invalid-feedback';
                    stockInput.parentElement.appendChild(feedbackDiv);
                }
                feedbackDiv.textContent = 'Stock Quantity must be greater than or equal to Low Stock Alert (' + lowStock + ')';
                
                // Scroll to the field
                stockInput.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                stockInput.focus();
            }
        });
        
        // Real-time validation when low stock threshold changes
        lowStockInput.addEventListener('change', function() {
            const stock = parseInt(stockInput.value) || 0;
            const lowStock = parseInt(this.value) || 0;
            
            if (stock < lowStock && stock > 0) {
                stockInput.classList.add('is-invalid');
                
                // Dispose existing popover if any
                var existingPopover = bootstrap.Popover.getInstance(stockInput);
                if (existingPopover) {
                    existingPopover.dispose();
                }
                
                // Let browser show native validation message
                stockInput.focus();
            } else {
                stockInput.classList.remove('is-invalid');
                var popover = bootstrap.Popover.getInstance(stockInput);
                if (popover) {
                    popover.dispose();
                }
            }
        });
    }
    
    // Sale Price validation - cannot be higher than Regular Price
    const priceInput = document.getElementById('price');
    const salePriceInput = document.getElementById('sale_price');
    
    if (form && priceInput && salePriceInput) {
        form.addEventListener('submit', function(e) {
            const price = parseFloat(priceInput.value) || 0;
            const salePrice = parseFloat(salePriceInput.value) || 0;
            
            if (salePrice > price && salePrice > 0) {
                e.preventDefault();
                
                // Add error class
                salePriceInput.classList.add('is-invalid');
                
                // Create or update invalid-feedback div
                let feedbackDiv = salePriceInput.parentElement.querySelector('.invalid-feedback');
                if (!feedbackDiv) {
                    feedbackDiv = document.createElement('div');
                    feedbackDiv.className = 'invalid-feedback';
                    salePriceInput.parentElement.appendChild(feedbackDiv);
                }
                feedbackDiv.textContent = 'Sale Price cannot be higher than Regular Price (' + price + ')';
                
                // Scroll to the field
                salePriceInput.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                salePriceInput.focus();
            }
        });
        
        // Real-time validation when price changes
        priceInput.addEventListener('change', function() {
            const price = parseFloat(this.value) || 0;
            const salePrice = parseFloat(salePriceInput.value) || 0;
            
            if (salePrice > price && salePrice > 0) {
                salePriceInput.classList.add('is-invalid');
                salePriceInput.focus();
            } else {
                salePriceInput.classList.remove('is-invalid');
            }
        });
    }
});

// Preview featured image
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">';
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endpush
