---
name: delete-image-functionality
description: Add delete button for existing images in edit pages with circular badge style at top-right corner and AJAX deletion.
---

# Delete Image Functionality

**Route Setup:**

Route::delete('/colors/{color}/image', [ColorController::class, 'deleteImage'])->name('colors.image.delete');

**Controller Method:**

public function deleteImage(Color $color)
{
    if ($color->image) {
        ImageHelper::deleteImage($color->image);
        $color->update(['image' => null]);
    }
    return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
}

**Blade Template (Edit Page):**

@if($color->image)
<div class="mb-3" id="currentImageContainer">
    <label class="form-label">Current Image</label>
    <div class="position-relative d-inline-block">
        <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
            <img src="{{ asset($color->image) }}" alt="{{ $color->name }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <button type="button" class="badge bg-danger rounded-circle border-0 position-absolute p-0"
            style="top: -4px; right: -4px; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer;"
            onclick="removeColorImage({{ $color->id }})">
            <i class="bi bi-x" style="font-size: 12px;"></i>
        </button>
    </div>
    <input type="hidden" name="delete_image" id="deleteImageInput" value="0">
</div>
@endif

**JavaScript Implementation:**

function removeColorImage(colorId) {
    if (!confirm('Delete this image?')) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const imageContainer = document.getElementById('currentImageContainer');
    fetch(`/admin/colors/${colorId}/image`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('deleteImageInput').value = '1';
            imageContainer.remove();
        }
    });
}

**Update Method Handling:**

if ($request->delete_image === '1' && $color->image) {
    ImageHelper::deleteImage($color->image);
    $color->update(['image' => null]);
}
if ($request->hasFile('image')) {
    if ($color->image) ImageHelper::deleteImage($color->image);
    // Process and save new image
}