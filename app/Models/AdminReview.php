<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminReview extends Model
{
    protected $fillable = ['product_id', 'customer_name', 'rating', 'comment', 'is_approved'];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(AdminProduct::class, 'product_id');
    }
}
