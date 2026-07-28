<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RajaOngkir Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi RajaOngkir Shipping API
    |
    */

    'api_key' => env('RAJAONGKIR_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    */
    'base_url' => 'https://api.rajaongkir.com/starter',

    /*
    |--------------------------------------------------------------------------
    | Couriers
    |--------------------------------------------------------------------------
    |
    | Kurir yang didukung:
    | - jne
    | - tiki
    | - pos
    | - jnt
    | - sicepat
    | - anteraja
    | - dhl
    |
    */
    'couriers' => [
        'jne',
        'tiki',
        'pos',
        'jnt',
        'sicepat',
        'anteraja',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | Durasi cache untuk data provinsi/kota/kecamatan (dalam menit)
    |
    */
    'cache_duration' => 1440, // 24 hours

    /*
    |--------------------------------------------------------------------------
    | Weight Unit
    |--------------------------------------------------------------------------
    |
    | RajaOngkir menggunakan gram sebagai satuan berat
    | Konversi dari kg ke gram
    |
    */
    'weight_unit' => 'gram',

    /*
    |--------------------------------------------------------------------------
    | Origin City
    |--------------------------------------------------------------------------
    |
    | Kota asal untuk pengiriman
    |
    */
    'origin_city' => env('RAJAONGKIR_ORIGIN_CITY', '501'), // Bandung
];
