<?php

namespace Beres\Shipping\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Shipping\Services\RajaOngkirService;
use Beres\Shipping\Services\ShippingCalculatorService;
use Illuminate\Support\Facades\Response;

class ShippingController extends Controller
{
    public function __construct(
        protected RajaOngkirService $rajaOngkirService,
        protected ShippingCalculatorService $shippingCalculator
    ) {}

    /**
     * Get provinces list.
     */
    public function provinces()
    {
        $provinces = $this->rajaOngkirService->getProvinces();

        return response()->json([
            'success' => true,
            'data'    => $provinces,
        ]);
    }

    /**
     * Get cities by province.
     */
    public function cities(Request $request)
    {
        $request->validate([
            'province_id' => 'required|integer',
        ]);

        $cities = $this->rajaOngkirService->getCities($request->input('province_id'));

        return response()->json([
            'success' => true,
            'data'    => $cities,
        ]);
    }

    /**
     * Get districts by city.
     */
    public function districts(Request $request)
    {
        $request->validate([
            'city_id' => 'required|integer',
        ]);

        $districts = $this->rajaOngkirService->getDistricts($request->input('city_id'));

        return response()->json([
            'success' => true,
            'data'    => $districts,
        ]);
    }

    /**
     * Calculate shipping cost.
     */
    public function calculateCost(Request $request)
    {
        $request->validate([
            'origin'      => 'required|integer',
            'destination' => 'required|integer',
            'weight'      => 'required|integer|min:1',
            'couriers'    => 'required|array|min:1',
            'couriers.*'  => 'required|string|alpha_dash',
        ]);

        $availableCouriers = $this->shippingCalculator->getAvailableCouriers();
        $couriers = array_values(array_intersect(
            $request->input('couriers'),
            array_keys($availableCouriers)
        ));

        if ($couriers === []) {
            return response()->json([
                'success' => false,
                'message' => 'Kurir yang dipilih tidak tersedia.',
            ], 422);
        }

        $costs = $this->rajaOngkirService->calculateShippingCosts(
            $request->input('origin'),
            $request->input('destination'),
            $request->input('weight'),
            $couriers
        );

        if ($costs === []) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada layanan pengiriman untuk tujuan ini.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data'    => $costs,
        ]);
    }

    /**
     * Get available couriers.
     */
    public function couriers()
    {
        $couriers = $this->shippingCalculator->getAvailableCouriers();

        return response()->json([
            'success' => true,
            'data'    => $couriers,
        ]);
    }

    /**
     * Search address.
     */
    public function searchAddress(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $results = $this->rajaOngkirService->searchAddress($request->input('query'));

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }
}
