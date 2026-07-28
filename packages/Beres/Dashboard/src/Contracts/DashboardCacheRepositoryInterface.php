<?php

namespace Beres\Dashboard\Contracts;

interface DashboardCacheRepositoryInterface
{
    /**
     * Get cached value by key.
     */
    public function get(string $key): ?array;

    /**
     * Set cache value.
     */
    public function set(string $key, array $value, int $ttlMinutes = 5): bool;

    /**
     * Clear cache by key.
     */
    public function clear(string $key): bool;

    /**
     * Clear all dashboard cache.
     */
    public function clearAll(): bool;

    /**
     * Check if cache exists and is valid.
     */
    public function has(string $key): bool;

    /**
     * Get or set cache value.
     */
    public function remember(string $key, int $ttlMinutes, callable $callback): array;
}
