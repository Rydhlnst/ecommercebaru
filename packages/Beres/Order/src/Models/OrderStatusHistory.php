<?php

namespace Beres\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Order\Contracts\OrderStatusHistory as OrderStatusHistoryContract;

class OrderStatusHistory extends Model implements OrderStatusHistoryContract
{
    use HasFactory;

    protected $table = 'order_status_histories';

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'old_status',
        'note',
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
     * Create a status history entry.
     */
    public static function log(
        int $orderId,
        string $status,
        string $oldStatus = null,
        string $note = null
    ): self {
        $user = auth()->guard('admin')->user();

        return static::create([
            'order_id'   => $orderId,
            'user_id'    => $user?->id,
            'status'     => $status,
            'old_status' => $oldStatus,
            'note'       => $note,
        ]);
    }
}
