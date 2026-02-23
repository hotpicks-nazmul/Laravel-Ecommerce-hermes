<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Staff Controller for managing staff members.
 */
class StaffController extends Controller
{
    public function index()
    {
        return view('admin.staffs.index');
    }

    public function create()
    {
        return view('admin.staffs.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.staffs.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function edit($id)
    {
        return view('admin.staffs.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.staffs.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.staffs.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    public function warehouse()
    {
        return view('admin.staffs.warehouse');
    }

    public function permissions()
    {
        return view('admin.staffs.permissions');
    }

    public function updatePermissions(Request $request)
    {
        return redirect()->route('admin.staffs.permissions')
            ->with('success', 'Permissions updated successfully.');
    }
}
