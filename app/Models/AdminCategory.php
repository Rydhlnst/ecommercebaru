<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AdminCategory extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'parent_id'];

    protected $appends = ['image_url'];

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

    protected static function booted(): void
    {
        static::creating(function (AdminCategory $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function (AdminCategory $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image;
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }
        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return asset('storage/'.$path);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(AdminProduct::class, 'category_id');
    }
}
