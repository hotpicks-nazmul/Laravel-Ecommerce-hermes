<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    /**
     * Display a listing of refunds.
     */
    public function index(Request $request)
    {
        $query = Refund::with(['order', 'user']);

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('refund_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by reason
        if ($request->reason) {
            $query->where('reason', $request->reason);
        }

        // Sorting
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->per_page ?? 25;
        $refunds = $query->paginate($perPage);

        // Statistics
        $stats = [
            'total' => Refund::count(),
            'pending' => Refund::pending()->count(),
            'approved' => Refund::approved()->count(),
            'rejected' => Refund::rejected()->count(),
            'processed' => Refund::processed()->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.refunds.partials.table-rows', compact('refunds'))->render(),
                'pagination' => $refunds->links()->toHtml(),
                'stats' => $stats
            ]);
        }

        return view('admin.refunds.index', compact('refunds', 'stats'));
    }

    /**
     * Display refund requests (pending).
     */
    public function requests(Request $request)
    {
        $query = Refund::with(['order', 'user'])->pending();

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('refund_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by reason
        if ($request->reason) {
            $query->where('reason', $request->reason);
        }

        // Sorting
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'asc'; // Oldest first for pending
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->per_page ?? 25;
        $refunds = $query->paginate($perPage);

        // Statistics
        $stats = [
            'total' => Refund::count(),
            'pending' => Refund::pending()->count(),
            'approved' => Refund::approved()->count(),
            'rejected' => Refund::rejected()->count(),
            'processed' => Refund::processed()->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.refunds.partials.table-rows', compact('refunds'))->render(),
                'pagination' => $refunds->links()->toHtml(),
                'stats' => $stats
            ]);
        }

        return view('admin.refunds.requests', compact('refunds', 'stats'));
    }

    /**
     * Display approved refunds.
     */
    public function approved(Request $request)
    {
        $query = Refund::with(['order', 'user'])->approved();

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('refund_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->per_page ?? 25;
        $refunds = $query->paginate($perPage);

        // Statistics
        $stats = [
            'total' => Refund::count(),
            'pending' => Refund::pending()->count(),
            'approved' => Refund::approved()->count(),
            'rejected' => Refund::rejected()->count(),
            'processed' => Refund::processed()->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.refunds.partials.table-rows', compact('refunds'))->render(),
                'pagination' => $refunds->links()->toHtml(),
                'stats' => $stats
            ]);
        }

        return view('admin.refunds.approved', compact('refunds', 'stats'));
    }

    /**
     * Display rejected refunds.
     */
    public function rejected(Request $request)
    {
        $query = Refund::with(['order', 'user'])->rejected();

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('refund_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->per_page ?? 25;
        $refunds = $query->paginate($perPage);

        // Statistics
        $stats = [
            'total' => Refund::count(),
            'pending' => Refund::pending()->count(),
            'approved' => Refund::approved()->count(),
            'rejected' => Refund::rejected()->count(),
            'processed' => Refund::processed()->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.refunds.partials.table-rows', compact('refunds'))->render(),
                'pagination' => $refunds->links()->toHtml(),
                'stats' => $stats
            ]);
        }

        return view('admin.refunds.rejected', compact('refunds', 'stats'));
    }

    /**
     * Display refund configuration.
     */
    public function configuration()
    {
        return view('admin.refunds.configuration');
    }

    /**
     * Update refund configuration.
     */
    public function updateConfiguration(Request $request)
    {
        // Save configuration settings
        return redirect()->route('admin.refunds.configuration')
            ->with('success', 'Refund configuration updated successfully.');
    }

    /**
     * Show refund details.
     */
    public function show($id)
    {
        $refund = Refund::with(['order', 'order.items', 'user', 'processedBy'])->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

    /**
     * Approve a refund.
     */
    public function approve(Request $request, $id)
    {
        $refund = Refund::findOrFail($id);
        
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $refund->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        // Update order status to refunded
        $order = $refund->order;
        if ($order && $order->status !== 'cancelled') {
            $order->update(['status' => 'refunded']);
        }

        return redirect()->back()
            ->with('success', 'Refund approved successfully.');
    }

    /**
     * Reject a refund.
     */
    public function reject(Request $request, $id)
    {
        $refund = Refund::findOrFail($id);
        
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $refund->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Refund rejected successfully.');
    }

    /**
     * Mark refund as processed (complete the refund).
     */
    public function process(Request $request, $id)
    {
        $refund = Refund::findOrFail($id);
        
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $refund->update([
            'status' => 'processed',
            'admin_note' => $refund->admin_note . "\n" . $request->admin_note,
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        // Update order payment status to refunded
        $order = $refund->order;
        if ($order) {
            $order->update(['payment_status' => 'refunded']);
        }

        return redirect()->back()
            ->with('success', 'Refund processed successfully.');
    }
}
