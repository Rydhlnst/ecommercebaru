<?php

namespace Beres\Shipping\Services;

use Beres\Shipping\DTOs\ShippingCostDTO;
use Beres\Shipping\DTOs\ShippingServiceDTO;

class ShippingCalculatorService
{
    public function __construct(
        protected RajaOngkirService $rajaOngkirService
    ) {}

    /**
     * Calculate shipping costs for cart.
     */
    public function calculateShippingCosts(
        int $originCityId,
        int $destinationCityId,
        int $weightGrams,
        ?array $couriers = null
    ): array {
        $couriers = $couriers ?? $this->rajaOngkirService->getEnabledCouriers();

        $results = $this->rajaOngkirService->calculateShippingCost(
            $originCityId,
            $destinationCityId,
            $weightGrams,
            $couriers
        );

        $shippingServices = [];

        foreach ($results as $result) {
            $shippingService = ShippingServiceDTO::fromArray($result);
            $shippingServices[] = $shippingService;
        }

        return $shippingServices;
    }

    /**
     * Get available couriers.
     */
    public function getAvailableCouriers(): array
    {
        if (! $this->rajaOngkirService->isActive() || ! $this->rajaOngkirService->isConfigured()) {
            return [];
        }

        $couriers = [
            'jne'      => 'JNE',
            'tiki'     => 'TIKI',
            'pov'      => 'POS Indonesia',
            'jnt'      => 'J&T Express',
            'sicepat'  => 'SiCepat',
            'anteraja' => 'AnterAja',
            'ninja'    => 'Ninja Xpress',
            'ide'      => 'ID Express',
            'sap'      => 'SAP Express',
            'lion'     => 'Lion Parcel',
        ];

        return array_intersect_key($couriers, array_flip($this->rajaOngkirService->getEnabledCouriers()));
    }

    /**
     * Get the configured shipping origin city.
     */
    public function getOriginCity(): int
    {
        return $this->rajaOngkirService->getOriginCity();
    }

    /**
     * Get service types for a courier.
     */
    public function getServiceTypes(string $courier): array
    {
        return match ($courier) {
            'jne' => [
                'OKE'      => 'JNE OKE (Reguler)',
                'REG'      => 'JNE REG (Cepat)',
                'JPS'      => 'JNE JPS (Sameday)',
            ],
            'tiki' => [
                'ECO'  => 'TIKI ECO',
                'REG'  => 'TIKI REG',
                'ONS'  => 'TIKI ONS',
                'JNE'  => 'TIKI JNE',
            ],
            'jnt' => [
                'REG'  => 'J&T Reguler',
                'JNT'  => 'J&T Express',
                'JTR'  => 'J&T JTR',
            ],
            'sicepat' => [
                'REG'  => 'SiCepat Reguler',
                'EXP'  => 'SiCepat Express',
                'BEST' => 'SiCepat Best',
                'COD'  => 'SiCepat COD',
            ],
            default => [],
        };
    }

    /**
     * Format shipping cost for display.
     */
    public function formatShippingCost(float $cost): string
    {
        return 'Rp ' . number_format($cost, 0, ',', '.');
    }

    /**
     * Estimate delivery time.
     */
    public function estimateDeliveryTime(int $etd): string
    {
        if ($etd <= 0) {
            return 'Informasi tidak tersedia';
        }

        $startDate = now()->addDay();
        $endDate = now()->addDays($etd + 2); // Add buffer days

        return $startDate->format('d M') . ' - ' . $endDate->format('d M');
    }
}
