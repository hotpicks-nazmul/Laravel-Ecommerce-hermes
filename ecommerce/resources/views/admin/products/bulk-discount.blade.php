@extends('admin.layouts.app')

@section('title', 'Bulk Discount')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-percent me-2"></i>Bulk Discount</h4>
        <p class="text-muted mb-0">Apply discounts to multiple products at once</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Discount Settings Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Discount Settings</h6>
            </div>
            <div class="card-body">
                <form id="discountForm" method="POST" action="{{ route('admin.products.bulk-discount.apply') }}">
                    @csrf
                    
                    <!-- Discount Type & Value -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                            <select name="discount_type" id="discountType" class="form-select" required>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="discount_value" id="discountValue" class="form-control" 
                                       placeholder="Enter value" step="0.01" min="0" required>
                                <span class="input-group-text" id="discountTypeLabel">%</span>
                            </div>
                            <small class="text-muted" id="discountHelp">Enter a percentage value (e.g., 10 for 10% off)</small>
                        </div>
                    </div>

                    <!-- Apply To Selection -->
                    <div class="mb-4">
                        <label class="form-label">Apply To <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check card p-3 border">
                                    <input class="form-check-input" type="radio" name="apply_to" id="applyAll" value="all" checked>
                                    <label class="form-check-label w-100" for="applyAll">
                                        <i class="bi bi-box-seam text-primary me-2"></i>
                                        <strong>All Products</strong>
                                        <p class="text-muted small mb-0">Apply to all products</p>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card p-3 border">
                                    <input class="form-check-input" type="radio" name="apply_to" id="applyCategory" value="category">
                                    <label class="form-check-label w-100" for="applyCategory">
                                        <i class="bi bi-folder text-success me-2"></i>
                                        <strong>By Category</strong>
                                        <p class="text-muted small mb-0">Select a category</p>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card p-3 border">
                                    <input class="form-check-input" type="radio" name="apply_to" id="applySelected" value="selected">
                                    <label class="form-check-label w-100" for="applySelected">
                                        <i class="bi bi-check2-square text-info me-2"></i>
                                        <strong>Selected</strong>
                                        <p class="text-muted small mb-0">Choose specific products</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Selection (conditional) -->
                    <div class="mb-4" id="categorySelection" style="display: none;">
                        <label class="form-label">Select Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="categoryId" class="form-select">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Selection (conditional) -->
                    <div class="mb-4" id="productSelection" style="display: none;">
                        <label class="form-label">Select Products <span class="text-danger">*</span></label>
                        
                        <!-- Product Search -->
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" id="productSearch" class="form-control" placeholder="Search products by name or SKU...">
                                <select id="productCategoryFilter" class="form-select" style="max-width: 200px;">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" onclick="searchProducts()">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Selected Products Count -->
                        <div class="alert alert-info py-2" id="selectedCount" style="display: none;">
                            <i class="bi bi-check-circle me-1"></i>
                            <span id="selectedCountText">0</span> product(s) selected
                            <button type="button" class="btn btn-sm btn-link p-0 ms-2" onclick="clearProductSelection()">Clear</button>
                        </div>

                        <!-- Product List -->
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAllProducts" onchange="toggleSelectAll()">
                                        </th>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Sale Price</th>
                                    </tr>
                                </thead>
                                <tbody id="productList">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            Search for products to select
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Hidden input for selected product IDs -->
                        <div id="selectedProductIds"></div>
                    </div>

                    <!-- Additional Options -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Price Range Filter</label>
                            <div class="input-group">
                                <input type="number" name="price_min" class="form-control" placeholder="Min Price" step="0.01">
                                <span class="input-group-text">to</span>
                                <input type="number" name="price_max" class="form-control" placeholder="Max Price" step="0.01">
                            </div>
                            <small class="text-muted">Only apply to products within this price range</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="active_only" id="activeOnly" value="1" checked>
                                <label class="form-check-label" for="activeOnly">
                                    Apply to active products only
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="clear_existing_sale_price" id="clearExisting" value="1">
                                <label class="form-check-label" for="clearExisting">
                                    Clear existing sale prices first
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="mb-4" id="previewSection" style="display: none;">
                        <label class="form-label">Discount Preview</label>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Original Price:</small>
                                        <div class="h5 mb-0" id="previewOriginal">$100.00</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">After Discount:</small>
                                        <div class="h5 mb-0 text-success" id="previewDiscounted">$90.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="applyDiscountBtn">
                            <i class="bi bi-percent me-1"></i> Apply Discount
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="showRemoveDiscountModal()">
                            <i class="bi bi-x-circle me-1"></i> Remove Discounts
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
        @endif

        @if(session('warning'))
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            @if(session('discount_errors'))
                <ul class="mb-0 mt-2">
                    @foreach(session('discount_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Statistics Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Product Statistics</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Products:</span>
                    <strong>{{ $totalProducts }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Products on Sale:</span>
                    <strong id="productsOnSale">-</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Regular Price Only:</span>
                    <strong id="regularPriceOnly">-</strong>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Discounts</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Apply preset discounts quickly:</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickDiscount(10)">
                        <i class="bi bi-percent me-1"></i> 10% Off All Products
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickDiscount(20)">
                        <i class="bi bi-percent me-1"></i> 20% Off All Products
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickDiscount(50)">
                        <i class="bi bi-percent me-1"></i> 50% Off All Products
                    </button>
                </div>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
            </div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li class="mb-2">Percentage discounts are calculated from the regular price</li>
                    <li class="mb-2">Fixed amounts are subtracted from the regular price</li>
                    <li class="mb-2">Sale prices must be less than regular prices</li>
                    <li class="mb-2">Use "Clear existing sale prices" to reset before applying new discounts</li>
                    <li>Products with existing sale prices will be updated</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Remove Discount Modal -->
<div class="modal fade" id="removeDiscountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Remove Discounts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.products.bulk-discount.remove') }}">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to remove sale prices from products?</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Remove From</label>
                        <select name="apply_to" class="form-select" required>
                            <option value="all">All Products</option>
                            <option value="category">Specific Category</option>
                            <option value="selected">Selected Products</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="removeCategorySelect" style="display: none;">
                        <label class="form-label">Select Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Remove Discounts</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Floating Action Buttons -->
<div class="floating-save-container">
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="discountForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-percent me-1"></i> Apply Discount
    </button>
</div>

@push('styles')
<style>
    /* Add padding at bottom to prevent floating button overlap */
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    let selectedProducts = new Set();
    let allProducts = [];

    // Discount type change
    document.getElementById('discountType').addEventListener('change', function() {
        const label = document.getElementById('discountTypeLabel');
        const help = document.getElementById('discountHelp');
        
        if (this.value === 'percentage') {
            label.textContent = '%';
            help.textContent = 'Enter a percentage value (e.g., 10 for 10% off)';
        } else {
            label.textContent = '$';
            help.textContent = 'Enter a fixed amount to subtract (e.g., 5 for $5 off)';
        }
        updatePreview();
    });

    // Discount value change
    document.getElementById('discountValue').addEventListener('input', updatePreview);

    function updatePreview() {
        const type = document.getElementById('discountType').value;
        const value = parseFloat(document.getElementById('discountValue').value) || 0;
        const originalPrice = 100;
        let discountedPrice;

        if (type === 'percentage') {
            discountedPrice = originalPrice - (originalPrice * value / 100);
        } else {
            discountedPrice = originalPrice - value;
        }

        document.getElementById('previewOriginal').textContent = '$' + originalPrice.toFixed(2);
        document.getElementById('previewDiscounted').textContent = '$' + Math.max(0, discountedPrice).toFixed(2);
        document.getElementById('previewSection').style.display = value > 0 ? 'block' : 'none';
    }

    // Apply to selection change
    document.querySelectorAll('input[name="apply_to"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('categorySelection').style.display = this.value === 'category' ? 'block' : 'none';
            document.getElementById('productSelection').style.display = this.value === 'selected' ? 'block' : 'none';
            
            // Update required attributes
            document.getElementById('categoryId').required = this.value === 'category';
        });
    });

    // Search products
    function searchProducts() {
        const search = document.getElementById('productSearch').value;
        const category = document.getElementById('productCategoryFilter').value;
        
        fetch(`{{ route('admin.products.bulk-discount.products') }}?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}`)
            .then(res => res.json())
            .then(data => {
                allProducts = data.products;
                renderProductList(data.products);
            });
    }

    function renderProductList(products) {
        const tbody = document.getElementById('productList');
        
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        No products found
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = products.map(p => `
            <tr>
                <td>
                    <input type="checkbox" class="product-checkbox" value="${p.id}" 
                           ${selectedProducts.has(p.id) ? 'checked' : ''}
                           onchange="toggleProduct(${p.id})">
                </td>
                <td>
                    <div class="fw-medium">${p.name}</div>
                    <small class="text-muted">${p.category}</small>
                </td>
                <td><code>${p.sku}</code></td>
                <td>$${parseFloat(p.price).toFixed(2)}</td>
                <td>${p.sale_price ? '<span class="text-success">$' + parseFloat(p.sale_price).toFixed(2) + '</span>' : '<span class="text-muted">-</span>'}</td>
            </tr>
        `).join('');
    }

    function toggleProduct(id) {
        if (selectedProducts.has(id)) {
            selectedProducts.delete(id);
        } else {
            selectedProducts.add(id);
        }
        updateSelectedCount();
    }

    function toggleSelectAll() {
        const checkbox = document.getElementById('selectAllProducts');
        const checkboxes = document.querySelectorAll('.product-checkbox');
        
        checkboxes.forEach(cb => {
            const id = parseInt(cb.value);
            if (checkbox.checked) {
                selectedProducts.add(id);
                cb.checked = true;
            } else {
                selectedProducts.delete(id);
                cb.checked = false;
            }
        });
        updateSelectedCount();
    }

    function clearProductSelection() {
        selectedProducts.clear();
        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAllProducts').checked = false;
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = selectedProducts.size;
        const countDiv = document.getElementById('selectedCount');
        const countText = document.getElementById('selectedCountText');
        const idsContainer = document.getElementById('selectedProductIds');
        
        countDiv.style.display = count > 0 ? 'block' : 'none';
        countText.textContent = count;
        
        // Update hidden inputs
        idsContainer.innerHTML = Array.from(selectedProducts).map(id => 
            `<input type="hidden" name="product_ids[]" value="${id}">`
        ).join('');
    }

    // Quick discount
    function quickDiscount(percent) {
        document.getElementById('discountType').value = 'percentage';
        document.getElementById('discountValue').value = percent;
        document.querySelector('input[name="apply_to"][value="all"]').checked = true;
        document.getElementById('discountTypeLabel').textContent = '%';
        updatePreview();
    }

    // Remove discount modal
    function showRemoveDiscountModal() {
        new bootstrap.Modal(document.getElementById('removeDiscountModal')).show();
    }

    // Remove discount category toggle
    document.querySelector('#removeDiscountModal select[name="apply_to"]').addEventListener('change', function() {
        document.getElementById('removeCategorySelect').style.display = this.value === 'category' ? 'block' : 'none';
    });

    // Load initial statistics
    fetch('{{ route('admin.products.bulk-discount.products') }}')
        .then(res => res.json())
        .then(data => {
            const onSale = data.products.filter(p => p.sale_price).length;
            document.getElementById('productsOnSale').textContent = onSale;
            document.getElementById('regularPriceOnly').textContent = data.products.length - onSale;
        });

    // Initial preview update
    updatePreview();
</script>
@endpush
@endsection