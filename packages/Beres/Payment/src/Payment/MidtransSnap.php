<?php

namespace Beres\Payment\Payment;

use Beres\Payment\Services\MidtransService;
use Illuminate\Support\Facades\Log;
use Webkul\Payment\Payment\Payment;

/**
 * Midtrans Snap payment method — registered as a Bagisto payment method.
 * All settings (server_key, client_key, active, environment) live in
 * admin dashboard: Configure → Storefront → Midtrans Payment.
 */
class MidtransSnap extends Payment
{
    /**
     * @var string
     */
    protected $code = 'midtrans';

    /**
     * Read config from admin dashboard (Beres\Settings), fallback to
     * Bagisto sales.payment_methods.midtrans.$field (for `image`, `sort` etc.
     * still editable via Bagisto's built-in payment method UI).
     */
    public function getConfigData($field)
    {
        $adminValue = core()->getConfigData("beres_storefront.midtrans.$field");
        if ($adminValue !== null && $adminValue !== '') {
            return $adminValue;
        }

        return parent::getConfigData($field);
    }

    /**
     * Available when: admin toggled active AND cart has shippable items.
     */
    public function isAvailable()
    {
        if (! $this->cart) {
            $this->setCart();
        }

        return (bool) core()->getConfigData('beres_storefront.midtrans.active')
            && $this->cart?->hasOnlyStockableItems();
    }

    /**
     * Build a Snap payment URL and redirect the customer there.
     */
    public function getRedirectUrl()
    {
        $cart = $this->getCart();

        if (! $cart) {
            return route('shop.checkout.cart.index');
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id'     => (string) $cart->id . '-' . time(),
                    'gross_amount' => (int) round($cart->grand_total),
                ],
                'customer_details' => [
                    'first_name' => $cart->customer_first_name ?? 'Guest',
                    'last_name'  => $cart->customer_last_name ?? '',
                    'email'      => $cart->customer_email ?? '',
                    'phone'      => $cart->billing_address?->phone ?? '',
                ],
                'enabled_payments' => app(MidtransService::class)->getPaymentTypes(),
            ];

            return app(MidtransService::class)->getSnapUrl($params);
        } catch (\Throwable $e) {
            Log::error('MidtransSnap redirect error: ' . $e->getMessage());

            return route('shop.checkout.cart.index');
        }
    }

    public function getTitle()
    {
        $adminTitle = (string) core()->getConfigData('beres_storefront.midtrans.title');

        return $adminTitle !== '' ? $adminTitle : 'Midtrans (Kartu, VA, GoPay, QRIS)';
    }

    public function getDescription()
    {
        return 'Bayar via Midtrans Snap — Kartu kredit, Virtual Account, GoPay, ShopeePay, QRIS, dan lainnya.';
    }

    public function getSortOrder()
    {
        return 1;
    }

    public function getImage()
    {
        return bagisto_asset('images/cash-on-delivery.png', 'shop');
    }
}
