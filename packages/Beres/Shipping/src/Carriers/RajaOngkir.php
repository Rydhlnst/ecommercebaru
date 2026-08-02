<?php

namespace Beres\Shipping\Carriers;

use Beres\Shipping\Services\RajaOngkirService;
use Illuminate\Support\Facades\Log;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Shipping\Carriers\AbstractShipping;

/**
 * RajaOngkir carrier — registered as a Bagisto shipping method.
 * Settings (api_key, api_type, origin_city, couriers, active) live in
 * admin dashboard: Configure → Storefront → Pengiriman (RajaOngkir).
 */
class RajaOngkir extends AbstractShipping
{
    /**
     * @var string
     */
    protected $code = 'rajaongkir';

    /**
     * @var string
     */
    protected $method = 'rajaongkir_regular';

    /**
     * Read config from admin dashboard (Beres\Settings), fallback to
     * Bagisto sales.carriers.rajaongkir.$field for optional overrides.
     */
    public function getConfigData($field)
    {
        $adminValue = core()->getConfigData("beres_storefront.shipping.$field");
        if ($adminValue !== null && $adminValue !== '') {
            return $adminValue;
        }

        return parent::getConfigData($field);
    }

    /**
     * Available when admin toggled active AND API key configured.
     */
    public function isAvailable()
    {
        return (bool) core()->getConfigData('beres_storefront.shipping.active')
            && (string) core()->getConfigData('beres_storefront.shipping.api_key') !== '';
    }

    /**
     * Compute shipping rates for the current cart.
     * Returns array of CartShippingRate — one per courier service, or
     * false when no destination / no matching city / API failure.
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        $cart = Cart::getCart();
        if (! $cart || ! $cart->shipping_address) {
            return false;
        }

        $service = app(RajaOngkirService::class);

        $originCityId      = $service->getOriginCity();
        $destinationCityId = $this->resolveDestinationCityId($cart->shipping_address, $service);

        if ($originCityId <= 0 || $destinationCityId <= 0) {
            return false;
        }

        $weightGrams = max(1000, $this->totalWeightGrams($cart));
        $couriers    = $service->getEnabledCouriers();

        if (empty($couriers)) {
            return false;
        }

        try {
            $results = $service->calculateShippingCost(
                $originCityId,
                $destinationCityId,
                $weightGrams,
                $couriers
            );
        } catch (\Throwable $e) {
            Log::error('RajaOngkir carrier calculate error: ' . $e->getMessage());

            return false;
        }

        return $this->buildRates($results);
    }

    /**
     * Sum cart items shipping weight (kg → grams). Minimum 1kg (RajaOngkir rule).
     */
    protected function totalWeightGrams($cart): int
    {
        $totalKg = 0.0;

        foreach ($cart->items as $item) {
            $itemWeight = (float) ($item->product?->weight ?? 0);
            $totalKg   += $itemWeight * (int) $item->quantity;
        }

        return (int) round($totalKg * 1000);
    }

    /**
     * Best-effort match: cart address's city name → RajaOngkir city_id.
     * Falls back to 0 (calculate() will bail).
     */
    protected function resolveDestinationCityId($address, RajaOngkirService $service): int
    {
        // Admin can override on the address record directly if their schema
        // extends Bagisto's address (custom attribute `rajaongkir_city_id`).
        if (isset($address->rajaongkir_city_id) && (int) $address->rajaongkir_city_id > 0) {
            return (int) $address->rajaongkir_city_id;
        }

        $cityName = (string) ($address->city ?? '');
        if ($cityName === '') {
            return 0;
        }

        try {
            // Fetch province → cities. Uses the service's built-in cache.
            $provinces = $service->getProvinces();

            foreach ($provinces as $province) {
                $cities = $service->getCities((int) ($province['province_id'] ?? 0));
                foreach ($cities as $city) {
                    $candidate = strtolower(($city['type'] ?? '') . ' ' . ($city['city_name'] ?? ''));
                    if (str_contains($candidate, strtolower($cityName))
                        || str_contains(strtolower($city['city_name'] ?? ''), strtolower($cityName))) {
                        return (int) ($city['city_id'] ?? 0);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('RajaOngkir city lookup failed: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * RajaOngkir result → array of CartShippingRate.
     * Response shape:
     *   [{ code, name, costs: [ { service, description, cost: [{value, etd, note}] } ] }, ...]
     */
    protected function buildRates(array $results): array
    {
        $rates = [];

        foreach ($results as $courier) {
            $courierCode = strtoupper($courier['code'] ?? '');

            foreach ($courier['costs'] ?? [] as $costItem) {
                $service = $costItem['service'] ?? '';
                $etd     = $costItem['cost'][0]['etd'] ?? '';
                $value   = (float) ($costItem['cost'][0]['value'] ?? 0);

                if ($value <= 0) {
                    continue;
                }

                $rate = new CartShippingRate;
                $rate->carrier            = $this->getCode();
                $rate->carrier_title      = $this->getTitle() ?: 'RajaOngkir';
                $rate->method             = $this->getCode() . '_' . strtolower($courierCode) . '_' . strtolower($service);
                $rate->method_title       = trim("$courierCode $service");
                $rate->method_description = trim(($costItem['description'] ?? '') . " (ETD $etd hari)");
                $rate->price              = core()->convertPrice($value);
                $rate->base_price         = $value;

                $rates[] = $rate;
            }
        }

        return $rates;
    }

    public function getTitle()
    {
        $adminTitle = (string) core()->getConfigData('beres_storefront.shipping.title');

        return $adminTitle !== '' ? $adminTitle : 'RajaOngkir';
    }

    public function getDescription()
    {
        return 'Ongkir dihitung otomatis via RajaOngkir (JNE, J&T, SiCepat, dll).';
    }
}
