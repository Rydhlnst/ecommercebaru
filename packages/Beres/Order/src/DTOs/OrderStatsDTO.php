<?php

namespace Beres\Order\DTOs;

class OrderStatsDTO
{
    public function __construct(
        public readonly int $totalOrders,
        public readonly float $totalRevenue,
        public readonly int $pendingOrders,
        public readonly int $processingOrders,
        public readonly int $completedOrders,
        public readonly int $cancelledOrders,
        public readonly float $averageOrderValue,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            totalOrders: $data['total_orders'] ?? 0,
            totalRevenue: (float) ($data['total_revenue'] ?? 0),
            pendingOrders: $data['pending_orders'] ?? 0,
            processingOrders: $data['processing_orders'] ?? 0,
            completedOrders: $data['completed_orders'] ?? 0,
            cancelledOrders: $data['cancelled_orders'] ?? 0,
            averageOrderValue: (float) ($data['average_order_value'] ?? 0),
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'total_orders'       => $this->totalOrders,
            'total_revenue'      => $this->totalRevenue,
            'pending_orders'     => $this->pendingOrders,
            'processing_orders'  => $this->processingOrders,
            'completed_orders'   => $this->completedOrders,
            'cancelled_orders'   => $this->cancelledOrders,
            'average_order_value' => $this->averageOrderValue,
        ];
    }
}
