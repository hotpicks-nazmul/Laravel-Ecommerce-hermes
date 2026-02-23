<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Flash Deal Controller for managing flash deals.
 */
class FlashDealController extends Controller
{
    public function index()
    {
        return view('admin.marketing.flash-deals.index');
    }

    public function create()
    {
        return view('admin.marketing.flash-deals.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.marketing.flash-deals.index')
            ->with('success', 'Flash deal created successfully.');
    }

    public function edit($id)
    {
        return view('admin.marketing.flash-deals.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.marketing.flash-deals.index')
            ->with('success', 'Flash deal updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.marketing.flash-deals.index')
            ->with('success', 'Flash deal deleted successfully.');
    }
}
