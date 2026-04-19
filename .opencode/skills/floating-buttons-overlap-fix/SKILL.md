---
name: floating-buttons-overlap-fix
description: Add bottom padding to prevent floating action buttons from overlapping with content at the bottom of pages.
---

# Floating Buttons Overlap Fix

**Problem:** When using floating action buttons on pages with forms or content that extends to the bottom, the floating buttons can block or overlap with form elements or other components at the bottom.

**Affected Pages:** Bulk Import, Bulk Export, Bulk Discount, and any other pages with floating buttons.

**Solution:** Add bottom padding to the content area to create space for the floating buttons. Include this CSS in the `@push('styles')` section of each affected page:

@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush

**Why This Works:**
- The `.floating-save-container` is positioned `fixed` at `bottom: 20px`
- The floating buttons have a height of approximately 48px
- Adding 100px padding-bottom ensures content doesn't extend behind the floating buttons
- The `!important` flag overrides any conflicting styles

**Alternative Approach (Global):**
If you want to apply this globally, add the `has-floating-save` class to the content-area div in the layout file:

<div class="content-area has-floating-save">
    @yield('content')
</div>

The CSS for this class is already defined in the layout. However, the per-page approach is preferred to avoid unnecessary padding on pages without floating buttons.