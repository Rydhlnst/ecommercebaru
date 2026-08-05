<?php

namespace Database\Seeders;

use App\Models\AdminCategory;
use App\Models\AdminOrder;
use App\Models\AdminOrderItem;
use App\Models\AdminProduct;
use App\Models\AdminReview;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@toko.com'],
            [
                'name' => 'Admin Beres',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        $cat1 = AdminCategory::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $cat2 = AdminCategory::create(['name' => 'Fashion', 'slug' => 'fashion']);
        $cat3 = AdminCategory::create(['name' => 'Aksesoris', 'slug' => 'aksesoris', 'parent_id' => $cat2->id]);
        $cat4 = AdminCategory::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $cat5 = AdminCategory::create(['name' => 'Minuman', 'slug' => 'minuman', 'parent_id' => $cat4->id]);

        $products = [
            ['name' => 'Laptop ASUS VivoBook', 'category_id' => $cat1->id, 'badge' => 'new', 'price' => 8500000, 'stock' => 15, 'is_featured' => true, 'has_variations' => false, 'description' => 'Laptop ASUS VivoBook 14 inch, RAM 8GB, SSD 512GB.'],
            ['name' => 'Samsung Galaxy S24', 'category_id' => $cat1->id, 'badge' => 'sale', 'price' => 12500000, 'stock' => 8, 'is_featured' => true, 'has_variations' => false, 'description' => 'Samsung Galaxy S24 Ultra, 256GB, AI features.'],
            ['name' => 'Kaos Katun Premium', 'category_id' => $cat2->id, 'badge' => null, 'price' => 89000, 'stock' => 150, 'is_featured' => false, 'has_variations' => true, 'description' => 'Kaos katun premium 30s, tersedia berbagai warna.'],
            ['name' => 'Tas Ransel Laptop', 'category_id' => $cat3->id, 'badge' => 'new', 'price' => 250000, 'stock' => 45, 'is_featured' => false, 'has_variations' => false, 'description' => 'Tas ransel laptop anti-air, muat hingga 15.6 inch.'],
            ['name' => 'Kopi Arabika Gayo', 'category_id' => $cat5->id, 'badge' => 'habis_terjual', 'price' => 65000, 'stock' => 0, 'is_featured' => true, 'has_variations' => false, 'description' => 'Kopi arabika Gayo single origin, roasted medium.'],
            ['name' => 'Headphone Sony WH-1000XM5', 'category_id' => $cat1->id, 'badge' => 'sale', 'price' => 4200000, 'stock' => 12, 'is_featured' => true, 'has_variations' => false, 'description' => 'Headphone noise cancelling Sony, 30 jam battery.'],
            ['name' => 'Jaket Kulit Pria', 'category_id' => $cat2->id, 'badge' => null, 'price' => 450000, 'stock' => 30, 'is_featured' => false, 'has_variations' => true, 'description' => 'Jaket kulit asli, desain klasik.'],
            ['name' => 'Sneakers Nike Air Max', 'category_id' => $cat2->id, 'badge' => 'new', 'price' => 1800000, 'stock' => 20, 'is_featured' => true, 'has_variations' => true, 'description' => 'Nike Air Max 90, original.'],
        ];

        foreach ($products as $pData) {
            $slug = Str::slug($pData['name']).random_int(100, 999);
            AdminProduct::create(array_merge($pData, ['slug' => $slug]));
        }

        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $customers = [
            ['name' => 'Budi Santoso', 'phone' => '081234567890'],
            ['name' => 'Siti Rahayu', 'phone' => '081298765432'],
            ['name' => 'Ahmad Fauzi', 'phone' => '081345678901'],
            ['name' => 'Dewi Lestari', 'phone' => '085678901234'],
            ['name' => 'Rizki Pratama', 'phone' => '087890123456'],
        ];

        for ($i = 1; $i <= 10; $i++) {
            $cust = $customers[array_rand($customers)];
            $status = $statuses[array_rand($statuses)];
            $subtotal = rand(100000, 5000000);
            $shippingCost = rand(15000, 50000);
            $order = AdminOrder::create([
                'order_number' => 'ORD-'.strtoupper(Str::random(8)),
                'customer_name' => $cust['name'],
                'customer_phone' => $cust['phone'],
                'customer_address' => 'Jl. Contoh No. '.$i.', Jakarta Selatan',
                'shipping_address' => 'Jl. Contoh No. '.$i.', Jakarta Selatan',
                'shipping_courier' => 'JNE',
                'shipping_service' => 'Regular',
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'total' => $subtotal + $shippingCost,
                'status' => $status,
                'payment_status' => $status === 'completed' ? 'paid' : (rand(0, 1) ? 'paid' : 'pending'),
            ]);

            $product = AdminProduct::inRandomOrder()->first();
            AdminOrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => rand(1, 3),
                'price' => $product->price,
                'total' => $product->price * rand(1, 3),
            ]);
        }

        $blogCat1 = BlogCategory::create(['name' => 'Tips & Trik', 'slug' => 'tips-trik']);
        $blogCat2 = BlogCategory::create(['name' => 'Berita', 'slug' => 'berita']);
        $blogCat3 = BlogCategory::create(['name' => 'Panduan', 'slug' => 'panduan']);

        BlogPost::create([
            'title' => 'Tips Memilih Laptop yang Tepat',
            'slug' => 'tips-memilih-laptop-yang-tepat'.random_int(100, 999),
            'content' => '<p>Memilih laptop yang tepat memerlukan pertimbangan beberapa faktor penting seperti kebutuhan, anggaran, dan spesifikasi.</p>',
            'blog_category_id' => $blogCat1->id,
            'tags' => 'tips,laptop,teknologi',
            'is_published' => true,
            'published_at' => now()->subDays(3),
        ]);

        BlogPost::create([
            'title' => 'Cara Merawat Sepatu Sneakers',
            'slug' => 'cara-merawat-sepatu-sneakers'.random_int(100, 999),
            'content' => '<p>Sneakers memerlukan perawatan khusus agar tetap awet dan terlihat baru.</p>',
            'blog_category_id' => $blogCat1->id,
            'tags' => 'sepatu,perawatan,fashion',
            'is_published' => true,
            'published_at' => now()->subDays(1),
        ]);

        BlogPost::create([
            'title' => 'Draft Postingan Baru',
            'slug' => 'draft-postingan-baru'.random_int(100, 999),
            'content' => '<p>Ini adalah konten draft yang belum dipublikasikan.</p>',
            'blog_category_id' => $blogCat2->id,
            'tags' => 'draft',
            'is_published' => false,
        ]);

        $reviewNames = ['Rina', 'Dedi', 'Maya', 'Tono', 'Lia'];
        $reviewComments = [
            'Produk sangat bagus, sesuai deskripsi!',
            'Pengiriman cepat, kemasan rapi.',
            'Kualitas oke, harga terjangkau.',
            'Barang original, recommended seller.',
            'Puas dengan pembelian ini.',
        ];

        $products = AdminProduct::all();
        for ($i = 0; $i < 8; $i++) {
            $product = $products->random();
            AdminReview::create([
                'product_id' => $product->id,
                'customer_name' => $reviewNames[array_rand($reviewNames)],
                'rating' => rand(3, 5),
                'comment' => $reviewComments[array_rand($reviewComments)],
                'is_approved' => rand(0, 1),
            ]);
        }

        Faq::create(['question' => 'Bagaimana cara memesan produk?', 'answer' => '<p>Pilih produk yang diinginkan, masukkan ke keranjang, lalu ikuti proses checkout.</p>', 'is_active' => true, 'sort_order' => 1]);
        Faq::create(['question' => 'Berapa lama pengiriman?', 'answer' => '<p>Pengiriman biasanya memakan waktu 2-5 hari kerja tergantung lokasi.</p>', 'is_active' => true, 'sort_order' => 2]);
        Faq::create(['question' => 'Apakah bisa retur barang?', 'answer' => '<p>Retur bisa dilakukan dalam 7 hari setelah barang diterima dengan kondisi masih baik.</p>', 'is_active' => true, 'sort_order' => 3]);
        Faq::create(['question' => 'Metode pembayaran apa saja yang diterima?', 'answer' => '<p>Kami menerima transfer bank, e-wallet (GoPay, OVO, Dana), dan kartu kredit.</p>', 'is_active' => true, 'sort_order' => 4]);

        SiteSetting::setValue('policy_privacy', '<h3>Privacy Policy</h3><p>Kami menghargai privasi Anda. Data yang dikumpulkan hanya digunakan untuk keperluan transaksi.</p>');
        SiteSetting::setValue('policy_refund', '<h3>Refund Policy</h3><p>Refund dilakukan dalam 3-5 hari kerja setelah pengajuan disetujui.</p>');
        SiteSetting::setValue('policy_shipping', '<h3>Shipping Policy</h3><p>Pengiriman dilakukan via JNE, J&T, dan SiCepat. Estimasi 2-5 hari kerja.</p>');
        SiteSetting::setValue('policy_terms', '<h3>Terms of Service</h3><p>Dengan menggunakan situs ini, Anda menyetujui syarat dan ketentuan yang berlaku.</p>');

        SiteSetting::setValue('store_whatsapp', '6281234567890');
        SiteSetting::setValue('store_country', 'Indonesia');
        SiteSetting::setValue('store_address', 'Jl. Merdeka No. 123, Jakarta Selatan, DKI Jakarta 12345');
        SiteSetting::setValue('store_phone', '021-1234567');
        SiteSetting::setValue('store_email', 'info@berescommerce.id');
    }
}
