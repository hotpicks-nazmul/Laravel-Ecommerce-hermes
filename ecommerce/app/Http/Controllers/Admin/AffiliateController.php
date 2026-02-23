<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateRequest;
use App\Models\AffiliateSale;
use App\Models\AffiliateWithdrawal;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    /**
     * Display list of affiliate users
     * 
     * @return \Illuminate\View\View
     */
    public function users()
    {
        $affiliates = Affiliate::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.affiliate.users.index', compact('affiliates'));
    }

    /**
     * Show affiliate user details
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showUser($id)
    {
        $affiliate = Affiliate::with(['user', 'sales', 'withdrawals'])
            ->withCount(['clicks', 'sales'])
            ->findOrFail($id);
        
        $recentSales = AffiliateSale::where('affiliate_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.affiliate.users.show', compact('affiliate', 'recentSales'));
    }

    /**
     * Approve affiliate user
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveUser($id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $affiliate->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Affiliate user approved successfully.');
    }

    /**
     * Suspend affiliate user
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspendUser($id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $affiliate->update(['status' => 'suspended']);

        return redirect()->back()->with('success', 'Affiliate user suspended successfully.');
    }

    /**
     * Delete affiliate user
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyUser($id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $affiliate->delete();

        return redirect()->route('admin.affiliate.users.index')
            ->with('success', 'Affiliate user deleted successfully.');
    }

    /**
     * Display affiliate configuration settings
     * 
     * @return \Illuminate\View\View
     */
    public function configuration()
    {
        $settings = [
            'affiliate_enabled' => Setting::get('affiliate_enabled', '1') === '1' || Setting::get('affiliate_enabled', '1') === true,
            'default_commission_rate' => (float) Setting::get('default_commission_rate', '5.00'),
            'min_withdrawal_amount' => (float) Setting::get('min_withdrawal_amount', '50.00'),
            'cookie_lifetime' => (int) Setting::get('affiliate_cookie_lifetime', '30'),
            'affiliate_registration' => Setting::get('affiliate_registration', 'manual'),
            'commission_type' => Setting::get('affiliate_commission_type', 'percentage'),
        ];
        
        return view('admin.affiliate.configuration', compact('settings'));
    }

    /**
     * Update affiliate configuration settings
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateConfiguration(Request $request)
    {
        $validated = $request->validate([
            'affiliate_enabled' => 'required|boolean',
            'default_commission_rate' => 'required|numeric|min:0|max:100',
            'min_withdrawal_amount' => 'required|numeric|min:0',
            'cookie_lifetime' => 'required|integer|min:1|max:365',
            'affiliate_registration' => 'required|in:auto,manual',
            'commission_type' => 'required|in:percentage,fixed',
        ]);

        Setting::set('affiliate_enabled', $validated['affiliate_enabled'] ? '1' : '0', 'affiliate');
        Setting::set('default_commission_rate', (string) $validated['default_commission_rate'], 'affiliate');
        Setting::set('min_withdrawal_amount', (string) $validated['min_withdrawal_amount'], 'affiliate');
        Setting::set('affiliate_cookie_lifetime', (string) $validated['cookie_lifetime'], 'affiliate');
        Setting::set('affiliate_registration', $validated['affiliate_registration'], 'affiliate');
        Setting::set('affiliate_commission_type', $validated['commission_type'], 'affiliate');

        return redirect()->back()->with('success', 'Affiliate configuration updated successfully.');
    }

    /**
     * Display affiliate payouts
     * 
     * @return \Illuminate\View\View
     */
    public function payouts()
    {
        $withdrawals = AffiliateWithdrawal::with('affiliate.user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.affiliate.payouts', compact('withdrawals'));
    }

    /**
     * Approve affiliate payout
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approvePayout($id)
    {
        $withdrawal = AffiliateWithdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending withdrawals can be approved.');
        }

        DB::transaction(function () use ($withdrawal) {
            $withdrawal->update([
                'status' => 'approved',
                'processed_at' => now(),
            ]);

            // Deduct from affiliate balance
            $affiliate = $withdrawal->affiliate;
            $affiliate->decrement('balance', $withdrawal->amount);
        });

        return redirect()->back()->with('success', 'Payout approved successfully.');
    }

    /**
     * Reject affiliate payout
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectPayout($id)
    {
        $withdrawal = AffiliateWithdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending withdrawals can be rejected.');
        }

        DB::transaction(function () use ($withdrawal) {
            $withdrawal->update([
                'status' => 'rejected',
                'processed_at' => now(),
            ]);

            // Return amount to affiliate balance
            $affiliate = $withdrawal->affiliate;
            $affiliate->increment('balance', $withdrawal->amount);
            $affiliate->decrement('pending_balance', $withdrawal->amount);
        });

        return redirect()->back()->with('success', 'Payout rejected successfully.');
    }

    /**
     * Display affiliate requests
     * 
     * @return \Illuminate\View\View
     */
    public function requests()
    {
        $requests = AffiliateRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.affiliate.requests', compact('requests'));
    }

    /**
     * Approve affiliate request
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveRequest($id)
    {
        $request = AffiliateRequest::findOrFail($id);
        
        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be approved.');
        }

        DB::transaction(function () use ($request) {
            $request->update([
                'status' => 'approved',
                'processed_at' => now(),
            ]);

            // Create affiliate account
            Affiliate::create([
                'user_id' => $request->user_id,
                'commission_rate' => (float) Setting::get('default_commission_rate', '5.00'),
                'status' => 'approved',
                'approved_at' => now(),
                'website' => $request->website,
                'social_links' => $request->social_links,
            ]);
        });

        return redirect()->back()->with('success', 'Affiliate request approved successfully.');
    }

    /**
     * Reject affiliate request
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectRequest($id)
    {
        $request = AffiliateRequest::findOrFail($id);
        
        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be rejected.');
        }

        $request->update([
            'status' => 'rejected',
            'processed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Affiliate request rejected successfully.');
    }

    /**
     * Display affiliate reports
     * 
     * @return \Illuminate\View\View
     */
    public function reports()
    {
        $stats = [
            'total_affiliates' => Affiliate::count(),
            'total_sales' => AffiliateSale::sum('sale_amount'),
            'total_commissions' => AffiliateSale::sum('commission_amount'),
            'total_clicks' => DB::table('affiliate_clicks')->count(),
        ];

        $affiliates = Affiliate::with('user')
            ->withCount(['clicks', 'sales'])
            ->withSum('sales as total_sales', 'sale_amount')
            ->withSum('sales as total_commission', 'commission_amount')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.affiliate.reports', compact('stats', 'affiliates'));
    }

    /**
     * Export affiliate reports
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportReports()
    {
        // TODO: Implement CSV/Excel export
        return redirect()->back()->with('success', 'Reports export feature coming soon.');
    }
}
