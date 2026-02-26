@extends('admin.layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Order #{{ $order->order_number }}</h4>
        <small class="text-muted">
            <i class="bi bi-calendar3 me-1"></i>Created: {{ $order->created_at->format('d M, Y H:i') }}
            <span class="badge bg-info ms-2">Inhouse Order</span>
        </small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-outline-secondary" target="_blank">
            <i class="bi bi-receipt me-1"></i> Invoice
        </a>
        <a href="{{ route('admin.orders.in-house') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Inhouse Orders
        </a>
    </div>
</div>

<!-- Status Overview Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Order Status</div>
                <span class="badge {{ $order->status_badge_class }} fs-6">{{ ucfirst($order->status) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Payment Status</div>
                <span class="badge {{ $order->payment_status_badge_class }} fs-6">{{ ucfirst($order->payment_status) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Payment Method</div>
                <div class="fw-semibold">{{ ucfirst($order->payment_method ?? 'N/A') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Order Total</div>
                <div class="h4 mb-0 text-primary">৳{{ number_format($order->total, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Order Status & Actions & Items -->
    <div class="col-lg-8 mb-4">
        <!-- Order Status Management -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Order Status Management</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" id="statusForm">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Update Order Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Update Payment Status</label>
                            <select name="payment_status" class="form-select" form="paymentForm">
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-lg me-1"></i> Update
                            </button>
                        </div>
                    </div>
                </form>
                
                <form action="{{ route('admin.orders.payment-status', $order->id) }}" method="POST" id="paymentForm" class="d-none"></form>
                
                @if(in_array($order->status, ['processing', 'confirmed']))
                <hr>
                <form action="{{ route('admin.orders.ship', $order->id) }}" method="POST" id="shipForm">
                    @csrf
                    <h6 class="fw-semibold mb-3"><i class="bi bi-truck me-2"></i>Ship Order</h6>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Tracking Number</label>
                            <input type="text" name="tracking_number" class="form-control" placeholder="Enter tracking number" value="{{ $order->tracking_number }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Shipping Company</label>
                            <input type="text" name="shipping_company" class="form-control" placeholder="e.g., SSL Commerce, Pathao" value="{{ $order->shipping_company }}" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-box-arrow-up me-1"></i> Ship Order
                            </button>
                        </div>
                    </div>
                </form>
                @endif
                
                @if($order->tracking_number)
                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-geo-alt me-2"></i>
                    <strong>Tracking Information:</strong> {{ $order->tracking_number }}
                    @if($order->shipping_company)
                        <span class="text-muted">via {{ $order->shipping_company }}</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Order Items ({{ $order->items->count() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $imageUrl = $item->product ? $item->product->featured_image : null;
                                            if($imageUrl && !str_starts_with($imageUrl, '/storage/') && !str_starts_with($imageUrl, 'http')) {
                                                $imageUrl = '/storage/' . $imageUrl;
                                            }
                                        @endphp
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-box text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $item->product_name }}</h6>
                                            @if($item->variation && is_array($item->variation))
                                                <small class="text-muted">
                                                    @foreach($item->variation as $key => $value)
                                                        {{ ucfirst($key) }}: {{ $value }} 
                                                    @endforeach
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>৳{{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-end">৳{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end">৳{{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            @if($order->discount > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                                <td class="text-end text-danger">-৳{{ number_format($order->discount, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->shipping_cost > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                                <td class="text-end">৳{{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->tax > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                                <td class="text-end">৳{{ number_format($order->tax, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->coupon_code)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Coupon ({{ $order->coupon_code }}):</strong></td>
                                <td class="text-end text-success">-৳{{ number_format($order->discount, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong class="text-primary fs-5">৳{{ number_format($order->total, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Customer & Payment Info -->
    <div class="col-lg-4">
        <!-- Customer Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Customer</h5>
            </div>
            <div class="card-body">
                @if($order->user)
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-person-fill text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $order->user->name }}</h6>
                        <small class="text-muted">{{ $order->user->email }}</small>
                    </div>
                </div>
                <a href="{{ route('admin.customers.show', $order->user->id) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-eye me-1"></i> View Customer Profile
                </a>
                @else
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-person text-secondary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $order->billing_full_name }}</h6>
                        <small class="text-muted">Manual Order</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Billing Address -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-file-earmark-post me-2"></i>Billing Address</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $order->billing_full_name }}</strong></p>
                <p class="mb-1"><i class="bi bi-envelope me-2 text-muted"></i>{{ $order->billing_email }}</p>
                @if($order->billing_phone)
                <p class="mb-1"><i class="bi bi-phone me-2 text-muted"></i>{{ $order->billing_phone }}</p>
                @endif
                <p class="mb-0 mt-2">
                    <i class="bi bi-geo-alt me-2 text-muted"></i>
                    {{ $order->billing_address }}<br>
                    {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postcode }}<br>
                    {{ $order->billing_country }}
                </p>
            </div>
        </div>
        
        <!-- Shipping Address -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Shipping Address</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $order->shipping_full_name }}</strong></p>
                @if($order->shipping_email)
                <p class="mb-1"><i class="bi bi-envelope me-2 text-muted"></i>{{ $order->shipping_email }}</p>
                @endif
                @if($order->shipping_phone)
                <p class="mb-1"><i class="bi bi-phone me-2 text-muted"></i>{{ $order->shipping_phone }}</p>
                @endif
                <p class="mb-0 mt-2">
                    <i class="bi bi-geo-alt me-2 text-muted"></i>
                    {{ $order->shipping_address ?? $order->billing_address }}<br>
                    {{ $order->shipping_city ?? $order->billing_city }}, {{ $order->shipping_state ?? $order->billing_state }} {{ $order->shipping_postcode ?? $order->billing_postcode }}<br>
                    {{ $order->shipping_country ?? $order->billing_country }}
                </p>
            </div>
        </div>
        
        <!-- Payment Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment Details</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment Method:</span>
                    <span class="fw-semibold">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment Status:</span>
                    <span class="badge {{ $order->payment_status_badge_class }}">{{ ucfirst($order->payment_status) }}</span>
                </div>
                @if($order->transaction_id)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Transaction ID:</span>
                    <span class="fw-semibold small">{{ $order->transaction_id }}</span>
                </div>
                @endif
                @if($order->payment_gateway)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Gateway:</span>
                    <span class="fw-semibold">{{ ucfirst($order->payment_gateway) }}</span>
                </div>
                @endif
                @if($order->notes)
                <hr>
                <div>
                    <span class="text-muted">Order Notes:</span>
                    <p class="mb-0 mt-1 small">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Order Timeline -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Order Timeline</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Order Created:</span>
                    <span class="small">{{ $order->created_at->format('d M, Y H:i') }}</span>
                </div>
                @if($order->updated_at != $order->created_at)
                <div class="d-flex justify-content-between mb-0">
                    <span class="text-muted">Last Updated:</span>
                    <span class="small">{{ $order->updated_at->format('d M, Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
