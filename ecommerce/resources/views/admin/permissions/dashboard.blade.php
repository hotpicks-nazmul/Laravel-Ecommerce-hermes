@extends('admin.layouts.app')

@section('title', 'Permissions')

@php
    $keyExists = [];
    foreach ($permissions as $module => $perms) {
        foreach ($perms as $perm) {
            $keyExists[$perm->name] = true;
        }
    }
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Permissions</h4>
</div>

<ul class="nav nav-tabs mb-4" id="permissionsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="keys-tab" data-bs-toggle="tab" data-bs-target="#keys" type="button" role="tab">
            <i class="bi bi-key me-1"></i> Permission Keys
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab">
            <i class="bi bi-person-badge me-1"></i> Role Templates
            <span class="badge bg-secondary ms-1">{{ $roles->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab">
            <i class="bi bi-shield-lock me-1"></i> Staff Permissions
            <span class="badge bg-secondary ms-1">{{ $staff->total() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="permissionsTabsContent">

    {{-- ============ TAB 1: PERMISSION KEYS (Toggle Matrix) ============ --}}
    <div class="tab-pane fade show active" id="keys" role="tabpanel">

        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle me-2"></i>
            Toggle each action pill below to <strong>create</strong> or <strong>delete</strong> permission keys.
            Green pills = key exists, gray pills = no key.
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Permission Keys Matrix</h6>
                <span class="text-muted small">{{ $permissions->flatten()->count() }} keys</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 200px;">Module</th>
                                <th>Actions</th>
                                <th style="width: 90px;">Sidebar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissionModules as $moduleKey => $module)
                                @php
                                    $modulePerms = $permissions[$moduleKey] ?? collect();
                                    $submenus = \App\Helpers\PermissionHelper::submenus()[$moduleKey] ?? [];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $module['icon'] }} me-2"></i>
                                            <strong>{{ $module['label'] }}</strong>
                                            <span class="badge bg-primary ms-2">{{ $modulePerms->count() }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($allActions as $act)
                                                @php
                                                    $permName = $moduleKey . '.' . $act;
                                                    $exists = isset($keyExists[$permName]);
                                                @endphp
                                                <span class="badge rounded-pill perm-toggle"
                                                      data-module="{{ $moduleKey }}"
                                                      data-action="{{ $act }}"
                                                      data-state="{{ $exists ? '1' : '0' }}"
                                                      style="cursor:pointer; padding: 0.4em 0.8em; font-size: 0.82em; {{ $exists ? 'background: #198754; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }} transition: all 0.15s;">
                                                    {{ $act }}
                                                </span>
                                            @endforeach
                                        </div>
                                        @if(count($submenus) > 0)
                                        <div class="mt-2 pt-2 border-top">
                                            <small class="text-muted d-block mb-1"><i class="bi bi-layout-text-sidebar-reverse me-1"></i>Submenu:</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($submenus as $routeName => $label)
                                                    @php
                                                        $visible = \App\Helpers\PermissionHelper::isSubmenuVisible($routeName);
                                                    @endphp
                                                    <span class="badge rounded-pill submenu-visibility-toggle"
                                                          data-submenu="{{ $routeName }}"
                                                          data-state="{{ $visible ? '1' : '0' }}"
                                                          style="cursor:pointer; padding: 0.35em 0.7em; font-size: 0.75em; {{ $visible ? 'background: #0d6efd; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }} transition: all 0.15s;">
                                                        {{ $label }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input module-visibility-toggle" type="checkbox" role="switch"
                                                   data-module="{{ $moduleKey }}"
                                                   {{ \App\Helpers\PermissionHelper::isModuleVisible($moduleKey) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        No permissions yet. Click <strong>"Add Module"</strong> to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TAB 2: ROLE TEMPLATES ============ --}}
    <div class="tab-pane fade" id="roles" role="tabpanel">

        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Role Templates:</strong> Pre-configured permission sets assignable to staff.
        </div>

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Create Role
            </a>
        </div>

        <div class="row">
            @forelse($roles as $role)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="mb-0">
                                <i class="bi bi-shield-check text-success me-2"></i>
                                {{ ucwords(str_replace('-', ' ', $role->name)) }}
                            </h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('admin.roles.edit', $role->id) }}">
                                        <i class="bi bi-pencil me-2"></i> Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Delete this role? Staff assigned to it will lose those permissions.');">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <p class="text-muted small mb-2">
                            <code>{{ $role->name }}</code> &middot; {{ $role->permissions->count() }} permissions
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($role->permissions->take(8) as $perm)
                                <span class="badge bg-light text-dark border">{{ $perm->name }}</span>
                            @endforeach
                            @if($role->permissions->count() > 8)
                                <span class="badge bg-secondary">+{{ $role->permissions->count() - 8 }} more</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-person-badge" style="font-size: 3rem;"></i>
                    <p class="mt-2">No role templates yet.</p>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Create First Role
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ============ TAB 3: STAFF PERMISSIONS ============ --}}
    <div class="tab-pane fade" id="staff" role="tabpanel">

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Assign <strong>role templates</strong> for quick setup or toggle individual <strong>CRUD permissions</strong> per module.
                    Super admins and admins automatically have full access.
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Manage Staff Permissions</h6>
                <span class="text-muted small">{{ $staff->total() }} staff member(s)</span>
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
                                <th>Assigned Roles</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($staff->count() > 0)
                                @foreach($staff as $member)
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
                                        @if($member->role === 'super_admin')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-shield-check me-1"></i> Super Admin</span>
                                        @elseif($member->role === 'admin')
                                            <span class="badge bg-primary"><i class="bi bi-shield me-1"></i> Admin</span>
                                        @else
                                            <span class="badge bg-secondary">Staff</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($member->role === 'staff')
                                            @php $spatieRoles = $member->roles ?? collect(); @endphp
                                            @if($spatieRoles->isNotEmpty())
                                                @foreach($spatieRoles as $r)
                                                    <span class="badge bg-success me-1">{{ $r->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted small">Custom</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">Full Access</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$member->is_super_admin)
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#staffPermModal{{ $member->id }}">
                                                <i class="bi bi-gear"></i> Manage
                                            </button>
                                        @else
                                            <span class="text-muted small">No action needed</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mb-2 mt-2">No staff members found</p>
                                        <a href="{{ route('admin.staffs.create') }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Add First Staff
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if($staff->hasPages())
                    <div class="px-3 py-2 border-top">{{ $staff->links() }}</div>
                @endif
            </div>
        </div>

        @if($staff->count() > 0)
            @foreach($staff as $member)
                @if(!$member->is_super_admin)
                <div class="modal fade" id="staffPermModal{{ $member->id }}" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <form action="{{ route('admin.staffs.permissions.update') }}" method="POST" id="staffPermForm{{ $member->id }}">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i>Permissions - {{ $member->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="staff_id" value="{{ $member->id }}">

                                    <div class="card border mb-4">
                                        <div class="card-body">
                                            <label class="form-label fw-bold"><i class="bi bi-person-badge me-1"></i> Role Template</label>
                                            <select class="form-select staff-role-template" data-staff-id="{{ $member->id }}" name="role_template">
                                                <option value="">-- Custom Permissions --</option>
                                                @foreach($roleTemplates as $template)
                                                    <option value="{{ $template->id }}"
                                                        data-permissions="{{ json_encode($template->permissions->pluck('name')) }}"
                                                        {{ $member->roles->contains($template->id) ? 'selected' : '' }}>
                                                        {{ ucwords(str_replace('-', ' ', $template->name)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0"><i class="bi bi-shield-check me-1"></i>Individual Permissions</h6>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-success staff-select-all" data-staff-id="{{ $member->id }}">
                                                <i class="bi bi-check-all me-1"></i> Select All
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger staff-clear-all ms-1" data-staff-id="{{ $member->id }}">
                                                <i class="bi bi-x-circle me-1"></i> Clear All
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 140px;">Module</th>
                                                    <th>Permissions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($permissionModules as $moduleKey => $module)
                                                    @php $modulePrefix = $module['key']; @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="{{ $module['icon'] }} me-2"></i>
                                                                <strong>{{ $module['label'] }}</strong>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach($module['actions'] as $action)
                                                                    @php
                                                                        $permName = $modulePrefix . '.' . $action;
                                                                        $checked = $member->hasPermission($permName) ? '1' : '0';
                                                                    @endphp
                                                                    <span class="badge rounded-pill staff-perm-pill"
                                                                          data-staff-id="{{ $member->id }}"
                                                                          data-perm="{{ $permName }}"
                                                                          data-state="{{ $checked }}"
                                                                          style="cursor:pointer; padding: 0.4em 0.8em; font-size: 0.82em; {{ $checked == '1' ? 'background: #198754; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }} transition: all 0.15s;">
                                                                        {{ $action }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                            @if(\App\Helpers\PermissionHelper::submenus()[$moduleKey] ?? [])
                                                            <div class="mt-2 pt-2 border-top">
                                                                <small class="text-muted d-block mb-1"><i class="bi bi-layout-text-sidebar-reverse me-1"></i>Sidebar Submenus:</small>
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @php $submenus = \App\Helpers\PermissionHelper::submenus()[$moduleKey]; @endphp
                                                                    @foreach($submenus as $routeName => $label)
                                                                        @php $globalVisible = \App\Helpers\PermissionHelper::isSubmenuVisible($routeName); @endphp
                                                                        @if($globalVisible)
                                                                            @php
                                                                                $permName = 'submenu:' . $routeName;
                                                                                $disabledPerm = 'submenu_disabled:' . $routeName;
                                                                                $legacyPerms = is_array($member->legacy_permissions) ? $member->legacy_permissions : json_decode($member->legacy_permissions ?? '[]', true);
                                                                                $hasPerm = empty($legacyPerms) ? true : !in_array($disabledPerm, $legacyPerms);
                                                                            @endphp
                                                                            <span class="badge rounded-pill staff-submenu-pill"
                                                                                  data-staff-id="{{ $member->id }}"
                                                                                  data-submenu="{{ $routeName }}"
                                                                                  data-state="{{ $hasPerm ? '1' : '0' }}"
                                                                                  style="cursor:pointer; padding: 0.35em 0.7em; font-size: 0.75em; {{ $hasPerm ? 'background: #0d6efd; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }} transition: all 0.15s;">
                                                                                  {{ $label }}
                                                                            </span>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i> Cancel</button>
                                    <button type="button" class="btn btn-primary staff-save-perms" data-form-id="staffPermForm{{ $member->id }}">
                                        <i class="bi bi-check-lg me-1"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            @endforeach
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
    .bi::before, [class*="bi bi-"]::before {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        font-family: 'bootstrap-icons' !important;
    }
    .perm-toggle:hover, .staff-perm-pill:hover {
        transform: scale(1.08);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============ TAB 1: Permission Key Toggles (AJAX create/delete) ============
    document.querySelectorAll('.perm-toggle').forEach(function(pill) {
        pill.addEventListener('click', function() {
            const module = this.dataset.module;
            const action = this.dataset.action;
            const wasOn = this.dataset.state === '1';
            const originalHtml = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch('{{ route('admin.permissions.toggle-key') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ module: module, action: action })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.dataset.state = data.state ? '1' : '0';
                    if (data.state) {
                        this.style.background = '#198754';
                        this.style.color = '#fff';
                        if (typeof adminToast === 'function') {
                            adminToast('success', 'Created', "'" + module + '.' + action + "' created.");
                        }
                    } else {
                        this.style.background = '#e9ecef';
                        this.style.color = '#6c757d';
                        if (typeof adminToast === 'function') {
                            adminToast('warning', 'Removed', "'" + module + '.' + action + "' removed.");
                        }
                    }
                }
                this.innerHTML = action;
            })
            .catch(function() {
                this.innerHTML = action;
                if (typeof adminToast === 'function') {
                    adminToast('error', 'Error', 'Failed to toggle permission.');
                }
            }.bind(this));
        });
    });

    // Module visibility toggle
    document.querySelectorAll('.module-visibility-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const module = this.dataset.module;
            fetch('/admin/permissions/toggle-visibility/' + module, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success && typeof adminToast === 'function') {
                    adminToast(data.visible ? 'success' : 'warning', 'Sidebar Visibility', data.message);
                }
            });
        });
    });

    // Submenu visibility toggle (clickable badge)
    document.querySelectorAll('.submenu-visibility-toggle').forEach(function(badge) {
        badge.addEventListener('click', function() {
            const submenuKey = this.dataset.submenu;
            const wasOn = this.dataset.state === '1';
            const originalText = this.innerText;

            fetch('/admin/permissions/toggle-submenu/' + submenuKey, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.dataset.state = data.visible ? '1' : '0';
                    if (data.visible) {
                        this.style.background = '#0d6efd';
                        this.style.color = '#fff';
                    } else {
                        this.style.background = '#e9ecef';
                        this.style.color = '#6c757d';
                    }
                    if (typeof adminToast === 'function') {
                        adminToast(data.visible ? 'success' : 'warning', 'Submenu Visibility', data.message);
                    }
                }
                this.innerText = originalText;
            })
            .catch(function() {
                this.innerText = originalText;
            }.bind(this));
        });
    });

    // ============ TAB 3: Staff Permission Pills (visual toggle, saved on submit) ============
    document.querySelectorAll('.staff-perm-pill').forEach(function(pill) {
        pill.addEventListener('click', function() {
            const newState = this.dataset.state === '1' ? '0' : '1';
            this.dataset.state = newState;
            if (newState === '1') {
                this.style.background = '#0d6efd';
                this.style.color = '#fff';
            } else {
                this.style.background = '#e9ecef';
                this.style.color = '#6c757d';
            }
        });
    });

    // Submenu pill toggle (clickable, stays blue when enabled)
    document.querySelectorAll('.staff-submenu-pill').forEach(function(pill) {
        pill.addEventListener('click', function() {
            const newState = this.dataset.state === '1' ? '0' : '1';
            this.dataset.state = newState;
            if (newState === '1') {
                this.style.background = '#0d6efd';
                this.style.color = '#fff';
            } else {
                this.style.background = '#e9ecef';
                this.style.color = '#6c757d';
            }
        });
    });

    // Role template selection fills pills
    document.querySelectorAll('.staff-role-template').forEach(function(select) {
        select.addEventListener('change', function() {
            const staffId = this.dataset.staffId;
            const selected = this.options[this.selectedIndex];
            const form = document.getElementById('staffPermForm' + staffId);
            if (!form) return;

            // Reset all pills in this form to OFF
            form.querySelectorAll('.staff-perm-pill, .staff-submenu-pill').forEach(function(p) {
                p.dataset.state = '0';
                p.style.background = '#e9ecef';
                p.style.color = '#6c757d';
            });

            if (this.value) {
                try {
                    const perms = JSON.parse(selected.dataset.permissions || '[]');
                    perms.forEach(function(permName) {
                        // Regular permission pill
                        const pill = form.querySelector('.staff-perm-pill[data-perm="' + permName + '"]');
                        if (pill) {
                            pill.dataset.state = '1';
                            pill.style.background = '#0d6efd';
                            pill.style.color = '#fff';
                        }
                        // Submenu permission pill (submenu:routeName format)
                        if (permName.startsWith('submenu:')) {
                            const subpill = form.querySelector('.staff-submenu-pill[data-submenu="' + permName.replace('submenu:', '') + '"]');
                            if (subpill) {
                                subpill.dataset.state = '1';
                                subpill.style.background = '#0d6efd';
                                subpill.style.color = '#fff';
                            }
                        }
                    });
                } catch(e) {}
            }
        });
    });

    // Select All within a staff form
    document.querySelectorAll('.staff-select-all').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const staffId = this.dataset.staffId;
            const form = document.getElementById('staffPermForm' + staffId);
            if (!form) return;
            form.querySelectorAll('.staff-perm-pill, .staff-submenu-pill').forEach(function(p) {
                p.dataset.state = '1';
                p.style.background = '#0d6efd';
                p.style.color = '#fff';
            });
        });
    });

    // Clear All within a staff form
    document.querySelectorAll('.staff-clear-all').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const staffId = this.dataset.staffId;
            const form = document.getElementById('staffPermForm' + staffId);
            if (!form) return;
            form.querySelectorAll('.staff-perm-pill, .staff-submenu-pill').forEach(function(p) {
                p.dataset.state = '0';
                p.style.background = '#e9ecef';
                p.style.color = '#6c757d';
            });
            const select = form.querySelector('.staff-role-template');
            if (select) select.value = '';
        });
    });

    // Save permissions via AJAX
    document.querySelectorAll('.staff-save-perms').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const formId = this.dataset.formId;
            const form = document.getElementById(formId);
            if (!form) return;

            // Build permissions array from pills that are ON
            const checkedPerms = [];
            form.querySelectorAll('.staff-perm-pill[data-state="1"]').forEach(function(p) {
                checkedPerms.push(p.dataset.perm);
            });

            // Build submenu permissions array
            // ON: submenu:routeName, OFF: submenu_disabled:routeName
            const checkedSubmenus = [];
            const uncheckedSubmenus = [];
            form.querySelectorAll('.staff-submenu-pill').forEach(function(p) {
                if (p.dataset.state === '1') {
                    checkedSubmenus.push('submenu:' + p.dataset.submenu);
                } else {
                    uncheckedSubmenus.push('submenu_disabled:' + p.dataset.submenu);
                }
            });

            const formData = new FormData(form);
            // Remove any existing permissions[] entries and add our built list
            checkedPerms.forEach(function(p) {
                formData.append('permissions[]', p);
            });
            // Add enabled submenus
            checkedSubmenus.forEach(function(p) {
                formData.append('permissions[]', p);
            });
            // Add disabled submenus
            uncheckedSubmenus.forEach(function(p) {
                formData.append('permissions[]', p);
            });

            const submitBtn = this;
            const originalText = submitBtn.innerHTML;
            const modalEl = form.closest('.modal');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                if (!response.ok) throw new Error('Request failed');
                return response.json();
            })
            .then(function(data) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                if (typeof adminToast === 'function') {
                    adminToast('success', 'Success!', 'Permissions updated successfully.');
                } else {
                    alert('Permissions saved successfully!');
                }
                setTimeout(function() { location.reload(); }, 1500);
            })
            .catch(function(err) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (typeof adminToast === 'function') {
                    adminToast('error', 'Error!', 'Failed to save permissions.');
                } else {
                    alert('Failed to save permissions.');
                }
            });
        });
    });
});
</script>
@endpush
