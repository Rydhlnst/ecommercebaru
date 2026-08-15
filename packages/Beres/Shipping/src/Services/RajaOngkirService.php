<?php

namespace Beres\Shipping\Services;

use App\Models\SiteSetting;
use Beres\Shipping\Contracts\RajaOngkirCacheRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
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
        try {
            $siteKey = (string) SiteSetting::getValue('rajaongkir_api_key');
            if ($siteKey !== '') {
                return $siteKey;
            }
        } catch (\Throwable $e) {
        }

        $adminKey = (string) core()->getConfigData('beres_storefront.shipping.api_key');
        if ($adminKey !== '') {
            return $adminKey;
        }

        return (string) config('rajaongkir.api_key', '');
    }

    /**
     * Build base URL from api_type (starter / basic / pro). Pro uses a different host.
     */
    protected function readBaseUrl(): string
    {
        $type = 'starter';
        try {
            $siteType = (string) SiteSetting::getValue('rajaongkir_api_type');
            if ($siteType !== '') {
                $type = $siteType;
            } else {
                $adminType = (string) core()->getConfigData('beres_storefront.shipping.api_type');
                if ($adminType !== '') {
                    $type = $adminType;
                }
            }
        } catch (\Throwable $e) {
        }

        return match ($type) {
            'pro' => 'https://pro.rajaongkir.com/api',
            'basic' => 'https://api.rajaongkir.com/basic',
            default => (string) config('rajaongkir.base_url', 'https://api.rajaongkir.com/starter'),
        };
    }

    /**
     * Check if RajaOngkir is enabled by admin.
     */
    public function isActive(): bool
    {
        try {
            $siteActive = SiteSetting::getValue('rajaongkir_is_active');
            if ($siteActive !== null && $siteActive !== '') {
                return (bool) $siteActive;
            }
        } catch (\Throwable $e) {
        }

        return (bool) core()->getConfigData('beres_storefront.shipping.active', true);
    }

    /**
     * Origin city ID (from admin dashboard, fallback package config).
     */
    public function getOriginCity(): int
    {
        try {
            $siteCity = (string) SiteSetting::getValue('rajaongkir_origin_city');
            if ($siteCity !== '') {
                return (int) $siteCity;
            }
        } catch (\Throwable $e) {
        }

        $admin = (string) core()->getConfigData('beres_storefront.shipping.origin_city');
        if ($admin !== '') {
            return (int) $admin;
        }

        return (int) config('rajaongkir.origin_city', 152);
    }

    /**
     * Enabled couriers (comma-separated in admin, returns array).
     */
    public function getEnabledCouriers(): array
    {
        $raw = (string) core()->getConfigData('beres_storefront.shipping.couriers');
        if ($raw === '') {
            return (array) config('rajaongkir.couriers', ['jne', 'jnt', 'sicepat']);
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /**
     * Get all provinces.
     */
    public function getProvinces(): array
    {
        $cacheKey = 'provinces';

        if ($this->cacheRepository->has('province', $cacheKey)) {
            return $this->cacheRepository->get('province', $cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/province");

            $data = $response->json();

            if ($data['rajaongkir']['status']['code'] == 200) {
                $provinces = $data['rajaongkir']['results'];
                $this->cacheRepository->set('province', $cacheKey, $provinces);

                return $provinces;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Province Error: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Get cities by province.
     */
    public function getCities(int $provinceId): array
    {
        $cacheKey = "cities_{$provinceId}";

        if ($this->cacheRepository->has('city', $cacheKey)) {
            return $this->cacheRepository->get('city', $cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/city", [
                'province' => $provinceId,
            ]);

            $data = $response->json();

            if ($data['rajaongkir']['status']['code'] == 200) {
                $cities = $data['rajaongkir']['results'];
                $this->cacheRepository->set('city', $cacheKey, $cities);

                return $cities;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir City Error: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Get districts by city.
     */
    public function getDistricts(int $cityId): array
    {
        $cacheKey = "districts_{$cityId}";

        if ($this->cacheRepository->has('district', $cacheKey)) {
            return $this->cacheRepository->get('district', $cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/subdistrict", [
                'city' => $cityId,
            ]);

            $data = $response->json();

            if ($data['rajaongkir']['status']['code'] == 200) {
                $districts = $data['rajaongkir']['results'];
                $this->cacheRepository->set('district', $cacheKey, $districts);

                return $districts;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir District Error: '.$e->getMessage());

            return [];
        }
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
        $cacheKey = "cost_{$origin}_{$destination}_{$weight}_".implode('_', $couriers);

        if ($this->cacheRepository->has('shipping_cost', $cacheKey)) {
            return $this->cacheRepository->get('shipping_cost', $cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->post("{$this->baseUrl}/cost", [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => implode(',', $couriers),
            ]);

            $data = $response->json();

            if ($data['rajaongkir']['status']['code'] == 200) {
                $costs = $data['rajaongkir']['results'];
                $this->cacheRepository->set('shipping_cost', $cacheKey, $costs, 60); // Cache for 1 hour

                return $costs;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Shipping Cost Error: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Search for address.
     */
    public function searchAddress(string $query): array
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/search", [
                'query' => $query,
            ]);

            $data = $response->json();

            if ($data['rajaongkir']['status']['code'] == 200) {
                return $data['rajaongkir']['results'];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Address Search Error: '.$e->getMessage());

            return [];
        }
    }
}
