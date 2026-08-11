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
                'name' => 'Admin Ankish',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // ─── Categories ──────────────────────────────────────────────────────
        $makanan = AdminCategory::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $minuman = AdminCategory::create(['name' => 'Minuman', 'slug' => 'minuman']);
        $sayur = AdminCategory::create(['name' => 'Sayuran', 'slug' => 'sayuran']);
        $buah = AdminCategory::create(['name' => 'Buah-buahan', 'slug' => 'buah-buahan']);
        $daging = AdminCategory::create(['name' => 'Daging & Seafood', 'slug' => 'daging-seafood']);
        $bumbu = AdminCategory::create(['name' => 'Bumbu & Rempah', 'slug' => 'bumbu-rempah']);
        $snack = AdminCategory::create(['name' => 'Snack & Cemilan', 'slug' => 'snack-cemilan', 'parent_id' => $makanan->id]);
        $kopi = AdminCategory::create(['name' => 'Kopi & Teh', 'slug' => 'kopi-teh', 'parent_id' => $minuman->id]);

        // ─── Products ────────────────────────────────────────────────────────
        // Variasi (weight in kg): 0.25 = 250g, 0.50 = 500g, 1.00 = 1000g.
        // Urutkan ascending agar "first variant" = berat/termurah (auto-select).
        $products = [
            // Makanan
            ['name' => 'Nasi Goreng Spesial', 'category_id' => $makanan->id, 'badge' => 'new', 'price' => 35000, 'stock' => 50, 'is_featured' => true, 'has_variations' => false, 'description' => 'Nasi goreng dengan ayam, telur, sayuran, dan bumbu rahasia. Porsi besar, rasa mantap.'],
            ['name' => 'Ayam Bakar Madu', 'category_id' => $makanan->id, 'badge' => 'sale', 'price' => 45000, 'stock' => 30, 'is_featured' => true, 'has_variations' => false, 'description' => 'Ayam bakar dengan bumbu madu khas, dibakar hingga kecokelatan. Cocok dengan nasi putih hangat.'],
            ['name' => 'Sate Ayam Madura', 'category_id' => $makanan->id, 'badge' => null, 'price' => 25000, 'stock' => 100, 'is_featured' => false, 'has_variations' => true, 'description' => 'Sate ayam 10 tusuk dengan bumbu kacang khas Madura. Saus dan lontong disertakan.', 'variations' => [
                ['weight' => 0.50, 'price' => 25000, 'stock' => 60],
                ['weight' => 1.00, 'price' => 45000, 'stock' => 40],
            ]],
            ['name' => 'Rendang Daging Sapi', 'category_id' => $makanan->id, 'badge' => 'new', 'price' => 85000, 'stock' => 20, 'is_featured' => true, 'has_variations' => false, 'description' => 'Rendang daging sapi Padang autentik, dimasak perlahan dengan rempah pilihan. Tahan lama, cocok untuk stok.'],
            ['name' => 'Gado-Gado Jakarta', 'category_id' => $makanan->id, 'badge' => null, 'price' => 28000, 'stock' => 40, 'is_featured' => false, 'has_variations' => false, 'description' => 'Gado-gado dengan sayuran segar, tahu, tempe, dan bumbu kacang. Porsi lengkap.'],

            // Snack & Cemilan
            ['name' => 'Keripik Tempe', 'category_id' => $snack->id, 'badge' => 'sale', 'price' => 18000, 'stock' => 80, 'is_featured' => false, 'has_variations' => true, 'description' => 'Keripik tempe renyah dengan bumbu original. Camilan ringan khas Jawa.', 'variations' => [
                ['weight' => 0.25, 'price' => 18000, 'stock' => 45],
                ['weight' => 0.50, 'price' => 32000, 'stock' => 35],
            ]],
            ['name' => 'Kue Lumpur Bogor', 'category_id' => $snack->id, 'badge' => 'new', 'price' => 22000, 'stock' => 35, 'is_featured' => true, 'has_variations' => false, 'description' => 'Kue lumpur khas Bogor, lembut dan manis. Isian kentang pilihan, cocok untuk teman teh.'],
            ['name' => 'Pastel Abon', 'category_id' => $snack->id, 'badge' => null, 'price' => 15000, 'stock' => 60, 'is_featured' => false, 'has_variations' => false, 'description' => 'Pastel goreng dengan isian abon sapi dan sayuran. Kulit renyah, isian melimpah.'],

            // Minuman
            ['name' => 'Kopi Arabika Gayo', 'category_id' => $kopi->id, 'badge' => 'habis_terjual', 'price' => 45000, 'stock' => 0, 'is_featured' => true, 'has_variations' => true, 'description' => 'Kopi arabika Gayo single origin, roasted medium. Aroma kuat, rasa fruity.', 'variations' => [
                ['weight' => 0.25, 'price' => 45000, 'stock' => 0],
                ['weight' => 0.50, 'price' => 85000, 'stock' => 0],
                ['weight' => 1.00, 'price' => 160000, 'stock' => 0],
            ]],
            ['name' => 'Teh Melati Wangi', 'category_id' => $kopi->id, 'badge' => null, 'price' => 35000, 'stock' => 45, 'is_featured' => false, 'has_variations' => true, 'description' => 'Teh melati pilihan, dikeringkan secara alami. Aroma harum, rasa lembut.', 'variations' => [
                ['weight' => 0.25, 'price' => 35000, 'stock' => 25],
                ['weight' => 0.50, 'price' => 65000, 'stock' => 20],
            ]],
            ['name' => 'Es Jeruk Segar', 'category_id' => $minuman->id, 'badge' => 'new', 'price' => 18000, 'stock' => 100, 'is_featured' => true, 'has_variations' => false, 'description' => 'Jus jeruk peras segar tanpa pemanis buatan. Diperas langsung dari jeruk segar.'],
            ['name' => 'Jus Alpukat Kocok', 'category_id' => $minuman->id, 'badge' => 'sale', 'price' => 22000, 'stock' => 50, 'is_featured' => false, 'has_variations' => false, 'description' => 'Jus alpukat kocok dengan susu coklat dan es. Creamy dan menyegarkan.'],

            // Sayuran
            ['name' => 'Bayam Organik 500gr', 'category_id' => $sayur->id, 'badge' => 'new', 'price' => 12000, 'stock' => 70, 'is_featured' => false, 'has_variations' => false, 'description' => 'Bayam segar organik, dipetik hari ini. Cocok untuk tumis, sup, atau salad.'],
            ['name' => 'Wortel Impor 1kg', 'category_id' => $sayur->id, 'badge' => null, 'price' => 15000, 'stock' => 60, 'is_featured' => false, 'has_variations' => false, 'description' => 'Wortel impor kualitas premium, ukuran besar. Manis dan renyah.'],
            ['name' => 'Brokoli Hijau Segar', 'category_id' => $sayur->id, 'badge' => 'sale', 'price' => 18000, 'stock' => 40, 'is_featured' => true, 'has_variations' => false, 'description' => 'Brokoli hijau segar, kaya vitamin. Cocok untuk tumis, sup, atau steam.'],

            // Buah-buahan
            ['name' => 'Mangga Harumanis 2kg', 'category_id' => $buah->id, 'badge' => 'new', 'price' => 55000, 'stock' => 25, 'is_featured' => true, 'has_variations' => false, 'description' => 'Mangga harumanis pilihan, manis dan harum. Dipetik saat matang pohon.'],
            ['name' => 'Jeruk Mandarin 1kg', 'category_id' => $buah->id, 'badge' => null, 'price' => 32000, 'stock' => 50, 'is_featured' => false, 'has_variations' => false, 'description' => 'Jeruk mandarin manis, kulit tipis, mudah dikupas. Cocok untuk jus atau dimakan langsung.'],
            ['name' => 'Pisang Cavendish 1kg', 'category_id' => $buah->id, 'badge' => 'sale', 'price' => 18000, 'stock' => 80, 'is_featured' => false, 'has_variations' => false, 'description' => 'Pisang cavendish import, ukuran besar. Manis dan lembut, cocok untuk diet.'],

            // Daging & Seafood
            ['name' => 'Dada Ayam Fillet 500gr', 'category_id' => $daging->id, 'badge' => 'new', 'price' => 42000, 'stock' => 35, 'is_featured' => true, 'has_variations' => false, 'description' => 'Dada ayam fillet segar, tanpa tulang dan kulit. Cocok untuk masak apa saja.'],
            ['name' => 'Ikan Salmon Steak', 'category_id' => $daging->id, 'badge' => 'sale', 'price' => 125000, 'stock' => 15, 'is_featured' => true, 'has_variations' => false, 'description' => 'Salmon steak segar, potongan tebal. Kaya omega-3, cocok untuk grill atau pan-fry.'],
            ['name' => 'Udang Vaname 500gr', 'category_id' => $daging->id, 'badge' => null, 'price' => 75000, 'stock' => 20, 'is_featured' => false, 'has_variations' => false, 'description' => 'Udang vaname segar, ukuran besar. Cocok untuk tumis, goreng, atau bakar.'],

            // Bumbu & Rempah
            ['name' => 'Rendang Instan', 'category_id' => $bumbu->id, 'badge' => 'new', 'price' => 35000, 'stock' => 60, 'is_featured' => false, 'has_variations' => true, 'description' => 'Bumbu rendang instan, tinggal campur daging. Rasa autentik Padang.', 'variations' => [
                ['weight' => 0.25, 'price' => 35000, 'stock' => 30],
                ['weight' => 0.50, 'price' => 60000, 'stock' => 20],
                ['weight' => 1.00, 'price' => 110000, 'stock' => 10],
            ]],
            ['name' => 'Sambal Terasi Botolan', 'category_id' => $bumbu->id, 'badge' => 'sale', 'price' => 22000, 'stock' => 55, 'is_featured' => true, 'has_variations' => false, 'description' => 'Sambal terasi homemade, pedas dan gurih. Cocok untuk semua masakan.'],
        ];

        foreach ($products as $pData) {
            $slug = Str::slug($pData['name']).random_int(100, 999);
            $seedVariations = $pData['variations'] ?? null;
            unset($pData['variations']);

            $product = AdminProduct::create(array_merge($pData, ['slug' => $slug]));

            // Sinkron dengan AdminProductController::saveVariations():
            // price = harga variasi pertama (termurah), stock = total stok variasi.
            if (! empty($seedVariations)) {
                usort($seedVariations, fn ($a, $b) => $a['weight'] <=> $b['weight']);

                foreach ($seedVariations as $v) {
                    $product->variations()->create([
                        'weight' => $v['weight'],
                        'price' => $v['price'],
                        'stock' => $v['stock'],
                    ]);
                }

                $product->update([
                    'price' => $seedVariations[0]['price'],
                    'stock' => array_sum(array_column($seedVariations, 'stock')),
                ]);
            }
        }

        // ─── Orders ──────────────────────────────────────────────────────────
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
            $subtotal = rand(50000, 500000);
            $shippingCost = rand(10000, 30000);
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

        // ─── Blog ────────────────────────────────────────────────────────────
        $blogCat1 = BlogCategory::create(['name' => 'Tips Memasak', 'slug' => 'tips-memasak']);
        $blogCat2 = BlogCategory::create(['name' => 'Resep', 'slug' => 'resep']);
        $blogCat3 = BlogCategory::create(['name' => 'Gaya Hidup Sehat', 'slug' => 'gaya-hidup-sehat']);

        BlogPost::create([
            'title' => '5 Tips Memasak Rendang yang Empuk dan Lezat',
            'slug' => 'tips-memasak-rendang-'.random_int(100, 999),
            'content' => '<p>Rendang adalah masakan khas Padang yang memerlukan waktu lama untuk dimasak. Berikut tips agar rendang empuk dan bumbunya meresap...</p>',
            'blog_category_id' => $blogCat1->id,
            'tags' => 'rendang,memasak,tips',
            'is_published' => true,
            'published_at' => now()->subDays(3),
        ]);

        BlogPost::create([
            'title' => 'Resep Sambal Terasi Pedas Manis',
            'slug' => 'resep-sambal-terasi-'.random_int(100, 999),
            'content' => '<p>Sambal terasi adalah pelengkap wajib untuk semua masakan Indonesia. Berikut resep sederhana yang bisa dicoba di rumah...</p>',
            'blog_category_id' => $blogCat2->id,
            'tags' => 'sambal,resep,pedas',
            'is_published' => true,
            'published_at' => now()->subDays(1),
        ]);

        BlogPost::create([
            'title' => 'Manfaat Kopi Gayo untuk Kesehatan',
            'slug' => 'manfaat-kopi-gayo-'.random_int(100, 999),
            'content' => '<p>Kopi Gayo dari Aceh memiliki banyak manfaat kesehatan. Kandungan antioksidannya tinggi dan dapat membantu meningkatkan metabolisme...</p>',
            'blog_category_id' => $blogCat3->id,
            'tags' => 'kopi,kesehatan,gayo',
            'is_published' => true,
            'published_at' => now()->subDays(2),
        ]);

        // ─── Reviews ─────────────────────────────────────────────────────────
        $reviewNames = ['Rina', 'Dedi', 'Maya', 'Tono', 'Lia'];
        $reviewComments = [
            'Rasanya enak banget, seperti buatan rumah!',
            'Pengiriman cepat, kemasan masih hangat.',
            'Kualitas oke, harga terjangkau.',
            'Fresh, sesuai dengan yang dijanjikan.',
            'Puas banget, pasti order lagi.',
            'Bumbu meresap sempurna, recommended!',
            'Porsi cukup besar, worth it.',
            'Kemasan rapi dan aman.',
        ];

        $products = AdminProduct::all();
        for ($i = 0; $i < 10; $i++) {
            $product = $products->random();
            AdminReview::create([
                'product_id' => $product->id,
                'customer_name' => $reviewNames[array_rand($reviewNames)],
                'rating' => rand(4, 5),
                'comment' => $reviewComments[array_rand($reviewComments)],
                'is_approved' => rand(0, 1),
            ]);
        }

        // ─── FAQs ────────────────────────────────────────────────────────────
        Faq::create(['question' => 'Apakah makanan dijamin fresh?', 'answer' => '<p>Semua makanan kami diproses dan dikirim di hari yang sama untuk menjamin kesegaran.</p>', 'is_active' => true, 'sort_order' => 1]);
        Faq::create(['question' => 'Berapa lama pengiriman?', 'answer' => '<p>Pengiriman reguler 2-3 hari kerja. Same day delivery tersedia untuk area Jakarta.</p>', 'is_active' => true, 'sort_order' => 2]);
        Faq::create(['question' => 'Bagaimana cara menyimpan makanan yang dikirim?', 'answer' => '<p>Untuk makanan basah, segera simpan di kulkas. Untuk kering, simpan di tempat sejuk dan kering.</p>', 'is_active' => true, 'sort_order' => 3]);
        Faq::create(['question' => 'Apakah bisa retur makanan?', 'answer' => '<p>Karena produk makanan, retur hanya diterima jika produk rusak atau tidak sesuai pesanan dalam 1x24 jam.</p>', 'is_active' => true, 'sort_order' => 4]);
        Faq::create(['question' => 'Metode pembayaran apa saja yang diterima?', 'answer' => '<p>Kami menerima transfer bank, e-wallet (GoPay, OVO, Dana), dan kartu kredit.</p>', 'is_active' => true, 'sort_order' => 5]);

        // ─── Settings ────────────────────────────────────────────────────────
        SiteSetting::setValue('policy_privacy', '<h3>Privacy Policy</h3><p>Kami menghargai privasi Anda. Data yang dikumpulkan hanya digunakan untuk keperluan transaksi.</p>');
        SiteSetting::setValue('policy_refund', '<h3>Refund Policy</h3><p>Refund dilakukan dalam 1-3 hari kerja setelah pengajuan disetujui. Untuk produk makanan, refund hanya jika produk rusak.</p>');
        SiteSetting::setValue('policy_shipping', '<h3>Shipping Policy</h3><p>Pengiriman dilakukan via JNE, J&T, dan SiCepat. Estimasi 2-3 hari kerja. Same day delivery untuk area Jakarta.</p>');
        SiteSetting::setValue('policy_terms', '<h3>Terms of Service</h3><p>Dengan menggunakan situs ini, Anda menyetujui syarat dan ketentuan yang berlaku.</p>');

        SiteSetting::setValue('store_whatsapp', '6281234567890');
        SiteSetting::setValue('store_country', 'Indonesia');
        SiteSetting::setValue('store_address', 'Jl. Merdeka No. 123, Jakarta Selatan, DKI Jakarta 12345');
        SiteSetting::setValue('store_phone', '021-1234567');
        SiteSetting::setValue('store_email', 'info@ankishmart.id');
    }
}
