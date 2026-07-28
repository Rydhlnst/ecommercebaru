<?php

namespace Beres\Checkout\Services;

use Beres\Checkout\Contracts\CheckoutSessionRepositoryInterface;
use Beres\Checkout\DTOs\CheckoutDTO;
use Beres\Checkout\DTOs\OrderSummaryDTO;
use Beres\Checkout\Models\CheckoutSession;
use Beres\Shipping\Services\ShippingCalculatorService;
use Beres\Payment\Services\PaymentService;
use Webkul\Cart\Facades\Cart as CartFacade;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutService
{
    public function __construct(
        protected CheckoutSessionRepositoryInterface $sessionRepository,
        protected ShippingCalculatorService $shippingCalculator,
        protected PaymentService $paymentService,
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Get order summary for checkout.
     */
    public function getOrderSummary(int $cartId, ?string $shippingMethod = null): OrderSummaryDTO
    {
        $cart = CartFacade::getCart();

        if (!$cart) {
            throw new \Exception('Cart is empty');
        }

        $subtotal = (float) $cart->grand_total;
        $shippingCost = 0;
        $taxAmount = (float) $cart->tax_total;
        $discountAmount = (float) $cart->discount_amount;

        // Calculate shipping if method is provided
        if ($shippingMethod) {
            $shippingCost = $this->calculateShippingCost($cart, $shippingMethod);
        }

        $grandTotal = $subtotal + $shippingCost + $taxAmount - $discountAmount;

        return OrderSummaryDTO::fromArray([
            'subtotal'         => $subtotal,
            'shipping_cost'    => $shippingCost,
            'tax_amount'       => $taxAmount,
            'discount_amount'  => $discountAmount,
            'grand_total'      => $grandTotal,
            'currency'         => core()->getCurrentCurrencyCode(),
            'items'            => $cart->items->toArray(),
            'shipping_address' => [],
            'shipping_method'  => $shippingMethod,
            'payment_method'   => '',
        ]);
    }

    /**
     * Calculate shipping cost for cart.
     */
    protected function calculateShippingCost($cart, string $shippingMethod): float
    {
        try {
            // Get cart weight
            $weight = 0;
            foreach ($cart->items as $item) {
                $weight += ($item->product->weight ?? 0) * $item->qty;
            }

            // Default origin city from config
            $originCity = config('rajaongkir.origin_city', '501');

            // Get destination from cart address
            $destinationCity = $cart->shipping_address?->city_id ?? null;

            if (!$destinationCity) {
                return 0;
            }

            // Calculate shipping costs
            $couriers = ['jne', 'jnt', 'sicepat'];
            $costs = $this->shippingCalculator->calculateShippingCosts(
                (int) $originCity,
                (int) $destinationCity,
                $weight,
                $couriers
            );

            // Find matching shipping method
            foreach ($costs as $service) {
                foreach ($service->services as $cost) {
                    if (strtolower($cost->service) === strtolower($shippingMethod)) {
                        return $cost->cost;
                    }
                }
            }

            return 0;
        } catch (\Exception $e) {
            Log::error('Shipping calculation error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Create checkout session.
     */
    public function createSession(CheckoutDTO $dto): object
    {
        return $this->sessionRepository->create([
            'cart_id'           => $dto->cartId,
            'customer_id'       => $dto->customerId,
            'shipping_address'  => $dto->shippingAddress,
            'billing_address'   => $dto->billingAddress,
            'shipping_method'   => $dto->shippingMethod,
            'shipping_cost'     => $dto->shippingCost,
            'payment_method'    => $dto->paymentMethod,
            'notes'             => $dto->notes,
            'status'            => 'active',
        ]);
    }

    /**
     * Update checkout session.
     */
    public function updateSession(int $sessionId, array $data): bool
    {
        return $this->sessionRepository->update($sessionId, $data);
    }

    /**
     * Place order from checkout.
     */
    public function placeOrder(int $sessionId): ?Order
    {
        $session = $this->sessionRepository->getById($sessionId);

        if (!$session) {
            throw new \Exception('Checkout session not found');
        }

        DB::beginTransaction();

        try {
            // Get cart
            $cart = CartFacade::getCart();

            if (!$cart) {
                throw new \Exception('Cart is empty');
            }

            // Create order using Bagisto's order repository
            $orderData = [
                'cart_id'             => $cart->id,
                'customer_id'         => $session->customer_id,
                'customer_email'      => $cart->customer_email ?? $session->shipping_address['email'] ?? '',
                'customer_first_name' => $cart->customer_first_name ?? $session->shipping_address['first_name'] ?? '',
                'customer_last_name'  => $cart->customer_last_name ?? $session->shipping_address['last_name'] ?? '',
                'shipping_method'     => $session->shipping_method,
                'shipping_amount'     => $session->shipping_cost,
                'payment_method'      => $session->payment_method,
                'grand_total'         => $cart->grand_total + $session->shipping_cost,
                'status'              => 'pending',
            ];

            // Use Bagisto's checkout to create order
            $order = app(\Webkul\Checkout\Http\Controllers\OnepageController::class)
                ->store($request ?? new \Illuminate\Http\Request());

            if ($order) {
                // Mark session as completed
                $this->sessionRepository->markCompleted($sessionId);

                Log::info("Order placed successfully: #{$order->id}");

                DB::commit();

                return $order;
            }

            throw new \Exception('Failed to create order');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order placement failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get checkout session.
     */
    public function getSession(int $sessionId): ?object
    {
        return $this->sessionRepository->getById($sessionId);
    }

    /**
     * Get active session for cart.
     */
    public function getActiveSession(int $cartId): ?object
    {
        return $this->sessionRepository->getActiveByCartId($cartId);
    }
}
