---
name: toast-notification-status-toggle
description: Show success toast notification when toggling status in admin tables, with AJAX status toggle and auto-dismissing toast at bottom-right.
---

# Toast Notification for Status Toggle

**Status Button in Table:**

<td>
    <button type="button" class="btn btn-sm status-toggle {{ $item->status === 'active' ? 'btn-success' : 'btn-outline-secondary' }}" 
            data-id="{{ $item->id }}" data-status="{{ $item->status }}">
        {{ $item->status === 'active' ? 'Active' : 'Inactive' }}
    </button>
</td>

**Toast Notification Function:**

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed`;
    toast.style.cssText = 'bottom: 20px; right: 20px; z-index: 9999;';
    toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    setTimeout(() => toast.remove(), 5000);
}

**Toggle Status JavaScript:**

function initStatusToggle() {
    document.querySelectorAll('.status-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`/admin/items/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.textContent = data.status === 'active' ? 'Active' : 'Inactive';
                    this.classList.toggle('btn-success', data.status === 'active');
                    this.classList.toggle('btn-outline-secondary', data.status !== 'active');
                    showToast(data.message || 'Status updated successfully', 'success');
                }
            });
        });
    });
}

**Controller Response:**

public function toggleStatus(Item $item)
{
    $item->status = $item->status === 'active' ? 'inactive' : 'active';
    $item->save();
    return response()->json(['success' => true, 'status' => $item->status, 'message' => 'Status updated successfully']);
}

**Key Points:**
- Button style: `btn-success` when active, `btn-outline-secondary` when inactive
- Toast position: Fixed at bottom: 20px, right: 20px
- Toast duration: Auto-dismiss after 5 seconds
- Use direct URL path instead of route() helper to avoid placeholder replacement issues