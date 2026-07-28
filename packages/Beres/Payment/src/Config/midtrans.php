<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi Midtrans Payment Gateway
    |
    */

    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Set ke 'production' untuk live environment
    | Set ke 'sandbox' untuk testing
    |
    */
    'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Snap URL
    |--------------------------------------------------------------------------
    */
    'snap_url' => [
        'sandbox'    => 'https://app.sandbox.midtrans.com/snap/snap.js',
        'production' => 'https://app.midtrans.com/snap/snap.js',
    ],

    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    */
    'api_url' => [
        'sandbox'    => 'https://api.sandbox.midtrans.com',
        'production' => 'https://api.midtrans.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Metode pembayaran yang didukung:
    | - bank_transfer (VA BCA, Mandiri, BRI, BNI, etc)
    | - e_wallet (GoPay, OVO, Dana, ShopeePay)
    | - credit_card
    | - convenience_store (Alfamart, Indomaret)
    |
    */
    'enabled_methods' => [
        'bank_transfer',
        'e_wallet',
        'credit_card',
        'convenience_store',
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback URL
    |--------------------------------------------------------------------------
    */
    'finish_redirect_url' => env('MIDTRANS_FINISH_URL', 'http://localhost/checkout/success'),
    'unfinish_redirect_url' => env('MIDTRANS_UNFINISH_URL', 'http://localhost/checkout'),
    'error_redirect_url' => env('MIDTRANS_ERROR_URL', 'http://localhost/checkout'),
];
