@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Orders</h4>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders ?? [] as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</td>
                        <td>৳{{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $order->status === 'completed' ? 'bg-success' : ($order->status === 'processing' ? 'bg-info' : ($order->status === 'cancelled' ? 'bg-danger' : 'bg-warning')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d M, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($orders) && method_exists($orders, 'hasPages') && $orders->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#ordersTable').DataTable({
        pageLength: 25,
        order: [[5, 'desc']]
    });
});
</script>
@endpush
