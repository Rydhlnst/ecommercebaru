<?php

namespace Beres\Dashboard\Services;

use Beres\Dashboard\Contracts\DashboardCacheRepositoryInterface;
use Beres\Dashboard\Contracts\ActivityLogRepositoryInterface;
use Beres\Dashboard\DTOs\DashboardMetricsDTO;
use Beres\Dashboard\Models\ActivityLog;
use Webkul\Order\Models\Order;
use Webkul\Customer\Models\Customer;
use Webkul\Product\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Cache keys.
     */
    const CACHE_KEY_METRICS = 'dashboard:metrics';
    const CACHE_KEY_REVENUE_CHART = 'dashboard:revenue_chart';
    const CACHE_KEY_TOP_PRODUCTS = 'dashboard:top_products';
    const CACHE_KEY_RECENT_ORDERS = 'dashboard:recent_orders';

    /**
     * Cache durations in minutes.
     */
    const CACHE_TTL_SHORT = 5;
    const CACHE_TTL_MEDIUM = 15;
    const CACHE_TTL_LONG = 30;

    public function __construct(
        protected DashboardCacheRepositoryInterface $cacheRepository,
        protected ActivityLogRepositoryInterface $activityLogRepository
    ) {}

    /**
     * Get all dashboard metrics.
     */
    public function getMetrics(): DashboardMetricsDTO
    {
        return $this->cacheRepository->remember(
            self::CACHE_KEY_METRICS,
            self::CACHE_TTL_SHORT,
            fn () => $this->fetchMetrics()
        );
    }

    /**
     * Fetch metrics from database.
     */
    protected function fetchMetrics(): array
    {
        $today = Carbon::today();

        return [
            'today_revenue'         => $this->getTodayRevenue(),
            'today_orders'          => $this->getTodayOrders(),
            'pending_orders'        => $this->getOrdersByStatus('pending'),
            'paid_orders'           => $this->getOrdersByStatus('processing'),
            'cancelled_orders'      => $this->getOrdersByStatus('canceled'),
            'total_customers'       => $this->getTotalCustomers(),
            'top_selling_products'  => $this->getTopSellingProducts(),
            'recent_orders'         => $this->getRecentOrders(),
            'monthly_revenue'       => $this->getMonthlyRevenue(),
            'latest_activity'       => $this->getLatestActivity(),
        ];
    }

    /**
     * Get today's revenue.
     */
    public function getTodayRevenue(): float
    {
        return (float) Order::whereDate('created_at', Carbon::today())
            ->whereIn('status', ['processing', 'completed', 'pending_payment'])
            ->sum('grand_total');
    }

    /**
     * Get today's orders count.
     */
    public function getTodayOrders(): int
    {
        return Order::whereDate('created_at', Carbon::today())->count();
    }

    /**
     * Get orders count by status.
     */
    public function getOrdersByStatus(string $status): int
    {
        return Order::where('status', $status)->count();
    }

    /**
     * Get total customers count.
     */
    public function getTotalCustomers(): int
    {
        return Customer::count();
    }

    /**
     * Get top selling products.
     */
    public function getTopSellingProducts(int $limit = 10): array
    {
        return Product::select('products.*', DB::raw('SUM(order_items.qty_ordered) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['processing', 'completed'])
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get recent orders.
     */
    public function getRecentOrders(int $limit = 10): array
    {
        return Order::with('customer')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get monthly revenue for chart.
     */
    public function getMonthlyRevenue(): array
    {
        return Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(grand_total) as revenue')
            )
            ->whereIn('status', ['processing', 'completed', 'pending_payment'])
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    /**
     * Get latest activity.
     */
    public function getLatestActivity(int $limit = 10): array
    {
        return $this->activityLogRepository->getRecent(24, $limit);
    }

    /**
     * Log an activity.
     */
    public function logActivity(
        string $action,
        $subject = null,
        string $description = null,
        array $properties = null
    ): ActivityLog {
        return ActivityLog::log($action, $subject, $description, $properties);
    }

    /**
     * Clear dashboard cache.
     */
    public function clearCache(): bool
    {
        return $this->cacheRepository->clearAll();
    }

    /**
     * Get revenue chart data formatted for frontend.
     */
    public function getRevenueChartData(): array
    {
        $monthlyRevenue = $this->cacheRepository->remember(
            self::CACHE_KEY_REVENUE_CHART,
            self::CACHE_TTL_LONG,
            fn () => $this->getMonthlyRevenue()
        );

        $labels = [];
        $data = [];

        foreach ($monthlyRevenue as $record) {
            $labels[] = Carbon::create($record['year'], $record['month'])->format('M Y');
            $data[] = (float) $record['revenue'];
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }
}
