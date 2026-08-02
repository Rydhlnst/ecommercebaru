<?php

use Beres\Shipping\Carriers\RajaOngkir;

/**
 * Merged into Bagisto's `carriers` config in ShippingServiceProvider.
 * Runtime availability handled by RajaOngkir::isAvailable() reading
 * beres_storefront.shipping.active from the admin dashboard.
 */
return [
    'rajaongkir' => [
        'code'         => 'rajaongkir',
        'title'        => 'RajaOngkir',
        'description'  => 'Ongkir dihitung otomatis (JNE, J&T, SiCepat, dll).',
        'active'       => true,
        'class'        => RajaOngkir::class,
        'type'         => 'per_order',
    ],
];
