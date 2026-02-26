@forelse($orders as $order)
<tr>
    <td>
        <strong>{{ $order->order_number }}</strong>
    </td>
    <td>
        <div>
            <strong>{{ $order->billing_full_name }}</strong>
            @if($order->user)
                <br><small class="text-muted">{{ $order->user->email }}</small>
            @else
                <br><small class="text-muted">{{ $order->billing_email }}</small>
            @endif
            @if($order->billing_phone)
                <br><small class="text-muted"><i class="bi bi-phone me-1"></i>{{ $order->billing_phone }}</small>
            @endif
        </div>
    </td>
    <td>
        <div>৳{{ number_format($order->total, 2) }}</div>
        <small class="text-muted">
            <i class="bi bi-cart3 me-1"></i>{{ $order->items->count() }} items
        </small>
    </td>
    <td>
        <span class="badge {{ $order->payment_status_badge_class }}">
            {{ ucfirst($order->payment_status) }}
        </span>
        <br><small class="text-muted">{{ ucfirst($order->payment_method ?? 'N/A') }}</small>
    </td>
    <td>
        <span class="badge {{ $order->status_badge_class }}">
            {{ ucfirst($order->status) }}
        </span>
    </td>
    <td>
        {{ $order->created_at->format('d M, Y') }}
        <br><small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
    </td>
    <td>
        <div class="d-flex gap-1">
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-secondary" title="Invoice" target="_blank">
                <i class="bi bi-receipt"></i>
            </a>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="More Actions">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}">
                            <i class="bi bi-eye me-2"></i> View Details
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank">
                            <i class="bi bi-receipt me-2"></i> View Invoice
                        </a>
                    </li>
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
                                <i class="bi bi-check-circle me-2"></i> Mark Confirmed
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="shipped">
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-truck me-2"></i> Mark Shipped
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-check2-all me-2"></i> Mark Delivered
                            </button>
                        </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to cancel this order?')">
                                <i class="bi bi-x-circle me-2"></i> Cancel Order
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-5">
        <div class="text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <p class="mb-0">No orders found.</p>
        </div>
    </td>
</tr>
@endforelse
