<?php

namespace Beres\Order\Observers;

use Beres\Order\Models\OrderStatusHistory;
use Beres\Order\Models\OrderActivityLog;
use Webkul\Sales\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        OrderStatusHistory::log($order->id, $order->status, null, 'Order created');
        OrderActivityLog::log('created', $order, 'Order created');
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $changes = $order->getChanges();
        unset($changes['updated_at']);

        if (isset($changes['status'])) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            OrderStatusHistory::log($order->id, $newStatus, $oldStatus);
            OrderActivityLog::log(
                'status_changed',
                $order,
                "Status changed from {$oldStatus} to {$newStatus}",
                ['status' => $oldStatus],
                ['status' => $newStatus]
            );
        }

        if (!empty($changes) && !isset($changes['status'])) {
            OrderActivityLog::log('updated', $order, 'Order updated', $changes);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        OrderActivityLog::log('deleted', $order, 'Order deleted');
    }
}
