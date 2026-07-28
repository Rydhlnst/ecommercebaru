<?php

namespace Beres\Order\Services;

use Beres\Order\Contracts\OrderStatusHistoryRepositoryInterface;
use Beres\Order\Contracts\OrderActivityLogRepositoryInterface;
use Beres\Order\DTOs\OrderDTO;
use Beres\Order\DTOs\OrderStatsDTO;
use Beres\Order\Models\OrderStatusHistory;
use Beres\Order\Models\OrderActivityLog;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Valid order statuses.
     */
    const STATUSES = [
        'pending'           => 'Menunggu',
        'waiting_payment'   => 'Menunggu Pembayaran',
        'paid'              => 'Dibayar',
        'processing'        => 'Diproses',
        'packing'           => 'Dikemas',
        'shipped'           => 'Dikirim',
        'completed'         => 'Selesai',
        'canceled'          => 'Dibatalkan',
        'refunded'          => 'Dikembalikan',
    ];

    /**
     * Valid status transitions.
     */
    const STATUS_TRANSITIONS = [
        'pending'           => ['waiting_payment', 'paid', 'canceled'],
        'waiting_payment'   => ['paid', 'canceled'],
        'paid'              => ['processing', 'canceled'],
        'processing'        => ['packing', 'canceled'],
        'packing'           => ['shipped'],
        'shipped'           => ['completed'],
        'completed'         => [],
        'canceled'          => [],
        'refunded'          => [],
    ];

    public function __construct(
        protected OrderStatusHistoryRepositoryInterface $statusHistoryRepository,
        protected OrderActivityLogRepositoryInterface $activityLogRepository
    ) {}

    /**
     * Get order as DTO.
     */
    public function getOrderDto(Order $order): OrderDTO
    {
        return OrderDTO::fromArray([
            'id'              => $order->id,
            'increment_id'    => $order->increment_id,
            'status'          => $order->status,
            'grand_total'     => $order->grand_total,
            'currency'        => $order->currency,
            'customer_name'   => $order->customer ? $order->customer->full_name : null,
            'customer_email'  => $order->customer?->email,
            'shipping_method' => $order->shipping_method,
            'payment_method'  => $order->payment_method,
            'items'           => $order->items->toArray(),
            'created_at'      => $order->created_at,
        ]);
    }

    /**
     * Get order statistics.
     */
    public function getStats(): OrderStatsDTO
    {
        return OrderStatsDTO::fromArray([
            'total_orders'       => Order::count(),
            'total_revenue'      => (float) Order::sum('grand_total'),
            'pending_orders'     => Order::where('status', 'pending')->count(),
            'processing_orders'  => Order::where('status', 'processing')->count(),
            'completed_orders'   => Order::where('status', 'completed')->count(),
            'cancelled_orders'   => Order::where('status', 'canceled')->count(),
            'average_order_value' => Order::avg('grand_total') ?? 0,
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(int $orderId, string $newStatus, string $note = null): bool
    {
        $order = Order::find($orderId);

        if (!$order) {
            return false;
        }

        $oldStatus = $order->status;

        // Validate status transition
        if (!$this->isValidTransition($oldStatus, $newStatus)) {
            return false;
        }

        DB::beginTransaction();

        try {
            $order->update(['status' => $newStatus]);

            // Log status history
            OrderStatusHistory::log($orderId, $newStatus, $oldStatus, $note);

            // Log activity
            OrderActivityLog::log(
                'status_changed',
                $order,
                "Status changed from {$oldStatus} to {$newStatus}",
                ['status' => $oldStatus],
                ['status' => $newStatus]
            );

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if status transition is valid.
     */
    public function isValidTransition(string $from, string $to): bool
    {
        return in_array($to, self::STATUS_TRANSITIONS[$from] ?? []);
    }

    /**
     * Get valid transitions for a status.
     */
    public function getValidTransitions(string $status): array
    {
        return self::STATUS_TRANSITIONS[$status] ?? [];
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(string $status): string
    {
        return self::STATUSES[$status] ?? $status;
    }

    /**
     * Get order status history.
     */
    public function getStatusHistory(int $orderId, int $limit = 50): array
    {
        return $this->statusHistoryRepository->getByOrder($orderId, $limit);
    }

    /**
     * Get order activity log.
     */
    public function getActivityLog(int $orderId, int $limit = 50): array
    {
        return $this->activityLogRepository->getByOrder($orderId, $limit);
    }

    /**
     * Search orders.
     */
    public function search(array $filters): array
    {
        $query = Order::with(['customer', 'items']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['customer_email'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->where('email', 'LIKE', "%{$filters['customer_email']}%");
            });
        }

        if (isset($filters['min_total'])) {
            $query->where('grand_total', '>=', $filters['min_total']);
        }

        if (isset($filters['max_total'])) {
            $query->where('grand_total', '<=', $filters['max_total']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page'])->toArray();
        }

        return $query->get()->toArray();
    }

    /**
     * Export orders to CSV.
     */
    public function exportToCsv(array $filters = []): string
    {
        $orders = Order::with(['customer']);

        if (!empty($filters['ids'])) {
            $orders->whereIn('id', $filters['ids']);
        }

        $orders = $orders->get();

        $tempFile = tempnam(sys_get_temp_dir(), 'order_export_');
        $handle = fopen($tempFile, 'w');

        // Headers
        fputcsv($handle, ['id', 'increment_id', 'status', 'customer', 'email', 'grand_total', 'currency', 'created_at']);

        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->id,
                $order->increment_id,
                $order->status,
                $order->customer?->full_name ?? 'Guest',
                $order->customer?->email ?? '',
                $order->grand_total,
                $order->currency,
                $order->created_at,
            ]);
        }

        fclose($handle);

        return $tempFile;
    }
}
