<?php

namespace Beres\Dashboard\Repositories;

use Beres\Dashboard\Contracts\DashboardCacheRepositoryInterface;
use Beres\Dashboard\Models\DashboardCache;
use Carbon\Carbon;

class DashboardCacheRepository implements DashboardCacheRepositoryInterface
{
    public function __construct(
        protected DashboardCache $model
    ) {}

    /**
     * {@inheritdoc}
     */
    public function get(string $key): ?array
    {
        $cache = $this->model->where('cache_key', $key)
            ->valid()
            ->first();

        return $cache?->cache_value;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, array $value, int $ttlMinutes = 5): bool
    {
        $this->model->updateOrCreate(
            ['cache_key' => $key],
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
    public function clear(string $key): bool
    {
        return (bool) $this->model->where('cache_key', $key)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll(): bool
    {
        return (bool) $this->model->query()->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return $this->model->where('cache_key', $key)->valid()->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function remember(string $key, int $ttlMinutes, callable $callback): array
    {
        $cached = $this->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        $this->set($key, $value, $ttlMinutes);

        return $value;
    }

    /**
     * Clear expired cache entries.
     */
    public function prune(): int
    {
        return $this->model->expired()->delete();
    }
}
