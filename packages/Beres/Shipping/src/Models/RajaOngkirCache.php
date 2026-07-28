<?php

namespace Beres\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Shipping\Contracts\RajaOngkirCache as RajaOngkirCacheContract;

class RajaOngkirCache extends Model implements RajaOngkirCacheContract
{
    use HasFactory;

    protected $table = 'rajaongkir_cache';

    protected $fillable = [
        'cache_type',
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
}
