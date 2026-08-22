<?php

namespace Webkul\Shop\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Shop\Http\Resources\OrderResource;

class OrderController extends APIController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Display a listing of the customer's orders.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $orders = $this->orderRepository
            ->where('customer_id', auth()->guard('customer')->user()->id)
            ->with(['items.product', 'addresses'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return OrderResource|JsonResponse
     */
    public function view($id)
    {
        $order = $this->orderRepository->findOneWhere([
            'customer_id' => auth()->guard('customer')->user()->id,
            'id' => $id,
        ]);

        if (! $order) {
            return response()->json([
                'message' => 'Order not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $order->load(['items.product', 'addresses', 'invoices', 'shipments', 'refunds']);

        return new OrderResource($order);
    }

    /**
     * Cancel the specified order.
     *
     * @param  int  $id
     */
    public function cancel($id): JsonResponse
    {
        $customer = auth()->guard('customer')->user();

        $order = $customer->orders()->find($id);

        if (! $order) {
            return response()->json([
                'message' => 'Order not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        if (! $order->canCancel()) {
            return response()->json([
                'message' => 'This order cannot be canceled.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->orderRepository->cancel($order);

        if ($result) {
            return response()->json([
                'message' => 'Order has been canceled successfully.',
            ]);
        }

        return response()->json([
            'message' => 'Failed to cancel the order.',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Reorder items from a previous order.
     *
     * @param  int  $id
     */
    public function reorder($id): JsonResponse
    {
        $customer = auth()->guard('customer')->user();

        $order = $customer->orders()->find($id);

        if (! $order) {
            return response()->json([
                'message' => 'Order not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        if (! $order->canReorder()) {
            return response()->json([
                'message' => 'This order cannot be reordered.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $skippedBooking = false;

        foreach ($order->items as $item) {
            if ($item->type === 'booking') {
                $skippedBooking = true;

                continue;
            }

            try {
                Cart::addProduct($item->product, $item->additional);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to add product to cart: '.$item->name,
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $message = 'Items have been added to your cart.';

        if ($skippedBooking) {
            $message .= ' Booking products were skipped.';
        }

        return response()->json([
            'message' => $message,
        ]);
    }
}
