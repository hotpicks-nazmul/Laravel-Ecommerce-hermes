---
name: bootstrap-modal-popup
description: Reusable Bootstrap modal for showing messages, confirmations, or information without page reload, with better UX than browser alerts or error pages.
---

# Bootstrap Modal Popup

**Common Use Cases:**
- Show access denied messages instead of 403 error pages
- Display success/error messages after form submissions
- Confirm destructive actions (delete, etc.)
- Show information modals with detailed content

**JavaScript Function to Show Modal:**

@push('scripts')
<script>
    function showAccessDenied() {
        var modal = new bootstrap.Modal(document.getElementById('accessDeniedModal'));
        modal.show();
    }
</script>
@endpush

**Modal HTML:**

<div class="modal fade" id="accessDeniedModal" tabindex="-1" aria-labelledby="accessDeniedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accessDeniedModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>Access Denied
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-shield-lock text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">Staff members cannot create other staff members.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

**Triggering the Modal:**

@if(auth()->user()->role !== 'staff')
    <a href="{{ route('admin.staffs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Staff
    </a>
@else
    <button type="button" class="btn btn-primary" onclick="showAccessDenied()">
        <i class="bi bi-plus-lg me-1"></i> Add New Staff
    </button>
@endif

**Key Points:**
- Use Bootstrap modal for seamless integration with admin panel
- Call modal function from onclick handler
- Use unique modal IDs that don't conflict with other modals
- Include icons and clear messages for user-friendly experience