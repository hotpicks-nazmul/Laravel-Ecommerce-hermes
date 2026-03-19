@forelse($products as $product)
<tr>
    <td>
        <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}" onchange="updateSelection({{ $product->id }}, this.checked)">
    </td>
    <td>
        @if($product->featured_image)
            <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
        @else
            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="bi bi-file-earmark text-muted"></i>
            </div>
        @endif
    </td>
    <td>
        <div>
            <a href="{{ route('admin.products.digital.edit', $product->id) }}" class="fw-semibold text-decoration-none">
                {{ $product->name }}
            </a>
            @if($product->is_featured)
                <span class="badge bg-warning text-dark ms-1">Featured</span>
            @endif
            @if($product->requires_license_key)
                <span class="badge bg-info ms-1">License</span>
            @endif
        </div>
        <small class="text-muted">SKU: {{ $product->sku }}</small>
        @if($product->version)
            <span class="badge bg-secondary ms-1">v{{ $product->version }}</span>
        @endif
    </td>
    <td>
        @if($product->file_format)
            <div>
                <span class="badge bg-light text-dark">{{ $product->file_format }}</span>
                <span class="text-muted small">{{ $product->file_size_formatted }}</span>
            </div>
        @elseif($product->download_link)
            <div>
                <span class="badge bg-light text-dark">External Link</span>
            </div>
        @else
            <span class="text-muted">No file</span>
        @endif
    </td>
    <td>
        @if($product->digitalCategory)
            <span class="badge bg-light text-dark">{{ $product->digitalCategory->name }}</span>
        @elseif($product->category)
            <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td>
        <div>
            @if($product->sale_price && $product->sale_price < $product->price)
                <del class="text-muted small">৳{{ number_format($product->price, 0) }}</del>
                <span class="text-danger fw-semibold">৳{{ number_format($product->sale_price, 0) }}</span>
            @else
                <span class="fw-semibold">৳{{ number_format($product->price, 0) }}</span>
            @endif
        </div>
    </td>
    <td>
        <div class="text-center">
            <div class="fw-semibold">{{ $product->digitalDownloads()->count() }}</div>
            <small class="text-muted">downloads</small>
        </div>
    </td>
    <td>
        <button type="button" class="btn btn-sm status-toggle {{ $product->is_active ? 'btn-success' : 'btn-secondary' }}" onclick="toggleStatus({{ $product->id }})" title="Toggle Status">
            {{ $product->is_active ? 'Active' : 'Inactive' }}
        </button>
    </td>
    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.products.digital.edit', $product->id) }}">
                        <i class="bi bi-pencil me-2"></i> Edit
                    </a>
                </li>
                @if($product->requires_license_key)
                <li>
                    <a class="dropdown-item" href="#" onclick="showLicenseKeys({{ $product->id }})">
                        <i class="bi bi-key me-2"></i> License Keys
                    </a>
                </li>
                @endif
                <li>
                    <a class="dropdown-item" href="#" onclick="showDownloadStats({{ $product->id }})">
                        <i class="bi bi-bar-chart me-2"></i> Download Stats
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button type="button" class="dropdown-item text-danger" onclick="deleteProduct({{ $product->id }})">
                        <i class="bi bi-trash me-2"></i> Delete
                    </button>
                </li>
            </ul>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="text-center py-5">
        <div class="text-muted">
            <i class="bi bi-file-earmark display-4"></i>
            <p class="mt-2">No digital products found.</p>
            <a href="{{ route('admin.products.digital.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Digital Product
            </a>
        </div>
    </td>
</tr>
@endforelse
