---
name: image-upload-functionality
description: Complete image upload solution with WebP conversion, automatic resizing, thumbnail generation, and multiple format support using ImageHelper class.
---

# Image Upload Functionality

**Frontend – Blade Template:**

<div class="mb-3">
    <label for="image" class="form-label">Featured Image</label>
    <input type="file" class="form-control" id="image" name="image" accept="image/*" form="item-form" 
           onchange="previewFeaturedImage(this)">
    <div class="form-text">Main image. Max 5MB. Recommended: 1920x1080px</div>
    <div id="featuredImagePreview" class="mt-2"></div>
</div>

**JavaScript Preview:**

function previewFeaturedImage(input) {
    const preview = document.getElementById('featuredImagePreview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

**Backend – ImageHelper Usage:**

use App\Helpers\ImageHelper;

$result = ImageHelper::processImage(
    $request->file('image'),
    'products',      // Storage directory
    1920,            // Max width (0 = no resize)
    300,             // Thumbnail width (0 = no thumbnail)
    85               // WebP quality (0-100)
);

// Returns: ['path' => '/storage/products/abc123.webp', 'thumbnail' => '/storage/products/abc123_thumb.webp']

$images = ImageHelper::processGalleryImages(
    $request->file('images'),
    'products/gallery',
    1200,
    85
);

**Controller Integration:**

if ($request->hasFile('image')) {
    if (ImageHelper::isValidImage($request->file('image'))) {
        $imageResult = ImageHelper::processImage($request->file('image'), 'items', 1920, 300, 85);
        $data['featured_image'] = $imageResult['path'];
        $data['thumbnail'] = $imageResult['thumbnail'] ?? null;
    }
}

**Recommended Configurations by Use Case:**
- Featured Image: 1920px width, 300px thumbnail, 85 quality
- Gallery Images: 1200px width, no thumbnail, 85 quality
- Category Image: 800px width, 200px thumbnail, 80 quality
- Brand Logo: 400px width, 150px thumbnail, 85 quality
- User Avatar: 300px width, 100px thumbnail, 90 quality