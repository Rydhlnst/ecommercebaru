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
        float $weightKg,
        array $couriers = null
    ): array {
        // Convert weight to grams
        $weightGrams = (int) ($weightKg * 1000);

        // Get default couriers if not specified
        $couriers = $couriers ?? config('rajaongkir.couriers');

        // Calculate shipping costs
        $results = $this->rajaOngkirService->calculateShippingCosts(
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
        return [
            'jne'      => 'JNE',
            'tiki'     => 'TIKI',
            'pos'      => 'POS Indonesia',
            'jnt'      => 'J&T Express',
            'sicepat'  => 'SiCepat',
            'anteraja' => 'AnterAja',
        ];
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
