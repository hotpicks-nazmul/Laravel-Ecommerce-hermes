---
name: auto-generate-slug-from-name
description: Real-time slug auto-generation when typing in the name field, with JavaScript slug transformation and backend fallback using Str::slug.
---

# Auto-generate Slug from Name

**Frontend JavaScript Implementation:**

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');
    
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            slugInput.value = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')      // Remove non-alphanumeric except spaces/hyphens
                .replace(/\s+/g, '-')              // Replace spaces with hyphens
                .replace(/-+/g, '-')               // Replace multiple hyphens with one
                .replace(/^-|-$/g, '');            // Remove leading/trailing hyphens
        });
    }
});
</script>
@endpush

**HTML Form Fields:**

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $attribute->name ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug', $attribute->slug ?? '') }}" placeholder="Auto-generated from name">
        <div class="form-text">Leave empty to auto-generate</div>
    </div>
</div>

**Backend Fallback (Controller):**

use Illuminate\Support\Str;

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:attributes,slug',
    ]);

    $attribute = Attribute::create([
        'name' => $validated['name'],
        'slug' => $validated['slug'] ?? Str::slug($validated['name']),
    ]);
}

**Model Auto-generation (Optional):**

protected static function boot()
{
    parent::boot();

    static::creating(function ($attribute) {
        if (empty($attribute->slug)) {
            $attribute->slug = Str::slug($attribute->name);
        }
    });
}

**Key Points:**
- Wrap in DOMContentLoaded event listener to ensure DOM is loaded
- Check `nameInput && slugInput` exist before adding listener
- Convert to lowercase before replacing characters
- Always provide Str::slug() in controller as backup