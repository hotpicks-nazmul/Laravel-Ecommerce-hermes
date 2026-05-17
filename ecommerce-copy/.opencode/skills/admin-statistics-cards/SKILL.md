---
name: admin-statistics-cards
description: Two styles of statistics cards for admin dashboards – full-width centered cards with icons and compact Bootstrap cards.
---

# Admin Statistics Cards

**Full-Width Centered Card Style (Recommended):**

<div class="row g-3 mb-4" id="statsCards">
    <div class="col">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-icon"><i class="bi bi-bag-check"></i></div>
            <div class="stat-card-content">
                <span class="stat-card-label">Total Orders</span>
                <span class="stat-card-value">{{ number_format($stats['total_orders']) }}</span>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card stat-card-warning">
            <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-card-content">
                <span class="stat-card-label">Pending Shipments</span>
                <span class="stat-card-value">{{ number_format($stats['pending_shipments']) }}</span>
            </div>
            <a href="{{ route('admin.orders.index') }}?status=pending" class="stat-card-link">
                View pending <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

**CSS Styles (add in @push('styles')):**

.stat-card {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 20px 24px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
    transition: all 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

**Color Variants:**
- `stat-card-primary`: #e8f4fd background, #0d6efd icon
- `stat-card-warning`: #fff3cd background, #ffc107 icon
- `stat-card-info`: #cff4fc background, #0dcaf0 icon
- `stat-card-success`: #d1e7dd background, #198754 icon
- `stat-card-danger`: #f8d7da background, #dc3545 icon

**Compact Card Style (no custom CSS):**

<div class="row g-2 mb-4">
    <div class="col-md col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Shipments</div>
                <div class="h4 mb-0 text-primary">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>