<?php

namespace Beres\Checkout\Http\Controllers;

use App\Models\AdminOrder;
use App\Services\CartService;
use Beres\Checkout\DTOs\CheckoutDTO;
use Beres\Checkout\Services\CheckoutService;
use Beres\Payment\Services\PaymentService;
use Beres\Shipping\Services\ShippingCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
        protected ShippingCalculatorService $shippingCalculator,
        protected PaymentService $paymentService,
        protected CartService $cart,
    ) {}

    /**
     * Display checkout page — reads the session cart (custom AdminProduct
     * catalogue), fully decoupled from Bagisto's Webkul cart.
     */
    public function index()
    {
        $cart = $this->cart->summary();

        if ($cart['count'] === 0) {
            return redirect()->route('shop.home.index')
                ->with('warning', 'Keranjang kosong');
        }

        $couriers = $this->shippingCalculator->getAvailableCouriers();

        return view('beres-checkout::checkout.index', [
            'cart' => $cart,
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
            'weight' => 'required|integer|min:1',
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
                'data' => $costs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order summary from the session cart.
     */
    public function getSummary(Request $request)
    {
        $request->validate([
            'shipping_method' => 'nullable|string',
        ]);

        $cart = $this->cart->summary();

        if ($cart['count'] === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'subtotal' => $cart['subtotal'],
                'items_qty' => $cart['items_qty'],
                'shipping_cost' => 0,
                'grand_total' => $cart['subtotal'],
                'currency' => core()->getCurrentCurrencyCode(),
                'items' => $cart['items'],
                'shipping_method' => $request->input('shipping_method'),
            ],
        ]);
    }

    /**
     * Create checkout session. No Bagisto cart required — cart_id is
     * recorded as 0; the live session cart is read at placeOrder time.
     */
    public function createSession(Request $request)
    {
        $data = $request->validate([
            'shipping_address.first_name' => 'required|string',
            'shipping_address.last_name' => 'required|string',
            'shipping_address.email' => 'required|email',
            'shipping_address.phone' => 'required|string',
            'shipping_address.address1' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.postcode' => 'required|string',
            'shipping_address.country' => 'required|string',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
            'shipping_cost' => 'nullable|numeric',
        ]);

        if ($this->cart->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $dto = CheckoutDTO::fromArray([
            'cart_id' => 0,
            'customer_id' => auth()->guard('customer')->id(),
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $request->input('billing_address'),
            'shipping_method' => $data['shipping_method'],
            'shipping_cost' => (float) ($data['shipping_cost'] ?? 0),
            'payment_method' => $data['payment_method'],
            'notes' => $request->input('notes'),
        ]);

        $session = $this->checkoutService->createSession($dto);

        return response()->json([
            'success' => true,
            'data' => $session,
        ]);
    }

    /**
     * Place order — creates an AdminOrder (+ items) from the session cart.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:checkout_sessions,id',
        ]);

        try {
            $order = $this->checkoutService->placeOrder($request->input('session_id'));

            if (! $order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat pesanan',
                ], 400);
            }

            return response()->json([
                'success' => true,
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
        $order = AdminOrder::with('items')->find($request->query('order_id'));

        return view('beres-checkout::checkout.success', [
            'order' => $order,
        ]);
    }
}
