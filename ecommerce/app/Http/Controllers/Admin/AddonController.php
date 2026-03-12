<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AddonController extends Controller
{
    /**
     * Display a listing of the addons.
     */
    public function index(Request $request)
    {
        $query = Addon::query();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%")
                    ->orWhere('author', 'like', "%{$request->search}%");
            });
        }

        // Sorting
        $sort = $request->sort ?? 'sort_order';
        $direction = $request->direction ?? 'asc';
        $query->orderBy($sort, $direction);

        $addons = $query->paginate(15);

        // Stats
        $stats = [
            'total' => Addon::count(),
            'active' => Addon::where('status', 'active')->count(),
            'inactive' => Addon::where('status', 'inactive')->count(),
            'uninstalled' => Addon::where('status', 'uninstalled')->count(),
        ];

        return view('admin.addons.index', compact('addons', 'stats'));
    }

    /**
     * Show the form for installing a new addon.
     */
    public function install()
    {
        return view('admin.addons.install');
    }

    /**
     * Process the installation of a new addon.
     */
    public function processInstall(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:addons,name',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'author' => 'nullable|string|max:255',
            'author_website' => 'nullable|string|url',
            'website' => 'nullable|string|url',
            'icon' => 'nullable|string|max:100',
            'is_core' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['status'] = 'inactive';
        $data['is_core'] = $request->boolean('is_core');
        $data['installed_at'] = now();

        Addon::create($data);

        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon installed successfully. You can now activate it.');
    }

    /**
     * Show the form for editing the specified addon.
     */
    public function edit($id)
    {
        $addon = Addon::findOrFail($id);
        return view('admin.addons.edit', compact('addon'));
    }

    /**
     * Update the specified addon in storage.
     */
    public function update(Request $request, $id)
    {
        $addon = Addon::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:addons,name,' . $id,
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'author' => 'nullable|string|max:255',
            'author_website' => 'nullable|string|url',
            'website' => 'nullable|string|url',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_core' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_core'] = $request->boolean('is_core');

        $addon->update($data);

        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon updated successfully.');
    }

    /**
     * Activate the specified addon.
     */
    public function activate($id)
    {
        $addon = Addon::findOrFail($id);

        if ($addon->is_core) {
            return redirect()->back()->with('error', 'Core addons cannot be deactivated.');
        }

        $addon->activate();

        return redirect()->back()
            ->with('success', $addon->name . ' activated successfully.');
    }

    /**
     * Deactivate the specified addon.
     */
    public function deactivate($id)
    {
        $addon = Addon::findOrFail($id);

        if ($addon->is_core) {
            return redirect()->back()->with('error', 'Core addons cannot be deactivated.');
        }

        $addon->deactivate();

        return redirect()->back()
            ->with('success', $addon->name . ' deactivated successfully.');
    }

    /**
     * Toggle the status of the addon.
     */
    public function toggleStatus($id)
    {
        $addon = Addon::findOrFail($id);

        if ($addon->is_core) {
            return redirect()->back()->with('error', 'Core addons status cannot be changed.');
        }

        if ($addon->isActive()) {
            $addon->deactivate();
            $message = $addon->name . ' deactivated successfully.';
        } else {
            $addon->activate();
            $message = $addon->name . ' activated successfully.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified addon from storage.
     */
    public function destroy($id)
    {
        $addon = Addon::findOrFail($id);

        if ($addon->is_core) {
            return redirect()->back()->with('error', 'Core addons cannot be uninstalled.');
        }

        $addon->delete();

        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon removed successfully.');
    }

    /**
     * Update addon settings.
     */
    public function settings(Request $request, $id)
    {
        $addon = Addon::findOrFail($id);

        $request->validate([
            'settings' => 'nullable|array',
        ]);

        $addon->update(['settings' => $request->settings ?? []]);

        return redirect()->back()->with('success', 'Addon settings updated successfully.');
    }

    /**
     * Bulk action on addons.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:addons,id',
        ]);

        $addons = Addon::whereIn('id', $request->ids)->get();
        $count = 0;

        foreach ($addons as $addon) {
            if ($addon->is_core && $request->action !== 'activate') {
                continue;
            }

            switch ($request->action) {
                case 'activate':
                    $addon->activate();
                    $count++;
                    break;
                case 'deactivate':
                    if (!$addon->is_core) {
                        $addon->deactivate();
                        $count++;
                    }
                    break;
                case 'delete':
                    if (!$addon->is_core) {
                        $addon->delete();
                        $count++;
                    }
                    break;
            }
        }

        $message = match ($request->action) {
            'activate' => "{$count} addon(s) activated successfully.",
            'deactivate' => "{$count} addon(s) deactivated successfully.",
            'delete' => "{$count} addon(s) deleted successfully.",
            default => 'Action completed.',
        };

        return redirect()->back()->with('success', $message);
    }

    /**
     * Reorder addons.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $id) {
            Addon::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true, 'message' => 'Addons reordered successfully.']);
    }

    /**
     * Get available addon templates (for demo - can be extended to fetch from remote).
     */
    public function templates()
    {
        // This can be extended to fetch addon templates from a remote server
        $templates = collect([
            (object)[
                'name' => 'Social Login',
                'slug' => 'social-login',
                'description' => 'Allow customers to login via social media platforms.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-person-bounding-box',
            ],
            (object)[
                'name' => 'Multi-Vendor',
                'slug' => 'multi-vendor',
                'description' => 'Enable multi-vendor marketplace functionality.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-shuffle',
            ],
            (object)[
                'name' => 'Live Chat',
                'slug' => 'live-chat',
                'description' => 'Add live chat support to your store.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-chat-dots',
            ],
            (object)[
                'name' => 'Point of Sale',
                'slug' => 'pos',
                'description' => 'Enable POS system for offline sales.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-cash-stack',
            ],
            (object)[
                'name' => 'Product Reviews Pro',
                'slug' => 'reviews-pro',
                'description' => 'Advanced product review system with ratings and photos.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-star',
            ],
            (object)[
                'name' => 'SEO Booster',
                'slug' => 'seo-booster',
                'description' => 'Advanced SEO tools to boost your search rankings.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-search',
            ],
        ]);

        return response()->json(['templates' => $templates]);
    }

    /**
     * Install from template.
     */
    public function installFromTemplate(Request $request)
    {
        $request->validate([
            'slug' => 'required|string',
        ]);

        // In a real implementation, this would fetch addon details from a remote server
        $templateSlugs = ['social-login', 'multi-vendor', 'live-chat', 'pos', 'reviews-pro', 'seo-booster'];

        if (!in_array($request->slug, $templateSlugs)) {
            return redirect()->back()->with('error', 'Invalid addon template.');
        }

        // Check if already installed
        $existing = Addon::where('slug', $request->slug)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'This addon is already installed.');
        }

        // Create addon from template
        $templateData = [
            'social-login' => [
                'name' => 'Social Login',
                'description' => 'Allow customers to login via social media platforms.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-person-bounding-box',
            ],
            'multi-vendor' => [
                'name' => 'Multi-Vendor',
                'description' => 'Enable multi-vendor marketplace functionality.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-shuffle',
            ],
            'live-chat' => [
                'name' => 'Live Chat',
                'description' => 'Add live chat support to your store.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-chat-dots',
            ],
            'pos' => [
                'name' => 'Point of Sale',
                'description' => 'Enable POS system for offline sales.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-cash-stack',
            ],
            'reviews-pro' => [
                'name' => 'Product Reviews Pro',
                'description' => 'Advanced product review system with ratings and photos.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-star',
            ],
            'seo-booster' => [
                'name' => 'SEO Booster',
                'description' => 'Advanced SEO tools to boost your search rankings.',
                'version' => '1.0.0',
                'author' => 'Hamko Ecommerce',
                'icon' => 'bi bi-search',
            ],
        ];

        $data = $templateData[$request->slug];
        $data['slug'] = $request->slug;
        $data['status'] = 'inactive';
        $data['is_core'] = false;
        $data['installed_at'] = now();

        Addon::create($data);

        return redirect()->route('admin.addons.index')
            ->with('success', $data['name'] . ' installed successfully. You can now activate it.');
    }
}
