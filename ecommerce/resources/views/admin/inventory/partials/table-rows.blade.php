@forelse($products as $product)
<tr>
    <td>
        <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}" onchange="toggleItem(this)">
    </td>
    <td>
        @php
            $imageUrl = $product->featured_image;
            if($imageUrl && !str_starts_with($imageUrl, '/storage/') && !str_starts_with($imageUrl, 'http')) {
                $imageUrl = '/storage/' . $imageUrl;
            }
        @endphp
        @if($imageUrl)
        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
        @else
        <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
            <i class="bi bi-image text-white"></i>
        </div>
        @endif
        <div class="d-inline-block">
            <div class="fw-medium">{{ $product->name }}</div>
            @if($product->short_description)
            <small class="text-muted">{{ Str::limit($product->short_description, 30) }}</small>
            @endif
        </div>
    </td>
    <td style="white-space: nowrap;">
    <div class="small text-truncate" style="max-width: 120px;">
        <span class="badge bg-primary">{{ $product->sku ?? 'N/A' }}</span>
    </div>
</td>
    <td>{{ $product->category?->name ?? 'Uncategorized' }}</td>
    <td class="text-center">
        <span class="fw-medium {{ $product->quantity <= 0 ? 'text-danger' : ($product->quantity <= 10 ? 'text-warning' : 'text-success') }}">
            {{ $product->quantity }}
        </span>
        @if($product->low_stock_threshold && $product->quantity <= $product->low_stock_threshold)
        <small class="d-block text-muted">Min: {{ $product->low_stock_threshold }}</small>
        @endif
    </td>
    <td class="text-center">
        @if($product->quantity <= 0)
        <span class="badge bg-danger">Out of Stock</span>
        @elseif($product->quantity <= 10)
        <span class="badge bg-warning text-dark">Low Stock</span>
        @else
        <span class="badge bg-success">In Stock</span>
        @endif
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showAdjustModal({{ $product->id }})" title="Adjust Stock">
            <i class="bi bi-plus-slash"></i> Adjust
        </button>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit Product">
            <i class="bi bi-pencil"></i>
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-4">
        <div class="text-muted">
            <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
            No products found
        </div>
    </td>
</tr>
@endforelse
