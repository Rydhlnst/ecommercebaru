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
        'is_featured', 'has_variations', 'price', 'compare_at_price', 'stock', 'status',
    ];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'has_variations' => 'boolean',
            'price' => 'decimal:2',
            'compare_at_price' => 'float',
        ];
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->relationLoaded('images')) {
            return $this->images->first()?->card_url;
        }

        return $this->images()->first()?->card_url;
    }

    protected static function booted(): void
    {
        static::creating(function (AdminProduct $model) {
            if (empty($model->slug)) {
                $model->slug = static::makeUniqueSlug($model->name);
            }
        });

        static::updating(function (AdminProduct $model) {
            if ($model->isDirty('name') && ! $model->isDirty('slug')) {
                $model->slug = static::makeUniqueSlug($model->name, $model->id);
            }
        });
    }

    protected static function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::limit(Str::slug($name) ?: 'product', 240, '');
        $slug = $baseSlug;
        $suffix = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
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
}
