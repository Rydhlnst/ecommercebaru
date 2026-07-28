<?php

namespace Beres\Shipping\Repositories;

use Beres\Shipping\Contracts\RajaOngkirCacheRepositoryInterface;
use Beres\Shipping\Models\RajaOngkirCache;
use Carbon\Carbon;

class RajaOngkirCacheRepository implements RajaOngkirCacheRepositoryInterface
{
    public function __construct(
        protected RajaOngkirCache $model
    ) {}

    /**
     * {@inheritdoc}
     */
    public function get(string $type, string $key): ?array
    {
        $cache = $this->model
            ->where('cache_type', $type)
            ->where('cache_key', $key)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        return $cache?->cache_value;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $type, string $key, array $value, int $ttlMinutes = 1440): bool
    {
        $this->model->updateOrCreate(
            ['cache_type' => $type, 'cache_key' => $key],
            [
                'cache_value' => $value,
                'expires_at'  => Carbon::now()->addMinutes($ttlMinutes),
            ]
        );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $type, string $key): bool
    {
        return $this->model
            ->where('cache_type', $type)
            ->where('cache_key', $key)
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function prune(): int
    {
        return $this->model
            ->where('expires_at', '<=', Carbon::now())
            ->delete();
    }
}
