@extends('admin.layouts.app')

@section('title', 'Delivery Boys')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-person-badge me-2"></i>Delivery Boys</h4>
            <p class="text-muted mb-0">Manage delivery personnel and assignments</p>
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Delivery Boy
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="text-center py-5">
                <i class="bi bi-person-badge text-warning" style="font-size: 4rem;"></i>
                <h5 class="mt-3">Delivery Boys Management</h5>
                <p class="text-muted">This feature is under development. Here you will be able to:</p>
                <ul class="text-start d-inline-block">
                    <li>Add and manage delivery personnel</li>
                    <li>Assign orders to delivery boys</li>
                    <li>Track delivery boy locations in real-time</li>
                    <li>View delivery boy performance metrics</li>
                    <li>Manage delivery boy schedules and shifts</li>
                    <li>Handle delivery boy payments and commissions</li>
                </ul>
                <div class="mt-4">
                    <span class="badge bg-primary">Coming Soon</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
