@php
    use App\Models\AdminCategory;
    use App\Models\AdminReview;
    use App\Models\BlogPost;

    $channel = core()->getCurrentChannel();

    // Helper baca config editable
    $c = fn(string $k, string $default = '') => (string) (core()->getConfigData("beres_storefront.$k") ?: $default);

    // Palette hijau untuk placeholder produk yang belum punya gambar
    $bgPool = ['#E8F0E5','#DCE8D6','#F0F5EC','#EAF1E4','#F5F9F3','#C8DBBE'];
    $bgPick = fn(int $i) => $bgPool[$i % count($bgPool)];

    // ============ HOMEPAGE HIGHLIGHTS — via HomepageHighlightService ============
    $highlightService = app(\Beres\Highlight\Services\HomepageHighlightService::class);

    try {
        $newProductsDb   = $highlightService->getProducts(\Beres\Highlight\Models\HomepageHighlight::SECTION_NEW_ARRIVALS, 4);
        $featuredProduct = $highlightService->getFeaturedProduct();
        $bundlesDb       = $highlightService->getProducts(\Beres\Highlight\Models\HomepageHighlight::SECTION_KITS_BUNDLES, 4);
        $bestSellersDb   = $highlightService->getProducts(\Beres\Highlight\Models\HomepageHighlight::SECTION_BEST_SELLERS, 4);
        $superfoodsDb    = $highlightService->getProducts(\Beres\Highlight\Models\HomepageHighlight::SECTION_SEEDS, 5);
    } catch (\Throwable $e) {
        $newProductsDb   = collect();
        $featuredProduct = null;
        $bundlesDb       = collect();
        $bestSellersDb   = collect();
        $superfoodsDb    = collect();
    }

    // ============ CATEGORIES — from admin_categories ============
    try {
        $categoriesDb = $highlightService->getCategories(8);
    } catch (\Throwable $e) {
        $categoriesDb = AdminCategory::withCount('products')->orderByDesc('products_count')->limit(8)->get()
            ->map(fn ($cat) => ['id' => $cat->id, 'name' => $cat->name, 'slug' => $cat->slug, 'image' => $cat->image ? asset('storage/'.$cat->image) : null]);
    }

    // ============ REVIEWS — from admin_reviews (approved only) ============
    try {
        $reviewsDb = AdminReview::with('product')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->limit(3)->get();
    } catch (\Throwable $e) {
        $reviewsDb = collect();
    }

    // ============ BLOGS — from blog_posts (published only) ============
    try {
        $blogsDb = BlogPost::with('category')
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)->get();
    } catch (\Throwable $e) {
        $blogsDb = collect();
    }

    // ============ Dummy fallback (kalau DB masih kosong) ============
    $dummyProducts = [
        ['Masala Diabetic Roti', 'Rp 32.500', null,          ['1kg','5kg']],
        ['Chatpata Masala Oats', 'Rp 25.000', null,          ['500g','1kg']],
        ['Chicken Masala Oats',  'Rp 28.000', 'Rp 35.000',   ['500g','1kg']],
        ['Masala Quinoa Roti',   'Rp 35.000', null,          ['1kg']],
    ];
    $dummyCategories = ['Signature Oats','Granola','Seeds & Superfoods','Fry Mixes','Sauces','Special Spices','Tea & Drinks','Bakery'];

    // Trust badges (editable via admin)
    $trustBadges = [
        [core()->getConfigData('beres_storefront.trust.badge1_title') ?: 'Ambil di Toko',       core()->getConfigData('beres_storefront.trust.badge1_desc') ?: 'Ambil di gerai fisik kami'],
        [core()->getConfigData('beres_storefront.trust.badge2_title') ?: 'Pengiriman Tercepat',core()->getConfigData('beres_storefront.trust.badge2_desc') ?: 'Sameday area Jakarta'],
        [core()->getConfigData('beres_storefront.trust.badge3_title') ?: 'Zona Penawaran Terbaik', core()->getConfigData('beres_storefront.trust.badge3_desc') ?: 'Promo harian & bundle hemat'],
        [core()->getConfigData('beres_storefront.trust.badge4_title') ?: 'Kualitas Terbaik',    core()->getConfigData('beres_storefront.trust.badge4_desc') ?: '100% alami & bersertifikat lab'],
    ];

    // FAQ dari config admin
    $faqs = [
        [$c('faq.q1','Berapa lama produk saya dibuat & dikirim?'), $c('faq.a1','Untuk area Jakarta pesanan sebelum jam 10 pagi sampai di hari yang sama.')],
        [$c('faq.q2','Berapa biaya pengiriman?'),                   $c('faq.a2','Gratis ongkir untuk pembelian di atas Rp 200.000 area Jabodetabek.')],
        [$c('faq.q3','Bagaimana kalau saya tidak puas?'),           $c('faq.a3','Ganti atau refund penuh dalam 24 jam setelah barang diterima.')],
        [$c('faq.q4','Bisa ubah alamat setelah pesan?'),            $c('faq.a4','Bisa selama status pesanan masih "Diproses". Hubungi CS via WhatsApp.')],
    ];
@endphp

@php
    $siteName    = $channel->name ?? config('app.name');
    $homeTitle   = $channel->home_seo['meta_title']       ?? "$siteName — Belanja bahan segar dan pantry esensial";
    $homeDesc    = $channel->home_seo['meta_description'] ?? 'Pasar online untuk bahan segar dan pantry esensial. Langsung dari petani dan produsen lokal.';
    $homeUrl     = route('shop.home.index');
    $homeOgImage = $channel->logo_url ?? null;
@endphp

@push('styles')
<style>
    @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    /* Custom scrollbar for horizontal product scroll */
    .product-scroll-mobile {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #2D5A27 #E8F0E5;
        padding-bottom: 1rem;
        gap: 1rem;
    }
    .product-scroll-mobile > * {
        flex: 0 0 calc((100% - 2rem) / 3);
        scroll-snap-align: start;
    }
    @media (max-width: 767px) {
        .product-scroll-mobile > * {
            flex: 0 0 calc(70%);
        }
    }
    /* Webkit scrollbar (Chrome, Safari, Edge) */
    .product-scroll-mobile::-webkit-scrollbar {
        height: 6px;
    }
    .product-scroll-mobile::-webkit-scrollbar-track {
        background: #E8F0E5;
        border-radius: 10px;
    }
    .product-scroll-mobile::-webkit-scrollbar-thumb {
        background: #2D5A27;
        border-radius: 10px;
    }
    .product-scroll-mobile::-webkit-scrollbar-thumb:hover {
        background: #1E3D1A;
    }
    .beres-hero{position:relative;width:100%;overflow:hidden;background:#000;}
    .beres-hero__track{display:flex;transition:transform .6s cubic-bezier(.4,0,.2,1);will-change:transform;}
    .beres-hero__slide{flex:0 0 100%;width:100%;aspect-ratio:16/7;background:#111;}
    .beres-hero__slide img{width:100%;height:100%;object-fit:cover;display:block;}
    @media (max-width:768px){.beres-hero__slide{aspect-ratio:4/5;}}
    .beres-hero__nav{position:absolute;top:50%;transform:translateY(-50%);width:44px;height:44px;border-radius:999px;background:rgba(255,255,255,.85);color:#1A3E1A;border:0;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:2;box-shadow:0 2px 8px rgba(0,0,0,.15);}
    .beres-hero__nav:hover{background:#fff;}
    .beres-hero__nav--prev{left:16px;}
    .beres-hero__nav--next{right:16px;}
    .beres-hero__dots{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;gap:8px;z-index:2;}
    .beres-hero__dot{width:10px;height:10px;border-radius:999px;background:rgba(255,255,255,.5);border:0;cursor:pointer;padding:0;transition:all .2s;}
    .beres-hero__dot.is-active{background:#fff;width:28px;}
    @media (max-width:640px){.beres-hero__nav{width:36px;height:36px;font-size:14px;}.beres-hero__nav--prev{left:8px;}.beres-hero__nav--next{right:8px;}}
</style>
@endpush

@push('scripts')
<script>
    // Hero carousel — di-inisialisasi setelah Vue mount supaya tidak konflik
    (function(){
        function initBeresHero(){
            var track = document.getElementById('beresHeroTrack');
            if (!track) return;
            var slides = track.querySelectorAll('.beres-hero__slide');
            var dots   = document.querySelectorAll('.beres-hero__dot');
            var total  = slides.length;
            var idx    = 0;
            if (total < 2) return;

            window.beresHeroGoto = function(i){
                idx = (i + total) % total;
                track.style.transform = 'translateX(-' + (idx * 100) + '%)';
                dots.forEach(function(d,k){ d.classList.toggle('is-active', k === idx); });
            };
            window.beresHeroGo = function(dir){ window.beresHeroGoto(idx + dir); };

            var timer = setInterval(function(){ window.beresHeroGo(1); }, 5000);
            var wrap  = track.parentElement;
            wrap.addEventListener('mouseenter', function(){ clearInterval(timer); });
            wrap.addEventListener('mouseleave', function(){ timer = setInterval(function(){ window.beresHeroGo(1); }, 5000); });

            var startX = 0;
            track.addEventListener('touchstart', function(e){ startX = e.touches[0].clientX; }, {passive:true});
            track.addEventListener('touchend',   function(e){ var dx = e.changedTouches[0].clientX - startX; if (Math.abs(dx) > 40) window.beresHeroGo(dx < 0 ? 1 : -1); }, {passive:true});
        }
        // Delay sedikit supaya jalan SETELAH Vue selesai mount & render DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function(){ setTimeout(initBeresHero, 100); });
        } else {
            setTimeout(initBeresHero, 100);
        }
    })();

    // Wire Add to Cart form ke Bagisto API endpoint
    window.beresAddToCart = async function (form) {
        const btn      = form.querySelector('button[type="submit"]');
        const original = btn ? btn.textContent : '';
        if (btn) { btn.disabled = true; btn.textContent = 'Menambahkan…'; }

        const productId = form.querySelector('[name="product_id"]').value;
        const quantity  = parseInt(form.querySelector('[name="quantity"]').value || 1, 10);
        const token     = document.querySelector('meta[name="csrf-token"]')?.content
                       || form.querySelector('[name="_token"]')?.value;

        // Configurable product: send the selected child variant.
        // Bagisto's Configurable::prepareForCart() reads ONLY
        // `selected_configurable_option` (the child product id) and derives
        // the weight attribute/option automatically from the child. Sending
        // super_attribute[...] is unnecessary and, with a child id, wrong.
        const variantInput = form.querySelector('.beres-variant-input');
        const selectedVariantId = variantInput ? parseInt(variantInput.value, 10) : null;

        const payload = { product_id: productId, quantity };

        if (selectedVariantId && !isNaN(selectedVariantId)) {
            payload.selected_configurable_option = selectedVariantId;
        }

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (res.ok) {
                if (btn) btn.textContent = '✓ Ditambahkan';
                document.dispatchEvent(new CustomEvent('cart:updated'));
                setTimeout(() => {
                    if (btn) { btn.disabled = false; btn.textContent = original; }
                }, 1500);
            } else {
                const data = await res.json().catch(() => ({}));
                alert(data.message || 'Gagal menambahkan ke keranjang. Coba lagi.');
                if (btn) { btn.disabled = false; btn.textContent = original; }
            }
        } catch (e) {
            alert('Gagal terhubung ke server.');
            if (btn) { btn.disabled = false; btn.textContent = original; }
        }
    };

    // Qty stepper
    window.beresQty = function(btn, delta) {
        const input = btn.parentElement.querySelector('input[name="quantity"]');
        if (!input) return;
        let v = parseInt(input.value || 1, 10) + delta;
        if (v < 1) v = 1;
        if (v > 99) v = 99;
        input.value = v;
    };

    // Variant selector for configurable products
    window.beresSelectVariant = function(btn, variantId, price) {
        const card = btn.closest('.beres-card');
        if (!card) return;

        // Update hidden input
        const hiddenInput = card.querySelector('.beres-variant-input');
        if (hiddenInput) hiddenInput.value = variantId;

        // Reset ALL sibling buttons to default state
        const siblings = btn.parentElement.querySelectorAll('button');
        siblings.forEach(b => {
            b.style.backgroundColor = '';
            b.style.borderColor = '#E8F0E5';
            b.classList.remove('text-white');
            b.classList.add('text-[#171717]');
        });

        // Highlight ONLY the selected button
        btn.style.backgroundColor = '#2D5A27';
        btn.style.borderColor = '#2D5A27';
        btn.classList.add('text-white');
        btn.classList.remove('text-[#171717]');

        // Update price display
        const priceEl = card.querySelector('.text-xl.font-bold');
        if (priceEl && price) priceEl.textContent = price;
    };

    // Wishlist toggle → Bagisto API
    window.beresToggleWishlist = async function(btn, productId) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const svg   = btn.querySelector('svg');
        const filled = btn.getAttribute('data-active') === '1';
        try {
            const res = await fetch('{{ url('/api/customer/wishlist') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ product_id: productId }),
            });
            if (res.status === 401 || res.redirected) {
                window.location.href = '{{ route('shop.customer.session.create') }}';
                return;
            }
            if (res.ok) {
                btn.setAttribute('data-active', filled ? '0' : '1');
                if (svg) svg.setAttribute('fill', filled ? 'none' : '#2D5A27');
            }
        } catch (e) { /* silent */ }
    };
</script>
@endpush

@push('meta')
    <meta name="title"       content="{{ $homeTitle }}" />
    <meta name="description" content="{{ $homeDesc }}" />
    <meta property="og:title" content="{{ $homeTitle }}" />
    <meta property="og:description" content="{{ $homeDesc }}" />
    <meta property="og:url" content="{{ $homeUrl }}" />
    @if ($homeOgImage)<meta property="og:image" content="{{ $homeOgImage }}" />@endif
    <meta property="og:type" content="website" />
@endPush

<x-shop::layouts>
    <x-slot:title>{{ $channel->home_seo['meta_title'] ?? '' }}</x-slot>

    {{-- ============ HERO CAROUSEL (full-width, gambar penuh) ============ --}}
    @php
        $heroSlides = array_values(array_filter([
            $c('hero.slide1_img'),
            $c('hero.slide2_img'),
            $c('hero.slide3_img'),
            $c('hero.slide4_img'),
        ]));
        if (empty($heroSlides)) {
            $heroSlides = [
                'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=2000&q=80',
                'https://images.unsplash.com/photo-1506806732259-39c2d0268443?auto=format&fit=crop&w=2000&q=80',
                'https://images.unsplash.com/photo-1490818387583-1baba5e638af?auto=format&fit=crop&w=2000&q=80',
            ];
        }
    @endphp

    <section class="beres-hero" aria-label="Carousel hero">
        <div class="beres-hero__track" id="beresHeroTrack">
            @foreach ($heroSlides as $i => $img)
                <div class="beres-hero__slide" data-idx="{{ $i }}">
                    <img src="{{ $img }}" alt="Slide {{ $i + 1 }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                </div>
            @endforeach
        </div>

        @if (count($heroSlides) > 1)
            <button type="button" class="beres-hero__nav beres-hero__nav--prev" aria-label="Slide sebelumnya" onclick="beresHeroGo(-1)">&#10094;</button>
            <button type="button" class="beres-hero__nav beres-hero__nav--next" aria-label="Slide berikutnya" onclick="beresHeroGo(1)">&#10095;</button>

            <div class="beres-hero__dots" role="tablist">
                @foreach ($heroSlides as $i => $img)
                    <button type="button" class="beres-hero__dot {{ $i === 0 ? 'is-active' : '' }}" data-idx="{{ $i }}" aria-label="Slide {{ $i + 1 }}" onclick="beresHeroGoto({{ $i }})"></button>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Style & script hero dipindah ke @push di bawah supaya tidak di-drop oleh Vue --}}

    {{-- ============ FEATURED PRODUCT SPOTLIGHT ============ --}}
    @if ($featuredProduct)
        @php
            $fpName  = $featuredProduct->name ?? $featuredProduct->url_key ?? 'Featured Product';
            if ($featuredProduct instanceof \App\Models\AdminProduct) {
                $fpPrice = core()->currency($featuredProduct->price ?? 0);
                $fpUrl   = route('shop.product_or_category.index', $featuredProduct->slug ?? '#');
                $fpImage = ($featuredProduct->images && $featuredProduct->images->count())
                                ? asset('storage/'.$featuredProduct->images->first()->image_path)
                                : null;
            } else {
                $fpPrice = core()->currency($featuredProduct->getTypeInstance()->getMinimalPrice() ?? 0);
                $fpUrl   = route('shop.product_or_category.index', $featuredProduct->url_key ?? '#');
                $fpImage = product_image()->getProductBaseImage($featuredProduct)['medium_image_url'] ?? null;
            }
        @endphp
        <section class="bg-white beres-reveal">
            <div class="mx-auto max-w-[1400px] px-4 sm:px-6 md:px-10 lg:px-14 py-16 md:py-24">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-start">
                    <a href="{{ $fpUrl }}" class="block aspect-square md:aspect-[4/5] overflow-hidden" style="background-color:#E8F0E5;">
                        @if ($fpImage)
                            <img src="{{ $fpImage }}" alt="{{ $fpName }}" class="w-full h-full object-cover" loading="lazy">
                        @endif
                    </a>

                    <div>
                        <h2 class="text-2xl md:text-3xl text-[#171717]" style="font-weight:600;">{{ $fpName }}</h2>
                        <p class="mt-2 text-2xl md:text-3xl" style="color:#2D5A27; font-weight:700;">{{ $fpPrice }}</p>

                        @if (!empty($featuredProduct->short_description))
                            <div class="mt-6 text-sm text-[#404040] leading-relaxed prose prose-sm max-w-none">
                                {!! $featuredProduct->short_description !!}
                            </div>
                        @endif

                        <form action="{{ route('shop.api.checkout.cart.store') }}" method="POST"
                              class="mt-6 flex flex-col sm:flex-row gap-3"
                              onsubmit="event.preventDefault(); beresAddToCart(this);">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $featuredProduct->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="px-8 py-3 text-[13px] tracking-[0.14em] uppercase text-white hover:opacity-90 transition-opacity" style="background-color:#2D5A27; font-weight:600; border-radius:999px;">
                                Tambah ke Keranjang
                            </button>
                            <a href="{{ $fpUrl }}" class="px-8 py-3 text-[13px] tracking-[0.14em] uppercase border transition-colors hover:bg-[#E8F0E5] text-center" style="border-color:#2D5A27; color:#2D5A27; font-weight:600; border-radius:999px;">
                                Beli Sekarang
                            </a>
                        </form>

                        <a href="{{ $fpUrl }}" class="mt-6 inline-block text-sm underline text-[#737373] hover:text-[#2D5A27]">Lihat detail lengkap</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ NEW ARRIVALS ============ --}}
    <section class="bg-white beres-reveal">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-16 md:py-24">
            <h2 class="text-center text-2xl md:text-3xl text-[#171717] mb-8 md:mb-10" style="font-weight:600;">{{ $c('sections.new_title', 'Produk Terbaru') }}</h2>

            <div class="product-scroll-mobile">
                @if ($newProductsDb->isNotEmpty())
                    @foreach ($newProductsDb as $i => $product)
                        @include('shop::components.layouts._product-card', ['product'=>$product, 'bg'=>$bgPick($i), 'index'=>$i])
                    @endforeach
                @else
                    @foreach ($dummyProducts as $i => [$name, $price, $compare, $variants])
                        @include('shop::components.layouts._product-card', ['name'=>$name, 'price'=>$price, 'compare'=>$compare, 'variants'=>$variants, 'bg'=>$bgPick($i)])
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    {{-- ============ KITS & BUNDLES ============ --}}
    <section class="bg-white border-t beres-reveal" style="border-color:#F5F9F3;">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-16 md:py-24">
            <h2 class="text-center text-2xl md:text-3xl text-[#171717] mb-8 md:mb-10" style="font-weight:600;">{{ $c('sections.bundle_title', 'Paket & Bundel') }}</h2>

            <div class="product-scroll-mobile">
                @if ($bundlesDb->isNotEmpty())
                    @foreach ($bundlesDb as $i => $product)
                        @include('shop::components.layouts._product-card', ['product'=>$product, 'bg'=>$bgPick($i), 'index'=>$i])
                    @endforeach
                @else
                    @foreach ($dummyProducts as $i => [$name, $price, $compare, $variants])
                        @include('shop::components.layouts._product-card', ['name'=>$name, 'price'=>$price, 'compare'=>$compare, 'variants'=>['Bundle'], 'bg'=>$bgPick($i)])
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    {{-- ============ 100% NATURAL BANNER ============ --}}
    <section class="beres-reveal" style="background-color:#2D5A27;">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-10">
            <div class="flex items-center justify-center gap-6 md:gap-16 text-white text-center">
                <p class="text-lg md:text-2xl lg:text-3xl tracking-[0.15em]" style="font-weight:700;">{{ $c('natural_banner.text1', '100% ALAMI') }}</p>
                <span class="text-white/40 text-2xl">✦</span>
                <p class="text-lg md:text-2xl lg:text-3xl tracking-[0.15em]" style="font-weight:700;">{{ $c('natural_banner.text2', 'BERSERTIFIKAT LAB') }}</p>
            </div>
        </div>
    </section>

    {{-- ============ SHOP BY CATEGORY ============ --}}
    @php
        $homeCats = app(\Beres\Highlight\Services\HomepageHighlightService::class)
            ->getCategories(6, $channel->root_category_id ?? 1);
    @endphp
    <section class="bg-white beres-reveal">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-16 md:py-24">
            <div class="flex items-center justify-between mb-6 md:mb-8">
                <h2 class="text-xl md:text-2xl text-[#171717]" style="font-weight:600;">{{ $c('sections.cat_title', 'All Kitchen Needs') }}</h2>
                <a href="{{ route('shop.search.index') }}" class="text-sm underline text-[#2D5A27] hover:opacity-70">See All Categories</a>
            </div>

            {{-- Horizontal scroll row — single row on desktop --}}
            <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-none" style="-ms-overflow-style:none; scrollbar-width:none;">
                @foreach ($homeCats as $i => $cat)
                    <a href="{{ route('shop.product_or_category.index', $cat['slug']) }}" class="group block shrink-0 w-[140px] sm:w-[160px] md:w-[180px]">
                        <div class="aspect-[3/4] overflow-hidden transition-transform duration-500 group-hover:scale-[1.03] rounded-lg" style="background-color:{{ $bgPick($i) }};"></div>
                        <p class="mt-2 text-center text-[11px] md:text-xs text-white px-2 py-1.5" style="background-color:#2D5A27; font-weight:500; border-radius:999px;">
                            {{ $cat['name'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ ALL TIME BEST SELLER ============ --}}
    <section class="bg-white border-t beres-reveal" style="border-color:#F5F9F3;">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-16 md:py-24">
            <h2 class="text-xl md:text-2xl text-[#171717] mb-6 md:mb-8" style="font-weight:600;">{{ $c('sections.best_title', 'Produk Terlaris') }}</h2>

            <div class="product-scroll-mobile">
                @if ($bestSellersDb->isNotEmpty())
                    @foreach ($bestSellersDb as $i => $product)
                        @include('shop::components.layouts._product-card', ['product'=>$product, 'bg'=>$bgPick($i), 'index'=>$i])
                    @endforeach
                @else
                    @foreach ($dummyProducts as $i => [$name, $price, $compare, $variants])
                        @include('shop::components.layouts._product-card', ['name'=>$name, 'price'=>$price, 'compare'=>$compare, 'variants'=>$variants, 'bg'=>$bgPick($i)])
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    {{-- ============ CATEGORY TICKER (Marquee) ============ --}}
    <section class="overflow-hidden beres-reveal" style="background-color:#2D5A27;">
        <div class="py-4 md:py-5">
            <div class="flex whitespace-nowrap animate-[marquee_35s_linear_infinite] text-white gap-10 md:gap-14">
                @php $tickerItems = ['Fruits & Vegetables','Meat & Seafood','Bread & Bakery','Drink','Spices & Herbs','Healthy Snacks','Kitchen Essentials']; @endphp
                @for ($rep = 0; $rep < 4; $rep++)
                    @foreach ($tickerItems as $t)
                        <span class="text-sm md:text-base tracking-[0.12em] uppercase" style="font-weight:500;">{{ $t }}</span>
                        <span class="text-white/40">—</span>
                    @endforeach
                @endfor
            </div>
        </div>
    </section>

    {{-- ============ SEEDS & SUPERFOODS ============ --}}
    <section class="bg-white beres-reveal">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-16 md:py-24">
            <div class="flex items-center justify-between mb-6 md:mb-8">
                <h2 class="text-xl md:text-2xl text-[#171717]" style="font-weight:600;">Biji & Superfood Kami</h2>
                <a href="{{ route('shop.search.index') }}" class="text-sm underline text-[#2D5A27] hover:opacity-70">Lihat Semua</a>
            </div>

            <div class="product-scroll-mobile">
                @if ($superfoodsDb->isNotEmpty())
                    @foreach ($superfoodsDb as $i => $product)
                        @php
                            $sfName  = $product->name ?? 'Product';
                            if ($product instanceof \App\Models\AdminProduct) {
                                $sfPrice = core()->currency($product->price ?? 0);
                                $sfUrl   = route('shop.product_or_category.index', $product->slug ?? '#');
                                $sfImg   = ($product->images && $product->images->count())
                                                ? asset('storage/'.$product->images->first()->image_path)
                                                : null;
                            } else {
                                $sfPrice = core()->currency($product->getTypeInstance()->getMinimalPrice() ?? 0);
                                $sfUrl   = route('shop.product_or_category.index', $product->url_key ?? '#');
                                $sfImg   = product_image()->getProductBaseImage($product)['small_image_url'] ?? null;
                            }
                        @endphp
                        <a href="{{ $sfUrl }}" class="group block">
                            <div class="aspect-square overflow-hidden transition-transform duration-500 group-hover:scale-[1.02]" style="background-color:{{ $bgPick($i) }};">
                                @if ($sfImg)<img src="{{ $sfImg }}" alt="{{ $sfName }}" class="w-full h-full object-cover" loading="lazy">@endif
                            </div>
                            <p class="mt-3 text-sm text-[#171717]" style="font-weight:500;">{{ $sfName }}</p>
                            <p class="mt-1 text-sm" style="color:#2D5A27; font-weight:600;">{{ $sfPrice }}</p>
                        </a>
                    @endforeach
                @else
                    @foreach (['Masala Quinoa Roti','Chia Seeds','Pumpkin Seeds','Sunflower Seeds','Flax Seeds Mix'] as $i => $name)
                        <a href="{{ route('shop.search.index') }}" class="group block">
                            <div class="aspect-square overflow-hidden transition-transform duration-500 group-hover:scale-[1.02]" style="background-color:{{ $bgPick($i) }};"></div>
                            <p class="mt-3 text-sm text-[#171717]" style="font-weight:500;">{{ $name }}</p>
                            <p class="mt-1 text-sm" style="color:#2D5A27; font-weight:600;">Rp —</p>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    {{-- ============ TRUST BADGES with icons ============ --}}
    <section class="bg-white border-t beres-reveal" style="border-color:#F5F9F3;">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-10 md:py-14">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @php
                    $badgeIcons = [
                        '<path d="M3 3h5v5H3zM3 16h5v5H3zM16 3h5v5h-5zM16 16h5v5h-5z"/>',
                        '<path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/>',
                        '<circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/>',
                        '<circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/>',
                    ];
                @endphp
                @foreach ($trustBadges as $i => [$title, $desc])
                    <div class="p-5 md:p-6 text-center flex flex-col items-center" style="background-color:#F5F9F3;">
                        <span class="w-12 h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center mb-3" style="background-color:#E8F0E5; color:#2D5A27;">
                            <svg class="w-6 h-6 md:w-7 md:h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $badgeIcons[$i] !!}</svg>
                        </span>
                        <p class="text-sm md:text-base text-[#171717]" style="font-weight:600;">{{ $title }}</p>
                        <p class="mt-1 text-xs md:text-sm text-[#737373] leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CUSTOMER REVIEWS ============ --}}
    <section class="beres-reveal" style="background-color:#F5F9F3;">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-16 md:py-24">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-8">
                <div>
                    <p class="text-xl md:text-2xl text-[#171717]" style="font-weight:600;">{{ $c('sections.review_title', 'Ulasan Pelanggan') }}</p>
                    <p class="mt-1 text-sm" style="color:#2D5A27;">★★★★★ &nbsp; {{ $c('sections.review_eyebrow', '4,8 dari 2.400+ ulasan') }}</p>
                </div>
                <a href="{{ $c('contact.google_review_url', 'https://www.google.com/search?q=Ankesh+Online+Store') }}" target="_blank" rel="noopener" class="text-sm underline text-[#2D5A27] hover:opacity-70">Tulis ulasan</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                @if ($reviewsDb->isNotEmpty())
                    @foreach ($reviewsDb as $review)
                        @php
                            $revName    = $review->name ?? 'Anonim';
                            $revInitial = strtoupper(mb_substr($revName, 0, 1));
                            $revText    = $review->comment ?? $review->title ?? '';
                            $revStars   = str_repeat('★', (int) ($review->rating ?? 5)) . str_repeat('☆', 5 - (int) ($review->rating ?? 5));
                        @endphp
                        <div class="p-5 md:p-6 bg-white beres-card" style="border-radius:16px;">
                            <p class="text-sm" style="color:#2D5A27;">{{ $revStars }}</p>
                            <p class="mt-3 text-sm md:text-base text-[#404040] leading-relaxed">"{{ $revText }}"</p>
                            <div class="mt-5 flex items-center gap-3">
                                <span class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm" style="background-color:#2D5A27; font-weight:600;">{{ $revInitial }}</span>
                                <span class="text-sm text-[#171717]" style="font-weight:500;">{{ $revName }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    @foreach ([['A','Ahmad Rizky','Sayurannya benar-benar segar seperti baru dipetik. Pengiriman tepat waktu di pagi hari.'],['S','Siti Nurhaliza','Kualitas dagingnya konsisten. Berlangganan 6 bulan tanpa kecewa.'],['B','Budi Santoso','Kopinya harum dan segar, terasa baru di-roasting. Harga wajar untuk single origin.']] as [$initial, $name, $text])
                        <div class="p-5 md:p-6 bg-white beres-card" style="border-radius:16px;">
                            <p class="text-sm" style="color:#2D5A27;">★★★★★</p>
                            <p class="mt-3 text-sm md:text-base text-[#404040] leading-relaxed">"{{ $text }}"</p>
                            <div class="mt-5 flex items-center gap-3">
                                <span class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm" style="background-color:#2D5A27; font-weight:600;">{{ $initial }}</span>
                                <span class="text-sm text-[#171717]" style="font-weight:500;">{{ $name }}</span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="mt-6 text-center">
                <a href="{{ $c('contact.google_review_url', 'https://www.google.com/search?q=Ankesh+Online+Store') }}" target="_blank" rel="noopener" class="text-sm underline text-[#2D5A27] hover:opacity-70">Lihat semua ulasan</a>
            </div>
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="bg-white beres-reveal">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 md:px-10 py-16 md:py-24">
            <h2 class="text-2xl md:text-3xl text-[#171717] mb-8" style="font-weight:600;">{{ $c('sections.faq_title', 'FAQ') }}</h2>

            <div class="space-y-3">
                @foreach ($faqs as $i => [$q, $a])
                    <details class="group border overflow-hidden transition-all hover:border-[#2D5A27]" style="border-color:#E8F0E5; border-radius:14px;" @if($i === 0) open @endif>
                        <summary class="flex items-center justify-between cursor-pointer list-none px-5 md:px-6 py-4 md:py-5 hover:bg-[#F5F9F3] transition-colors">
                            <span class="text-sm md:text-base text-[#171717] pr-4" style="font-weight:500;">{{ $q }}</span>
                            <span class="text-2xl transition-transform duration-300 group-open:rotate-45 shrink-0 leading-none" style="color:#2D5A27;">+</span>
                        </summary>
                        <p class="px-5 md:px-6 pb-4 md:pb-5 text-sm md:text-base text-[#404040] leading-relaxed">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ LATEST BLOGS ============ --}}
    <section class="bg-white beres-reveal">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-16 md:py-24">
            <div class="flex items-center justify-between mb-8 md:mb-10">
                <h2 class="text-2xl md:text-3xl text-[#171717]" style="font-weight:700;">Latest Blogs</h2>
                <a href="{{ route('shop.search.index') }}" class="text-sm underline text-[#2D5A27] hover:opacity-70">Browse Wellness Blogs</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                @if ($blogsDb->isNotEmpty())
                    @foreach ($blogsDb as $i => $page)
                        @php
                            $blogTranslation = $page->translations?->first();
                            $blogTitle   = $page->page_title ?? ($blogTranslation?->page_title ?? 'Untitled');
                            $blogExcerpt = \Illuminate\Support\Str::limit(strip_tags($page->html_content ?? ($blogTranslation?->html_content ?? '')), 120);
                            $blogUrl     = $page->url_key ? route('shop.cms.page', $page->url_key) : '#';
                            $blogDate    = $page->created_at ? $page->created_at->format('d M Y') : '';
                        @endphp
                        <article class="group bg-white overflow-hidden" style="border-radius:16px; border:1px solid #E8F0E5;">
                            {{-- Banner Image --}}
                            <a href="{{ $blogUrl }}" class="block">
                                <div class="relative aspect-[16/10] overflow-hidden" style="background: linear-gradient(135deg, #F5F9F3 0%, #E8F0E5 100%);">
                                    <div class="absolute inset-0 flex items-center justify-between p-6">
                                        <div class="flex-1">
                                            <p class="text-xs text-[#2D5A27] mb-2" style="font-weight:500;">Grannis Kitchen</p>
                                            <h3 class="text-xl md:text-2xl text-[#171717] leading-tight" style="font-weight:700;">{{ $blogTitle }}</h3>
                                        </div>
                                        <div class="w-24 h-24 md:w-32 md:h-32 flex-shrink-0 ml-4">
                                            <div class="w-full h-full rounded-lg" style="background-color: #D4A574;"></div>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            {{-- Content --}}
                            <div class="p-5 md:p-6">
                                @if ($blogDate)
                                    <div class="flex items-center gap-2 text-xs text-[#737373] mb-3">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        <span>{{ strtoupper($blogDate) }}</span>
                                    </div>
                                @endif
                                <a href="{{ $blogUrl }}" class="block">
                                    <h4 class="text-base md:text-lg text-[#171717] group-hover:text-[#2D5A27] transition-colors line-clamp-2" style="font-weight:600;">{{ $blogTitle }}</h4>
                                </a>
                                <p class="mt-2 text-sm text-[#737373] leading-relaxed line-clamp-2">{{ $blogExcerpt }}</p>
                            </div>
                        </article>
                    @endforeach
                @else
                    @foreach ([['High Fiber Breakfast Ideas for Healthy Living','Adding high-fiber food to breakfast is the simplest of methods to make the meal more nutritious.'],['High Protein Breakfast Ideas for Busy Professionals','Many professionals have to rush to get to work in the morning. With all the preparations for a healthy start...'],['Best Healthy Breakfast Foods for Everyone','The most effective breakfast meal can definitely make a difference in everything from your energy levels...']] as $i => [$title, $excerpt])
                        <article class="group bg-white overflow-hidden" style="border-radius:16px; border:1px solid #E8F0E5;">
                            <a href="{{ route('shop.search.index') }}" class="block">
                                <div class="relative aspect-[16/10] overflow-hidden" style="background: linear-gradient(135deg, #F5F9F3 0%, #E8F0E5 100%);">
                                    <div class="absolute inset-0 flex items-center justify-between p-6">
                                        <div class="flex-1">
                                            <p class="text-xs text-[#2D5A27] mb-2" style="font-weight:500;">Grannis Kitchen</p>
                                            <h3 class="text-xl md:text-2xl text-[#171717] leading-tight" style="font-weight:700;">{{ $title }}</h3>
                                        </div>
                                        <div class="w-24 h-24 md:w-32 md:h-32 flex-shrink-0 ml-4">
                                            <div class="w-full h-full rounded-lg" style="background-color: #D4A574;"></div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="p-5 md:p-6">
                                <div class="flex items-center gap-2 text-xs text-[#737373] mb-3">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span>{{ strtoupper(now()->subDays($i * 6)->format('d M Y')) }}</span>
                                </div>
                                <a href="{{ route('shop.search.index') }}" class="block">
                                    <h4 class="text-base md:text-lg text-[#171717] group-hover:text-[#2D5A27] transition-colors line-clamp-2" style="font-weight:600;">{{ $title }}</h4>
                                </a>
                                <p class="mt-2 text-sm text-[#737373] leading-relaxed line-clamp-2">{{ $excerpt }}</p>
                            </div>
                        </article>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    {{-- ============ MAP & CONTACT ============ --}}
    @include('shop::components.layouts.map-section')
</x-shop::layouts>
