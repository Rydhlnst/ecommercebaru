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

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}
