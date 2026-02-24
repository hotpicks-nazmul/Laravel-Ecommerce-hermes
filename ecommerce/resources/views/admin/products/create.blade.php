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
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter product name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="2" maxlength="500" placeholder="Brief product description (max 500 characters)">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required placeholder="Detailed product description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Regular Price (৳) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required placeholder="0.00">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_price" class="form-label">Sale Price (৳)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" placeholder="Leave empty if no sale">
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
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku') }}" required placeholder="Unique identifier">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="product_code" class="form-label">Product Code</label>
                                <input type="text" class="form-control @error('product_code') is-invalid @enderror" id="product_code" name="product_code" value="{{ old('product_code') }}" placeholder="e.g., PRD-001">
                                @error('product_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode') }}" placeholder="Product barcode">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand') }}" placeholder="Product brand">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">Purchase/Cost Price (৳)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" placeholder="Cost price">
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Used for profit calculation</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}" required>
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
                                <input type="number" min="0" class="form-control @error('low_stock_threshold') is-invalid @enderror" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" placeholder="10">
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
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required form="product-form">
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
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" form="product-form" onchange="previewFeaturedImage(this)">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Main product image. Max 5MB.</div>
                    <div id="featuredImagePreview" class="mt-2"></div>
                </div>
                
                <div class="mb-3">
                    <label for="images" class="form-label">Gallery Images</label>
                    <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*" form="product-form" onchange="previewGalleryImages(this)">
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
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} form="product-form">
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
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" form="product-form" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Create Product
                    </button>
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
@endsection

@push('scripts')
<script>
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
