<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * SMS Controller for managing SMS communications.
 */
class SmsController extends Controller
{
    public function bulkSms()
    {
        return view('admin.marketing.bulk-sms.index');
    }

    public function sendBulkSms(Request $request)
    {
        return redirect()->route('admin.marketing.bulk-sms.index')
            ->with('success', 'Bulk SMS sent successfully.');
    }
}
