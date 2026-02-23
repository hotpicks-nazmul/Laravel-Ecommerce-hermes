<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * System Controller for managing system settings.
 */
class SystemController extends Controller
{
    public function update()
    {
        return view('admin.system.update');
    }

    public function performUpdate(Request $request)
    {
        return redirect()->route('admin.system.update')
            ->with('success', 'System updated successfully.');
    }

    public function serverStatus()
    {
        return view('admin.system.server-status');
    }
}
