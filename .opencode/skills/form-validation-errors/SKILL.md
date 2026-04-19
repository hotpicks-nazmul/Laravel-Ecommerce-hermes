---
name: form-validation-errors
description: Handle validation errors with Bootstrap invalid-feedback, auto-scroll to first error field, and modal for general server errors.
---

# Form Validation Errors

**The Strategy:**

| Error Type | Display Method |
|------------|----------------|
| Input Field Errors | Bootstrap invalid-feedback div (below input) |
| Custom JavaScript Validation | Create invalid-feedback div dynamically |
| General Errors | Bootstrap Modal Popup |

**Form Field with Invalid-Feedback:**

<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

**Auto-Scroll to First Error:**

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            var firstErrorField = document.querySelector('.is-invalid');
            if (firstErrorField) {
                setTimeout(function() {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstErrorField.focus();
                }, 100);
            }
        @endif
    });
</script>
@endpush

**Custom JavaScript Validation:**

form.addEventListener('submit', function(e) {
    if (stock < lowStock) {
        e.preventDefault();
        stockInput.classList.add('is-invalid');
        let feedbackDiv = stockInput.parentElement.querySelector('.invalid-feedback');
        if (!feedbackDiv) {
            feedbackDiv = document.createElement('div');
            feedbackDiv.className = 'invalid-feedback';
            stockInput.parentElement.appendChild(feedbackDiv);
        }
        feedbackDiv.textContent = 'Stock Quantity must be greater than or equal to Low Stock Alert';
        stockInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        stockInput.focus();
    }
});