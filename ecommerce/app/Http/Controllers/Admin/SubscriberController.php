<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Subscriber Controller for managing subscribers.
 */
class SubscriberController extends Controller
{
    public function index()
    {
        return view('admin.marketing.subscribers.index');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.marketing.subscribers.index')
            ->with('success', 'Subscriber removed successfully.');
    }

    public function export()
    {
        return redirect()->route('admin.marketing.subscribers.index')
            ->with('success', 'Subscribers exported successfully.');
    }
}
