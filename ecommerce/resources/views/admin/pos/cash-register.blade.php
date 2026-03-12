@extends('admin.layouts.app')

@section('title', 'Cash Register')

@section('content')
<div class="cash-register-page">
    <!-- Date Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> View
                    </button>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.pos.terminal') }}" class="btn btn-success">
                        <i class="bi bi-plus-lg me-1"></i> New Sale
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Total Sales</div>
                    <div class="h3 mb-0 text-primary">৳{{ number_format($totalSales, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Transactions</div>
                    <div class="h3 mb-0 text-success">{{ $transactionCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Cash</div>
                    <div class="h3 mb-0 text-info">৳{{ number_format($totalCash, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Card/Digital</div>
                    <div class="h3 mb-0 text-warning">৳{{ number_format($totalCard + $totalDigital, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Transactions - {{ $date }}</h5>
        </div>
        <div class="card-body p-0">
            @if(count($transactions) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Time</th>
                            <th>Items</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Paid</th>
                            <th>Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>
                                <span class="fw-medium">{{ $transaction['order_number'] }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($transaction['created_at'])->format('H:i:s') }}</td>
                            <td>{{ count($transaction['items']) }}</td>
                            <td>৳{{ number_format($transaction['subtotal'], 2) }}</td>
                            <td>৳{{ number_format($transaction['discount'], 2) }}</td>
                            <td class="fw-bold">৳{{ number_format($transaction['total'], 2) }}</td>
                            <td>
                                @if($transaction['payment_method'] === 'cash')
                                    <span class="badge bg-info">Cash</span>
                                @elseif($transaction['payment_method'] === 'card')
                                    <span class="badge bg-primary">Card</span>
                                @else
                                    <span class="badge bg-warning">Digital</span>
                                @endif
                            </td>
                            <td>৳{{ number_format($transaction['paid_amount'], 2) }}</td>
                            <td class="text-success">৳{{ number_format($transaction['change'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No transactions for this date</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Database Orders Section (if different from session) -->
    @if(count($dbOrders) > 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-database me-2"></i>Database Orders (Backup Check)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dbOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.in-house.show', $order->id) }}" class="fw-medium">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="fw-bold">৳{{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($order->payment_method) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection