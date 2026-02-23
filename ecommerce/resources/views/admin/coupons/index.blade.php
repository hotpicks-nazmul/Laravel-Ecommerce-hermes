@extends('admin.layouts.app')

@section('title', 'Coupons')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Coupons</h4>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Coupon
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Min Order</th>
                        <th>Usage</th>
                        <th>Valid Period</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                    <tr>
                        <td>
                            <strong class="text-primary">{{ $coupon->code }}</strong>
                        </td>
                        <td>
                            <span class="badge {{ $coupon->type === 'percentage' ? 'bg-info' : 'bg-success' }}">
                                {{ ucfirst($coupon->type) }}
                            </span>
                        </td>
                        <td>
                            @if($coupon->type === 'percentage')
                                <strong>{{ $coupon->value }}%</strong>
                                @if($coupon->max_discount)
                                    <br><small class="text-muted">Max: ৳{{ number_format($coupon->max_discount, 2) }}</small>
                                @endif
                            @else
                                <strong>৳{{ number_format($coupon->value, 2) }}</strong>
                            @endif
                        </td>
                        <td>
                            @if($coupon->min_order_amount)
                                ৳{{ number_format($coupon->min_order_amount, 2) }}
                            @else
                                <span class="text-muted">No minimum</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) ? 'text-danger' : '' }}">
                                {{ $coupon->used_count ?? 0 }}
                            </span>
                            @if($coupon->usage_limit)
                                / {{ $coupon->usage_limit }}
                            @else
                                <span class="text-muted">/ ∞</span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->start_date || $coupon->end_date)
                                @if($coupon->start_date)
                                    <small class="text-muted">From: {{ $coupon->start_date->format('d M, Y') }}</small><br>
                                @endif
                                @if($coupon->end_date)
                                    <small class="{{ $coupon->end_date->isPast() ? 'text-danger' : 'text-muted' }}">
                                        Until: {{ $coupon->end_date->format('d M, Y') }}
                                    </small>
                                @endif
                            @else
                                <span class="text-muted">No limit</span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->status === 'active')
                                @if($coupon->end_date && $coupon->end_date->isPast())
                                    <span class="badge bg-secondary">Expired</span>
                                @elseif($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit)
                                    <span class="badge bg-warning">Limit Reached</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.coupons.toggle', $coupon->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $coupon->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $coupon->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                    <i class="bi bi-{{ $coupon->status === 'active' ? 'pause' : 'play' }}-circle"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-ticket-perforated fs-1 d-block mb-2"></i>
                                <p class="mb-2">No coupons found.</p>
                                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Add your first coupon
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($coupons->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-center">
            {{ $coupons->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
