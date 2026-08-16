@extends('layouts.admin')

@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')

@section('admin_content')
<div class="page-header">
    <h1>Pengaturan Toko</h1>
</div>

<form method="POST" action="{{ route('admin.settings.store.update') }}" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Banner Hero Beranda --}}
        <div class="admin-panel-card lg:col-span-2">
            <h3 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">
                <i class="fas fa-image text-purple-600"></i> Banner Hero Beranda (Halaman Depan Toko)
            </h3>
            <p class="text-xs text-gray-500 mb-4">
                Upload atau ganti foto banner produk yang tampil di bagian paling atas halaman utama toko.
            </p>

            <div class="flex flex-col md:flex-row items-start md:items-center gap-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="w-full md:w-1/2">
                    <label class="form-label text-xs font-semibold text-gray-700 mb-2 block">Upload Gambar Banner Baru</label>
                    <input type="file" name="hero_banner" accept="image/*" class="form-input text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-2">Format didukung: JPG, PNG, WEBP. Rekomendasi resolusi landscape.</p>
                </div>
                <div class="w-full md:w-1/2">
                    <label class="form-label text-xs font-semibold text-gray-700 mb-2 block">Preview Banner Saat Ini</label>
                    <div class="rounded-lg overflow-hidden border border-gray-300 bg-white shadow-xs">
                        <img src="{{ \App\Models\SiteSetting::getValue('hero_banner_image') ?: '/images/hero-products.jpg' }}" alt="Current Hero Banner" class="w-full h-36 object-contain bg-gray-100">
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-store mr-2 text-blue-500"></i>Informasi Toko</h3>
            <p class="text-xs text-gray-500 mb-4">
                Nomor telepon, WhatsApp, email & alamat di sini otomatis tersinkron ke <strong>semua bagian website</strong>: section lokasi & peta (front page), footer, FAQ, dan tombol "Pesan via WhatsApp" di halaman produk.
            </p>

            <div class="mb-4">
                <label class="form-label">Nomor WhatsApp</label>
                <input type="text" name="store_whatsapp" value="{{ old('store_whatsapp', $settings['store_whatsapp'] ?? '') }}" class="form-input" placeholder="6281234567890">
            </div>

            <div class="mb-4">
                <label class="form-label">Link Google Maps Embed</label>
                <textarea name="store_maps_embed" rows="3" class="form-input" placeholder="<iframe src=...">{{ old('store_maps_embed', $settings['store_maps_embed'] ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">Negara</label>
                    <input type="text" name="store_country" value="{{ old('store_country', $settings['store_country'] ?? 'Indonesia') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email'] ?? '') }}" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">Telepon</label>
                    <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone'] ?? '') }}" class="form-input">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Alamat</label>
                <textarea name="store_address" rows="3" class="form-input">{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-shopping-bag mr-2 text-orange-500"></i>Link Marketplace</h3>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fab fa-shopify text-orange-500 mr-1"></i> Shopee
                </label>
                <input type="url" name="store_shopee" value="{{ old('store_shopee', $settings['store_shopee'] ?? '') }}" class="form-input" placeholder="https://shopee.co.id/...">
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fas fa-store text-green-500 mr-1"></i> Tokopedia
                </label>
                <input type="url" name="store_tokopedia" value="{{ old('store_tokopedia', $settings['store_tokopedia'] ?? '') }}" class="form-input" placeholder="https://tokopedia.com/...">
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fas fa-shopping-cart text-blue-500 mr-1"></i> Lazada
                </label>
                <input type="url" name="store_lazada" value="{{ old('store_lazada', $settings['store_lazada'] ?? '') }}" class="form-input" placeholder="https://www.lazada.co.id/...">
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fab fa-tiktok text-gray-800 mr-1"></i> TikTok Shop
                </label>
                <input type="url" name="store_tiktok" value="{{ old('store_tiktok', $settings['store_tiktok'] ?? '') }}" class="form-input" placeholder="https://www.tiktok.com/@...">
            </div>
        </div>

        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-share-alt mr-2 text-indigo-500"></i>Media Sosial (Footer Toko)</h3>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fab fa-instagram text-pink-500 mr-1"></i> Instagram URL
                </label>
                <input type="url" name="store_instagram" value="{{ old('store_instagram', $settings['store_instagram'] ?? '') }}" class="form-input" placeholder="https://instagram.com/ankeshmart">
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook URL
                </label>
                <input type="url" name="store_facebook" value="{{ old('store_facebook', $settings['store_facebook'] ?? '') }}" class="form-input" placeholder="https://facebook.com/ankeshmart">
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fab fa-youtube text-red-600 mr-1"></i> YouTube URL
                </label>
                <input type="url" name="store_youtube" value="{{ old('store_youtube', $settings['store_youtube'] ?? '') }}" class="form-input" placeholder="https://youtube.com/@ankeshmart">
            </div>
        </div>

        {{-- SEO & Browser Tab Title Settings --}}
        <div class="admin-panel-card lg:col-span-2">
            <h3 class="font-semibold text-gray-900 mb-1">
                <i class="fas fa-window-maximize mr-2 text-purple-600"></i>SEO & Nama Tab Browser
            </h3>
            <p class="text-xs text-gray-500 mb-5">
                Atur nama tab browser (title) website. Perubahan langsung berlaku di semua halaman.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Nama Situs</label>
                    <input type="text" name="seo_site_name" value="{{ old('seo_site_name', $settings['seo_site_name'] ?? '') }}" class="form-input" placeholder="Ankesh Mart">
                    <p class="text-xs text-gray-400 mt-1">Dipakai di footer, credit & meta social. Kosong = {{ config('app.name') }}.</p>
                </div>
                <div>
                    <label class="form-label">Nama Tab Homepage</label>
                    <input type="text" name="seo_home_title" value="{{ old('seo_home_title', $settings['seo_home_title'] ?? '') }}" class="form-input" placeholder="Ankesh Mart — Belanja bahan segar">
                    <p class="text-xs text-gray-400 mt-1">Title tab halaman utama. Kosong = default SEO channel.</p>
                </div>
                <div>
                    <label class="form-label">Suffix Title Semua Halaman</label>
                    <input type="text" name="seo_title_suffix" value="{{ old('seo_title_suffix', $settings['seo_title_suffix'] ?? '— Ankesh Mart') }}" class="form-input" placeholder="— Ankesh Mart">
                    <p class="text-xs text-gray-400 mt-1">Contoh "— Ankesh Mart". Otomatis ditambahkan di akhir nama tab tiap halaman. Kosongkan untuk menonaktifkan.</p>
                </div>
            </div>
        </div>

        {{-- Section Titles Homepage --}}
        @php
            $sectionKeys = [
                'new_eyebrow' => ['label' => 'New Arrivals — Eyebrow', 'placeholder' => 'Baru datang'],
                'new_title' => ['label' => 'New Arrivals — Judul', 'placeholder' => 'Produk terbaru.'],
                'bundle_eyebrow' => ['label' => 'Kits & Bundles — Eyebrow', 'placeholder' => 'Bundle hemat'],
                'bundle_title' => ['label' => 'Kits & Bundles — Judul', 'placeholder' => 'Paket & bundle pilihan.'],
                'cat_eyebrow' => ['label' => 'Kategori — Eyebrow', 'placeholder' => 'Belanja per kategori'],
                'cat_title' => ['label' => 'Kategori — Judul', 'placeholder' => 'Semua kebutuhan dapur.'],
                'best_eyebrow' => ['label' => 'Best Sellers — Eyebrow', 'placeholder' => 'Favorit pelanggan'],
                'best_title' => ['label' => 'Best Sellers — Judul', 'placeholder' => 'Best seller.'],
                'seed_eyebrow' => ['label' => 'Seeds & Superfoods — Eyebrow', 'placeholder' => 'Pilihan terbaik'],
                'seed_title' => ['label' => 'Seeds & Superfoods — Judul', 'placeholder' => 'Biji & Superfood Kami.'],
                'review_eyebrow' => ['label' => 'Reviews — Eyebrow', 'placeholder' => '4.8 dari 2.400+ ulasan'],
                'review_title' => ['label' => 'Reviews — Judul', 'placeholder' => 'Ulasan pelanggan'],
                'faq_title' => ['label' => 'FAQ — Judul', 'placeholder' => 'Pertanyaan umum.'],
                'google_review_eyebrow' => ['label' => 'Google Review — Eyebrow', 'placeholder' => ''],
                'google_review_title' => ['label' => 'Google Review — Judul', 'placeholder' => 'Ulasan Google Pelanggan'],
                'blog_eyebrow' => ['label' => 'Blog — Eyebrow', 'placeholder' => 'Artikel & Wawasan'],
                'blog_title' => ['label' => 'Blog — Judul', 'placeholder' => 'Artikel & Tips Terbaru'],
            ];
        @endphp
        <div class="admin-panel-card lg:col-span-2">
            <h3 class="font-semibold text-gray-900 mb-1">
                <i class="fas fa-heading mr-2 text-green-600"></i>Judul Section Homepage
            </h3>
            <p class="text-xs text-gray-500 mb-5">
                Atur teks eyebrow (label kecil) dan judul utama setiap section di halaman utama toko.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($sectionKeys as $key => $meta)
                    <div class="p-3 bg-gray-50/80 border border-gray-200 rounded-xl">
                        <label class="form-label text-xs font-semibold text-gray-700">{{ $meta['label'] }}</label>
                        <input type="text" name="section_{{ $key }}" value="{{ old('section_'.$key, core()->getConfigData('beres_storefront.sections.'.$key) ?? '') }}" placeholder="{{ $meta['placeholder'] }}" class="form-input text-sm bg-white">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Natural Banner --}}
        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-1">
                <i class="fas fa-leaf mr-2 text-green-700"></i>Banner Natural
            </h3>
            <p class="text-xs text-gray-500 mb-4">Banner hijau "100% NATURAL · LAB CERTIFIED".</p>

            <div class="mb-3">
                <label class="form-label">Text Kiri</label>
                <input type="text" name="natural_text1" value="{{ old('natural_text1', core()->getConfigData('beres_storefront.natural_banner.text1') ?? '100% NATURAL') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Text Kanan</label>
                <input type="text" name="natural_text2" value="{{ old('natural_text2', core()->getConfigData('beres_storefront.natural_banner.text2') ?? 'LAB CERTIFIED') }}" class="form-input">
            </div>
        </div>

        {{-- Newsletter --}}
        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-1">
                <i class="fas fa-envelope mr-2 text-blue-500"></i>Newsletter
            </h3>
            <p class="text-xs text-gray-500 mb-4">Section signup email di dekat footer.</p>

            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="newsletter_title" value="{{ old('newsletter_title', core()->getConfigData('beres_storefront.newsletter.title') ?? 'Resep, tips, dan promo mingguan.') }}" class="form-input">
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="newsletter_desc" rows="2" class="form-input">{{ old('newsletter_desc', core()->getConfigData('beres_storefront.newsletter.description') ?? '') }}</textarea>
            </div>
            <div>
                <label class="form-label">Tombol</label>
                <input type="text" name="newsletter_button" value="{{ old('newsletter_button', core()->getConfigData('beres_storefront.newsletter.button') ?? 'Daftar') }}" class="form-input">
            </div>
        </div>

        {{-- Header Navigation Menu Settings --}}
        <div class="admin-panel-card lg:col-span-2">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i class="fas fa-bars mr-2 text-green-700"></i>Menu Navigasi Header (Kategori Atas)
            </h3>
            <p class="text-xs text-gray-500 mb-3">
                Atur daftar menu/kategori yang tampil di bar navigasi atas website. Tulis <strong>1 menu per baris</strong> dengan format: <code>Nama Menu|Link_Atau_Slug</code>. <br>
                <em>(Jika dikosongkan, website akan otomatis menampilkan menu "Unggulan" dan seluruh kategori aktif dari menu Kategori).</em>
            </p>
            <div class="mb-2">
                <textarea name="header_nav_items" rows="5" class="form-input font-mono text-sm" placeholder="Unggulan|/&#10;Buah & Sayur|/category/buah-sayur&#10;Minuman|/category/minuman">{{ old('header_nav_items', $settings['header_nav_items'] ?? '') }}</textarea>
            </div>
            <p class="text-xs text-gray-400">
                Contoh: <code>Unggulan|/</code> &bull; <code>Buah & Sayur|/category/buah-sayur</code> &bull; <code>Promo|/search?query=promo</code>
            </p>
        </div>

        {{-- Footer Link Columns Settings --}}
        @php
            $col1Raw = $settings['footer_col1_links'] ?? '';
            $col1Rows = [];
            if (!empty(trim($col1Raw))) {
                foreach (preg_split("/\r?\n/", $col1Raw) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    [$t, $u] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
                    if ($t !== '') {
                        $col1Rows[] = ['title' => $t, 'url' => $u];
                    }
                }
            }
            if (empty($col1Rows)) {
                $col1Rows = [
                    ['title' => 'Contact Us', 'url' => '/contact-us'],
                    ['title' => 'Customer Service', 'url' => '/customer-service'],
                    ['title' => "What's New", 'url' => '/whats-new'],
                    ['title' => 'Terms of Use', 'url' => '/terms'],
                    ['title' => 'Terms & Conditions', 'url' => '/page/terms-conditions'],
                ];
            }

            $col2Raw = $settings['footer_col2_links'] ?? '';
            $col2Rows = [];
            if (!empty(trim($col2Raw))) {
                foreach (preg_split("/\r?\n/", $col2Raw) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    [$t, $u] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
                    if ($t !== '') {
                        $col2Rows[] = ['title' => $t, 'url' => $u];
                    }
                }
            }
            if (empty($col2Rows)) {
                $col2Rows = [
                    ['title' => 'Payment Policy', 'url' => '/page/payment-policy'],
                    ['title' => 'Shipping Policy', 'url' => '/page/shipping-policy'],
                    ['title' => 'Refund Policy', 'url' => '/page/refund-policy'],
                    ['title' => 'Return Policy', 'url' => '/page/return-policy'],
                    ['title' => 'FAQ', 'url' => '/faq'],
                ];
            }
        @endphp
        <div class="admin-panel-card lg:col-span-2">
            <h3 class="font-semibold text-gray-900 mb-1">
                <i class="fas fa-list-ul mr-2 text-blue-600"></i>Kelola Menu & Link Footer (Bentuk List Baris)
            </h3>
            <p class="text-xs text-gray-500 mb-5">
                Tambahkan baris menu atau hapus sesuai kebutuhan. Sangat mudah, cukup isi <strong>Nama Menu</strong> dan <strong>Link URL</strong>.
            </p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Footer Column 1 --}}
                <div class="p-4 bg-gray-50/80 border border-gray-200 rounded-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-sm text-gray-900 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span> Kolom 1 (Kiri)
                            </h4>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-xs font-semibold text-gray-700">Judul Kolom</label>
                            <input type="text" name="footer_col1_title" value="{{ old('footer_col1_title', $settings['footer_col1_title'] ?? 'About Us') }}" placeholder="About Us" class="form-input text-sm bg-white">
                        </div>

                        <label class="form-label text-xs font-semibold text-gray-700 mb-2 block">Daftar Baris Link</label>
                        <div class="space-y-2" id="col1-container">
                            @foreach($col1Rows as $row)
                                <div class="flex items-center gap-2 link-row bg-white p-2 border border-gray-200 rounded-lg shadow-xs">
                                    <input type="text" name="footer_col1_titles[]" value="{{ $row['title'] }}" placeholder="Nama Menu (misal: Hubungi Kami)" class="form-input text-xs flex-1">
                                    <input type="text" name="footer_col1_urls[]" value="{{ $row['url'] }}" placeholder="URL (misal: /contact-us)" class="form-input text-xs flex-1">
                                    <button type="button" onclick="this.closest('.link-row').remove()" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Baris">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" onclick="addFooterLinkRow('col1-container', 'footer_col1_titles[]', 'footer_col1_urls[]')" class="mt-4 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors w-full">
                        <i class="fas fa-plus text-xs"></i> + Tambah Baris Link Baru
                    </button>
                </div>

                {{-- Footer Column 2 --}}
                <div class="p-4 bg-gray-50/80 border border-gray-200 rounded-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-sm text-gray-900 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Kolom 2 (Kanan)
                            </h4>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-xs font-semibold text-gray-700">Judul Kolom</label>
                            <input type="text" name="footer_col2_title" value="{{ old('footer_col2_title', $settings['footer_col2_title'] ?? 'Privacy Policy') }}" placeholder="Privacy Policy" class="form-input text-sm bg-white">
                        </div>

                        <label class="form-label text-xs font-semibold text-gray-700 mb-2 block">Daftar Baris Link</label>
                        <div class="space-y-2" id="col2-container">
                            @foreach($col2Rows as $row)
                                <div class="flex items-center gap-2 link-row bg-white p-2 border border-gray-200 rounded-lg shadow-xs">
                                    <input type="text" name="footer_col2_titles[]" value="{{ $row['title'] }}" placeholder="Nama Menu (misal: Syarat & Ketentuan)" class="form-input text-xs flex-1">
                                    <input type="text" name="footer_col2_urls[]" value="{{ $row['url'] }}" placeholder="URL (misal: /page/terms)" class="form-input text-xs flex-1">
                                    <button type="button" onclick="this.closest('.link-row').remove()" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Baris">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" onclick="addFooterLinkRow('col2-container', 'footer_col2_titles[]', 'footer_col2_urls[]')" class="mt-4 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors w-full">
                        <i class="fas fa-plus text-xs"></i> + Tambah Baris Link Baru
                    </button>
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-gray-100">
                <label class="form-label text-xs font-semibold text-gray-700">Teks Deskripsi Newsletter (Footer Kiri Bawah Logo)</label>
                <input type="text" name="footer_newsletter_text" value="{{ old('footer_newsletter_text', $settings['footer_newsletter_text'] ?? '') }}" placeholder="Jadilah yang pertama mendengar tentang produk baru, acara eksklusif, dan penawaran online." class="form-input text-sm">
            </div>
        </div>

        {{-- Layanan & Trust Badges (Editable Feature Icons) --}}
        @php
            $featuresRaw = $settings['service_features'] ?? null;
            $featuresList = [];
            if ($featuresRaw) {
                $featuresList = json_decode($featuresRaw, true) ?: [];
            }
            if (empty($featuresList)) {
                $featuresList = [
                    ['icon' => 'fas fa-truck-fast', 'title' => 'Gratis Ongkir', 'description' => 'Bebas ongkir untuk pesanan tertentu'],
                    ['icon' => 'fas fa-rotate-left', 'title' => 'Garansi Produk', 'description' => 'Jaminan ganti baru bila rusak'],
                    ['icon' => 'fas fa-shield-halved', 'title' => 'Pembayaran Aman', 'description' => 'Transaksi aman & terenkripsi'],
                    ['icon' => 'fas fa-headset', 'title' => 'Layanan 24/7', 'description' => 'Dukungan ramah via chat & CS'],
                ];
            }
        @endphp
        <div class="admin-panel-card lg:col-span-2">
            <h3 class="font-semibold text-gray-900 mb-1">
                <i class="fas fa-shield-alt mr-2 text-emerald-600"></i>Layanan & Jaminan Toko (Trust Badges / Service Highlights)
            </h3>
            <p class="text-xs text-gray-500 mb-5">
                Atur 4 jaminan/layanan yang muncul di bagian bawah website. Anda dapat memilih icon, judul, dan deskripsi sesuai keinginan toko Anda.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="features-container">
                @foreach($featuresList as $fIdx => $fItem)
                    <div class="p-4 bg-gray-50/80 border border-gray-200 rounded-xl space-y-3 feature-card">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Layanan {{ $fIdx + 1 }}</span>
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shadow-xs feature-icon-preview">
                                <i class="{{ $fItem['icon'] ?? 'fas fa-shield-alt' }}"></i>
                            </div>
                        </div>

                        <div>
                            <label class="form-label text-xs font-semibold text-gray-700">Pilih / Ketik Ikon</label>
                            <select class="form-input text-xs mb-1" onchange="applyFeatureIcon(this)">
                                <option value="fas fa-truck-fast" {{ ($fItem['icon'] ?? '') === 'fas fa-truck-fast' ? 'selected' : '' }}>🚚 Truk / Pengiriman</option>
                                <option value="fas fa-rotate-left" {{ ($fItem['icon'] ?? '') === 'fas fa-rotate-left' ? 'selected' : '' }}>🔄 Pengembalian / Garansi</option>
                                <option value="fas fa-shield-halved" {{ ($fItem['icon'] ?? '') === 'fas fa-shield-halved' ? 'selected' : '' }}>🛡️ Keamanan / Proteksi</option>
                                <option value="fas fa-headset" {{ ($fItem['icon'] ?? '') === 'fas fa-headset' ? 'selected' : '' }}>🎧 CS / Support 24/7</option>
                                <option value="fas fa-credit-card" {{ ($fItem['icon'] ?? '') === 'fas fa-credit-card' ? 'selected' : '' }}>💳 Pembayaran / Cicilan</option>
                                <option value="fas fa-leaf" {{ ($fItem['icon'] ?? '') === 'fas fa-leaf' ? 'selected' : '' }}>🌿 100% Organik / Alami</option>
                                <option value="fas fa-award" {{ ($fItem['icon'] ?? '') === 'fas fa-award' ? 'selected' : '' }}>🏆 Kualitas Teruji</option>
                                <option value="fas fa-box-open" {{ ($fItem['icon'] ?? '') === 'fas fa-box-open' ? 'selected' : '' }}>📦 Kemasan Rapi</option>
                                <option value="fas fa-clock" {{ ($fItem['icon'] ?? '') === 'fas fa-clock' ? 'selected' : '' }}>⏰ Respon Cepat</option>
                                <option value="custom">Ketik Ikon Kustom Lainnya...</option>
                            </select>
                            <input type="text" name="feature_icons[]" value="{{ $fItem['icon'] ?? 'fas fa-shield-alt' }}" placeholder="fas fa-truck-fast" class="form-input text-xs feature-icon-input" oninput="updateFeatureIconInput(this)">
                        </div>

                        <div>
                            <label class="form-label text-xs font-semibold text-gray-700">Judul Layanan</label>
                            <input type="text" name="feature_titles[]" value="{{ $fItem['title'] ?? '' }}" placeholder="Gratis Ongkir" class="form-input text-xs bg-white font-medium">
                        </div>

                        <div>
                            <label class="form-label text-xs font-semibold text-gray-700">Deskripsi Singkat</label>
                            <textarea name="feature_descs[]" rows="2" placeholder="Bebas ongkir untuk pesanan tertentu" class="form-input text-xs bg-white">{{ $fItem['description'] ?? '' }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan Pengaturan
        </button>
    </div>
</form>
@endsection

@section('scripts')
function applyFeatureIcon(select) {
    const card = select.closest('.feature-card');
    const input = card.querySelector('.feature-icon-input');
    const preview = card.querySelector('.feature-icon-preview i');
    if (select.value !== 'custom') {
        input.value = select.value;
        preview.className = select.value;
    } else {
        input.focus();
    }
}

function updateFeatureIconInput(input) {
    const card = input.closest('.feature-card');
    const preview = card.querySelector('.feature-icon-preview i');
    preview.className = input.value || 'fas fa-shield-alt';
}

function addFooterLinkRow(containerId, titleName, urlName) {
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 link-row bg-white p-2 border border-gray-200 rounded-lg shadow-xs';
    div.innerHTML = `
        <input type="text" name="${titleName}" placeholder="Nama Menu" class="form-input text-xs flex-1">
        <input type="text" name="${urlName}" placeholder="URL (misal: /link-tujuan)" class="form-input text-xs flex-1">
        <button type="button" onclick="this.closest('.link-row').remove()" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Baris">
            <i class="fas fa-trash-alt text-xs"></i>
        </button>
    `;
    container.appendChild(div);
}
@endsection
