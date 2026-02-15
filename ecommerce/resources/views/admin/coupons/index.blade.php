@extends('admin.layouts.app')

@section('title', 'Coupons')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Coupons</h4>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Coupon
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="couponsTable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Min Order</th>
                        <th>Usage</th>
                        <th>Valid Until</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons ?? [] as $coupon)
                    <tr>
                        <td><strong>{{ $coupon->code }}</strong></td>
                        <td>{{ ucfirst($coupon->type) }}</td>
                        <td>
                            @if($coupon->type === 'percentage')
                                {{ $coupon->value }}%
                            @else
                                ৳{{ number_format($coupon->value, 2) }}
                            @endif
                        </td>
                        <td>৳{{ number_format($coupon->min_order ?? 0, 2) }}</td>
                        <td>{{ $coupon->used_count ?? 0 }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                        <td>{{ $coupon->valid_until ? $coupon->valid_until->format('d M, Y') : 'No Limit' }}</td>
                        <td>
                            <span class="badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">No coupons found. <a href="{{ route('admin.coupons.create') }}">Add your first coupon</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#couponsTable').DataTable({
        pageLength: 25
    });
});
</script>
@endpush
