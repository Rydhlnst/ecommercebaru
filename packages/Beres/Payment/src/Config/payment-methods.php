<?php

use Beres\Payment\Payment\MidtransSnap;

/**
 * Registered into Bagisto's `payment_methods` config via mergeConfigFrom
 * in PaymentServiceProvider. The `active` flag here is intentionally true;
 * runtime availability is decided by MidtransSnap::isAvailable(), which
 * reads the admin dashboard toggle at beres_storefront.midtrans.active.
 */
return [
    'midtrans' => [
        'code'             => 'midtrans',
        'title'            => 'Midtrans',
        'description'      => 'Bayar via Midtrans Snap.',
        'class'            => MidtransSnap::class,
        'active'           => true,
        'generate_invoice' => false,
        'sort'             => 1,
    ],
];
