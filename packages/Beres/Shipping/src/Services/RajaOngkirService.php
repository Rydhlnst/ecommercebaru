<?php

namespace Beres\Shipping\Services;

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
        $this->apiKey = (string) config('rajaongkir.api_key', '');
        $this->baseUrl = (string) config('rajaongkir.base_url', 'https://api.rajaongkir.com/starter');
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
            Log::error('RajaOngkir Province Error: ' . $e->getMessage());
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
            Log::error('RajaOngkir City Error: ' . $e->getMessage());
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
            Log::error('RajaOngkir District Error: ' . $e->getMessage());
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
        $cacheKey = "cost_{$origin}_{$destination}_{$weight}_" . implode('_', $couriers);

        if ($this->cacheRepository->has('shipping_cost', $cacheKey)) {
            return $this->cacheRepository->get('shipping_cost', $cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->post("{$this->baseUrl}/cost", [
                'origin'      => $origin,
                'destination' => $destination,
                'weight'      => $weight,
                'courier'     => implode(',', $couriers),
            ]);

            $data = $response->json();

            if ($data['rajaongkir']['status']['code'] == 200) {
                $costs = $data['rajaongkir']['results'];
                $this->cacheRepository->set('shipping_cost', $cacheKey, $costs, 60); // Cache for 1 hour
                return $costs;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Shipping Cost Error: ' . $e->getMessage());
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
            Log::error('RajaOngkir Address Search Error: ' . $e->getMessage());
            return [];
        }
    }
}
