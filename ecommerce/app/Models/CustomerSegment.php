<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'conditions',
        'customer_count',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'customer_count' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this segment
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get customers that belong to this segment
     */
    public function customers()
    {
        return $this->belongsToMany(User::class, 'customer_segment_members', 'segment_id', 'user_id')
            ->withPivot('added_at')
            ->withTimestamps();
    }

    /**
     * Apply segment conditions to get matching customers
     */
    public function applyConditions($query = null)
    {
        $baseQuery = $query ?? User::where('role', 'customer');
        
        if (empty($this->conditions)) {
            return $baseQuery;
        }

        $conditions = $this->conditions;
        
        // Handle different condition types
        if (isset($conditions['order_count_min']) || isset($conditions['order_count_max'])) {
            $baseQuery = $baseQuery->whereHas('orders', function ($q) use ($conditions) {
                $q->selectRaw('COUNT(*) as order_count');
                if (isset($conditions['order_count_min'])) {
                    $q->havingRaw('COUNT(*) >= ?', [$conditions['order_count_min']]);
                }
                if (isset($conditions['order_count_max'])) {
                    $q->havingRaw('COUNT(*) <= ?', [$conditions['order_count_max']]);
                }
            });
        }

        if (isset($conditions['total_spent_min']) || isset($conditions['total_spent_max'])) {
            $baseQuery = $baseQuery->whereHas('orders', function ($q) use ($conditions) {
                if (isset($conditions['total_spent_min'])) {
                    $q->havingRaw('SUM(grand_total) >= ?', [$conditions['total_spent_min']]);
                }
                if (isset($conditions['total_spent_max'])) {
                    $q->havingRaw('SUM(grand_total) <= ?', [$conditions['total_spent_max']]);
                }
            });
        }

        if (isset($conditions['last_order_days'])) {
            $baseQuery->whereHas('orders', function ($q) use ($conditions) {
                $q->where('created_at', '>=', now()->subDays($conditions['last_order_days']))
                  ->orderBy('created_at', 'desc')
                  ->limit(1);
            });
        }

        if (isset($conditions['customer_group_id'])) {
            $baseQuery->where('customer_group_id', $conditions['customer_group_id']);
        }

        if (isset($conditions['is_active'])) {
            $baseQuery->where('is_active', $conditions['is_active']);
        }

        if (isset($conditions['registration_date_from'])) {
            $baseQuery->where('created_at', '>=', $conditions['registration_date_from']);
        }

        if (isset($conditions['registration_date_to'])) {
            $baseQuery->where('created_at', '<=', $conditions['registration_date_to']);
        }

        return $baseQuery;
    }

    /**
     * Update the customer count for this segment
     */
    public function updateCustomerCount()
    {
        $this->customer_count = $this->applyConditions()->count();
        $this->save();
    }
}
