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

    /**
     * Weight stored as kg (decimal). Display as grams, e.g. 0.50 -> "500g", 1.00 -> "1000g".
     */
    public function getWeightLabelAttribute(): string
    {
        $grams = (int) round(((float) $this->weight) * 1000);

        return $grams.'g';
    }
}
