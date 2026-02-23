<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Addon Controller for managing addons/plugins.
 */
class AddonController extends Controller
{
    public function index()
    {
        return view('admin.addons.index');
    }

    public function install()
    {
        return view('admin.addons.install');
    }

    public function processInstall(Request $request)
    {
        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon installed successfully.');
    }

    public function activate($id)
    {
        return redirect()->back()
            ->with('success', 'Addon activated successfully.');
    }

    public function deactivate($id)
    {
        return redirect()->back()
            ->with('success', 'Addon deactivated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon removed successfully.');
    }
}
