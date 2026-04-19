---
name: product-images-path-handling
description: Proper image path handling for admin tables to ensure images display correctly regardless of storage prefix in database.
---

# Product Images Path Handling

**Problem:** When displaying product images in admin listing tables, images may not appear if the `featured_image` path doesn't include the `/storage/` prefix.

**Solution:** Use this pattern in Blade templates to properly handle product images:

@php
    $imageUrl = $product->featured_image;
    if($imageUrl && !str_starts_with($imageUrl, '/storage/') && !str_starts_with($imageUrl, 'http')) {
        $imageUrl = '/storage/' . $imageUrl;
    }
@endphp

@if($imageUrl)
    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
@else
    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
        <i class="bi bi-image text-white"></i>
    </div>
@endif

**Example in Table Rows:**

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
    </div>
</td>

**Key Points:**
- Check for `/storage/` prefix – Don't add if already present
- Check for `http` prefix – Don't modify external URLs
- Always provide fallback – Show placeholder icon when no image
- Use consistent styling – Match the image size with other table columns