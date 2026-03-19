@forelse($products as $product)
<tr class="{{ $product->quantity <= 0 ? 'table-danger' : ($product->quantity <= 5 ? 'table-warning' : '') }}">
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
            <small class="text-muted">{{ $product->category?->name ?? 'Uncategorized' }}</small>
        </div>
    </td>
    <td style="white-space: nowrap;">
    <div class="small text-truncate" style="max-width: 120px;">
        <span class="badge bg-primary">{{ $product->sku ?? 'N/A' }}</span>
    </div>
</td>
    <td class="text-center">
        <span class="fw-bold {{ $product->quantity <= 0 ? 'text-danger' : 'text-warning' }}">
            {{ $product->quantity }}
        </span>
    </td>
    <td class="text-center">
        {{ $product->low_stock_threshold ?? 10 }}
    </td>
    <td class="text-center">
        @if($product->quantity <= 0)
        <span class="badge bg-danger alert-badge">Critical</span>
        @elseif($product->quantity <= 5)
        <span class="badge bg-warning text-dark alert-badge">Warning</span>
        @else
        <span class="badge bg-info text-dark alert-badge">Notice</span>
        @endif
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-success" onclick="showRestockModal({{ $product->id }})" title="Restock">
            <i class="bi bi-plus-lg"></i> Restock
        </button>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-4">
        <div class="text-muted">
            <i class="bi bi-check-circle d-block mb-2 text-success" style="font-size: 2rem;"></i>
            No low stock products! All items are well-stocked.
        </div>
    </td>
</tr>
@endforelse
