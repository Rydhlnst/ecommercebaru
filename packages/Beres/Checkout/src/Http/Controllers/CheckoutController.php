<?php

namespace Beres\Checkout\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Checkout\Services\CheckoutService;
use Beres\Checkout\DTOs\CheckoutDTO;
use Beres\Shipping\Services\ShippingCalculatorService;
use Beres\Payment\Services\PaymentService;
use Webkul\Cart\Facades\Cart as CartFacade;
use Illuminate\Support\Facades\Response;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
        protected ShippingCalculatorService $shippingCalculator,
        protected PaymentService $paymentService
    ) {}

    /**
     * Display checkout page.
     */
    public function index()
    {
        $cart = CartFacade::getCart();

        if (!$cart) {
            return redirect()->route('shop.checkout.cart.index')
                ->with('warning', 'Keranjang kosong');
        }

        $couriers = $this->shippingCalculator->getAvailableCouriers();

        return view('beres-checkout::checkout.index', [
            'cart'     => $cart,
            'couriers' => $couriers,
        ]);
    }

    /**
     * Calculate shipping cost.
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'city_id' => 'required|integer',
            'weight'  => 'required|integer|min:1',
            'courier' => 'required|string',
        ]);

        try {
            $originCity = config('rajaongkir.origin_city', '501');
            $destinationCity = $request->input('city_id');
            $weight = $request->input('weight');
            $couriers = [$request->input('courier')];

            $costs = $this->shippingCalculator->calculateShippingCosts(
                (int) $originCity,
                (int) $destinationCity,
                $weight,
                $couriers
            );

            return response()->json([
                'success' => true,
                'data'    => $costs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order summary.
     */
    public function getSummary(Request $request)
    {
        $request->validate([
            'shipping_method' => 'nullable|string',
        ]);

        try {
            $cart = CartFacade::getCart();

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang kosong',
                ], 400);
            }

            $summary = $this->checkoutService->getOrderSummary(
                $cart->id,
                $request->input('shipping_method')
            );

            return response()->json([
                'success' => true,
                'data'    => $summary->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create checkout session.
     */
    public function createSession(Request $request)
    {
        $request->validate([
            'shipping_address.first_name' => 'required|string',
            'shipping_address.last_name'  => 'required|string',
            'shipping_address.email'      => 'required|email',
            'shipping_address.phone'      => 'required|string',
            'shipping_address.address1'   => 'required|string',
            'shipping_address.city'       => 'required|string',
            'shipping_address.state'      => 'required|string',
            'shipping_address.postcode'   => 'required|string',
            'shipping_address.country'    => 'required|string',
            'shipping_method'             => 'required|string',
            'payment_method'              => 'required|string',
        ]);

        try {
            $cart = CartFacade::getCart();

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang kosong',
                ], 400);
            }

            $dto = CheckoutDTO::fromArray([
                'cart_id'           => $cart->id,
                'customer_id'       => auth()->guard('customer')->id(),
                'shipping_address'  => $request->input('shipping_address'),
                'billing_address'   => $request->input('billing_address'),
                'shipping_method'   => $request->input('shipping_method'),
                'shipping_cost'     => $request->input('shipping_cost', 0),
                'payment_method'    => $request->input('payment_method'),
                'notes'             => $request->input('notes'),
            ]);

            $session = $this->checkoutService->createSession($dto);

            return response()->json([
                'success' => true,
                'data'    => $session,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Place order.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:checkout_sessions,id',
        ]);

        try {
            $order = $this->checkoutService->placeOrder($request->input('session_id'));

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat pesanan',
                ], 400);
            }

            // Create payment if using Midtrans
            if ($order->payment_method === 'midtrans') {
                $paymentUrl = $this->paymentService->createPayment($order->id);

                return response()->json([
                    'success'    => true,
                    'order_id'   => $order->id,
                    'payment_url' => $paymentUrl,
                ]);
            }

            return response()->json([
                'success'  => true,
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display checkout success page.
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order_id');

        $order = \Webkul\Sales\Models\Order::find($orderId);

        return view('beres-checkout::checkout.success', [
            'order' => $order,
        ]);
    }
}
