<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AdminProduct extends Model
{
    protected $fillable = [
        'name', 'slug', 'category_id', 'badge', 'description',
        'is_featured', 'has_variations', 'price', 'stock', 'status',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'has_variations' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AdminProduct $model) {
            $model->slug = Str::slug($model->name).random_int(100, 999);
        });

        static::updating(function (AdminProduct $model) {
            if ($model->isDirty('name') && ! $model->isDirty('slug')) {
                $model->slug = Str::slug($model->name).random_int(100, 999);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AdminCategory::class, 'category_id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(AdminProductVariation::class, 'product_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(AdminProductImage::class, 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AdminReview::class, 'product_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
