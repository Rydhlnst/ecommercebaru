<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProductVariation extends Model
{
    protected $fillable = ['product_id', 'weight', 'price', 'compare_at_price', 'stock'];

    protected function casts(): array
    {
        return [
            'weight' => 'integer',
            'price' => 'decimal:2',
            'compare_at_price' => 'float',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(AdminProduct::class, 'product_id');
    }

    /**
     * Weight stored in grams and displayed with a gram suffix.
     */
    public function getWeightLabelAttribute(): string
    {
        return (int) $this->weight.'g';
    }
}
