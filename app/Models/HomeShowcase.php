<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeShowcase extends Model
{
    protected $fillable = ['product_id', 'image', 'title', 'items'];

    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(AdminProduct::class, 'product_id');
    }
}
