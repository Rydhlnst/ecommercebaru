<?php

namespace Beres\Order\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Order\Services\OrderService;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\Response;

class OrderApiController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Get orders list.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'status', 'customer_email', 'min_total', 'max_total',
            'date_from', 'date_to', 'sort_by', 'sort_order', 'per_page',
        ]);

        $orders = $this->orderService->search($filters);

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    /**
     * Get order detail.
     */
    public function show($id)
    {
        $order = Order::with(['customer', 'items', 'invoices', 'shipments'])->findOrFail($id);
        $orderDto = $this->orderService->getOrderDto($order);
        $stats = $this->orderService->getStats();

        return response()->json([
            'success' => true,
            'data'    => [
                'order' => $orderDto->toArray(),
                'stats' => $stats->toArray(),
            ],
        ]);
    }

    /**
     * Get order status history.
     */
    public function statusHistory($id)
    {
        $statusHistory = $this->orderService->getStatusHistory($id);

        return response()->json([
            'success' => true,
            'data'    => $statusHistory,
        ]);
    }

    /**
     * Get order activity log.
     */
    public function activityLog($id)
    {
        $activityLog = $this->orderService->getActivityLog($id);

        return response()->json([
            'success' => true,
            'data'    => $activityLog,
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'note'   => 'nullable|string|max:1000',
        ]);

        $result = $this->orderService->updateStatus(
            $id,
            $request->input('status'),
            $request->input('note')
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status transition.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
    }
}
