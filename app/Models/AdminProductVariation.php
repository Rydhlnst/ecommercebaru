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
            'weight' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(AdminProduct::class, 'product_id');
    }

    /**
     * Weight stored in grams (integer). Display as grams, e.g. 500 -> "500g", 1000 -> "1000g".
     */
    public function getWeightLabelAttribute(): string
    {
        return (int) $this->weight . 'g';
    }
}
