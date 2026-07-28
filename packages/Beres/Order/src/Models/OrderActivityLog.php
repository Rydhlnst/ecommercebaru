<?php

namespace Beres\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Order\Contracts\OrderActivityLog as OrderActivityLogContract;

class OrderActivityLog extends Model implements OrderActivityLogContract
{
    use HasFactory;

    protected $table = 'order_activity_logs';

    protected $fillable = [
        'order_id',
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
     * Get the order.
     */
    public function order()
    {
        return $this->belongsTo(\Webkul\Sales\Models\Order::class);
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
        $order,
        string $description = null,
        array $oldValues = null,
        array $newValues = null
    ): self {
        $user = auth()->guard('admin')->user();

        return static::create([
            'order_id'    => $order->id,
            'user_id'     => $user?->id,
            'action'      => $action,
            'description' => $description,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => request()->ip(),
        ]);
    }
}
