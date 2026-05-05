@extends('admin.layouts.app')

@section('title', 'Create Role Template')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Create Role Template</h4>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Roles
    </a>
</div>

<form action="{{ route('admin.roles.store') }}" method="POST" id="roleForm">
    @csrf
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Role Name <small class="text-muted">(slug format, e.g. order-manager)</small></label>
                <input type="text" name="name" class="form-control" placeholder="order-manager" required pattern="[a-z-]+">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Permissions</h6>
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
                            <tr>
                                <td>
                                    <strong class="text-capitalize">{{ $module }}</strong>
                                    <span class="badge bg-primary ms-1" id="moduleCount_{{ $module }}">0/{{ $perms->count() }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($perms as $perm)
                                            @php $checked = old('permissions') ? in_array($perm->name, old('permissions', [])) : false; @endphp
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
            <i class="bi bi-check-lg me-1"></i> Create Role
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle pills - sync hidden checkbox
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
            // Sync hidden checkbox
            const cb = row.querySelector('.role-perm-checkbox[value="' + this.dataset.perm + '"]');
            if (cb) cb.checked = newState === '1';
            // Update module count badge
            const pills = row.querySelectorAll('.role-perm-pill');
            const checked = row.querySelectorAll('.role-perm-pill[data-state="1"]');
            document.getElementById('moduleCount_' + module).textContent = checked.length + '/' + pills.length;
        });
    });

    // Select All
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
            const module = el.id.replace('moduleCount_', '');
            const row = el.closest('tr');
            const pills = row.querySelectorAll('.role-perm-pill');
            el.textContent = pills.length + '/' + pills.length;
        });
    });

    // Clear All
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
            el.textContent = '0/' + el.textContent.split('/')[1];
        });
    });
});
</script>
@endpush
