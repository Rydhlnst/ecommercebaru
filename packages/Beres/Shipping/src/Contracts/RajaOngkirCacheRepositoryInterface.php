<?php

namespace Beres\Shipping\Contracts;

interface RajaOngkirCacheRepositoryInterface
{
    /**
     * Get cached value.
     */
    public function get(string $type, string $key): ?array;

    /**
     * Set cache value.
     */
    public function set(string $type, string $key, array $value, int $ttlMinutes = 1440): bool;

    /**
     * Check if cache exists and is valid.
     */
    public function has(string $type, string $key): bool;

    /**
     * Clear expired cache.
     */
    public function prune(): int;
}
