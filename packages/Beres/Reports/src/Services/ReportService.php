<?php

namespace Beres\Reports\Services;

use Webkul\Sales\Models\Order;
use Webkul\Customer\Models\Customer;
use Webkul\Product\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get revenue report.
     */
    public function getRevenueReport(string $period = 'monthly', ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Order::query()
            ->whereIn('status', ['processing', 'completed', 'pending_payment']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $revenue = $query->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(grand_total) as revenue'),
            DB::raw('COUNT(*) as order_count')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $summary = [
            'total_revenue' => $revenue->sum('revenue'),
            'total_orders' => $revenue->sum('order_count'),
            'average_order_value' => $revenue->avg('revenue'),
        ];

        return [
            'data'    => $revenue->toArray(),
            'summary' => $summary,
        ];
    }

    /**
     * Get orders report.
     */
    public function getOrdersReport(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Order::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $statusBreakdown = $query->clone()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $dailyOrders = $query->clone()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(grand_total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $summary = [
            'total_orders' => $dailyOrders->sum('count'),
            'total_revenue' => $dailyOrders->sum('revenue'),
        ];

        return [
            'status_breakdown' => $statusBreakdown->toArray(),
            'daily_orders'     => $dailyOrders->toArray(),
            'summary'          => $summary,
        ];
    }

    /**
     * Get products report.
     */
    public function getProductsReport(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Order::with(['items.product']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->get();

        $productSales = [];

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productId = $item->product_id;

                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = [
                        'product_id' => $productId,
                        'product_name' => $item->name,
                        'total_sold' => 0,
                        'total_revenue' => 0,
                    ];
                }

                $productSales[$productId]['total_sold'] += $item->qty_ordered;
                $productSales[$productId]['total_revenue'] += $item->total;
            }
        }

        // Sort by total sold
        uasort($productSales, fn($a, $b) => $b['total_sold'] <=> $a['total_sold']);

        $lowStock = Product::whereHas('inventories', function ($query) {
            $query->havingRaw('SUM(qty) < ?', [10])
                  ->groupBy('product_id');
        })->get(['id', 'name', 'sku']);

        return [
            'top_selling' => array_values($productSales),
            'low_stock'   => $lowStock->toArray(),
            'summary'     => [
                'total_products_sold' => array_sum(array_column($productSales, 'total_sold')),
                'total_revenue' => array_sum(array_column($productSales, 'total_revenue')),
            ],
        ];
    }

    /**
     * Get customers report.
     */
    public function getCustomersReport(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Customer::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $newCustomers = $query->clone()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topSpenders = Customer::withSum('orders', 'grand_total')
            ->orderByDesc('orders_sum_grand_total')
            ->limit(10)
            ->get();

        $summary = [
            'total_customers' => Customer::count(),
            'new_customers' => $newCustomers->sum('count'),
        ];

        return [
            'new_customers' => $newCustomers->toArray(),
            'top_spenders'  => $topSpenders->toArray(),
            'summary'       => $summary,
        ];
    }

    /**
     * Export report to CSV.
     */
    public function exportToCsv(array $data, string $filename): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'report_');
        $handle = fopen($tempFile, 'w');

        if (!empty($data)) {
            // Headers
            fputcsv($handle, array_keys($data[0]));

            // Data
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
        }

        fclose($handle);

        return $tempFile;
    }
}
