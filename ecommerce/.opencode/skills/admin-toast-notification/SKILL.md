---
name: admin-toast-notification
description: Built-in admin toast notification system with slide-in animation, white background, colored left border, and auto-dismiss after 4 seconds.
---

# Admin Toast Notification

The admin panel has a built-in toast notification system that displays slide-in messages from the right side with a white background.

**JavaScript API:**

// Show success toast
adminToast('success', 'Title', 'Success message here');

// Show error toast
adminToast('error', 'Error', 'Error message here');

// Show warning toast
adminToast('warning', 'Warning', 'Warning message here');

**Parameters:**
- `type`: `success`, `error`, or `warning`
- `title`: Toast title (bold text)
- `message`: Toast message (body text)
- `duration`: Auto-dismiss time in ms (default: 4000)

**Display Toast After Page Reload:**

When redirecting with a success message, pass the message via URL parameters.

JavaScript after AJAX success:

fetch('/admin/items/adjust', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: formData
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        window.location.href = '/admin/items?success=' + encodeURIComponent(data.message) + '&type=success';
    }
});

**Show Toast on Page Load:**

window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const type = urlParams.get('type') || 'success';

    if (success) {
        if (typeof adminToast === 'function') {
            adminToast(type, type === 'success' ? 'Success' : 'Error', decodeURIComponent(success));
        }
        window.history.replaceState({}, '', window.location.pathname);
    }
});

**Toast Styling:**
- White background with colored left border
- Slides in from right to left
- Fixed position at right side of screen
- Auto-dismiss after 4 seconds
- Close button to dismiss immediately

**Icon Colors by Type:**
- `success`: #10b981 (green) with `bi-check-circle-fill`
- `error`: #ef4444 (red) with `bi-x-circle-fill`
- `warning`: #f59e0b (amber) with `bi-exclamation-triangle-fill`