<?php

namespace Beres\Dashboard\DTOs;

use Illuminate\Support\Collection;

class DashboardMetricsDTO
{
    public function __construct(
        public readonly float $todayRevenue,
        public readonly int $todayOrders,
        public readonly int $pendingOrders,
        public readonly int $paidOrders,
        public readonly int $cancelledOrders,
        public readonly int $totalCustomers,
        public readonly Collection $topSellingProducts,
        public readonly Collection $recentOrders,
        public readonly Collection $monthlyRevenue,
        public readonly array $latestActivity,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            todayRevenue: $data['today_revenue'] ?? 0,
            todayOrders: $data['today_orders'] ?? 0,
            pendingOrders: $data['pending_orders'] ?? 0,
            paidOrders: $data['paid_orders'] ?? 0,
            cancelledOrders: $data['cancelled_orders'] ?? 0,
            totalCustomers: $data['total_customers'] ?? 0,
            topSellingProducts: collect($data['top_selling_products'] ?? []),
            recentOrders: collect($data['recent_orders'] ?? []),
            monthlyRevenue: collect($data['monthly_revenue'] ?? []),
            latestActivity: $data['latest_activity'] ?? [],
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'today_revenue'         => $this->todayRevenue,
            'today_orders'          => $this->todayOrders,
            'pending_orders'        => $this->pendingOrders,
            'paid_orders'           => $this->paidOrders,
            'cancelled_orders'      => $this->cancelledOrders,
            'total_customers'       => $this->totalCustomers,
            'top_selling_products'  => $this->topSellingProducts->toArray(),
            'recent_orders'         => $this->recentOrders->toArray(),
            'monthly_revenue'       => $this->monthlyRevenue->toArray(),
            'latest_activity'       => $this->latestActivity,
        ];
    }

    /**
     * Format revenue as currency.
     */
    public function formattedRevenue(): string
    {
        return core()->formatPrice($this->todayRevenue);
    }
}
