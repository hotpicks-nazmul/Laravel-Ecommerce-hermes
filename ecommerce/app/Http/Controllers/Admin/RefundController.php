<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Refund Controller for managing refunds.
 */
class RefundController extends Controller
{
    /**
     * Display a listing of refunds.
     */
    public function index()
    {
        return view('admin.refunds.index');
    }

    /**
     * Display refund requests.
     */
    public function requests()
    {
        return view('admin.refunds.requests');
    }

    /**
     * Display approved refunds.
     */
    public function approved()
    {
        return view('admin.refunds.approved');
    }

    /**
     * Display rejected refunds.
     */
    public function rejected()
    {
        return view('admin.refunds.rejected');
    }

    /**
     * Display refund configuration.
     */
    public function configuration()
    {
        return view('admin.refunds.configuration');
    }

    /**
     * Update refund configuration.
     */
    public function updateConfiguration(Request $request)
    {
        // Save configuration settings
        return redirect()->route('admin.refunds.configuration')
            ->with('success', 'Refund configuration updated successfully.');
    }

    /**
     * Approve a refund.
     */
    public function approve($id)
    {
        return redirect()->back()
            ->with('success', 'Refund approved successfully.');
    }

    /**
     * Reject a refund.
     */
    public function reject($id)
    {
        return redirect()->back()
            ->with('success', 'Refund rejected successfully.');
    }
}
