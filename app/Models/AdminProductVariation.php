<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProductVariation extends Model
{
    protected $fillable = ['product_id', 'weight', 'price', 'stock'];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(AdminProduct::class, 'product_id');
    }
}
