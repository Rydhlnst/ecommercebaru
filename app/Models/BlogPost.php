<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'thumbnail', 'blog_category_id',
        'tags', 'is_published', 'published_at',
    ];

    protected $appends = ['thumbnail_url'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BlogPost $model) {
            $model->slug = Str::slug($model->title).random_int(100, 999);
        });

        static::updating(function (BlogPost $model) {
            if ($model->isDirty('title') && ! $model->isDirty('slug')) {
                $model->slug = Str::slug($model->title).random_int(100, 999);
            }
        });
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $path = $this->thumbnail;
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}
