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
    if (Schema::hasTable('admin_product_images')) {
        AdminProductImage::truncate();
    }
    if (Schema::hasTable('admin_product_variations')) {
        AdminProductVariation::truncate();
    }
    AdminProduct::truncate();
    if (Schema::hasTable('homepage_highlights')) {
        DB::table('homepage_highlights')->where('section', '!=', 'categories')->delete();
    }
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $this->info('✓ Seluruh data produk, variasi, dan gambar berhasil dikosongkan!');

    return 0;
})->purpose('Kosongkan seluruh data produk dan variasinya');

Artisan::command('categories:clear', function () {
    if (! Schema::hasTable('admin_categories')) {
        $this->error('Tabel admin_categories belum ada.');

        return 1;
    }

    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    AdminCategory::truncate();
    if (Schema::hasTable('homepage_highlights')) {
        DB::table('homepage_highlights')->where('section', 'categories')->delete();
    }
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $this->info('✓ Seluruh data kategori berhasil dikosongkan!');

    return 0;
})->purpose('Kosongkan seluruh data kategori');

Artisan::command('highlights:clear', function () {
    if (Schema::hasTable('homepage_highlights')) {
        DB::table('homepage_highlights')->truncate();
        $this->info('✓ Pengaturan sorotan/highlight beranda berhasil dikosongkan!');
    } else {
        $this->info('Tabel homepage_highlights tidak ditemukan.');
    }

    return 0;
})->purpose('Kosongkan seluruh pengaturan highlight homepage');

Artisan::command('catalog:clear', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    if (Schema::hasTable('admin_product_images')) {
        AdminProductImage::truncate();
    }
    if (Schema::hasTable('admin_product_variations')) {
        AdminProductVariation::truncate();
    }
    if (Schema::hasTable('admin_products')) {
        AdminProduct::truncate();
    }
    if (Schema::hasTable('admin_categories')) {
        AdminCategory::truncate();
    }
    if (Schema::hasTable('homepage_highlights')) {
        DB::table('homepage_highlights')->truncate();
    }
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $this->info('✓ Seluruh katalog (produk, variasi, gambar, kategori, dan highlight) berhasil dikosongkan bersih!');

    return 0;
})->purpose('Kosongkan seluruh data katalog (produk, kategori, highlight)');
