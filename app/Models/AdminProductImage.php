<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProductImage extends Model
{
    protected $fillable = [
        'product_id', 'image_path', 'width', 'height', 'fit_mode', 'focal_x', 'focal_y',
        'alt_text', 'image_480_path', 'image_800_path', 'image_1600_path', 'sort_order',
    ];

    protected $appends = ['url', 'card_url', 'detail_url'];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'focal_x' => 'integer',
            'focal_y' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(AdminProduct::class, 'product_id');
    }

    public function getUrlAttribute(): string
    {
        return $this->urlForPath($this->image_path);
    }

    public function getCardUrlAttribute(): string
    {
        return $this->urlForPath($this->image_480_path ?: $this->image_path);
    }

    public function getDetailUrlAttribute(): string
    {
        return $this->urlForPath($this->image_800_path ?: $this->image_1600_path ?: $this->image_path);
    }

    public function getFitModeAttribute($value): string
    {
        return in_array($value, ['cover', 'contain'], true) ? $value : 'cover';
    }

    public function getFocalXAttribute($value): int
    {
        return max(0, min(100, (int) ($value ?? 50)));
    }

    public function getFocalYAttribute($value): int
    {
        return max(0, min(100, (int) ($value ?? 50)));
    }

    protected function urlForPath(?string $path): string
    {
        if (empty($path)) {
            return '';
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
}
