@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Customers</h4>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="customersTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers ?? [] as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                        <td><span class="badge bg-info">{{ $customer->orders_count ?? 0 }}</span></td>
                        <td>৳{{ number_format($customer->total_spent ?? 0, 2) }}</td>
                        <td>{{ $customer->created_at->format('d M, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No customers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($customers) && method_exists($customers, 'hasPages') && $customers->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#customersTable').DataTable({
        pageLength: 25
    });
});
</script>
@endpush
