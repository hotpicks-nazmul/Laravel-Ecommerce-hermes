@extends('admin.layouts.app')

@section('title', 'Courier Integration')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-plug me-2"></i>Courier Integration</h4>
            <p class="text-muted mb-0">Connect with third-party courier services</p>
        </div>
    </div>

    <div class="row g-3">
        <!-- Courier Services -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Available Courier Services</h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="bi bi-diagram-3 text-primary" style="font-size: 3rem;"></i>
                        <h6 class="mt-3">Courier Integration</h6>
                        <p class="text-muted small">Connect with popular courier services like:</p>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                            <span class="badge bg-light text-dark">FedEx</span>
                            <span class="badge bg-light text-dark">UPS</span>
                            <span class="badge bg-light text-dark">DHL</span>
                            <span class="badge bg-light text-dark">USPS</span>
                            <span class="badge bg-light text-dark">Royal Mail</span>
                            <span class="badge bg-light text-dark">Speedaf</span>
                            <span class="badge bg-light text-dark">Pathao</span>
                            <span class="badge bg-light text-dark">SSL Commercial</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Configuration -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-key me-2"></i>API Configuration</h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="bi bi-gear text-secondary" style="font-size: 3rem;"></i>
                        <h6 class="mt-3">API Settings</h6>
                        <p class="text-muted small">Configure API keys and credentials for courier services</p>
                        <div class="mt-4">
                            <span class="badge bg-primary">Coming Soon</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
            <div class="text-center py-4">
                <i class="bi bi-plug text-success" style="font-size: 4rem;"></i>
                <h5 class="mt-3">Courier Integration</h5>
                <p class="text-muted">This feature is under development. Here you will be able to:</p>
                <ul class="text-start d-inline-block">
                    <li>Integrate with multiple courier services</li>
                    <li>Auto-generate shipping labels</li>
                    <li>Automatic tracking number sync</li>
                    <li>Real-time shipping rate calculation</li>
                    <li>Bulk shipment booking</li>
                </ul>
                <div class="mt-4">
                    <span class="badge bg-primary">Coming Soon</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
