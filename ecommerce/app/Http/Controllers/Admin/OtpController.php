<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * OTP Controller for managing OTP settings.
 */
class OtpController extends Controller
{
    public function configuration()
    {
        return view('admin.otp.configuration');
    }

    public function updateConfiguration(Request $request)
    {
        return redirect()->route('admin.otp.configuration')
            ->with('success', 'OTP configuration updated successfully.');
    }

    public function smsTemplates()
    {
        return view('admin.otp.sms-templates');
    }

    public function updateSmsTemplates(Request $request)
    {
        return redirect()->route('admin.otp.sms-templates')
            ->with('success', 'SMS templates updated successfully.');
    }

    public function credentials()
    {
        return view('admin.otp.credentials');
    }

    public function updateCredentials(Request $request)
    {
        return redirect()->route('admin.otp.credentials')
            ->with('success', 'OTP credentials updated successfully.');
    }
}
