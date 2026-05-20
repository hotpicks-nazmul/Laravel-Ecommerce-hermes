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
        <div class="card-body p-3">
            <div class="role-perm-modules">
                @foreach($permissionModules as $moduleKey => $module)
                    @php
                        $modulePrefix = $module['key'];
                        $submenus = \App\Helpers\PermissionHelper::submenus()[$moduleKey] ?? [];
                        $pageSectionPerms = \App\Helpers\PermissionHelper::pageSectionPermissions();
                        $sectionActions = \App\Helpers\PermissionHelper::sectionActions();
                        $moduleSupportedActions = $moduleActions[$moduleKey] ?? [];
                        $nonSectionActions = array_values(array_filter($moduleSupportedActions, fn($a) => !in_array($a, $sectionActions)));
                        $hasAny = !empty($nonSectionActions) || !empty($submenus);
                    @endphp
                    @if($hasAny)
                    <div class="card border-0 rounded-3 mb-2 role-module-group" data-module="{{ $moduleKey }}" style="background: #fafbfc;">
                        <div class="role-module-header d-flex align-items-center gap-2 px-3 py-2" style="cursor:pointer;">
                            <span class="role-collapse-icon text-muted" style="font-size: 0.8rem; width: 16px; flex-shrink: 0;">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                            <div style="flex: 0 0 130px; flex-shrink: 0; display: flex; align-items: center; gap: 6px;">
                                <i class="{{ $module['icon'] }}" style="font-size: 1rem;"></i>
                                <strong style="font-size: 0.85rem;">{{ $module['label'] }}</strong>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                @if(!empty($nonSectionActions))
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($nonSectionActions as $action)
                                        @php
                                            $permName = $modulePrefix . '.' . $action;
                                            $checked = $role->permissions->contains('name', $permName);
                                            if (old('permissions')) {
                                                $checked = in_array($permName, old('permissions', []));
                                            }
                                        @endphp
                                        <span class="badge rounded-pill role-perm-pill"
                                              data-perm="{{ $permName }}"
                                              data-state="{{ $checked ? '1' : '0' }}"
                                              style="cursor:pointer; padding: 0.2em 0.65em; font-size: 0.8em; {{ $checked ? 'background: #198754; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }} transition: all 0.15s;">
                                            {{ $action }}
                                        </span>
                                        <input type="checkbox" name="permissions[]" value="{{ $permName }}"
                                               class="d-none role-perm-checkbox"
                                               {{ $checked ? 'checked' : '' }}>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <span class="text-muted" style="font-size: 0.7rem; flex-shrink: 0;">{{ count($submenus) }}p</span>
                        </div>
                        @if(!empty($submenus))
                        <div class="role-submenu-content" style="display: none;">
                            <div style="border-top: 1px solid #e9ecef; margin: 0 12px;"></div>
                            @php
                                $allPageComponents = \App\Helpers\PermissionHelper::pageComponents();
                                $childPages = \App\Helpers\PermissionHelper::childPages();
                            @endphp
                            @foreach($submenus as $routeName => $label)
                                @php
                                    $disabledKey = 'submenu_disabled:' . $routeName;
                                    $roleSettings = $role->submenu_visibility ?? [];
                                    $oldVis = old('submenu_visibility');
                                    if ($oldVis) {
                                        $isSubmenuVisible = !in_array($disabledKey, $oldVis);
                                    } else {
                                        $isSubmenuVisible = !in_array($disabledKey, $roleSettings);
                                    }
                                    $submenuState = $isSubmenuVisible ? '1' : '0';
                                    $submenuValue = $isSubmenuVisible ? 'submenu:' . $routeName : 'submenu_disabled:' . $routeName;
                                    $routeSections = $pageSectionPerms[$routeName] ?? [];
                                    $listComponents = $allPageComponents[$routeName] ?? [];
                                    $submenuChildren = $childPages[$routeName] ?? [];
                                @endphp
                                <div class="d-flex align-items-center gap-2 px-3 py-1-5" style="padding: 5px 12px 5px 44px;">
                                    <div style="flex: 0 0 200px; flex-shrink: 0; display: flex; align-items: center;">
                                        <span class="badge rounded-pill role-submenu-pill d-inline-flex align-items-center gap-1"
                                              data-submenu="{{ $routeName }}"
                                              data-state="{{ $submenuState }}"
                                              style="cursor:pointer; padding: 0.25em 0.6em; font-size: 0.72rem; white-space: nowrap; width: 100%; text-align: left; {{ $isSubmenuVisible ? 'background: #0d6efd; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }}">
                                            <i class="bi {{ $isSubmenuVisible ? 'bi-eye' : 'bi-eye-slash' }}"></i> <span style="overflow: hidden; text-overflow: ellipsis;">{{ $label }}</span>
                                        </span>
                                        <input type="hidden" name="submenu_visibility[]" value="{{ $submenuValue }}" class="role-submenu-input">
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        @if(!empty($routeSections))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($routeSections as $action)
                                                @php
                                                    $sPermName = $modulePrefix . '.' . $action;
                                                    $sChecked = $role->permissions->contains('name', $sPermName);
                                                    if (old('permissions')) {
                                                        $sChecked = in_array($sPermName, old('permissions', []));
                                                    }
                                                @endphp
                                                <span class="badge rounded-pill role-perm-pill"
                                                      data-perm="{{ $sPermName }}"
                                                      data-state="{{ $sChecked ? '1' : '0' }}"
                                                      style="cursor:pointer; padding: 0.2em 0.65em; font-size: 0.78em; {{ $sChecked ? 'background: #6f42c1; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }} transition: all 0.15s;">
                                                    {{ str_replace('view-', '', $action) }}
                                                </span>
                                                <input type="checkbox" name="permissions[]" value="{{ $sPermName }}"
                                                       class="d-none role-perm-checkbox"
                                                       {{ $sChecked ? 'checked' : '' }}>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                {{-- Tree: page component groups --}}
                                @if(!empty($listComponents))
                                    @foreach($listComponents as $groupLabel => $groupPerms)
                                    <div class="d-flex align-items-center gap-2 px-3 py-1" style="padding: 2px 12px 2px 58px;">
                                        <div style="flex: 0 0 158px; flex-shrink: 0;">
                                            <small class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-chevron-right me-1"></i>{{ $groupLabel }}</small>
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($groupPerms as $permName)
                                                    @php
                                                        $checked = $role->permissions->contains('name', $permName);
                                                        if (old('permissions')) {
                                                            $checked = in_array($permName, old('permissions', []));
                                                        }
                                                        $parts = explode('.', $permName);
                                                        $shortLabel = str_replace('-', ' ', end($parts));
                                                    @endphp
                                                    <span class="badge rounded-pill role-perm-pill role-page-action-pill"
                                                          data-perm="{{ $permName }}"
                                                          data-state="{{ $checked ? '1' : '0' }}"
                                                          style="cursor:pointer; padding: 0.2em 0.65em; font-size: 0.72rem; {{ $checked ? 'background: #e86c00; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }} transition: all 0.15s;">
                                                        {{ $shortLabel }}
                                                    </span>
                                                    <input type="checkbox" name="permissions[]" value="{{ $permName }}"
                                                           class="d-none role-perm-checkbox"
                                                           {{ $checked ? 'checked' : '' }}>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                                {{-- Tree: child detail pages --}}
                                @foreach($submenuChildren as $childRoute)
                                    @php
                                        $childComponents = $allPageComponents[$childRoute] ?? [];
                                        $childLabel = Str::of($childRoute)->afterLast('.')->replace('-', ' ')->title();
                                    @endphp
                                    @if(!empty($childComponents))
                                        <div class="d-flex align-items-center px-3 py-1" style="padding: 2px 12px 2px 58px;">
                                            <small class="text-muted fw-semibold" style="font-size: 0.72rem;"><i class="bi bi-layout-sidebar me-1"></i>{{ $childLabel }}</small>
                                        </div>
                                        @foreach($childComponents as $groupLabel => $groupPerms)
                                        <div class="d-flex align-items-center gap-2 px-3 py-1" style="padding: 1px 12px 1px 78px;">
                                            <div style="flex: 0 0 158px; flex-shrink: 0;">
                                                <small class="text-muted" style="font-size: 0.68rem;"><i class="bi bi-chevron-right me-1"></i>{{ $groupLabel }}</small>
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($groupPerms as $permName)
                                                        @php
                                                            $checked = $role->permissions->contains('name', $permName);
                                                            if (old('permissions')) {
                                                                $checked = in_array($permName, old('permissions', []));
                                                            }
                                                            $parts = explode('.', $permName);
                                                            $shortLabel = str_replace('-', ' ', end($parts));
                                                        @endphp
                                                        <span class="badge rounded-pill role-perm-pill role-page-action-pill"
                                                              data-perm="{{ $permName }}"
                                                              data-state="{{ $checked ? '1' : '0' }}"
                                                              style="cursor:pointer; padding: 0.2em 0.65em; font-size: 0.7rem; {{ $checked ? 'background: #e86c00; color: #fff;' : 'background: #e9ecef; color: #6c757d;' }} transition: all 0.15s;">
                                                            {{ $shortLabel }}
                                                        </span>
                                                        <input type="checkbox" name="permissions[]" value="{{ $permName }}"
                                                               class="d-none role-perm-checkbox"
                                                               {{ $checked ? 'checked' : '' }}>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endif
                @endforeach
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

@push('styles')
<style>
    .bi::before, [class*="bi bi-"]::before {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        font-family: 'bootstrap-icons' !important;
    }
    .role-perm-pill:hover {
        transform: scale(1.08);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============ MODULE COLLAPSE/EXPAND ============
    document.querySelectorAll('.role-module-header').forEach(function(header) {
        header.addEventListener('click', function(e) {
            if (e.target.closest('.role-perm-pill, .role-submenu-pill')) return;
            const group = this.closest('.role-module-group');
            const content = group.querySelector('.role-submenu-content');
            const icon = this.querySelector('.role-collapse-icon i');
            if (!content) return;
            const expanded = content.style.display !== 'none';
            content.style.display = expanded ? 'none' : 'block';
            icon.className = expanded ? 'bi bi-chevron-right' : 'bi bi-chevron-down';
        });
    });

    // ============ TOGGLE PERMISSION PILLS ============
    document.querySelectorAll('.role-perm-pill').forEach(function(pill) {
        pill.addEventListener('click', function(e) {
            e.stopPropagation();
            const newState = this.dataset.state === '1' ? '0' : '1';
            this.dataset.state = newState;
            if (newState === '1') {
                const isSection = this.dataset.perm && this.dataset.perm.match(/\.(view-customer|view-pricing|view-cost|view-financial|view-revenue|view-sales)$/);
                const isPageAction = this.classList.contains('role-page-action-pill');
                this.style.background = isPageAction ? '#e86c00' : (isSection ? '#6f42c1' : '#198754');
                this.style.color = '#fff';
            } else {
                this.style.background = '#e9ecef';
                this.style.color = '#6c757d';
            }
            const form = this.closest('form');
            const cb = form.querySelector('input[value="' + this.dataset.perm + '"]');
            if (cb) cb.checked = newState === '1';
        });
    });

    // ============ TOGGLE SUBMENU VISIBILITY ============
    document.querySelectorAll('.role-submenu-pill').forEach(function(pill) {
        pill.addEventListener('click', function(e) {
            e.stopPropagation();
            const newState = this.dataset.state === '1' ? '0' : '1';
            this.dataset.state = newState;
            const icon = this.querySelector('i');
            if (newState === '1') {
                this.style.background = '#0d6efd';
                this.style.color = '#fff';
                icon.className = 'bi bi-eye';
            } else {
                this.style.background = '#e9ecef';
                this.style.color = '#6c757d';
                icon.className = 'bi bi-eye-slash';
            }
            const form = this.closest('form');
            const submenu = this.dataset.submenu;
            const input = form.querySelector('.role-submenu-input[value="submenu:' + submenu + '"], .role-submenu-input[value="submenu_disabled:' + submenu + '"]');
            if (input) {
                input.value = newState === '1' ? 'submenu:' + submenu : 'submenu_disabled:' + submenu;
            }
        });
    });

    // ============ SELECT ALL ============
    document.getElementById('selectAllPermissions')?.addEventListener('click', function() {
        document.querySelectorAll('.role-perm-pill').forEach(function(pill) {
            pill.dataset.state = '1';
            const isSection = pill.dataset.perm && pill.dataset.perm.match(/\.(view-customer|view-pricing|view-cost|view-financial|view-revenue|view-sales)$/);
            const isPageAction = pill.classList.contains('role-page-action-pill');
            pill.style.background = isPageAction ? '#e86c00' : (isSection ? '#6f42c1' : '#198754');
            pill.style.color = '#fff';
            const form = pill.closest('form');
            const cb = form.querySelector('input[value="' + pill.dataset.perm + '"]');
            if (cb) cb.checked = true;
        });
    });

    // ============ CLEAR ALL ============
    document.getElementById('clearAllPermissions')?.addEventListener('click', function() {
        document.querySelectorAll('.role-perm-pill').forEach(function(pill) {
            pill.dataset.state = '0';
            pill.style.background = '#e9ecef';
            pill.style.color = '#6c757d';
            const form = pill.closest('form');
            const cb = form.querySelector('input[value="' + pill.dataset.perm + '"]');
            if (cb) cb.checked = false;
        });
    });

});
</script>
@endpush
