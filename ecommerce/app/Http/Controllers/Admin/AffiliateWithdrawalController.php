<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateWithdrawal;
use App\Models\Affiliate;
use Illuminate\Http\Request;

class AffiliateWithdrawalController extends Controller
{
    /**
     * Display list of affiliate withdrawals
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $withdrawals = AffiliateWithdrawal::with('affiliate.user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.affiliate.withdrawals.index', compact('withdrawals'));
    }

    /**
     * Display affiliate withdrawal details
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $withdrawal = AffiliateWithdrawal::with('affiliate.user')
            ->findOrFail($id);
        
        return view('admin.affiliate.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Approve affiliate withdrawal
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $withdrawal = AffiliateWithdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending withdrawals can be approved.');
        }

        \DB::transaction(function () use ($withdrawal) {
            $withdrawal->update([
                'status' => 'approved',
                'processed_at' => now(),
            ]);

            // Deduct from affiliate balance
            $affiliate = $withdrawal->affiliate;
            $affiliate->decrement('balance', $withdrawal->amount);
        });

        return redirect()->back()->with('success', 'Withdrawal approved successfully.');
    }

    /**
     * Reject affiliate withdrawal
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $withdrawal = AffiliateWithdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending withdrawals can be rejected.');
        }

        \DB::transaction(function () use ($withdrawal) {
            $withdrawal->update([
                'status' => 'rejected',
                'processed_at' => now(),
            ]);

            // Return amount to affiliate balance
            $affiliate = $withdrawal->affiliate;
            $affiliate->increment('balance', $withdrawal->amount);
            $affiliate->decrement('pending_balance', $withdrawal->amount);
        });

        return redirect()->back()->with('success', 'Withdrawal rejected successfully.');
    }
}
