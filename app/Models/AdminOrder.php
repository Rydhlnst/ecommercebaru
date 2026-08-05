<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AdminOrder extends Model
{
    protected $fillable = [
        'order_number', 'customer_name', 'customer_phone', 'customer_address',
        'shipping_address', 'shipping_courier', 'shipping_service', 'shipping_cost',
        'subtotal', 'total', 'status', 'payment_status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AdminOrder $model) {
            if (empty($model->order_number)) {
                $model->order_number = 'ORD-'.strtoupper(Str::random(8));
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(AdminOrderItem::class, 'order_id');
    }
}
