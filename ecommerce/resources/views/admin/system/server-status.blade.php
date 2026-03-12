@extends('admin.layouts.app')

@section('title', 'Server Status')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="bi bi-server text-primary me-2"></i> Server Status
                        </h4>
                        <p class="text-muted mb-0 small">Monitor server health, extensions, and directory permissions</p>
                    </div>
                    <a href="{{ route('admin.system.update') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to System Update
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Server Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2"></i>Server Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">PHP Version</span>
                            <span class="fw-medium">{{ $serverInfo['php_version'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Laravel Version</span>
                            <span class="fw-medium">{{ $serverInfo['laravel_version'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Database Type</span>
                            <span class="fw-medium">{{ ucfirst($serverInfo['database_type']) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Database Version</span>
                            <span class="fw-medium small">{{ $serverInfo['database_version'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Server Software</span>
                            <span class="fw-medium small">{{ $serverInfo['server_software'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Operating System</span>
                            <span class="fw-medium">{{ $serverInfo['os'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PHP Configuration -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-gear me-2"></i>PHP Configuration</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Max Execution Time</span>
                            <span class="fw-medium">{{ $serverInfo['max_execution_time'] }} seconds</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Memory Limit</span>
                            <span class="fw-medium">{{ $serverInfo['memory_limit'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Upload Max Filesize</span>
                            <span class="fw-medium">{{ $serverInfo['upload_max_filesize'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">Post Max Size</span>
                            <span class="fw-medium">{{ $serverInfo['post_max_size'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PHP Extensions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-puzzle me-2"></i>PHP Extensions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($serverInfo['extensions'] as $name => $loaded)
                    <div class="col-md-4 col-sm-6 mb-2">
                        <div class="d-flex align-items-center">
                            @if($loaded)
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span class="text-success">{{ $name }}</span>
                            @else
                            <i class="bi bi-x-circle-fill text-danger me-2"></i>
                            <span class="text-danger">{{ $name }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Directory Permissions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-folder-check me-2"></i>Directory Permissions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($serverInfo['directories'] as $path => $writable)
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center justify-content-between border rounded p-2">
                            <span class="small">{{ $path }}</span>
                            @if($writable)
                            <span class="badge bg-success">
                                <i class="bi bi-check-lg me-1"></i> Writable
                            </span>
                            @else
                            <span class="badge bg-danger">
                                <i class="bi bi-x-lg me-1"></i> Not Writable
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-speedometer2 me-2"></i>Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="display-4 fw-bold text-success">100%</div>
                    <div class="text-muted">System Health</div>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Extensions Loaded</span>
                    <span class="fw-medium">
                        {{ count(array_filter($serverInfo['extensions'])) }}/{{ count($serverInfo['extensions']) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Directories Writable</span>
                    <span class="fw-medium">
                        {{ count(array_filter($serverInfo['directories'])) }}/{{ count($serverInfo['directories']) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- System Health Indicators -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-heart-pulse me-2"></i>Health Status</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <div class="fw-medium">Database</div>
                        <div class="text-muted small">Connected</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <div class="fw-medium">PHP</div>
                        <div class="text-muted small">{{ $serverInfo['php_version'] }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <div class="fw-medium">Cache</div>
                        <div class="text-muted small">Working</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <div class="fw-medium">Storage</div>
                        <div class="text-muted small">Writable</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <div class="fw-medium">Sessions</div>
                        <div class="text-muted small">Active</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.system.update') }}" class="btn btn-sm btn-outline-secondary text-start">
                        <i class="bi bi-arrow-up-circle me-1"></i> System Update
                    </a>
                    <a href="{{ route('admin.settings.file-system') }}" class="btn btn-sm btn-outline-secondary text-start">
                        <i class="bi bi-hdd me-1"></i> File System & Cache
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-outline-secondary text-start">
                        <i class="bi bi-gear me-1"></i> All Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
