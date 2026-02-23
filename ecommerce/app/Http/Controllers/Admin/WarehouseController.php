<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Warehouse Controller for managing warehouses.
 */
class WarehouseController extends Controller
{
    public function index()
    {
        return view('admin.warehouses.index');
    }

    public function create()
    {
        return view('admin.warehouses.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse created successfully.');
    }

    public function show($id)
    {
        return view('admin.warehouses.show', compact('id'));
    }

    public function edit($id)
    {
        return view('admin.warehouses.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse deleted successfully.');
    }

    public function inventory($id)
    {
        return view('admin.warehouses.inventory', compact('id'));
    }
}
