<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminOrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'product_name', 'quantity', 'price', 'total'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(AdminOrder::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(AdminProduct::class, 'product_id');
    }
}
