@extends('admin.layouts.app')

@section('title', 'Affiliate User Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Affiliate User Details</h1>
        <a href="{{ route('admin.affiliate.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Profile</h5>
                </div>
                <div class="card-body text-center">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        This feature is not yet implemented.
                    </div>
                    
                    {{-- TODO: Implement affiliate user profile --}}
                    <div class="mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="bi bi-person" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h5>-</h5>
                    <p class="text-muted">-</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th>Total Clicks</th>
                            <td class="text-end">0</td>
                        </tr>
                        <tr>
                            <th>Total Conversions</th>
                            <td class="text-end">0</td>
                        </tr>
                        <tr>
                            <th>Total Sales</th>
                            <td class="text-end">$0.00</td>
                        </tr>
                        <tr>
                            <th>Total Earnings</th>
                            <td class="text-end">$0.00</td>
                        </tr>
                        <tr>
                            <th>Pending Balance</th>
                            <td class="text-end">$0.00</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Account Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name</th>
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
                            <th>Commission Rate</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Payment Details</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>Joined At</th>
                            <td>-</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No recent activity to display.
                    </div>
                </div>
            </div>

            <div class="text-end mt-3">
                <form action="{{ route('admin.affiliate.users.suspend', $id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning me-2" onclick="return confirm('Are you sure you want to suspend this affiliate?')">
                        <i class="bi bi-pause-circle me-2"></i>Suspend
                    </button>
                </form>
                <form action="{{ route('admin.affiliate.users.destroy', $id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this affiliate?')">
                        <i class="bi bi-trash me-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
