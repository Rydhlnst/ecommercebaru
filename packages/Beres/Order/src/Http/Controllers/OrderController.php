<?php

namespace Beres\Order\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Order\Services\OrderService;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Display order listing.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'status', 'customer_email', 'min_total', 'max_total',
            'date_from', 'date_to', 'sort_by', 'sort_order', 'per_page',
        ]);

        $orders = $this->orderService->search($filters);
        $statuses = OrderService::STATUSES;

        return view('beres-order::orders.index', [
            'orders'    => $orders,
            'filters'   => $filters,
            'statuses'  => $statuses,
        ]);
    }

    /**
     * Display order detail.
     */
    public function show($id)
    {
        $order = Order::with(['customer', 'items', 'invoices', 'shipments'])->findOrFail($id);
        $orderDto = $this->orderService->getOrderDto($order);
        $statusHistory = $this->orderService->getStatusHistory($id);
        $activityLog = $this->orderService->getActivityLog($id);
        $validTransitions = $this->orderService->getValidTransitions($order->status);
        $statuses = OrderService::STATUSES;

        return view('beres-order::orders.show', [
            'order'             => $orderDto,
            'orderModel'        => $order,
            'statusHistory'     => $statusHistory,
            'activityLog'       => $activityLog,
            'validTransitions'  => $validTransitions,
            'statuses'          => $statuses,
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,waiting_payment,paid,processing,packing,shipped,completed,canceled,refunded',
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

    /**
     * Export orders to CSV.
     */
    public function export(Request $request)
    {
        $filters = $request->only(['ids']);
        $filePath = $this->orderService->exportToCsv($filters);

        return response()->download($filePath, 'orders_export_' . date('Y-m-d_His') . '.csv', [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}
