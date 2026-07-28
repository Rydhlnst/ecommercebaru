<?php

namespace Beres\Dashboard\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Dashboard\Services\DashboardService;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display the dashboard.
     */
    public function index()
    {
        $metrics = $this->dashboardService->getMetrics();
        $chartData = $this->dashboardService->getRevenueChartData();

        return view('beres-dashboard::dashboard.index', [
            'metrics'   => $metrics,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Get dashboard metrics as JSON.
     */
    public function metrics(Request $request)
    {
        $metrics = $this->dashboardService->getMetrics();

        return response()->json([
            'success' => true,
            'data'    => $metrics->toArray(),
        ]);
    }

    /**
     * Get revenue chart data.
     */
    public function chart(Request $request)
    {
        $chartData = $this->dashboardService->getRevenueChartData();

        return response()->json([
            'success' => true,
            'data'    => $chartData,
        ]);
    }

    /**
     * Clear dashboard cache.
     */
    public function clearCache(Request $request)
    {
        $this->dashboardService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard cache cleared successfully.',
        ]);
    }

    /**
     * Get recent orders.
     */
    public function recentOrders(Request $request)
    {
        $limit = $request->input('limit', 10);
        $orders = $this->dashboardService->getRecentOrders($limit);

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    /**
     * Get top selling products.
     */
    public function topProducts(Request $request)
    {
        $limit = $request->input('limit', 10);
        $products = $this->dashboardService->getTopSellingProducts($limit);

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }
}
