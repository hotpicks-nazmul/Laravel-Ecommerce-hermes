@extends('admin.layouts.app')

@section('title', 'Delivery Dashboard')

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .delivery-tabs .nav-link {
        border: none;
        padding: 12px 20px;
        color: #6c757d;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .delivery-tabs .nav-link:hover {
        background: #f8f9fa;
        color: #667eea;
    }
    .delivery-tabs .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .order-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .quick-action-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    .quick-action-btn:hover {
        transform: scale(1.05);
    }
    .table-action-btn {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
    }
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="content-area">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-truck me-2"></i>Delivery Dashboard</h4>
            <p class="text-muted mb-0">Monitor and manage all delivery operations</p>
        </div>
        
        <!-- Date Range Filter -->
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('admin.delivery.index') }}" class="d-flex align-items-center gap-2">
                <select name="date_range" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 150px;">
                    <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="this_week" {{ $dateRange == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_7_days" {{ $dateRange == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                </select>
            </form>
            
            <a href="{{ route('admin.orders.in-house.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Create Order
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Orders -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total Orders</p>
                            <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-bag-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Shipments -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Pending Shipments</p>
                            <h3 class="mb-0">{{ number_format($stats['pending_shipments']) }}</h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    @if($stats['pending_shipments'] > 0)
                    <a href="{{ route('admin.orders.in-house') }}?status=pending" class="text-warning small text-decoration-none mt-2 d-inline-block">
                        View pending orders <i class="bi bi-arrow-right"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- In Transit -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">In Transit</p>
                            <h3 class="mb-0">{{ number_format($stats['in_transit']) }}</h3>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-truck"></i>
                        </div>
                    </div>
                    @if($stats['in_transit'] > 0)
                    <a href="{{ route('admin.delivery.tracking') }}" class="text-info small text-decoration-none mt-2 d-inline-block">
                        Track shipments <i class="bi bi-arrow-right"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Delivered -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Delivered</p>
                            <h3 class="mb-0">{{ number_format($stats['delivered']) }}</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row g-3 mb-4">
        <!-- Success Rate -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Success Rate</p>
                            <h3 class="mb-0">{{ $stats['success_rate'] }}%</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $stats['success_rate'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Delivery Time -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Avg. Delivery Time</p>
                            <h3 class="mb-0">{{ $stats['avg_delivery_time'] }} <small class="text-muted fs-6">days</small></h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Failed/Cancelled -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Failed/Cancelled</p>
                            <h3 class="mb-0">{{ number_format($stats['failed']) }}</h3>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Revenue -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Shipping Revenue</p>
                            <h3 class="mb-0">${{ number_format($stats['shipping_revenue'], 2) }}</h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.orders.in-house') }}?status=pending" class="btn btn-outline-warning quick-action-btn">
                            <i class="bi bi-hourglass-split me-1"></i> Process Pending Orders
                        </a>
                        <a href="{{ route('admin.delivery.tracking') }}" class="btn btn-outline-info quick-action-btn">
                            <i class="bi bi-geo-alt me-1"></i> Track Shipments
                        </a>
                        <a href="{{ route('admin.delivery.delivery-boys.index') }}" class="btn btn-outline-primary quick-action-btn">
                            <i class="bi bi-person-badge me-1"></i> Manage Delivery Boys
                        </a>
                        <a href="{{ route('admin.pickup-points.index') }}" class="btn btn-outline-success quick-action-btn">
                            <i class="bi bi-shop me-1"></i> Pickup Points
                        </a>
                        <a href="{{ route('admin.delivery.reports') }}" class="btn btn-outline-secondary quick-action-btn">
                            <i class="bi bi-bar-chart me-1"></i> View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Tabs -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <ul class="nav delivery-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                        <i class="bi bi-hourglass-split me-1"></i> Pending Shipment
                        @if($pendingShipments->count() > 0)
                        <span class="badge bg-warning ms-1">{{ $pendingShipments->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transit" type="button">
                        <i class="bi bi-truck me-1"></i> In Transit
                        @if($inTransit->count() > 0)
                        <span class="badge bg-info ms-1">{{ $inTransit->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#delivered" type="button">
                        <i class="bi bi-check-circle me-1"></i> Recently Delivered
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#failed" type="button">
                        <i class="bi bi-x-circle me-1"></i> Failed/Returned
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Pending Shipments -->
                <div class="tab-pane fade show active" id="pending">
                    @if($pendingShipments->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No Pending Shipments</h5>
                        <p class="text-muted">All orders have been processed</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingShipments as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.in-house.show', $order->id) }}" class="text-decoration-none fw-semibold">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $order->shipping_full_name }}</div>
                                                <small class="text-muted">{{ $order->shipping_phone }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $order->items->count() }} items</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $order->payment_status_badge_class }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-primary table-action-btn" 
                                                onclick="quickShip({{ $order->id }})"
                                                {{ $order->payment_status != 'paid' ? 'disabled' : '' }}>
                                                <i class="bi bi-truck"></i> Ship
                                            </button>
                                            <a href="{{ route('admin.orders.in-house.show', $order->id) }}" class="btn btn-sm btn-outline-secondary table-action-btn">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                <!-- In Transit -->
                <div class="tab-pane fade" id="transit">
                    @if($inTransit->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-truck text-info" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No Orders In Transit</h5>
                        <p class="text-muted">All shipments have been delivered</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Tracking #</th>
                                    <th>Total</th>
                                    <th>Shipped Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inTransit as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.in-house.show', $order->id) }}" class="text-decoration-none fw-semibold">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $order->shipping_full_name }}</div>
                                        <small class="text-muted">{{ $order->shipping_phone }}</small>
                                    </td>
                                    <td>
                                        @if($order->tracking_number)
                                        <span class="badge bg-info">{{ $order->tracking_number }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>{{ $order->updated_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-success table-action-btn" 
                                                onclick="markDelivered({{ $order->id }})">
                                                <i class="bi bi-check-lg"></i> Delivered
                                            </button>
                                            <a href="{{ route('admin.orders.in-house.show', $order->id) }}" class="btn btn-sm btn-outline-secondary table-action-btn">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                <!-- Recently Delivered -->
                <div class="tab-pane fade" id="delivered">
                    @if($recentlyDelivered->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No Delivered Orders</h5>
                        <p class="text-muted">Orders will appear here once delivered</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Delivered Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentlyDelivered as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.in-house.show', $order->id) }}" class="text-decoration-none fw-semibold">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $order->shipping_full_name }}</div>
                                        <small class="text-muted">{{ $order->shipping_phone }}</small>
                                    </td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>{{ $order->updated_at->format('M d, Y g:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.in-house.show', $order->id) }}" class="btn btn-sm btn-outline-secondary table-action-btn">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                <!-- Failed/Returned -->
                <div class="tab-pane fade" id="failed">
                    @if($failedDeliveries->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-smile text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No Failed Deliveries</h5>
                        <p class="text-muted">All deliveries were successful</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($failedDeliveries as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.in-house.show', $order->id) }}" class="text-decoration-none fw-semibold">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $order->shipping_full_name }}</div>
                                        <small class="text-muted">{{ $order->shipping_phone }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>{{ $order->updated_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.in-house.show', $order->id) }}" class="btn btn-sm btn-outline-secondary table-action-btn">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Ship Modal -->
<div class="modal fade" id="quickShipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-truck me-2"></i>Quick Ship Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickShipForm">
                    <input type="hidden" id="shipOrderId" name="order_id">
                    <div class="mb-3">
                        <label class="form-label">Tracking Number (Optional)</label>
                        <input type="text" class="form-control" id="trackingNumber" name="tracking_number" placeholder="Enter tracking number">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitQuickShip()">
                    <i class="bi bi-check-lg me-1"></i> Mark as Shipped
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Quick Ship function
    function quickShip(orderId) {
        document.getElementById('shipOrderId').value = orderId;
        document.getElementById('trackingNumber').value = '';
        var modal = new bootstrap.Modal(document.getElementById('quickShipModal'));
        modal.show();
    }

    function submitQuickShip() {
        const orderId = document.getElementById('shipOrderId').value;
        const trackingNumber = document.getElementById('trackingNumber').value;

        fetch('{{ route("admin.delivery.quick-ship") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: orderId,
                tracking_number: trackingNumber
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('quickShipModal')).hide();
                showToast('success', 'Success', data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('error', 'Error', data.message);
            }
        })
        .catch(error => {
            showToast('error', 'Error', 'An error occurred. Please try again.');
        });
    }

    // Mark as Delivered function
    function markDelivered(orderId) {
        if (confirm('Are you sure you want to mark this order as delivered?')) {
            fetch('{{ route("admin.delivery.mark-delivered") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Success', data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast('error', 'Error', data.message);
                }
            })
            .catch(error => {
                showToast('error', 'Error', 'An error occurred. Please try again.');
            });
        }
    }

    // Toast notification function
    function showToast(type, title, message) {
        const toastContainer = document.querySelector('.admin-toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `admin-toast ${type}`;
        toast.innerHTML = `
            <div class="admin-toast-icon">
                <i class="bi bi-${type === 'success' ? 'check-lg' : 'x-lg'}"></i>
            </div>
            <div class="admin-toast-content">
                <div class="admin-toast-title">${title}</div>
                <div class="admin-toast-message">${message}</div>
            </div>
            <button class="admin-toast-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        `;
        toastContainer.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'admin-toast-container';
        document.body.appendChild(container);
        return container;
    }
</script>
@endpush
@endsection
