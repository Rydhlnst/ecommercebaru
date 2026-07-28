<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | Definisi role untuk admin panel
    |
    */
    'roles' => [
        'owner' => [
            'name'        => 'Owner',
            'description' => 'Akses penuh ke semua fitur',
            'permissions' => ['*'],
        ],
        'admin' => [
            'name'        => 'Admin',
            'description' => 'Akses penuh kecuali pengaturan',
            'permissions' => [
                'dashboard.view',
                'products.*',
                'categories.*',
                'customers.*',
                'orders.*',
                'inventory.*',
                'reports.*',
                'cms.*',
            ],
        ],
        'warehouse' => [
            'name'        => 'Gudang',
            'description' => 'Akses untuk inventori dan pesanan',
            'permissions' => [
                'dashboard.view',
                'products.view',
                'inventory.*',
                'orders.view',
                'orders.update_status',
            ],
        ],
        'finance' => [
            'name'        => 'Keuangan',
            'description' => 'Akses untuk pesanan, pembayaran, dan laporan',
            'permissions' => [
                'dashboard.view',
                'orders.*',
                'reports.*',
                'customers.view',
            ],
        ],
        'customer_service' => [
            'name'        => 'Layanan Pelanggan',
            'description' => 'Akses untuk pelanggan dan pesanan',
            'permissions' => [
                'dashboard.view',
                'customers.*',
                'orders.view',
                'orders.update_status',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Definisi permission untuk admin panel
    |
    */
    'permissions' => [
        'dashboard' => [
            'view' => 'Lihat Dashboard',
        ],
        'products' => [
            'view'   => 'Lihat Produk',
            'create' => 'Tambah Produk',
            'update' => 'Edit Produk',
            'delete' => 'Hapus Produk',
        ],
        'categories' => [
            'view'   => 'Lihat Kategori',
            'create' => 'Tambah Kategori',
            'update' => 'Edit Kategori',
            'delete' => 'Hapus Kategori',
        ],
        'customers' => [
            'view'   => 'Lihat Pelanggan',
            'create' => 'Tambah Pelanggan',
            'update' => 'Edit Pelanggan',
            'delete' => 'Hapus Pelanggan',
        ],
        'orders' => [
            'view'          => 'Lihat Pesanan',
            'create'        => 'Buat Pesanan',
            'update'        => 'Edit Pesanan',
            'delete'        => 'Hapus Pesanan',
            'update_status' => 'Update Status Pesanan',
        ],
        'inventory' => [
            'view'   => 'Lihat Inventori',
            'update' => 'Update Inventori',
        ],
        'reports' => [
            'view'   => 'Lihat Laporan',
            'export' => 'Export Laporan',
        ],
        'settings' => [
            'view'   => 'Lihat Pengaturan',
            'update' => 'Update Pengaturan',
        ],
        'cms' => [
            'view'   => 'Lihat CMS',
            'create' => 'Tambah Halaman CMS',
            'update' => 'Edit Halaman CMS',
            'delete' => 'Hapus Halaman CMS',
        ],
    ],
];
