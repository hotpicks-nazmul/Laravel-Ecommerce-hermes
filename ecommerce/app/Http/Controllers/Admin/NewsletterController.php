<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Newsletter Controller for managing newsletters.
 */
class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.marketing.newsletters.index');
    }

    public function send(Request $request)
    {
        return redirect()->route('admin.marketing.newsletters.index')
            ->with('success', 'Newsletter sent successfully.');
    }
}
