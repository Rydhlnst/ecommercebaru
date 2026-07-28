<?php

namespace Beres\Dashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Beres\Dashboard\Contracts\DashboardCache as DashboardCacheContract;

class DashboardCache extends Model implements DashboardCacheContract
{
    use HasFactory;

    protected $table = 'dashboard_cache';

    protected $fillable = [
        'cache_key',
        'cache_value',
        'expires_at',
    ];

    protected $casts = [
        'cache_value' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if cache is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Get cached value or null if expired.
     */
    public function getValue()
    {
        if ($this->isExpired()) {
            return null;
        }

        return $this->cache_value;
    }

    /**
     * Scope to get valid (non-expired) cache entries.
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope to get expired cache entries.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }
}
