@extends('admin.layouts.app')

@section('title', 'Affiliate Product Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Affiliate Product Details</h1>
        <a href="{{ route('admin.affiliate.products.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Product Information</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                This feature is not yet implemented. Please check back later.
            </div>
            
            {{-- TODO: Implement affiliate product details view --}}
            
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        {{-- Product image will be shown here --}}
                        <div class="bg-light rounded p-5">
                            <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Product Name</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Commission Rate</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>External URL</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Total Clicks</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Total Sales</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>-</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
