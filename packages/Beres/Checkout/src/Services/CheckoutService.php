<?php

namespace Beres\Checkout\Services;

use App\Models\AdminOrder;
use App\Models\AdminOrderItem;
use App\Models\AdminProduct;
use App\Models\AdminProductVariation;
use App\Services\CartService;
use Beres\Checkout\Contracts\CheckoutSessionRepositoryInterface;
use Beres\Checkout\DTOs\CheckoutDTO;
use Beres\Checkout\DTOs\OrderSummaryDTO;
use Beres\Shipping\Services\ShippingCalculatorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Session-cart based checkout. Creates AdminOrder + AdminOrderItem rows
 * (the custom dashboard's order system) — fully independent of Bagisto's
 * Webkul cart / order pipeline.
 */
class CheckoutService
{
    public function __construct(
        protected CheckoutSessionRepositoryInterface $sessionRepository,
        protected ShippingCalculatorService $shippingCalculator,
        protected CartService $cart,
    ) {}

    /**
     * Order summary built from the session cart.
     */
    public function getOrderSummary(?string $shippingMethod = null): OrderSummaryDTO
    {
        $cart = $this->cart->summary();

        if ($cart['count'] === 0) {
            throw new \Exception('Cart is empty');
        }

        foreach ($cart['items'] as $line) {
            $product = AdminProduct::find($line['product_id'] ?? 0);

            if (! $product || $product->status !== 'active') {
                throw new \Exception('One of the products is no longer available.');
            }

            $stock = $product->stock;
            if (! empty($line['variation_id'])) {
                $variation = AdminProductVariation::where('product_id', $product->id)
                    ->find($line['variation_id']);
                $stock = $variation?->stock ?? 0;
            }

            if ((int) $stock < (int) ($line['quantity'] ?? 0)) {
                throw new \Exception('Insufficient stock for '.$product->name.'.');
            }
        }

        return OrderSummaryDTO::fromArray([
            'subtotal' => (float) $cart['subtotal'],
            'shipping_cost' => 0.0,
            'grand_total' => (float) $cart['subtotal'],
            'currency' => core()->getCurrentCurrencyCode(),
            'items' => $cart['items'],
            'shipping_address' => [],
            'shipping_method' => $shippingMethod,
            'payment_method' => '',
        ]);
    }

    /**
     * Create checkout session.
     */
    public function createSession(CheckoutDTO $dto): object
    {
        return $this->sessionRepository->create([
            'cart_id' => $dto->cartId,
            'customer_id' => $dto->customerId,
            'shipping_address' => $dto->shippingAddress,
            'billing_address' => $dto->billingAddress,
            'shipping_method' => $dto->shippingMethod,
            'shipping_cost' => $dto->shippingCost,
            'payment_method' => $dto->paymentMethod,
            'notes' => $dto->notes,
            'status' => 'active',
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
     * Place order from the session cart → AdminOrder + AdminOrderItem.
     */
    public function placeOrder(int $sessionId): AdminOrder
    {
        $session = $this->sessionRepository->getById($sessionId);

        if (! $session) {
            throw new \Exception('Checkout session not found');
        }

        $cart = $this->cart->summary();

        if ($cart['count'] === 0) {
            throw new \Exception('Cart is empty');
        }

        $ship = $session->shipping_address ?? [];

        $fullName = trim(($ship['first_name'] ?? '').' '.($ship['last_name'] ?? ''));
        $fullAddress = collect([
            $ship['address1'] ?? null,
            $ship['city'] ?? null,
            $ship['state'] ?? null,
            $ship['postcode'] ?? null,
            $ship['country'] ?? null,
        ])->filter()->implode(', ');

        $subtotal = (float) $cart['subtotal'];
        $shippingCost = (float) ($session->shipping_cost ?? 0);

        $notes = trim('Metode pembayaran: '.($session->payment_method ?? '-')."\n".($session->notes ?? ''));

        return DB::transaction(function () use ($session, $cart, $ship, $fullName, $fullAddress, $subtotal, $shippingCost, $notes, $sessionId) {
            $order = AdminOrder::create([
                'customer_name' => $fullName ?: '-',
                'customer_phone' => $ship['phone'] ?? null,
                'customer_address' => $fullAddress,
                'shipping_address' => $fullAddress,
                'shipping_courier' => explode('|', (string) $session->shipping_method)[0] ?? null,
                'shipping_service' => explode('|', (string) $session->shipping_method)[1] ?? $session->shipping_method,
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'total' => $subtotal + $shippingCost,
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $notes ?: null,
            ]);

            foreach ($cart['items'] as $line) {
                AdminOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product_id'] ?? null,
                    'product_name' => $line['name'],
                    'quantity' => $line['quantity'],
                    'price' => $line['price'],
                    'total' => $line['price'] * $line['quantity'],
                ]);
            }

            $this->cart->clear();
            $this->sessionRepository->markCompleted($sessionId);

            Log::info("Beres order placed: #{$order->order_number}");

            return $order;
        });
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
