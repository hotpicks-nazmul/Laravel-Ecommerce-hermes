---
name: admin-bulk-actions
description: Bulk actions bar that appears when items are selected, with select all checkbox, clear selection, and bulk operation buttons.
compatibility: opencode
---

# Admin Bulk Actions

**Bulk Actions Bar:**

<div class="card border-0 shadow-sm mb-3" id="bulkActionsBar" style="display: none;">
    <div class="card-body py-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted"><span id="selectedCount">0</span> selected</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                    Clear Selection
                </button>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('activate')">
                    <i class="bi bi-check-circle me-1"></i> Activate
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

**Bulk Action JavaScript:**

let selectedItems = new Set();

function updateBulkActions() {
    const count = selectedItems.size;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkActionsBar').style.display = count > 0 ? 'block' : 'none';
}

function bulkAction(action) {
    if (selectedItems.size === 0) {
        alert('Please select at least one item.');
        return;
    }
    if (!confirm(`Are you sure you want to ${action} ${selectedItems.size} item(s)?`)) return;
    
    document.getElementById('bulkActionInput').value = action;
    document.getElementById('bulkIdsInput').value = JSON.stringify(Array.from(selectedItems));
    document.getElementById('bulkActionForm').submit();
}