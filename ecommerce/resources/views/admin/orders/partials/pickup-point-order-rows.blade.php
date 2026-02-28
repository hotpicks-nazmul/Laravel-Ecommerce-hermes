@forelse($orders as $order)
<tr>
    <td>
        <div class="fw-semibold">{{ $order->order_number }}</div>
        @if($order->pickupPointLocation)
            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $order->pickupPointLocation->city }}</small>
        @endif
    </td>
    <td>
        @if($order->user)
            <div class="d-flex align-items-center">
                <div class="bg-light rounded-circle p-2 me-2">
                    <i class="bi bi-person text-muted"></i>
                </div>
                <div>
                    <div class="fw-medium">{{ $order->billing_full_name }}</div>
                    <small class="text-muted">{{ $order->user->email }}</small>
                </div>
            </div>
        @else
            <div class="fw-medium">{{ $order->billing_full_name }}</div>
            <small class="text-muted">{{ $order->billing_email }}</small>
        @endif
    </td>
    <td>
        @if($order->pickupPointLocation)
            <div class="fw-medium">{{ $order->pickupPointLocation->name }}</div>
            <small class="text-muted">{{ $order->pickupPointLocation->city }}</small>
        @else
            <span class="text-muted">Not assigned</span>
        @endif
    </td>
    <td>
        <div class="fw-semibold">৳{{ number_format($order->total, 2) }}</div>
        <small class="text-muted">{{ $order->items->count() }} item(s)</small>
    </td>
    <td>
        <div class="d-flex flex-column gap-1">
            <span class="badge {{ $order->payment_status_badge_class }}">
                {{ ucfirst($order->payment_status) }}
            </span>
            <small class="text-muted">{{ ucfirst($order->payment_method ?? 'N/A') }}</small>
        </div>
    </td>
    <td>
        @if($order->picked_up_at)
            <span class="badge bg-success">
                <i class="bi bi-check-circle me-1"></i>Picked Up
            </span>
            <small class="text-muted d-block">{{ $order->picked_up_at->format('d M, H:i') }}</small>
        @else
            <span class="badge {{ $order->status_badge_class }}">
                {{ ucfirst($order->status) }}
            </span>
        @endif
    </td>
    <td>
        <div>{{ $order->created_at->format('d M, Y') }}</div>
        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
    </td>
    <td>
        <div class="d-flex gap-1">
            <a href="{{ route('admin.orders.pickup-point.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-secondary" title="Invoice" target="_blank">
                <i class="bi bi-receipt"></i>
            </a>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-gear"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.orders.pickup-point.show', $order->id) }}">
                            <i class="bi bi-eye me-2"></i> View Details
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank">
                            <i class="bi bi-receipt me-2"></i> View Invoice
                        </a>
                    </li>
                    @if(!$order->picked_up_at && $order->status !== 'cancelled')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-arrow-repeat me-2"></i> Mark Processing
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-check-circle me-2"></i> Mark Ready
                                </button>
                            </form>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-success" data-bs-toggle="modal" data-bs-target="#markPickedUpModal{{ $order->id }}">
                                <i class="bi bi-box-seam me-2"></i> Mark Picked Up
                            </button>
                        </li>
                        <li>
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-x-circle me-2"></i> Cancel Order
                                </button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Mark as Picked Up Modal -->
        @if(!$order->picked_up_at && $order->status !== 'cancelled')
        <div class="modal fade" id="markPickedUpModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.orders.pickup-point.picked-up', $order->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Mark Order as Picked Up</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Order <span class="text-primary">{{ $order->order_number }}</span></label>
                                <p class="text-muted mb-0">Customer: {{ $order->billing_full_name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Picked Up By <span class="text-danger">*</span></label>
                                <input type="text" name="picked_up_by" class="form-control" placeholder="Name of person picking up" required>
                                <small class="text-muted">Enter the name of the person who picked up the order</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i> Confirm Pickup
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-5">
        <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
        <p class="mb-0 text-muted">No pick-up point orders found.</p>
        <small class="text-muted">Orders with pick-up point delivery will appear here.</small>
    </td>
</tr>
@endforelse
