<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProductImage extends Model
{
    protected $fillable = ['product_id', 'image_path', 'sort_order'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(AdminProduct::class, 'product_id');
    }
}
