<?php

use App\Models\AdminCategory;
use App\Models\AdminProduct;
use App\Models\AdminProductImage;
use App\Models\AdminProductVariation;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('products:count', function () {
    if (! Schema::hasTable('admin_products')) {
        $this->error('Tabel admin_products belum ada di database. Silakan jalankan php artisan migrate.');

        return 1;
    }

    $productCount = AdminProduct::count();
    $categoryCount = Schema::hasTable('admin_categories') ? AdminCategory::count() : 0;

    $this->info("Total Produk: {$productCount}");
    $this->info("Total Kategori: {$categoryCount}");

    return 0;
})->purpose('Cek jumlah produk dan kategori di database');

Artisan::command('products:clear', function () {
    if (! Schema::hasTable('admin_products')) {
        $this->error('Tabel admin_products belum ada.');

        return 1;
    }

    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    AdminProductImage::truncate();
    AdminProductVariation::truncate();
    AdminProduct::truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $this->info('✓ Seluruh data produk, variasi, dan gambar berhasil dikosongkan!');

    return 0;
})->purpose('Kosongkan seluruh data produk dan variasinya');
