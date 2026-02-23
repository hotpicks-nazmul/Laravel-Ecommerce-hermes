@extends('admin.layouts.app')

@section('title', 'Withdrawal Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Withdrawal Details</h1>
        <a href="{{ route('admin.affiliate.withdrawals.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Withdrawal Information</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                This feature is not yet implemented. Please check back later.
            </div>
            
            {{-- TODO: Implement affiliate withdrawal details view --}}
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Affiliate Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th width="40%">Name</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>Total Earnings</th>
                                    <td>-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Withdrawal Details</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th width="40%">Amount</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>Payment Method</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>Account Details</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>Requested At</th>
                                    <td>-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mt-3">
                <form action="{{ route('admin.affiliate.withdrawals.reject', $id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger me-2" onclick="return confirm('Are you sure you want to reject this withdrawal?')">
                        <i class="bi bi-x-circle me-2"></i>Reject
                    </button>
                </form>
                <form action="{{ route('admin.affiliate.withdrawals.approve', $id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this withdrawal?')">
                        <i class="bi bi-check-circle me-2"></i>Approve
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
