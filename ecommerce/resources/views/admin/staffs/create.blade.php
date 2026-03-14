@extends('admin.layouts.app')

@section('title', 'Add New Staff')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add New Staff</h4>
    <a href="{{ route('admin.staffs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Staffs
    </a>
</div>

<form id="staffForm" method="POST" action="{{ route('admin.staffs.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" id="designation" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" placeholder="e.g. Sales Manager, Support Agent">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    @if(count($allowedRoles) > 1)
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">Select Role</option>
                                @foreach($allowedRoles as $role)
                                    <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                @if(in_array('admin', $allowedRoles))
                                    <span class="text-info">Admin: Can create staff and manage business operations</span>
                                @endif
                                @if(in_array('staff', $allowedRoles))
                                    <span class="text-info">Staff: Limited access based on permissions</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                        <input type="hidden" name="role" value="{{ $allowedRoles[0] }}">
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avatar Upload -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>Profile Photo</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Upload Photo</label>
                        <input type="file" id="avatar" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                        <div class="form-text">Recommended size: 200x200px. Max size: 2MB.</div>
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status Settings -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Account Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="banned" {{ old('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" value="1" {{ old('is_super_admin') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_super_admin">
                            <i class="bi bi-shield-check text-warning me-1"></i> Super Admin
                        </label>
                        <div class="form-text">Super admins have access to all features</div>
                    </div>
                </div>
            </div>

            <!-- Warehouse Assignment -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Warehouse Assignment</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="warehouse_id" class="form-label">Assign to Warehouse</label>
                        <select id="warehouse_id" name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                            <option value="">No Warehouse Assigned</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave empty if this staff doesn't belong to any warehouse</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Floating Buttons -->
<div class="floating-save-container">
    <a href="{{ route('admin.staffs.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="staffForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create Staff
    </button>
</div>
@endsection

@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush
