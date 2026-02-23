<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Support Controller for managing support tickets.
 */
class SupportController extends Controller
{
    public function tickets()
    {
        return view('admin.support.tickets.index');
    }

    public function showTicket($id)
    {
        return view('admin.support.tickets.show', compact('id'));
    }

    public function replyTicket(Request $request, $id)
    {
        return redirect()->back()
            ->with('success', 'Reply sent successfully.');
    }

    public function closeTicket($id)
    {
        return redirect()->route('admin.support.tickets.index')
            ->with('success', 'Ticket closed successfully.');
    }

    public function productQueries()
    {
        return view('admin.support.product-queries.index');
    }

    public function replyQuery(Request $request, $id)
    {
        return redirect()->back()
            ->with('success', 'Query reply sent successfully.');
    }
}
