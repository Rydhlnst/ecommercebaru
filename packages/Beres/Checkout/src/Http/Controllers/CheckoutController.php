<?php

namespace Beres\Checkout\Http\Controllers;

use App\Models\AdminOrder;
use App\Services\CartService;
use App\Support\CheckoutSettings;
use Beres\Checkout\DTOs\CheckoutDTO;
use Beres\Checkout\Services\CheckoutService;
use Beres\Checkout\Services\WhatsAppOrderService;
use Beres\Payment\Services\MidtransService;
use Beres\Payment\Services\PaymentService;
use Beres\Shipping\Services\ShippingCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
        protected ShippingCalculatorService $shippingCalculator,
        protected PaymentService $paymentService,
        protected CartService $cart,
        protected WhatsAppOrderService $whatsappOrder,
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
        $midtrans = app(MidtransService::class);

        return view('beres-checkout::checkout.index', [
            'cart' => $cart,
            'couriers' => $couriers,
            'midtransActive' => $midtrans->isActive() && $midtrans->isConfigured(),
            'paymentMode' => CheckoutSettings::paymentMode(),
            'whatsappConfigured' => $this->whatsappOrder->isConfigured(),
            'cartWeight' => max(1, (int) collect($cart['items'])->sum(fn ($item) => ($item['weight'] ?? 0) * ($item['quantity'] ?? 1))),
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
            Log::warning('Shipping calculation failed', ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Biaya pengiriman belum dapat dihitung. Periksa kota tujuan dan coba lagi.',
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
        $paymentMode = CheckoutSettings::paymentMode();

        $data = $request->validate([
            'shipping_address.first_name' => 'required|string|max:100',
            'shipping_address.last_name' => 'required|string|max:100',
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.phone' => 'required|string|max:30',
            'shipping_address.address1' => 'required|string|max:255',
            'shipping_address.address2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:150',
            'shipping_address.city_id' => 'required|integer|min:1',
            'shipping_address.state' => 'required|string|max:150',
            'shipping_address.postcode' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:3',
            'shipping_method' => ['required', 'string', 'regex:/^[a-z0-9]+\|[^|]+$/i'],
            'payment_method' => ['required', \Illuminate\Validation\Rule::in([$paymentMode])],
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($paymentMode === CheckoutSettings::MIDTRANS
            && (! app(MidtransService::class)->isActive() || ! app(MidtransService::class)->isConfigured())) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran Midtrans belum tersedia. Silakan hubungi administrator.',
            ], 422);
        }

        if ($paymentMode === CheckoutSettings::WHATSAPP && ! $this->whatsappOrder->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp admin belum dikonfigurasi. Silakan hubungi administrator.',
            ], 422);
        }

        $cart = $this->cart->summary();

        if ($cart['count'] === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $shippingCost = $this->resolveShippingCost(
            $data['shipping_address'],
            $data['shipping_method'],
            max(1, (int) collect($cart['items'])->sum(fn ($item) => ($item['weight'] ?? 0) * ($item['quantity'] ?? 1)))
        );

        if ($shippingCost === null) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan pengiriman yang dipilih sudah tidak tersedia. Silakan pilih ulang.',
            ], 422);
        }

        $dto = CheckoutDTO::fromArray([
            'cart_id' => 0,
            'customer_id' => auth()->guard('customer')->id(),
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $request->input('billing_address'),
            'shipping_method' => $data['shipping_method'],
            // Always use the server-calculated rate. The browser value is not trusted.
            'shipping_cost' => $shippingCost,
            'payment_method' => $data['payment_method'],
            'notes' => $data['notes'] ?? null,
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
            $session = $this->checkoutService->getSession((int) $request->input('session_id'));
            $order = $this->checkoutService->placeOrder((int) $request->input('session_id'));

            if (! $order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat pesanan',
                ], 400);
            }

            $paymentUrl = null;
            if ($session && $session->payment_method === 'midtrans') {
                try {
                    $midtransService = app(MidtransService::class);
                    if ($midtransService->isActive() && $midtransService->isConfigured()) {
                        $items = $order->items->map(fn ($item) => [
                            'id' => (string) ($item->product_id ?? $item->id),
                            'price' => (int) $item->price,
                            'quantity' => (int) $item->quantity,
                            'name' => $item->product_name,
                        ])->values()->all();
                        if ((int) $order->shipping_cost > 0) {
                            $items[] = [
                                'id' => 'shipping',
                                'price' => (int) $order->shipping_cost,
                                'quantity' => 1,
                                'name' => 'Shipping',
                            ];
                        }
                        $params = [
                            'transaction_details' => [
                                'order_id' => $order->order_number,
                                'gross_amount' => (int) $order->total,
                            ],
                            'customer_details' => [
                                'first_name' => $session->shipping_address['first_name'] ?? $order->customer_name,
                                'last_name' => $session->shipping_address['last_name'] ?? '',
                                'email' => $session->shipping_address['email'] ?? '',
                                'phone' => $order->customer_phone,
                            ],
                            'item_details' => $items,
                            'enabled_payments' => $midtransService->getPaymentTypes(),
                        ];
                        $paymentUrl = $midtransService->createSnapToken($params);
                    }
                } catch (\Throwable $e) {
                    Log::error('Midtrans token generation error', ['exception' => $e, 'order_id' => $order->id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Pesanan tersimpan, tetapi halaman pembayaran belum dapat dibuka. Silakan coba lagi.',
                        'order_id' => $order->id,
                    ], 502);
                }
            }

            if ($session?->payment_method === 'midtrans' && ! $paymentUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran Midtrans belum dapat dibuat. Silakan coba lagi.',
                    'order_id' => $order->id,
                ], 502);
            }

            if ($session?->payment_method === CheckoutSettings::WHATSAPP) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'whatsapp_url' => $this->whatsappOrder->urlFor(
                        $order,
                        $session->shipping_address ?? []
                    ),
                ]);
            }

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'payment_url' => $paymentUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolve the selected shipping service again on the server so the order
     * total cannot be changed by editing the browser request.
     */
    private function resolveShippingCost(array $shippingAddress, string $shippingMethod, int $weight): ?float
    {
        [$courier, $service] = array_pad(explode('|', $shippingMethod, 2), 2, null);

        if (! $courier || ! $service || ! array_key_exists($courier, $this->shippingCalculator->getAvailableCouriers())) {
            return null;
        }

        try {
            $shippingServices = $this->shippingCalculator->calculateShippingCosts(
                $this->shippingCalculator->getOriginCity(),
                (int) $shippingAddress['city_id'],
                $weight,
                [$courier]
            );

            foreach ($shippingServices as $shippingService) {
                foreach ($shippingService->services ?? [] as $shippingCost) {
                    if (strcasecmp((string) $shippingCost->service, $service) === 0) {
                        return (float) $shippingCost->cost;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Checkout shipping rate validation failed.', [
                'courier' => $courier,
                'service' => $service,
                'exception' => $e,
            ]);
        }

        return null;
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
