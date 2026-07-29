@php
    $channel = core()->getCurrentChannel();

    // Lucide-style SVG icon paths (24x24 viewbox, stroke-currentColor)
    $svgIcons = [
        'leaf'      => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19.2 2.96a1 1 0 0 1 1.8.8 10 10 0 0 1-3.36 8.24A9.15 9.15 0 0 1 11 20z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/>',
        'fish'      => '<path d="M6.5 12c.94-3.46 4.94-6 8.5-6 3.56 0 6.06 2.54 7 6-.94 3.47-3.44 6-7 6s-7.56-2.53-8.5-6Z"/><path d="M18 12v.5"/><path d="M16 17.93a9.77 9.77 0 0 1 0-11.86"/><path d="M7 10.67C7 8 5.58 5.97 2.73 5.5c-1 1.5-1 5 .23 6.5-1.24 1.5-1.24 5-.23 6.5C5.58 18.03 7 16 7 13.33"/>',
        'croissant' => '<path d="m4.6 13.11 5.79-3.21c1.89-1.05 4.79 1.78 3.71 3.71l-3.22 5.81C8.8 23.16.79 15.23 4.6 13.11Z"/><path d="m10.5 9.5-1-2.29C9.2 6.48 8.8 6 8 6H4.5C2.79 6 2 6.5 2 8.5a7.71 7.71 0 0 0 2 4.83"/><path d="M8 6c0-1.55.24-4-2-4-2 0-2.5 2.17-2.5 4"/><path d="m14.5 13.5 2.29 1c.73.3 1.21.7 1.21 1.5v3.5c0 1.71-.5 2.5-2.5 2.5a7.71 7.71 0 0 1-4.83-2"/><path d="M18 16c1.55 0 4-.24 4 2 0 2-2.17 2.5-4 2.5"/>',
        'wine'      => '<path d="M8 22h8"/><path d="M7 10h10"/><path d="M12 15v7"/><path d="M12 15a5 5 0 0 0 5-5c0-2-.5-4-2-8H9c-1.5 4-2 6-2 8a5 5 0 0 0 5 5Z"/>',
        'flame'     => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>',
        'cookie'    => '<path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/><path d="M8.5 8.5v.01"/><path d="M16 15.5v.01"/><path d="M12 12v.01"/><path d="M11 17v.01"/><path d="M7 14v.01"/>',
        'coffee'    => '<path d="M10 2v2"/><path d="M14 2v2"/><path d="M16 8a1 1 0 0 1 1 1v8a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V9a1 1 0 0 1 1-1h14a4 4 0 1 1 0 8h-1"/><path d="M6 2v2"/>',
        'jar'       => '<path d="M18 8h1a4 4 0 1 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4Z"/><path d="M6 2v4"/><path d="M10 2v4"/><path d="M14 2v4"/>',
        'bottle'    => '<path d="M10 2v7.527a2 2 0 0 1-.211.896L7.211 15.632A2 2 0 0 0 7 16.527V20a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-3.473a2 2 0 0 0-.211-.896l-2.578-5.209A2 2 0 0 1 14 9.527V2"/><path d="M8 2h8"/><path d="M7.5 12h9"/>',
        'cheese'    => '<path d="M4.5 21 3 12l7.85-6.86a3 3 0 0 1 3.3-.51l7.86 3.87A3 3 0 0 1 23.5 12L22 21H4.5z"/><circle cx="8" cy="16" r="1"/><circle cx="13" cy="14" r="1"/><circle cx="16" cy="17" r="1"/>',
        'wheat'     => '<path d="M2 22 16 8"/><path d="M3.47 12.53 5 11l1.53 1.53a3.5 3.5 0 0 1 0 4.94L5 19l-1.53-1.53a3.5 3.5 0 0 1 0-4.94Z"/><path d="M7.47 8.53 9 7l1.53 1.53a3.5 3.5 0 0 1 0 4.94L9 15l-1.53-1.53a3.5 3.5 0 0 1 0-4.94Z"/><path d="M11.47 4.53 13 3l1.53 1.53a3.5 3.5 0 0 1 0 4.94L13 11l-1.53-1.53a3.5 3.5 0 0 1 0-4.94Z"/><path d="M20 2h2v2a4 4 0 0 1-4 4h-2V6a4 4 0 0 1 4-4Z"/><path d="M11.47 17.47 13 19l-1.53 1.53a3.5 3.5 0 0 1-4.94 0L5 19l1.53-1.53a3.5 3.5 0 0 1 4.94 0Z"/>',
        'cake'      => '<path d="M20 21v-8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8"/><path d="M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2.5 2 4 2 2-1 2-1"/><path d="M2 21h20"/><path d="M7 8v3"/><path d="M12 8v3"/><path d="M17 8v3"/>',
        'milk'      => '<path d="M8 2h8"/><path d="M9 2v2.789a4 4 0 0 1-.672 2.219l-.656.984A4 4 0 0 0 7 10.212V20a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-9.789a4 4 0 0 0-.672-2.219l-.656-.984A4 4 0 0 1 15 4.788V2"/><path d="M7 15a6.472 6.472 0 0 1 5 0 6.47 6.47 0 0 0 5 0"/>',
        'bagel'     => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/>',
        'salad'     => '<path d="M7 21h10"/><path d="M12 21a9 9 0 0 0 9-9H3a9 9 0 0 0 9 9Z"/><path d="M11.38 12a2.4 2.4 0 0 1-.4-4.77 2.4 2.4 0 0 1 3.2-2.77 2.4 2.4 0 0 1 3.47-.63 2.4 2.4 0 0 1 3.37 3.37 2.4 2.4 0 0 1-1.1 3.7 2.51 2.51 0 0 1 .03 1.1"/><path d="m13 12 4-4"/><path d="M10.9 7.25A3.99 3.99 0 0 0 4 10c0 .73.2 1.41.54 2"/>',
        'gift'      => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5c2 0 4.5 3 4.5 5-2 0-4.5-3-4.5-5z"/><path d="M16.5 8a2.5 2.5 0 0 0 0-5c-2 0-4.5 3-4.5 5 2 0 4.5-3 4.5-5z"/>',
        'utensils'  => '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>',
        'sprout'    => '<path d="M7 20h10"/><path d="M10 20c5.5-2.5.8-6.4 3-10"/><path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z"/><path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z"/>',
    ];

    // Category tiles: [label, bg tint, icon-key]
    $dummyCategories = [
        ['Buah & Sayur',      'E8F0E5', 'leaf'],
        ['Daging & Seafood',  'F4E4E1', 'fish'],
        ['Roti & Bakery',     'F5EBD9', 'croissant'],
        ['Minuman',           'E4EFF4', 'wine'],
        ['Bumbu & Rempah',    'F3EBD3', 'flame'],
        ['Snack Sehat',       'EDE9DE', 'cookie'],
    ];

    $newProducts = [
        ['Kopi Arabika Gayo 250g',        'Rp 128.000', 'Rp 165.000', 'E8DDCB', 'coffee'],
        ['Madu Hutan Organik 500ml',      'Rp 189.000', null,          'F3E4B7', 'jar'],
        ['Minyak Zaitun Extra Virgin',    'Rp 245.000', null,          'E5EBD8', 'bottle'],
        ['Keju Camembert Premium',        'Rp 189.000', 'Rp 220.000', 'F4EED9', 'cheese'],
    ];

    $bestSellers = [
        ['Beras Merah Organik 5kg',   'Rp 145.000', 'EEE5D3', 'wheat'],
        ['Coklat Dark 70% Kakao',     'Rp 89.000',  'E8D9C7', 'cake'],
        ['Yogurt Greek Style 500g',   'Rp 65.000',  'F3F0E4', 'milk'],
        ['Roti Sourdough Artisan',    'Rp 78.000',  'F0E7D1', 'bagel'],
    ];

    $reviews = [
        ['A', 'Ahmad Rizky',   'Sayurannya benar-benar segar seperti baru dipetik. Pengiriman tepat waktu di pagi hari, packaging rapi dengan ice pack untuk barang dingin.'],
        ['S', 'Siti Nurhaliza','Kualitas dagingnya konsisten, potongan bersih dan bebas bau. Saya berlangganan sudah 6 bulan dan belum pernah kecewa.'],
        ['B', 'Budi Santoso',  'Kopinya sangat harum dan segar, terasa baru di-roasting. Harga masih wajar untuk kualitas single origin seperti ini.'],
    ];

    $faqs = [
        ['Bagaimana produk segar dikirim?',        'Semua produk segar dikirim dengan cool box dan ice gel. Untuk area Jakarta, pesanan sebelum jam 10 pagi sampai di hari yang sama.'],
        ['Apakah bisa retur jika produk rusak?',   'Ya. Foto produk yang bermasalah dalam 24 jam setelah diterima, dan kami akan ganti atau refund penuh tanpa perlu mengembalikan barang.'],
        ['Dari mana sumber bahan-bahannya?',        'Kami bekerja sama langsung dengan 120+ petani dan produsen lokal di Jawa, Sumatra, dan Bali. Semua transparan di halaman produk.'],
        ['Metode pembayaran apa yang tersedia?',    'Transfer bank, QRIS, kartu kredit/debit, GoPay, OVO, ShopeePay, dan cicilan 0% hingga 12 bulan.'],
    ];

    $journals = [
        ['Resep',    'Pasta aglio e olio dengan minyak zaitun premium',   'Hidangan Italia klasik yang siap dalam 15 menit. Rahasianya ada di kualitas minyak zaitun dan bawang putih segar.',  'E8F0E5', 'utensils'],
        ['Sumber',   'Bertemu petani kopi kami di dataran tinggi Gayo',    'Perjalanan ke Aceh Tengah untuk melihat langsung proses panen dan sortir biji kopi arabika unggulan.',                'F3E4B7', 'coffee'],
        ['Panduan',  'Cara menyimpan sayuran agar tetap segar seminggu',   'Trik sederhana dari chef kami — mulai dari suhu kulkas ideal hingga wadah yang tepat untuk tiap jenis sayur.',       'EEE5D3', 'sprout'],
    ];
@endphp

@php
    $siteName    = $channel->name ?? config('app.name');
    $homeTitle   = $channel->home_seo['meta_title']       ?? "$siteName — Belanja bahan segar dan pantry esensial";
    $homeDesc    = $channel->home_seo['meta_description'] ?? 'Pasar online untuk bahan segar dan pantry esensial. Buah, sayur, daging, roti, minuman, bumbu, dan snack sehat langsung dari petani dan produsen lokal — diantar hari itu juga.';
    $homeKeys    = $channel->home_seo['meta_keywords']    ?? 'belanja bahan segar, pasar online, sayur online, buah online, daging online, roti, minuman, bumbu, snack sehat, kopi arabika gayo, madu organik, keju premium';
    $homeUrl     = route('shop.home.index');
    $homeOgImage = $channel->logo_url ?? null;
@endphp

@push ('meta')
    <meta name="title"       content="{{ $homeTitle }}" />
    <meta name="description" content="{{ $homeDesc }}" />
    <meta name="keywords"    content="{{ $homeKeys }}" />

    {{-- Explicit OG/Twitter for home (overrides layout defaults) --}}
    <meta property="og:title"        content="{{ $homeTitle }}" />
    <meta property="og:description"  content="{{ $homeDesc }}" />
    <meta property="og:url"          content="{{ $homeUrl }}" />
    @if ($homeOgImage)<meta property="og:image" content="{{ $homeOgImage }}" />@endif
    <meta property="og:type"         content="website" />
    <meta name="twitter:title"       content="{{ $homeTitle }}" />
    <meta name="twitter:description" content="{{ $homeDesc }}" />
    @if ($homeOgImage)<meta name="twitter:image" content="{{ $homeOgImage }}" />@endif

    {{-- WebSite + SearchAction (Sitelinks Search Box) --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        'name'            => $siteName,
        'url'             => url('/'),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => route('shop.search.index') . '?query={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- LocalBusiness (Contact section) --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context'  => 'https://schema.org',
        '@type'     => 'LocalBusiness',
        'name'      => $siteName,
        'image'     => $homeOgImage ?? url('/'),
        'telephone' => '+62-21-555-1234',
        'email'     => 'halo@ecommerce.id',
        'url'       => url('/'),
        'priceRange'=> 'Rp',
        'address'   => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Pasar Modern BSD, Blok C-12, Jl. Letnan Sutopo No. 12',
            'addressLocality' => 'Serpong',
            'addressRegion'   => 'Tangerang Selatan',
            'postalCode'      => '15321',
            'addressCountry'  => 'ID',
        ],
        'openingHoursSpecification' => [[
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
            'opens'     => '06:00',
            'closes'    => '21:00',
        ]],
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.9',
            'reviewCount' => '520',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- FAQPage --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(fn($f) => [
            '@type'          => 'Question',
            'name'           => $f[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
        ], [
            ['Bagaimana produk segar dikirim?',       'Semua produk segar dikirim dengan cool box dan ice gel. Untuk area Jakarta, pesanan sebelum jam 10 pagi sampai di hari yang sama.'],
            ['Apakah bisa retur jika produk rusak?',  'Ya. Foto produk yang bermasalah dalam 24 jam setelah diterima, dan kami akan ganti atau refund penuh tanpa perlu mengembalikan barang.'],
            ['Dari mana sumber bahan-bahannya?',      'Kami bekerja sama langsung dengan 120+ petani dan produsen lokal di Jawa, Sumatra, dan Bali.'],
            ['Metode pembayaran apa yang tersedia?',  'Transfer bank, QRIS, kartu kredit/debit, GoPay, OVO, ShopeePay, dan cicilan 0% hingga 12 bulan.'],
        ]),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endPush

@push('scripts')
    @if(! empty($categories))
        <script>
            localStorage.setItem('categories', JSON.stringify(@json($categories)));
        </script>
    @endif
@endpush

<x-shop::layouts>
    <x-slot:title>
        {{  $channel->home_seo['meta_title'] ?? '' }}
    </x-slot>

    {{-- HERO --}}
    <section class="bg-canvas">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-20 md:py-28 grid gap-12 lg:grid-cols-2 items-center">
            <div class="max-w-xl">
                <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone mb-5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">{!! $svgIcons['leaf'] !!}</svg>
                    Panen Hari Ini
                </p>
                <h1 class="text-5xl md:text-6xl lg:text-7xl leading-[1.02] text-ink" style="font-weight: 500; letter-spacing: -0.025em;">
                    Bahan segar untuk dapur Anda.
                </h1>
                <p class="mt-6 text-base md:text-lg text-cocoa/80 max-w-md">
                    Dari petani dan produsen lokal, langsung ke pintu rumah. Buah, sayur, daging, dan pantry esensial pilihan.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('shop.search.index') }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                        Belanja Sekarang
                    </a>
                    <a href="#" class="inline-flex items-center gap-2 text-[13px] tracking-[0.14em] uppercase text-ink border-b border-ink pb-0.5 hover:text-clay hover:border-clay transition-colors">
                        Lihat Katalog
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>

            <div class="relative aspect-[4/5] lg:aspect-[5/6] overflow-hidden" style="background-color:#E8F0E5;">
                <div class="absolute bottom-6 left-6 right-6 md:left-8 md:bottom-8 md:right-auto md:max-w-xs bg-cream/95 backdrop-blur p-5">
                    <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/></svg>
                        Terlaris
                    </p>
                    <p class="mt-2 text-xl leading-snug text-ink" style="font-weight: 500;">Bundle Sarapan Sehat — mulai Rp 149.000</p>
                    <a href="#" class="mt-3 inline-flex items-center gap-1 text-[13px] tracking-[0.14em] uppercase text-ink border-b border-ink pb-0.5">
                        Discover
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- VALUE PROPS --}}
    <section class="bg-cream border-b border-mist">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-12 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach ([
                ['Segar dari Petani', 'Langsung dari sumber', '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>'],
                ['Diantar Hari Ini', 'Pesan sebelum jam 10',  '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>'],
                ['Retur Mudah',       'Jaminan kualitas',       '<path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>'],
                ['Bersertifikat Halal','Diverifikasi MUI',      '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>'],
            ] as [$title, $sub, $svg])
                <div class="flex flex-col items-center gap-3">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full border border-mist bg-canvas text-ink">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">{!! $svg !!}</svg>
                    </span>
                    <div>
                        <p class="text-base text-ink" style="font-weight: 500;">{{ $title }}</p>
                        <p class="mt-0.5 text-xs text-stone">{{ $sub }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CATEGORY TILES --}}
    <section class="bg-cream">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone mb-3">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                        Kategori
                    </p>
                    <h2 class="text-4xl md:text-5xl text-ink" style="font-weight: 500; letter-spacing: -0.02em;">Belanja berdasarkan kategori.</h2>
                </div>
                <a href="{{ route('shop.search.index') }}" class="hidden md:inline-flex items-center gap-1.5 text-[13px] tracking-[0.14em] uppercase text-ink border-b border-ink pb-0.5 hover:text-clay hover:border-clay transition-colors">
                    Lihat semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>

            <div class="grid gap-4 md:gap-6 grid-cols-2 md:grid-cols-3 lg:grid-cols-6">
                @foreach ($dummyCategories as [$label, $bg, $iconKey])
                    <a href="#" class="group block">
                        <div class="relative aspect-square overflow-hidden transition-transform duration-500 group-hover:scale-[1.02]" style="background-color:#{{ $bg }};"></div>
                        <p class="mt-4 text-center text-[13px] tracking-[0.14em] uppercase text-ink group-hover:text-clay transition-colors">
                            {{ $label }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FEATURED PRODUCTS --}}
    <section class="bg-canvas">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-20">
            <div class="mb-10">
                <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone mb-3">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"/></svg>
                    Baru
                </p>
                <div class="flex items-end justify-between">
                    <h2 class="text-4xl md:text-5xl text-ink" style="font-weight: 500; letter-spacing: -0.02em;">Produk terbaru.</h2>
                    <a href="{{ route('shop.search.index', ['sort' => 'created_at', 'order' => 'desc']) }}"
                       class="hidden md:inline-flex items-center gap-1.5 text-[13px] tracking-[0.14em] uppercase text-ink border-b border-ink pb-0.5 hover:text-clay hover:border-clay transition-colors">
                        Lihat semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>

            <div class="grid gap-6 grid-cols-2 lg:grid-cols-4">
                @foreach ($newProducts as $i => [$name, $price, $compare, $bg, $iconKey])
                    <a href="#" class="group block">
                        <div class="relative aspect-[4/5] overflow-hidden transition-transform duration-500 group-hover:scale-[1.02]" style="background-color:#{{ $bg }};">
                            @if ($compare)
                                <span class="absolute top-3 left-3 bg-ink text-cream text-[10px] tracking-[0.14em] uppercase px-2.5 py-1">Sale</span>
                            @endif
                            <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="icon-heart w-9 h-9 rounded-full bg-cream/95 flex items-center justify-center text-lg text-ink" role="presentation"></span>
                                <span class="icon-eye w-9 h-9 rounded-full bg-cream/95 flex items-center justify-center text-lg text-ink" role="presentation"></span>
                            </div>
                        </div>
                        <p class="mt-4 text-[14px] text-ink group-hover:text-clay transition-colors">{{ $name }}</p>
                        <p class="mt-1 text-[14px] text-cocoa" style="font-weight: 500;">
                            {{ $price }}
                            @if ($compare)
                                <span class="ml-2 text-stone line-through" style="font-weight: 400;">{{ $compare }}</span>
                            @endif
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- EDITORIAL BAND --}}
    <section class="bg-ink text-cream">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-24 grid gap-12 lg:grid-cols-2 items-center">
            <div class="relative aspect-[4/3] overflow-hidden order-2 lg:order-1" style="background-color:#2A2A2A;"></div>
            <div class="max-w-lg order-1 lg:order-2 lg:pl-8">
                <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-sand mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/></svg>
                    Promo Spesial
                </p>
                <h2 class="text-4xl md:text-5xl leading-tight" style="font-weight: 500; letter-spacing: -0.02em;">
                    Diskon 30% untuk paket pertama Anda.
                </h2>
                <p class="mt-5 text-base text-mist max-w-md">
                    Coba layanan kami dengan paket sampler — kopi, madu, dan minyak zaitun premium — dengan diskon spesial untuk pesanan pertama.
                </p>
                <a href="{{ route('shop.search.index') }}"
                   class="mt-8 inline-flex items-center gap-2 text-[13px] tracking-[0.14em] uppercase text-cream border-b border-cream pb-0.5 hover:text-sand hover:border-sand transition-colors">
                    Ambil promo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- BEST SELLERS --}}
    <section class="bg-cream">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-20">
            <div class="mb-10">
                <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone mb-3">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/></svg>
                    Terlaris
                </p>
                <div class="flex items-end justify-between">
                    <h2 class="text-4xl md:text-5xl text-ink" style="font-weight: 500; letter-spacing: -0.02em;">Paling laris.</h2>
                    <a href="{{ route('shop.search.index', ['sort' => 'sales_count', 'order' => 'desc']) }}"
                       class="hidden md:inline-flex items-center gap-1.5 text-[13px] tracking-[0.14em] uppercase text-ink border-b border-ink pb-0.5 hover:text-clay hover:border-clay transition-colors">
                        Lihat semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>

            <div class="grid gap-6 grid-cols-2 lg:grid-cols-4">
                @foreach ($bestSellers as [$name, $price, $bg, $iconKey])
                    <a href="#" class="group block">
                        <div class="relative aspect-[4/5] overflow-hidden transition-transform duration-500 group-hover:scale-[1.02]" style="background-color:#{{ $bg }};">
                            <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="icon-heart w-9 h-9 rounded-full bg-cream/95 flex items-center justify-center text-lg text-ink" role="presentation"></span>
                                <span class="icon-eye w-9 h-9 rounded-full bg-cream/95 flex items-center justify-center text-lg text-ink" role="presentation"></span>
                            </div>
                        </div>
                        <p class="mt-4 text-[14px] text-ink group-hover:text-clay transition-colors">{{ $name }}</p>
                        <p class="mt-1 text-[14px] text-cocoa" style="font-weight: 500;">{{ $price }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- REVIEWS --}}
    <section class="bg-canvas">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-20">
            <div class="mb-10 text-center flex flex-col items-center">
                <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                    Ulasan Pelanggan
                </p>
                <h2 class="text-4xl md:text-5xl text-ink" style="font-weight: 500; letter-spacing: -0.02em;">Apa kata mereka.</h2>
                <p class="mt-3 inline-flex items-center gap-2 text-sm text-stone">
                    <span class="inline-flex gap-0.5 text-ink">
                        @for ($s = 0; $s < 5; $s++)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/></svg>
                        @endfor
                    </span>
                    4.9 dari 520+ ulasan Google
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($reviews as [$initial, $name, $text])
                    <div class="bg-cream p-8 border border-mist">
                        <div class="inline-flex gap-0.5 mb-4 text-ink">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/></svg>
                            @endfor
                        </div>
                        <p class="text-lg text-ink leading-relaxed" style="font-weight: 400;">“{{ $text }}”</p>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-canvas flex items-center justify-center text-sm text-cocoa" style="font-weight: 500;">{{ $initial }}</div>
                            <div>
                                <p class="text-sm text-ink flex items-center gap-1.5" style="font-weight: 500;">
                                    {{ $name }}
                                    <svg class="w-3.5 h-3.5 text-ink" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </p>
                                <p class="text-xs text-stone">Verified buyer · 2026</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="bg-cream">
        <div class="mx-auto max-w-3xl px-6 md:px-10 py-20">
            <div class="mb-10 text-center flex flex-col items-center">
                <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                    FAQ
                </p>
                <h2 class="text-4xl md:text-5xl text-ink" style="font-weight: 500; letter-spacing: -0.02em;">Pertanyaan umum.</h2>
            </div>

            <div class="divide-y divide-mist border-t border-b border-mist">
                @foreach ($faqs as [$q, $a])
                    <details class="group py-6">
                        <summary class="flex justify-between items-center cursor-pointer list-none">
                            <span class="text-xl text-ink" style="font-weight: 500;">{{ $q }}</span>
                            <svg class="w-5 h-5 text-stone transition-transform group-open:rotate-180 shrink-0 ml-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </summary>
                        <p class="mt-4 text-base text-cocoa/80 leading-relaxed">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- JOURNAL --}}
    <section class="bg-canvas">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-20">
            <div class="mb-10 flex items-end justify-between">
                <div>
                    <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone mb-3">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        Journal
                    </p>
                    <h2 class="text-4xl md:text-5xl text-ink" style="font-weight: 500; letter-spacing: -0.02em;">Cerita terbaru.</h2>
                </div>
                <a href="#" class="hidden md:inline-flex items-center gap-1.5 text-[13px] tracking-[0.14em] uppercase text-ink border-b border-ink pb-0.5 hover:text-clay hover:border-clay transition-colors">
                    Lihat semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($journals as $i => [$cat, $title, $excerpt, $bg, $iconKey])
                    <article class="group">
                        <a href="#" class="block">
                            <div class="relative aspect-[4/3] overflow-hidden mb-5 transition-transform duration-500 group-hover:scale-[1.02]" style="background-color:#{{ $bg }};"></div>
                            <p class="text-[11px] tracking-[0.14em] uppercase text-stone mb-2">{{ $cat }}</p>
                            <h3 class="text-2xl text-ink group-hover:text-clay transition-colors" style="font-weight: 500; letter-spacing: -0.015em;">{{ $title }}</h3>
                            <p class="mt-3 text-sm text-cocoa/80 leading-relaxed">{{ $excerpt }}</p>
                            <p class="mt-4 inline-flex items-center gap-1.5 text-[13px] tracking-[0.14em] uppercase text-ink border-b border-ink pb-0.5">
                                Baca selengkapnya
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </p>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CONTACT + LOCATION --}}
    <section class="bg-cream border-t border-mist">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-20 grid gap-12 lg:grid-cols-2 items-start">
            <div class="max-w-md">
                <p class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    Kunjungi Kami
                </p>
                <h2 class="text-4xl md:text-5xl text-ink" style="font-weight: 500; letter-spacing: -0.02em;">
                    Mampir ke pasar kami.
                </h2>
                <p class="mt-5 text-base text-cocoa/80">
                    Datang langsung untuk melihat produk segar hari itu, mencicipi sampel, dan berbincang dengan tim kami. Buka setiap hari.
                </p>

                <dl class="mt-10 divide-y divide-mist border-t border-b border-mist">
                    @foreach ([
                        ['<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>', 'Pasar Modern BSD, Blok C-12<br>Jl. Letnan Sutopo No. 12<br>Serpong, Tangerang Selatan 15321', null],
                        ['<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'Senin – Minggu<br>06:00 – 21:00 WIB', null],
                        ['<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>', '+62 21 555 1234', 'tel:+62215551234'],
                        ['<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>', 'halo@ecommerce.id', 'mailto:halo@ecommerce.id'],
                        ['<path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>', '+62 812 3456 7890', 'https://wa.me/6281234567890'],
                    ] as [$icon, $body, $href])
                        <div class="flex items-start gap-4 py-5">
                            <span class="inline-flex shrink-0 items-center justify-center w-9 h-9 rounded-full border border-mist text-ink">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">{!! $icon !!}</svg>
                            </span>
                            <dd class="text-sm text-ink leading-relaxed">
                                @if ($href)
                                    <a href="{{ $href }}" class="hover:text-clay transition-colors">{!! $body !!}</a>
                                @else
                                    {!! $body !!}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>

                <a href="https://www.google.com/maps/search/?api=1&query=Pasar+Modern+BSD"
                   target="_blank" rel="noopener"
                   class="mt-8 inline-flex items-center gap-2 text-[13px] tracking-[0.14em] uppercase text-ink border-b border-ink pb-0.5 hover:text-clay hover:border-clay transition-colors">
                    Buka di Google Maps
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            </div>

            <div class="relative w-full aspect-[4/5] lg:aspect-square bg-mist overflow-hidden">
                <iframe
                    src="https://maps.google.com/maps?q=Pasar%20Modern%20BSD%20Serpong&t=&z=16&ie=UTF8&iwloc=&output=embed"
                    class="absolute inset-0 w-full h-full border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Lokasi Pasar Modern BSD"
                    allowfullscreen
                ></iframe>
            </div>
        </div>
    </section>

    {{-- BRAND TICKER --}}
    <section class="bg-cream border-t border-mist">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-10 flex items-center justify-between flex-wrap gap-6">
            <span class="inline-flex items-center gap-2 text-[11px] tracking-[0.14em] uppercase text-stone">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
                Diliput oleh
            </span>
            <div class="flex items-center gap-10 flex-wrap opacity-70">
                @foreach (['Femina','Kompas Gaya Hidup','Tempo','Detik Food','Fimela','Chef Table'] as $brand)
                    <span class="text-xl text-cocoa" style="font-weight: 500;">{{ $brand }}</span>
                @endforeach
            </div>
        </div>
    </section>
</x-shop::layouts>
