@extends('admin.layouts.app')

@section('title', 'Product Queries')

@section('content')
<div class="content-area">
    <div class="container-fluid pt-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Product Queries</h4>
        </div>

        <!-- Coming Soon Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-question-circle text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-4">Product Queries</h4>
                <p class="text-muted">This feature will allow customers to ask questions about specific products.</p>
                <div class="alert alert-info mt-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Product queries will be available when customers ask questions about products from the frontend.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
