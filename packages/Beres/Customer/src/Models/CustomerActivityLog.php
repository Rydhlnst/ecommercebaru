<?php

namespace Beres\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Customer\Contracts\CustomerActivityLog as CustomerActivityLogContract;

class CustomerActivityLog extends Model implements CustomerActivityLogContract
{
    use HasFactory;

    protected $table = 'customer_activity_logs';

    protected $fillable = [
        'customer_id',
        'user_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the customer.
     */
    public function customer()
    {
        return $this->belongsTo(\Webkul\Customer\Models\Customer::class);
    }

    /**
     * Get the user (admin).
     */
    public function user()
    {
        return $this->belongsTo(\Webkul\User\Models\Admin::class);
    }

    /**
     * Create an activity log entry.
     */
    public static function log(
        string $action,
        $customer,
        string $description = null,
        array $oldValues = null,
        array $newValues = null
    ): self {
        $user = auth()->guard('admin')->user();

        return static::create([
            'customer_id' => $customer->id,
            'user_id'     => $user?->id,
            'action'      => $action,
            'description' => $description,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => request()->ip(),
        ]);
    }

    /**
     * Scope to filter by action.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}
