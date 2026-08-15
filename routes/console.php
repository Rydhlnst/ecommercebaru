<?php

use App\Models\AdminCategory;
use App\Models\AdminProduct;
use App\Models\AdminProductImage;
use App\Models\AdminProductVariation;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

Artisan::command('admin:set {email=ankeshmart@gmail.com} {password=AnkeshMart@2026!}', function ($email, $password) {
    if (! Schema::hasTable('users')) {
        $this->error('Tabel users belum ada.');

        return 1;
    }

    $user = User::where('email', $email)->orWhere('is_admin', true)->first();

    if ($user) {
        $user->update([
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);
        $this->info('✓ Akun Admin berhasil diperbarui!');
    } else {
        User::create([
            'name' => 'Admin Ankesh Mart',
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);
        $this->info('✓ Akun Admin baru berhasil dibuat!');
    }

    $this->line('----------------------------------------');
    $this->info("Email:    {$email}");
    $this->info("Password: {$password}");
    $this->line('----------------------------------------');

    return 0;
})->purpose('Set atau buat kredensial login admin baru');
