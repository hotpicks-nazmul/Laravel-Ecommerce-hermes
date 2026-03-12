@extends('admin.layouts.app')

@section('title', 'Staff Permissions')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Staff Permissions</h4>
    <a href="{{ route('admin.staffs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Staffs
    </a>
</div>

<!-- Info Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Note:</strong> Staff permissions allow you to control what each staff member can access in the admin panel. 
            Super admins automatically have access to all features regardless of their permissions.
        </div>
    </div>
</div>

<!-- Staff List with Permissions -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Manage Staff Permissions</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Staff Member</th>
                        <th>Designation</th>
                        <th>Warehouse</th>
                        <th>Role</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php
                                        $avatarUrl = $member->avatar;
                                        if ($avatarUrl && !str_starts_with($avatarUrl, '/storage/') && !str_starts_with($avatarUrl, 'http')) {
                                            $avatarUrl = '/storage/' . $avatarUrl;
                                        }
                                    @endphp
                                    @if($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $member->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $member->name }}</div>
                                        <div class="small text-muted">{{ $member->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $member->designation ?? 'N/A' }}</td>
                            <td>
                                @if($member->warehouse)
                                    <span class="badge bg-info">{{ $member->warehouse->name }}</span>
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($member->is_super_admin)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-shield-check me-1"></i> Super Admin
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Staff</span>
                                @endif
                            </td>
                            <td>
                                @if($member->is_super_admin)
                                    <span class="text-muted small">No action needed</span>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#permissionsModal{{ $member->id }}">
                                        <i class="bi bi-gear"></i> Manage
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Permissions Modal -->
                        @if(!$member->is_super_admin)
                        <div class="modal fade" id="permissionsModal{{ $member->id }}" tabindex="-1" aria-labelledby="permissionsModalLabel{{ $member->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('admin.staffs.permissions.update') }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="permissionsModalLabel{{ $member->id }}">
                                                Manage Permissions - {{ $member->name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="staff_id" value="{{ $member->id }}">
                                            
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="products" id="perm_products_{{ $member->id }}"
                                                            {{ in_array('products', json_decode($member->permissions ?? '[]', true) ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_products_{{ $member->id }}">
                                                            <i class="bi bi-box me-1"></i> Products Management
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="orders" id="perm_orders_{{ $member->id }}"
                                                            {{ in_array('orders', json_decode($member->permissions ?? '[]', true) ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_orders_{{ $member->id }}">
                                                            <i class="bi bi-cart me-1"></i> Orders Management
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="customers" id="perm_customers_{{ $member->id }}"
                                                            {{ in_array('customers', json_decode($member->permissions ?? '[]', true) ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_customers_{{ $member->id }}">
                                                            <i class="bi bi-people me-1"></i> Customers Management
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="reports" id="perm_reports_{{ $member->id }}"
                                                            {{ in_array('reports', json_decode($member->permissions ?? '[]', true) ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_reports_{{ $member->id }}">
                                                            <i class="bi bi-graph-up me-1"></i> Reports & Analytics
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="inventory" id="perm_inventory_{{ $member->id }}"
                                                            {{ in_array('inventory', json_decode($member->permissions ?? '[]', true) ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_inventory_{{ $member->id }}">
                                                            <i class="bi bi-box-seam me-1"></i> Inventory Management
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="marketing" id="perm_marketing_{{ $member->id }}"
                                                            {{ in_array('marketing', json_decode($member->permissions ?? '[]', true) ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_marketing_{{ $member->id }}">
                                                            <i class="bi bi-megaphone me-1"></i> Marketing Tools
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="settings" id="perm_settings_{{ $member->id }}"
                                                            {{ in_array('settings', json_decode($member->permissions ?? '[]', true) ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_settings_{{ $member->id }}">
                                                            <i class="bi bi-gear me-1"></i> System Settings
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="support" id="perm_support_{{ $member->id }}"
                                                            {{ in_array('support', json_decode($member->permissions ?? '[]', true) ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_support_{{ $member->id }}">
                                                            <i class="bi bi-headset me-1"></i> Support & Tickets
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-2 mt-2">No staff members found</p>
                                <a href="{{ route('admin.staffs.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Add First Staff
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
