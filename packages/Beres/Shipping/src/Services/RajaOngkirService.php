<?php

namespace Beres\Shipping\Services;

use App\Models\SiteSetting;
use Beres\Shipping\Contracts\RajaOngkirCacheRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected const MODERN_BASE_URL = 'https://rajaongkir.komerce.id/api/v1';

    protected string $apiKey;

    protected string $baseUrl;

    public function __construct(
        protected RajaOngkirCacheRepositoryInterface $cacheRepository
    ) {
        $this->apiKey = $this->readApiKey();
        $this->baseUrl = $this->readBaseUrl();
    }

    /**
     * Read API key: admin dashboard first, package config fallback.
     */
    protected function readApiKey(): string
    {
        $siteKey = $this->readSiteSetting('rajaongkir_api_key');
        if ($siteKey !== null && $siteKey !== '') {
            return $siteKey;
        }

        $adminKey = (string) core()->getConfigData('beres_storefront.shipping.api_key');
        if ($adminKey !== '') {
            return $adminKey;
        }

        return (string) config('rajaongkir.api_key', '');
    }

    /**
     * Use the current RajaOngkir/Komerce API V2 endpoint.
     */
    protected function readBaseUrl(): string
    {
        $configuredUrl = rtrim((string) config('rajaongkir.base_url', self::MODERN_BASE_URL), '/');

        // Existing deployments may still contain the retired V1 URL in .env.
        if (str_contains($configuredUrl, 'api.rajaongkir.com')) {
            return self::MODERN_BASE_URL;
        }

        return $configuredUrl;
    }

    /**
     * Check if RajaOngkir is enabled by admin.
     */
    public function isActive(): bool
    {
        $siteActive = $this->readSiteSetting('rajaongkir_is_active');
        if ($siteActive !== null && $siteActive !== '') {
            return in_array(strtolower($siteActive), ['1', 'true', 'yes', 'on'], true);
        }

        return filter_var(core()->getConfigData('beres_storefront.shipping.active', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Check that a usable API key and endpoint are available.
     */
    public function isConfigured(): bool
    {
        return $this->apiKey !== '' && $this->baseUrl !== '';
    }

    /**
     * Origin city ID (from admin dashboard, fallback package config).
     */
    public function getOriginCity(): int
    {
        $siteCity = $this->readSiteSetting('rajaongkir_origin_city');
        if ($siteCity !== null && $siteCity !== '') {
            return max(0, (int) $siteCity);
        }

        $admin = (string) core()->getConfigData('beres_storefront.shipping.origin_city');
        if ($admin !== '') {
            return max(0, (int) $admin);
        }

        return max(0, (int) config('rajaongkir.origin_city', 152));
    }

    /**
     * Enabled couriers (comma-separated in admin, returns array).
     */
    public function getEnabledCouriers(): array
    {
        $raw = $this->readSiteSetting('rajaongkir_couriers');
        $raw = $raw ?? (string) core()->getConfigData('beres_storefront.shipping.couriers');

        if ($raw === '') {
            $raw = implode(',', (array) config('rajaongkir.couriers', ['jne', 'jnt', 'sicepat']));
        }

        $aliases = ['pos' => 'pov'];

        return array_values(array_unique(array_filter(array_map(
            fn ($courier) => $aliases[strtolower(trim($courier))] ?? strtolower(trim($courier)),
            explode(',', $raw)
        ))));
    }

    /**
     * Get all provinces.
     */
    public function getProvinces(): array
    {
        return [];
    }

    /**
     * Get cities by province.
     */
    public function getCities(int $provinceId): array
    {
        return [];
    }

    /**
     * Get districts by city.
     */
    public function getDistricts(int $cityId): array
    {
        return [];
    }

    /**
     * Calculate shipping cost.
     */
    public function calculateShippingCost(
        int $origin,
        int $destination,
        int $weight,
        array $couriers
    ): array {
        if (! $this->isActive() || ! $this->isConfigured() || $origin <= 0 || $destination <= 0 || $weight <= 0) {
            return [];
        }

        $couriers = array_values(array_unique(array_map('strtolower', $couriers)));
        $couriers = array_values(array_intersect($couriers, $this->getEnabledCouriers()));
        if ($couriers === []) {
            return [];
        }

        sort($couriers);
        $cacheKey = "cost_{$origin}_{$destination}_{$weight}_".implode('_', $couriers);

        if ($this->cacheRepository->has('shipping_cost', $cacheKey)) {
            return $this->cacheRepository->get('shipping_cost', $cacheKey);
        }

        $payload = $this->request('post', 'calculate/domestic-cost', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => implode(',', $couriers),
        ]);

        $costs = $this->normaliseCostResults($payload);
        if ($costs !== []) {
            $this->cacheRepository->set('shipping_cost', $cacheKey, $costs, 60);
        }

        return $costs;
    }

    /**
     * Backward-compatible plural alias used by older checkout controllers.
     */
    public function calculateShippingCosts(
        int $origin,
        int $destination,
        int $weight,
        array $couriers
    ): array {
        return $this->calculateShippingCost($origin, $destination, $weight, $couriers);
    }

    /**
     * Search for address.
     */
    public function searchAddress(string $query): array
    {
        $query = trim($query);
        if (! $this->isConfigured() || ! $this->isActive() || mb_strlen($query) < 3) {
            return [];
        }

        $cacheKey = sha1(mb_strtolower($query));
        if ($this->cacheRepository->has('destination', $cacheKey)) {
            return $this->cacheRepository->get('destination', $cacheKey) ?? [];
        }

        $payload = $this->request('get', 'destination/domestic-destination', [
            'search' => $query,
            'limit' => 10,
            'offset' => 0,
        ]);
        $results = is_array($payload['data'] ?? null) ? $payload['data'] : [];
        if ($results !== []) {
            $this->cacheRepository->set('destination', $cacheKey, $results, 1440);
        }

        return $results;
    }

    protected function readSiteSetting(string $key): ?string
    {
        try {
            return SiteSetting::getValue($key);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function request(string $method, string $path, array $parameters = []): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $request = Http::acceptJson()
                ->withHeaders(['key' => $this->apiKey])
                ->connectTimeout(5)
                ->timeout(15);

            $response = $method === 'get'
                ? $request->get("{$this->baseUrl}/{$path}", $parameters)
                : $request->asForm()->post("{$this->baseUrl}/{$path}", $parameters);

            $payload = $response->json();
            $code = (int) data_get($payload, 'meta.code', data_get($payload, 'rajaongkir.status.code', 0));

            if (! $response->successful() || $code !== 200) {
                Log::warning('RajaOngkir API request failed.', [
                    'path' => $path,
                    'http_status' => $response->status(),
                    'api_code' => $code,
                    'message' => data_get($payload, 'meta.message', data_get($payload, 'rajaongkir.status.description')),
                ]);

                return null;
            }

            return is_array($payload) ? $payload : null;
        } catch (\Throwable $e) {
            Log::warning('RajaOngkir API request error.', [
                'path' => $path,
                'exception' => $e,
            ]);

            return null;
        }
    }

    protected function normaliseCostResults(?array $payload): array
    {
        if (! $payload) {
            return [];
        }

        $legacyResults = data_get($payload, 'rajaongkir.results');
        if (is_array($legacyResults)) {
            return $legacyResults;
        }

        $grouped = [];
        foreach ((array) ($payload['data'] ?? []) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $code = strtolower((string) ($item['code'] ?? ''));
            if ($code === '') {
                continue;
            }

            $cost = is_array($item['cost'] ?? null)
                ? (float) ($item['cost']['value'] ?? $item['cost'][0]['value'] ?? 0)
                : (float) ($item['cost'] ?? 0);

            if ($cost <= 0) {
                continue;
            }

            $grouped[$code] ??= [
                'code' => $code,
                'name' => $item['name'] ?? strtoupper($code),
                'costs' => [],
            ];
            $grouped[$code]['costs'][] = [
                'service' => (string) ($item['service'] ?? ''),
                'description' => (string) ($item['description'] ?? ''),
                'cost' => [[
                    'value' => $cost,
                    'etd' => (string) ($item['etd'] ?? ''),
                    'note' => (string) ($item['note'] ?? ''),
                ]],
            ];
        }

        return array_values($grouped);
    }
}
