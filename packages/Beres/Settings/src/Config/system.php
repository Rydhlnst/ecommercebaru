<?php

/**
 * Storefront content settings — muncul di admin: Configure → Storefront
 * Semua text di homepage & map section bisa di-edit dari sini oleh customer.
 * Read via: core()->getConfigData('beres_storefront.<section>.<key>')
 */

return [
    [
        'key' => 'beres_storefront',
        'name' => 'Storefront Content',
        'info' => 'Semua text yang tampil di homepage dan section kontak.',
        'sort' => 1,
    ],

    /* =========================
     | HERO section
     ========================= */
    [
        'key' => 'beres_storefront.hero',
        'name' => 'Hero Banner',
        'info' => 'Bagian teratas homepage.',
        'sort' => 1,
        'fields' => [
            ['name' => 'eyebrow',    'title' => 'Label kecil di atas',  'type' => 'text',     'default' => 'Panen Hari Ini'],
            ['name' => 'headline',   'title' => 'Headline',             'type' => 'textarea', 'default' => "Bahan segar,\ndapur bahagia."],
            ['name' => 'subhead',    'title' => 'Sub-headline',         'type' => 'textarea', 'default' => 'Buah, sayur, daging, dan pantry pilihan — langsung dari petani dan produsen lokal terpercaya, sampai depan pintu kamu hari itu juga.'],
            ['name' => 'cta_primary', 'title' => 'Tombol utama',         'type' => 'text',     'default' => 'Belanja sekarang'],
            ['name' => 'cta_secondary', 'title' => 'Tombol sekunder',    'type' => 'text',     'default' => 'Lihat kategori'],
            ['name' => 'promo_label',  'title' => 'Label promo',        'type' => 'text',     'default' => 'Terlaris minggu ini'],
            ['name' => 'promo_text',   'title' => 'Text promo',         'type' => 'text',     'default' => 'Bundle Sarapan Sehat — mulai Rp 149.000'],

            /* Hero carousel slide images — upload 1-4 gambar untuk carousel utama */
            ['name' => 'slide1_img',   'title' => 'Slide 1 — Gambar',   'type' => 'image',    'info' => 'Rasio ideal 16:7 (desktop), min 2000px lebar. Format: JPG/PNG/WebP.'],
            ['name' => 'slide2_img',   'title' => 'Slide 2 — Gambar',   'type' => 'image'],
            ['name' => 'slide3_img',   'title' => 'Slide 3 — Gambar',   'type' => 'image'],
            ['name' => 'slide4_img',   'title' => 'Slide 4 — Gambar',   'type' => 'image'],
        ],
    ],

    /* =========================
     | TRUST BADGES
     ========================= */
    [
        'key' => 'beres_storefront.trust',
        'name' => 'Trust Badges',
        'info' => '4 keunggulan di bar hijau di bawah hero.',
        'sort' => 2,
        'fields' => [
            ['name' => 'badge1_title', 'title' => 'Badge 1 — Judul', 'type' => 'text',     'default' => '100% Alami'],
            ['name' => 'badge1_desc',  'title' => 'Badge 1 — Desc',  'type' => 'text',     'default' => 'Tanpa pengawet & bahan sintetis'],
            ['name' => 'badge2_title', 'title' => 'Badge 2 — Judul', 'type' => 'text',     'default' => 'Lab Certified'],
            ['name' => 'badge2_desc',  'title' => 'Badge 2 — Desc',  'type' => 'text',     'default' => 'Diuji kualitas oleh laboratorium'],
            ['name' => 'badge3_title', 'title' => 'Badge 3 — Judul', 'type' => 'text',     'default' => 'Kirim Sameday'],
            ['name' => 'badge3_desc',  'title' => 'Badge 3 — Desc',  'type' => 'text',     'default' => 'Area Jakarta pesan pagi sampai sore'],
            ['name' => 'badge4_title', 'title' => 'Badge 4 — Judul', 'type' => 'text',     'default' => 'Retur Gratis'],
            ['name' => 'badge4_desc',  'title' => 'Badge 4 — Desc',  'type' => 'text',     'default' => 'Rusak? Ganti tanpa perlu kembalikan'],
        ],
    ],

    /* =========================
     | SECTION HEADINGS
     ========================= */
    [
        'key' => 'beres_storefront.sections',
        'name' => 'Judul Section',
        'info' => 'Heading tiap section homepage.',
        'sort' => 3,
        'fields' => [
            ['name' => 'new_eyebrow',    'title' => 'New — Eyebrow',    'type' => 'text', 'default' => 'Baru datang'],
            ['name' => 'new_title',      'title' => 'New — Judul',      'type' => 'text', 'default' => 'Produk terbaru.'],
            ['name' => 'bundle_eyebrow', 'title' => 'Bundle — Eyebrow', 'type' => 'text', 'default' => 'Bundle hemat'],
            ['name' => 'bundle_title',   'title' => 'Bundle — Judul',   'type' => 'text', 'default' => 'Paket & bundle pilihan.'],
            ['name' => 'cat_eyebrow',    'title' => 'Kategori — Eyebrow', 'type' => 'text', 'default' => 'Belanja per kategori'],
            ['name' => 'cat_title',      'title' => 'Kategori — Judul', 'type' => 'text', 'default' => 'Semua kebutuhan dapur.'],
            ['name' => 'best_eyebrow',   'title' => 'Best — Eyebrow',   'type' => 'text', 'default' => 'Favorit pelanggan'],
            ['name' => 'best_title',     'title' => 'Best — Judul',     'type' => 'text', 'default' => 'Best seller.'],
            ['name' => 'review_eyebrow', 'title' => 'Review — Eyebrow', 'type' => 'text', 'default' => '4.8 dari 2.400+ ulasan'],
            ['name' => 'review_title',   'title' => 'Review — Judul',   'type' => 'text', 'default' => 'Ulasan pelanggan'],
            ['name' => 'faq_title',      'title' => 'FAQ — Judul',      'type' => 'text', 'default' => 'Pertanyaan umum.'],
            ['name' => 'google_review_eyebrow', 'title' => 'Google Review — Eyebrow', 'type' => 'text', 'default' => ''],
            ['name' => 'google_review_title',   'title' => 'Google Review — Judul',   'type' => 'text', 'default' => 'Ulasan Google Pelanggan'],
            ['name' => 'seed_eyebrow',   'title' => 'Seeds — Eyebrow',  'type' => 'text', 'default' => 'Pilihan terbaik'],
            ['name' => 'seed_title',     'title' => 'Seeds — Judul',    'type' => 'text', 'default' => 'Biji & Superfood Kami.'],
            ['name' => 'blog_eyebrow',   'title' => 'Blog — Eyebrow',   'type' => 'text', 'default' => 'Artikel & Wawasan'],
            ['name' => 'blog_title',     'title' => 'Blog — Judul',     'type' => 'text', 'default' => 'Artikel & Tips Terbaru'],
            ['name' => 'journal_title',  'title' => 'Journal — Judul',  'type' => 'text', 'default' => 'Cerita dari dapur.'],
        ],
    ],

    /* =========================
     | NATURAL BANNER
     ========================= */
    [
        'key' => 'beres_storefront.natural_banner',
        'name' => 'Banner Natural',
        'info' => 'Banner hijau "100% NATURAL · LAB CERTIFIED".',
        'sort' => 7,
        'fields' => [
            ['name' => 'text1', 'title' => 'Text kiri',  'type' => 'text', 'default' => '100% NATURAL'],
            ['name' => 'text2', 'title' => 'Text kanan', 'type' => 'text', 'default' => 'LAB CERTIFIED'],
        ],
    ],

    /* =========================
     | BLOG CTA
     ========================= */
    [
        'key' => 'beres_storefront.blog_cta',
        'name' => 'Blog CTA Banner',
        'info' => 'Banner hijau ajakan baca blog.',
        'sort' => 8,
        'fields' => [
            ['name' => 'title',   'title' => 'Judul',   'type' => 'textarea', 'default' => "Baca tips produk favorit\nuntuk gaya hidup lebih sehat."],
            ['name' => 'button',  'title' => 'Tombol',  'type' => 'text',     'default' => 'Lihat semua blog'],
            ['name' => 'image',   'title' => 'Background image', 'type' => 'image', 'info' => 'Gambar background banner. Rasio 16:9 disarankan.'],
            ['name' => 'link',    'title' => 'Link tombol', 'type' => 'text', 'default' => '/pages/blog', 'info' => 'URL atau slug CMS page tujuan.'],
        ],
    ],

    /* =========================
     | NEWSLETTER
     ========================= */
    [
        'key' => 'beres_storefront.newsletter',
        'name' => 'Newsletter',
        'info' => 'Section signup email di dekat footer.',
        'sort' => 4,
        'fields' => [
            ['name' => 'title',       'title' => 'Judul',       'type' => 'text',     'default' => 'Resep, tips, dan promo mingguan.'],
            ['name' => 'description', 'title' => 'Deskripsi',   'type' => 'textarea', 'default' => 'Dapatkan inspirasi masak, panduan bahan segar, dan diskon eksklusif langsung ke inbox kamu.'],
            ['name' => 'button',      'title' => 'Tombol',      'type' => 'text',     'default' => 'Daftar'],
        ],
    ],

    /* =========================
     | FAQ
     ========================= */
    [
        'key' => 'beres_storefront.faq',
        'name' => 'FAQ',
        'info' => '4 pertanyaan yang muncul di section FAQ.',
        'sort' => 9,
        'fields' => [
            ['name' => 'q1', 'title' => 'Q1', 'type' => 'text',     'default' => 'Berapa lama produk saya dibuat & dikirim?'],
            ['name' => 'a1', 'title' => 'A1', 'type' => 'textarea', 'default' => 'Untuk area Jakarta pesanan sebelum jam 10 pagi sampai di hari yang sama. Luar kota 2-5 hari kerja tergantung ekspedisi.'],
            ['name' => 'q2', 'title' => 'Q2', 'type' => 'text',     'default' => 'Berapa biaya pengiriman?'],
            ['name' => 'a2', 'title' => 'A2', 'type' => 'textarea', 'default' => 'Gratis ongkir untuk pembelian di atas Rp 200.000 area Jabodetabek. Di bawah itu tarif menyesuaikan ekspedisi.'],
            ['name' => 'q3', 'title' => 'Q3', 'type' => 'text',     'default' => 'Bagaimana kalau saya tidak puas?'],
            ['name' => 'a3', 'title' => 'A3', 'type' => 'textarea', 'default' => 'Ganti atau refund penuh dalam 24 jam setelah barang diterima — cukup kirim foto ke customer service kami.'],
            ['name' => 'q4', 'title' => 'Q4', 'type' => 'text',     'default' => 'Bisa ubah alamat setelah pesan?'],
            ['name' => 'a4', 'title' => 'A4', 'type' => 'textarea', 'default' => 'Bisa selama status pesanan masih "Diproses". Hubungi CS via WhatsApp dan kami bantu update.'],
        ],
    ],

    /* =========================
     | CONTACT & MAP (di atas footer)
     ========================= */
    [
        'key' => 'beres_storefront.contact',
        'name' => 'Kontak & Lokasi',
        'info' => 'Section maps + info kontak di atas footer.',
        'sort' => 5,
        'fields' => [
            ['name' => 'eyebrow',      'title' => 'Label kecil',       'type' => 'text',     'default' => 'Kunjungi kami'],
            ['name' => 'title',        'title' => 'Judul',             'type' => 'text',     'default' => 'Lokasi & kontak.'],
            ['name' => 'description',  'title' => 'Deskripsi',         'type' => 'textarea', 'default' => 'Mampir ke gerai fisik kami untuk pengalaman belanja langsung, atau hubungi tim kami untuk pertanyaan seputar produk & pengiriman.'],
            ['name' => 'country',      'title' => 'Nama Negara / Wilayah', 'type' => 'text', 'default' => 'Indonesia'],
            ['name' => 'address',      'title' => 'Alamat lengkap',    'type' => 'textarea', 'default' => "Pasar Modern BSD, Blok C-12\nJl. Letnan Sutopo No. 12, Serpong\nTangerang Selatan 15321"],
            ['name' => 'hours',        'title' => 'Jam operasional',   'type' => 'textarea', 'default' => "Senin – Sabtu · 07.00 – 21.00\nMinggu · 08.00 – 20.00"],
            ['name' => 'phone',        'title' => 'Nomor telepon',     'type' => 'text',     'default' => '+62 21 555 1234'],
            ['name' => 'whatsapp_number', 'title' => 'Nomor WhatsApp (format 62xxx)', 'type' => 'text', 'default' => '',
                'info' => 'Dipakai untuk tombol "Pesan via WhatsApp" di halaman produk & FAQ. Contoh: 6281234567890'],
            ['name' => 'email',        'title' => 'Email',             'type' => 'text',     'default' => 'halo@ecommerce.beres.io'],
            ['name' => 'map_query',    'title' => 'Query maps (search)', 'type' => 'text',     'default' => 'Pasar Modern BSD, Serpong, Tangerang',
                'channel_based' => false],
            ['name' => 'google_review_url', 'title' => 'Google Review URL', 'type' => 'text', 'default' => 'https://www.google.com/search?hl=id-ID&gl=id&q=Ankesh+Online+Store&ludocid=15094669346839031336#lrd=0x391905e546039a5b:0xd17b09cbc4516e28,3',
                'channel_based' => false],
            ['name' => 'google_place_id',   'title' => 'Google Place ID',   'type' => 'text',  'default' => '',
                'channel_based' => false],
            ['name' => 'google_api_key',    'title' => 'Google Places API Key', 'type' => 'password', 'default' => '',
                'channel_based' => false],
        ],
    ],

    /* =========================
     | HEADER CATEGORY NAV
     ========================= */
    [
        'key' => 'beres_storefront.header_nav',
        'name' => 'Header Category Nav',
        'info' => '7 kategori di baris atas header. Format tiap baris: "Label|slug-atau-url". Baris pertama biasanya beranda ("Unggulan|/"). URL bisa slug kategori (buah-sayur) atau path absolut (/blog).',
        'sort' => 6,
        'fields' => [
            ['name' => 'items', 'title' => 'Menu (satu per baris)', 'type' => 'textarea', 'channel_based' => false,
                'default' => "Unggulan|/\nFruits & Vegetables|fruits-vegetables\nMeat & Seafood|meat-seafood\nBread & Bakery|bread-bakery\nDrink|drink\nSpices & Herbs|spices-herbs\nHealthy Snacks|healthy-snacks",
                'info' => 'Format: "Label|slug". Slug = kategori Bagisto, atau path (/blog). Baris kosong diabaikan.'],
        ],
    ],

    /* =========================
     | BOTTOM NAV LABELS (mobile)
     ========================= */
    [
        'key' => 'beres_storefront.bottom_nav',
        'name' => 'Bottom Navigation',
        'info' => 'Label icon di navigasi bawah mobile.',
        'sort' => 6,
        'fields' => [
            ['name' => 'label_shop',    'title' => 'Shop',    'type' => 'text', 'default' => 'Shop'],
            ['name' => 'label_cart',    'title' => 'Cart',    'type' => 'text', 'default' => 'Cart'],
            ['name' => 'label_account', 'title' => 'Account', 'type' => 'text', 'default' => 'Account'],
            ['name' => 'label_search',  'title' => 'Search',  'type' => 'text', 'default' => 'Search'],
        ],
    ],

    /* =========================
     | MIDTRANS PAYMENT
     ========================= */
    [
        'key' => 'beres_storefront.midtrans',
        'name' => 'Midtrans Payment',
        'info' => 'Konfigurasi payment gateway Midtrans. Dapatkan keys dari https://dashboard.sandbox.midtrans.com/',
        'sort' => 10,
        'fields' => [
            ['name' => 'active',        'title' => 'Aktifkan Midtrans',  'type' => 'boolean', 'default' => '0',
                'channel_based' => false],
            ['name' => 'server_key',    'title' => 'Server Key',        'type' => 'password', 'default' => '',
                'info' => 'Server Key dari Midtrans Dashboard → Settings → Access Keys', 'channel_based' => false],
            ['name' => 'client_key',    'title' => 'Client Key',        'type' => 'text',     'default' => '',
                'info' => 'Client Key dari Midtrans Dashboard → Settings → Access Keys', 'channel_based' => false],
            ['name' => 'merchant_id',   'title' => 'Merchant ID',       'type' => 'text',     'default' => '',
                'channel_based' => false],
            ['name' => 'environment',   'title' => 'Environment',       'type' => 'select',   'default' => 'sandbox',
                'options' => [
                    ['title' => 'Sandbox (Testing)', 'value' => 'sandbox'],
                    ['title' => 'Production',        'value' => 'production'],
                ],
                'channel_based' => false],
            ['name' => 'payment_types', 'title' => 'Tipe Pembayaran',   'type' => 'textarea', 'default' => 'credit_card,bca_va,bni_va,bri_va,mandiri_va,gopay,shopeepay,qris,indomaret,alfamart',
                'info' => 'Comma-separated list. Pilihan: credit_card,bca_va,bni_va,bri_va,mandiri_va,gopay,shopeepay,qris,indomaret,alfamart',
                'channel_based' => false],
        ],
    ],

    /* =========================
     | SHIPPING — RajaOngkir
     ========================= */
    [
        'key' => 'beres_storefront.shipping',
        'name' => 'Pengiriman (RajaOngkir)',
        'info' => 'Konfigurasi ongkir via RajaOngkir API. Daftar di https://rajaongkir.com/akun/daftar',
        'sort' => 11,
        'fields' => [
            ['name' => 'active',        'title' => 'Aktifkan RajaOngkir', 'type' => 'boolean', 'default' => '0',
                'channel_based' => false],
            ['name' => 'api_key',       'title' => 'API Key RajaOngkir',  'type' => 'password', 'default' => '',
                'info' => 'API Key dari dashboard RajaOngkir. Starter = gratis 10.000 request/bulan.', 'channel_based' => false],
            ['name' => 'api_type',      'title' => 'Tipe API',            'type' => 'select',   'default' => 'starter',
                'options' => [
                    ['title' => 'Starter (Gratis)', 'value' => 'starter'],
                    ['title' => 'Basic (Premium)',  'value' => 'basic'],
                    ['title' => 'Pro (Enterprise)',  'value' => 'pro'],
                ],
                'channel_based' => false],
            ['name' => 'origin_city',   'title' => 'ID Kota Asal',       'type' => 'text',     'default' => '152',
                'info' => 'ID kota asal pengiriman di RajaOngkir. 152 = Jakarta Pusat. Cek di https://rajaongkir.com/dokumentasi/kota',
                'channel_based' => false],
            ['name' => 'couriers',      'title' => 'Kurir Tersedia',     'type' => 'text',     'default' => 'jne,jnt,sicepat',
                'info' => 'Comma-separated. Pilihan: jne,jnt,sicepat,anteraja,ninja,tiki,pos',
                'channel_based' => false],
        ],
    ],

    /* =========================
     | EMAIL — Resend
     ========================= */
    [
        'key' => 'beres_storefront.email',
        'name' => 'Email (Resend)',
        'info' => 'Konfigurasi email transaksi via Resend API. Daftar di https://resend.com/',
        'sort' => 12,
        'fields' => [
            ['name' => 'api_key',       'title' => 'Resend API Key',     'type' => 'password', 'default' => '',
                'info' => 'API Key dari Resend Dashboard → API Keys. re_xxxxx', 'channel_based' => false],
            ['name' => 'from_email',    'title' => 'Email Pengirim',     'type' => 'text',     'default' => 'noreply@ankeshmart.com',
                'info' => 'Harus verified di Resend Dashboard → Domains', 'channel_based' => false],
            ['name' => 'from_name',     'title' => 'Nama Pengirim',      'type' => 'text',     'default' => 'Ankesh Mart',
                'channel_based' => false],
        ],
    ],

    /* =========================
      | SEO & NAMA TAB BROWSER
      ========================= */
    [
        'key' => 'beres_storefront.seo',
        'name' => 'SEO & Nama Tab Browser',
        'info' => 'Nama tab browser (title), nama situs, dan suffix title semua halaman.',
        'sort' => 13,
        'fields' => [
            ['name' => 'site_name',    'title' => 'Nama Situs',           'type' => 'text', 'default' => '',
                'info' => 'Dipakai di nama tab, footer copyright & meta og:site_name. Kosong = pakai nama channel / APP_NAME.',
                'channel_based' => false],
            ['name' => 'home_title',   'title' => 'Nama Tab Homepage',    'type' => 'text', 'default' => '',
                'info' => 'Title tab browser halaman utama. Kosong = pakai Home SEO title di Settings → Channels.',
                'channel_based' => false],
            ['name' => 'title_suffix', 'title' => 'Suffix Title (semua halaman)', 'type' => 'text', 'default' => '— Ankesh Mart',
                'info' => 'Otomatis ditambahkan ke nama tab semua halaman (mis. "— Ankesh Mart"). Kosongkan untuk menonaktifkan.',
                'channel_based' => false],
        ],
    ],
];
