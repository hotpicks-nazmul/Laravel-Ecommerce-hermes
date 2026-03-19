@extends('admin.layouts.app')

@section('title', 'Create Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Create New Product</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
                    @csrf
                    <input type="hidden" name="redirect_route" value="{{ $redirectRoute ?? 'admin.products.index' }}">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter product name"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="2" maxlength="500" placeholder="Brief product description (max 500 characters)"
                                  data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('short_description') }}">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required placeholder="Detailed product description"
                                  data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('description') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Regular Price (৳) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required placeholder="0.00"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('price') }}">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_price" class="form-label">Sale Price (৳)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" placeholder="Leave empty if no sale"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('sale_price') }}">
                                @error('sale_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">If set, this will be the selling price</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU <span class="text-success">(Auto-generated)</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" id="sku" name="sku" value="{{ $nextSku ?? 'SKU-1000' }}" readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateSKU()" title="Generate New SKU">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </div>
                                <div class="form-text">Sequential SKU - auto-generated on page load</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="product_code" class="form-label">Product Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('product_code') is-invalid @enderror" id="product_code" name="product_code" value="{{ old('product_code') }}" required placeholder="e.g., PRD-001"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('product_code') }}">
                                @error('product_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Manual input (manufacturer/supplier code)</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode') }}" placeholder="Product barcode"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('barcode') }}">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand <span class="text-danger">*</span></label>
                                 <select class="form-select @error('brand') is-invalid @enderror" id="brand" name="brand" required form="product-form"
                                         data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('brand') }}">
                                     <option value="">Select Brand</option>
                                     @foreach($brands as $id => $name)
                                         <option value="{{ $id }}" {{ old('brand') == $id ? 'selected' : '' }}>
                                             {{ $name }}
                                         </option>
                                     @endforeach
                                 </select>
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Choose from existing brands or <a href="{{ route('admin.brands.create') }}" target="_blank">create new</a></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">Purchase/Cost Price (৳)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" placeholder="Cost price"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('purchase_price') }}">
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Used for profit calculation</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}" required
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('stock') }}">
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="low_stock_threshold" class="form-label">Low Stock Alert</label>
                                <input type="number" min="0" class="form-control @error('low_stock_threshold') is-invalid @enderror" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" placeholder="10"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('low_stock_threshold') }}">
                                @error('low_stock_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Alert when stock falls below this</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Category Selection -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-folder me-2"></i>Category</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="category_id" class="form-label">Select Category <span class="text-danger">*</span></label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required form="product-form"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('category_id') }}">
                        <option value="">Select Category</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ old('category_id', $preselectedCategory) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Choose the most relevant category</div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.categories.create') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-lg me-1"></i> New Category
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Images -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-image me-2"></i>Product Images</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="image" class="form-label">Featured Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" form="product-form" onchange="previewFeaturedImage(this)"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('image') }}">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Main product image. Max 5MB.</div>
                    <div id="featuredImagePreview" class="mt-2"></div>
                </div>
                
                <div class="mb-3">
                    <label for="images" class="form-label">Gallery Images</label>
                    <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*" form="product-form" onchange="previewGalleryImages(this)"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $errors->first('images') }}">
                    @error('images')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Additional product images. Multiple allowed.</div>
                    <div id="galleryPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>
            </div>
        </div>
        
        <!-- Status -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Status</h6>
            </div>
            <div class="card-body">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} form="product-form">
                    <label class="form-check-label" for="is_active">
                        <i class="bi bi-check-circle text-success me-1"></i> Active
                    </label>
                    <div class="form-text">Product will be visible on the store</div>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} form="product-form">
                    <label class="form-check-label" for="is_featured">
                        <i class="bi bi-star text-warning me-1"></i> Featured Product
                    </label>
                    <div class="form-text">Featured products appear prominently</div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Save Button -->
<div class="floating-save-container">
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="product-form" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create Product
    </button>
</div>

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
@endsection

@push('styles')
<style>
    /* Add padding at bottom to prevent floating button overlap */
    .content-area {
        padding-bottom: 100px !important;
    }
    
    /* Popover close button styling */
    .btn-close-red {
        filter: invert(1) grayscale(100%) brightness(200%);
        opacity: 0.6;
        cursor: pointer;
    }
    .btn-close-red:hover {
        opacity: 1;
    }
    
    /* Popover header styling */
    .popover-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 8px 12px;
    }
</style>
@endpush

@push('scripts')
<script>
// Form validation for stock quantity
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('product-form');
    const stockInput = document.getElementById('stock');
    const lowStockInput = document.getElementById('low_stock_threshold');
    
    // Using existing invalid-feedback with auto-scroll
    
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

// Show error modal if there's a session error or general validation error
@if(session('error') || $errors->has('general'))
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('alertModal'));
    document.getElementById('alertModalLabel').innerHTML = '<i class="bi bi-exclamation-triangle text-danger me-2"></i>Error';
    @if(session('error'))
    document.getElementById('alertModalBody').innerHTML = '<p class="mb-0">{{ session('error') }}</p>';
    @else
    document.getElementById('alertModalBody').innerHTML = '<p class="mb-0">{{ $errors->first('general') }}</p>';
    @endif
    modal.show();
});
@endif

// Generate new sequential SKU
function generateSKU() {
    const skuInput = document.getElementById('sku');
    
    if (skuInput) {
        // Get current SKU number and increment
        const currentSku = skuInput.value;
        const parts = currentSku.split('-');
        if (parts.length === 2) {
            const lastNumber = parseInt(parts[1]);
            const newNumber = lastNumber + 1;
            skuInput.value = 'SKU-' + newNumber;
        }
    }
}

// Using default browser validation

// Preview featured image
function previewFeaturedImage(input) {
    const preview = document.getElementById('featuredImagePreview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview gallery images
function previewGalleryImages(input) {
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'position-relative';
                div.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">';
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>
@endpush
