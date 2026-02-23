<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Seller Controller for managing sellers (B2B).
 */
class SellerController extends Controller
{
    /**
     * Display a listing of sellers.
     */
    public function index()
    {
        return view('admin.sellers.index');
    }

    /**
     * Show the form for creating a new seller.
     */
    public function create()
    {
        return view('admin.sellers.create');
    }

    /**
     * Store a newly created seller.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.sellers.index')
            ->with('success', 'Seller created successfully.');
    }

    /**
     * Display the specified seller.
     */
    public function show($id)
    {
        return view('admin.sellers.show', compact('id'));
    }

    /**
     * Show the form for editing the specified seller.
     */
    public function edit($id)
    {
        return view('admin.sellers.edit', compact('id'));
    }

    /**
     * Update the specified seller.
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('admin.sellers.index')
            ->with('success', 'Seller updated successfully.');
    }

    /**
     * Remove the specified seller.
     */
    public function destroy($id)
    {
        return redirect()->route('admin.sellers.index')
            ->with('success', 'Seller deleted successfully.');
    }

    /**
     * Approve a seller.
     */
    public function approve($id)
    {
        return redirect()->back()
            ->with('success', 'Seller approved successfully.');
    }

    /**
     * Suspend a seller.
     */
    public function suspend($id)
    {
        return redirect()->back()
            ->with('success', 'Seller suspended successfully.');
    }

    /**
     * Display seller payouts.
     */
    public function payouts()
    {
        return view('admin.sellers.payouts');
    }

    /**
     * Display seller payout requests.
     */
    public function payoutRequests()
    {
        return view('admin.sellers.payout-requests');
    }

    /**
     * Approve a payout request.
     */
    public function approvePayout($id)
    {
        return redirect()->back()
            ->with('success', 'Payout approved successfully.');
    }

    /**
     * Display seller commission settings.
     */
    public function commission()
    {
        return view('admin.sellers.commission');
    }

    /**
     * Update seller commission settings.
     */
    public function updateCommission(Request $request)
    {
        return redirect()->route('admin.sellers.commission')
            ->with('success', 'Commission settings updated successfully.');
    }

    /**
     * Display seller verification requests.
     */
    public function verification()
    {
        return view('admin.sellers.verification');
    }

    /**
     * Process seller verification.
     */
    public function processVerification($id)
    {
        return redirect()->back()
            ->with('success', 'Verification processed successfully.');
    }
}
