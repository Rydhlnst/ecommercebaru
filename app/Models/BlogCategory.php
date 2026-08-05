<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::creating(function (BlogCategory $model) {
            $model->slug = Str::slug($model->name);
        });

        static::updating(function (BlogCategory $model) {
            if ($model->isDirty('name')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'blog_category_id');
    }
}
