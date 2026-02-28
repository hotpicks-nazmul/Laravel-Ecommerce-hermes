@extends('admin.layouts.app')

@section('title', 'Delivery Schedules')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-calendar-week me-2"></i>Delivery Schedules</h4>
            <p class="text-muted mb-0">Configure scheduled delivery options</p>
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Schedule
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="text-center py-5">
                <i class="bi bi-calendar-week text-info" style="font-size: 4rem;"></i>
                <h5 class="mt-3">Delivery Schedules</h5>
                <p class="text-muted">This feature is under development. Here you will be able to:</p>
                <ul class="text-start d-inline-block">
                    <li>Set up scheduled delivery time slots</li>
                    <li>Configure same-day delivery windows</li>
                    <li>Define next-day delivery options</li>
                    <li>Manage holiday delivery schedules</li>
                    <li>Set up express delivery options</li>
                </ul>
                <div class="mt-4">
                    <span class="badge bg-primary">Coming Soon</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
