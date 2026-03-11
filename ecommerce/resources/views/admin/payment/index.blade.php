@extends('admin.layouts.app')

@section('title', 'Payment Methods')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Payment Methods</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGatewayModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Payment Method
    </button>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Methods</div>
                <div class="h4 mb-0 text-primary">{{ $gateways->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Active</div>
                <div class="h4 mb-0 text-success">{{ $gateways->where('is_active', true)->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Test Mode</div>
                <div class="h4 mb-0 text-warning">{{ $gateways->where('test_mode', true)->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Default</div>
                <div class="h4 mb-0">{{ $gateways->where('is_default', true)->first()->name ?? 'None' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Gateways Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Logo</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 100px;">Test Mode</th>
                        <th style="width: 100px;">Default</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gateways as $gateway)
                    <tr>
                        <td>
                            @if($gateway->logo)
                            <img src="{{ Storage::url($gateway->logo) }}" alt="{{ $gateway->name }}" class="rounded" style="width: 40px; height: 40px; object-fit: contain;">
                            @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-credit-card text-white"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-medium">{{ $gateway->name }}</div>
                        </td>
                        <td>
                            <code class="small">{{ $gateway->slug }}</code>
                        </td>
                        <td>
                            <span class="text-muted small">{{ Str::limit($gateway->description, 50) }}</span>
                        </td>
                        <td>
                            <form action="{{ route('admin.payment.toggle', $gateway->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm {{ $gateway->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                    {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            @if($gateway->test_mode)
                            <span class="badge bg-warning">Test</span>
                            @else
                            <span class="badge bg-secondary">Live</span>
                            @endif
                        </td>
                        <td>
                            @if($gateway->is_default)
                            <span class="badge bg-primary"><i class="bi bi-check me-1"></i>Default</span>
                            @else
                            <form action="{{ route('admin.payment.set-default', $gateway->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    Set Default
                                </button>
                            </form>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editGatewayModal{{ $gateway->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#credentialsModal{{ $gateway->id }}" title="Credentials">
                                    <i class="bi bi-key"></i>
                                </button>
                                <form action="{{ route('admin.payment.destroy', $gateway->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment method?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Gateway Modal -->
                    <div class="modal fade" id="editGatewayModal{{ $gateway->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit {{ $gateway->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.payment.update', $gateway->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $gateway->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                                            <input type="text" name="slug" class="form-control" value="{{ $gateway->slug }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="2">{{ $gateway->description }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Logo</label>
                                            <input type="file" name="logo" class="form-control" accept="image/*">
                                            @if($gateway->logo)
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($gateway->logo) }}" alt="" style="height: 40px;">
                                            </div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Sort Order</label>
                                            <input type="number" name="sort_order" class="form-control" value="{{ $gateway->sort_order }}">
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_active{{ $gateway->id }}" {{ $gateway->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_active{{ $gateway->id }}">Active</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="test_mode" id="edit_test{{ $gateway->id }}" {{ $gateway->test_mode ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_test{{ $gateway->id }}">Test Mode</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Credentials Modal -->
                    <div class="modal fade" id="credentialsModal{{ $gateway->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $gateway->name }} Credentials</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.payment.credentials', $gateway->slug) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p class="text-muted small mb-3">Enter the API credentials for {{ $gateway->name }}. These will be encrypted and stored securely.</p>
                                        
                                        @if($gateway->slug === 'bkash')
                                        <div class="mb-3">
                                            <label class="form-label">API Key</label>
                                            <input type="text" name="api_key" class="form-control" value="{{ $gateway->getCredential('api_key') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">API Secret</label>
                                            <input type="password" name="api_secret" class="form-control" value="{{ $gateway->getCredential('api_secret') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Merchant Number</label>
                                            <input type="text" name="merchant_number" class="form-control" value="{{ $gateway->getCredential('merchant_number') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">App Key</label>
                                            <input type="text" name="app_key" class="form-control" value="{{ $gateway->getCredential('app_key') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">App Secret</label>
                                            <input type="password" name="app_secret" class="form-control" value="{{ $gateway->getCredential('app_secret') ?? '' }}">
                                        </div>
                                        @elseif($gateway->slug === 'sslcommerz')
                                        <div class="mb-3">
                                            <label class="form-label">Store ID</label>
                                            <input type="text" name="store_id" class="form-control" value="{{ $gateway->getCredential('store_id') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Store Password</label>
                                            <input type="password" name="store_password" class="form-control" value="{{ $gateway->getCredential('store_password') ?? '' }}">
                                        </div>
                                        @elseif($gateway->slug === 'nagad')
                                        <div class="mb-3">
                                            <label class="form-label">Merchant ID</label>
                                            <input type="text" name="merchant_id" class="form-control" value="{{ $gateway->getCredential('merchant_id') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Merchant Key</label>
                                            <input type="password" name="merchant_key" class="form-control" value="{{ $gateway->getCredential('merchant_key') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Public Key</label>
                                            <textarea name="public_key" class="form-control">{{ $gateway->getCredential('public_key') ?? '' }}</textarea>
                                        </div>
                                        @elseif($gateway->slug === 'rocket')
                                        <div class="mb-3">
                                            <label class="form-label">Merchant ID</label>
                                            <input type="text" name="merchant_id" class="form-control" value="{{ $gateway->getCredential('merchant_id') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Merchant Number</label>
                                            <input type="text" name="merchant_number" class="form-control" value="{{ $gateway->getCredential('merchant_number') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control" value="{{ $gateway->getCredential('password') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">API Key</label>
                                            <input type="text" name="api_key" class="form-control" value="{{ $gateway->getCredential('api_key') ?? '' }}">
                                        </div>
                                        @elseif($gateway->slug === 'cod')
                                        <div class="mb-3">
                                            <label class="form-label">Instructions</label>
                                            <textarea name="instructions" class="form-control" rows="3">{{ $gateway->getCredential('instructions') ?? 'Pay with cash upon delivery.' }}</textarea>
                                            <div class="form-text">Instructions shown to customers at checkout</div>
                                        </div>
                                        @elseif(in_array($gateway->slug, ['stripe', 'paypal', 'razorpay']))
                                        <div class="mb-3">
                                            <label class="form-label">Client ID / Key ID</label>
                                            <input type="text" name="client_id" class="form-control" value="{{ $gateway->getCredential('client_id') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Client Secret / Secret Key</label>
                                            <input type="password" name="client_secret" class="form-control" value="{{ $gateway->getCredential('client_secret') ?? '' }}">
                                        </div>
                                        @else
                                        <div class="mb-3">
                                            <label class="form-label">API Key</label>
                                            <input type="text" name="api_key" class="form-control" value="{{ $gateway->getCredential('api_key') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">API Secret</label>
                                            <input type="password" name="api_secret" class="form-control" value="{{ $gateway->getCredential('api_secret') ?? '' }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Additional Settings (JSON)</label>
                                            <textarea name="additional_settings" class="form-control" rows="3">{{ json_encode($gateway->getCredential('additional_settings') ?? [], JSON_PRETTY_PRINT) }}</textarea>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Credentials</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-credit-card text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-2 mt-2">No payment methods found</p>
                            <button type="button" class="btn btn-sm btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#createGatewayModal">
                                <i class="bi bi-plus-lg me-1"></i> Add First Payment Method
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Gateway Modal -->
<div class="modal fade" id="createGatewayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.payment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., bKash" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control" placeholder="e.g., bkash" required>
                        <div class="form-text">Unique identifier (lowercase, no spaces)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief description of the payment method"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <div class="form-text">Recommended size: 128x128px, PNG or JPG</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="create_active" checked>
                        <label class="form-check-label" for="create_active">Active</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="test_mode" id="create_test" checked>
                        <label class="form-check-label" for="create_test">Test Mode</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Payment Method</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection
