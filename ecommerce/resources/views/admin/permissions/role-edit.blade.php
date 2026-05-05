@extends('admin.layouts.app')

@section('title', 'Edit Role: ' . $role->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Role: <strong>{{ $role->name }}</strong></h4>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Roles
    </a>
</div>

<form action="{{ route('admin.roles.update', $role->id) }}" method="POST" id="roleForm">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="text" name="name" class="form-control" value="{{ $role->name }}" required pattern="[a-z-]+">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Permissions ({{ $role->permissions->count() }} assigned)</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-success" id="selectAllPermissions">
                    <i class="bi bi-check-all me-1"></i> Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger ms-1" id="clearAllPermissions">
                    <i class="bi bi-x-circle me-1"></i> Clear All
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 140px;">Module</th>
                            <th>Permissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $module => $perms)
                            @php
                                $moduleAssigned = $perms->filter(fn($p) => $role->permissions->contains('name', $p->name));
                            @endphp
                            <tr>
                                <td>
                                    <strong class="text-capitalize">{{ $module }}</strong>
                                    <span class="badge {{ $moduleAssigned->count() > 0 ? 'bg-success' : 'bg-secondary' }} ms-1" id="moduleCount_{{ $module }}">
                                        {{ $moduleAssigned->count() }}/{{ $perms->count() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($perms as $perm)
                                            @php $checked = $role->permissions->contains('name', $perm->name); @endphp
                                            <span class="badge rounded-pill role-perm-pill"
                                                  data-perm="{{ $perm->name }}"
                                                  data-module="{{ $module }}"
                                                  data-state="{{ $checked ? '1' : '0' }}"
                                                  style="cursor:pointer; padding: 0.4em 0.8em; font-size: 0.82em; {{ $checked ? 'background: #198754; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }} transition: all 0.15s;">
                                                {{ explode('.', $perm->name)[1] ?? $perm->name }}
                                            </span>
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                                   class="d-none role-perm-checkbox"
                                                   {{ $checked ? 'checked' : '' }}>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="floating-save-container">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> Update Role
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.role-perm-pill').forEach(function(pill) {
        pill.addEventListener('click', function() {
            const row = this.closest('tr');
            const module = this.dataset.module;
            const newState = this.dataset.state === '1' ? '0' : '1';
            this.dataset.state = newState;
            if (newState === '1') {
                this.style.background = '#198754';
                this.style.color = '#fff';
            } else {
                this.style.background = '#e9ecef';
                this.style.color = '#6c757d';
            }
            const cb = row.querySelector('.role-perm-checkbox[value="' + this.dataset.perm + '"]');
            if (cb) cb.checked = newState === '1';
            const pills = row.querySelectorAll('.role-perm-pill');
            const checked = row.querySelectorAll('.role-perm-pill[data-state="1"]');
            const badge = document.getElementById('moduleCount_' + module);
            badge.textContent = checked.length + '/' + pills.length;
            badge.className = 'badge ms-1';
            badge.classList.add(checked.length > 0 ? 'bg-success' : 'bg-secondary');
        });
    });

    document.getElementById('selectAllPermissions')?.addEventListener('click', function() {
        document.querySelectorAll('.role-perm-pill').forEach(function(pill) {
            pill.dataset.state = '1';
            pill.style.background = '#198754';
            pill.style.color = '#fff';
            const row = pill.closest('tr');
            const cb = row.querySelector('.role-perm-checkbox[value="' + pill.dataset.perm + '"]');
            if (cb) cb.checked = true;
        });
        document.querySelectorAll('[id^="moduleCount_"]').forEach(function(el) {
            const row = el.closest('tr');
            const pills = row.querySelectorAll('.role-perm-pill');
            el.textContent = pills.length + '/' + pills.length;
            el.className = 'badge ms-1 bg-success';
        });
    });

    document.getElementById('clearAllPermissions')?.addEventListener('click', function() {
        document.querySelectorAll('.role-perm-pill').forEach(function(pill) {
            pill.dataset.state = '0';
            pill.style.background = '#e9ecef';
            pill.style.color = '#6c757d';
            const row = pill.closest('tr');
            const cb = row.querySelector('.role-perm-checkbox[value="' + pill.dataset.perm + '"]');
            if (cb) cb.checked = false;
        });
        document.querySelectorAll('[id^="moduleCount_"]').forEach(function(el) {
            const total = el.textContent.split('/')[1];
            el.textContent = '0/' + total;
            el.className = 'badge ms-1 bg-secondary';
        });
    });
});
</script>
@endpush
