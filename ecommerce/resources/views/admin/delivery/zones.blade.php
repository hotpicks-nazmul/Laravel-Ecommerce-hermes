@extends('admin.layouts.app')

@section('title', 'Delivery Zones')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-map me-2"></i>Delivery Zones</h4>
            <p class="text-muted mb-0">Configure delivery zones and shipping rates</p>
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Zone
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="text-center py-5">
                <i class="bi bi-map text-success" style="font-size: 4rem;"></i>
                <h5 class="mt-3">Delivery Zones Management</h5>
                <p class="text-muted">This feature is under development. Here you will be able to:</p>
                <ul class="text-start d-inline-block">
                    <li>Create and manage delivery zones by region</li>
                    <li>Set up zone-specific shipping rates</li>
                    <li>Configure delivery timeframes per zone</li>
                    <li>Define free shipping thresholds by zone</li>
                </ul>
                <div class="mt-4">
                    <span class="badge bg-primary">Coming Soon</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
