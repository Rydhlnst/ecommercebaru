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
    $sectionLimits = \Beres\Highlight\Models\HomepageHighlight::getSectionDefinitions();

    try {
        $newProductsDb   = $highlightService->getProducts(\Beres\Highlight\Models\HomepageHighlight::SECTION_NEW_ARRIVALS, $sectionLimits[\Beres\Highlight\Models\HomepageHighlight::SECTION_NEW_ARRIVALS]['limit']);
        $featuredProduct = $highlightService->getFeaturedProduct();
        $bundlesDb       = $highlightService->getProducts(\Beres\Highlight\Models\HomepageHighlight::SECTION_KITS_BUNDLES, $sectionLimits[\Beres\Highlight\Models\HomepageHighlight::SECTION_KITS_BUNDLES]['limit']);
        $bestSellersDb   = $highlightService->getProducts(\Beres\Highlight\Models\HomepageHighlight::SECTION_BEST_SELLERS, $sectionLimits[\Beres\Highlight\Models\HomepageHighlight::SECTION_BEST_SELLERS]['limit']);
        $superfoodsDb    = $highlightService->getProducts(\Beres\Highlight\Models\HomepageHighlight::SECTION_SEEDS, $sectionLimits[\Beres\Highlight\Models\HomepageHighlight::SECTION_SEEDS]['limit']);
    } catch (\Throwable $e) {
        $newProductsDb   = collect();
        $featuredProduct = null;
        $bundlesDb       = collect();
        $bestSellersDb   = collect();
        $superfoodsDb    = collect();
    }

    // ============ CATEGORIES — from admin_categories ============
    try {
        $categoriesDb = $highlightService->getCategories($sectionLimits[\Beres\Highlight\Models\HomepageHighlight::SECTION_CATEGORIES]['limit']);
    } catch (\Throwable $e) {
        $categoriesDb = AdminCategory::withCount('products')->orderByDesc('products_count')->limit($sectionLimits[\Beres\Highlight\Models\HomepageHighlight::SECTION_CATEGORIES]['limit'])->get()
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

    // Trust badges (editable via admin)
    $trustBadges = array_values(array_filter([
        [core()->getConfigData('beres_storefront.trust.badge1_title'), core()->getConfigData('beres_storefront.trust.badge1_desc')],
        [core()->getConfigData('beres_storefront.trust.badge2_title'), core()->getConfigData('beres_storefront.trust.badge2_desc')],
        [core()->getConfigData('beres_storefront.trust.badge3_title'), core()->getConfigData('beres_storefront.trust.badge3_desc')],
        [core()->getConfigData('beres_storefront.trust.badge4_title'), core()->getConfigData('beres_storefront.trust.badge4_desc')],
    ], fn($b) => !empty($b[0])));

    // ============ FAQS — from faqs table in database ============
    try {
        $faqsDb = \App\Models\Faq::where('is_active', true)->orderBy('sort_order')->latest()->get();
    } catch (\Throwable $e) {
        $faqsDb = collect();
    }
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
    .beres-value-marquee { overflow: hidden; }
    .beres-value-marquee__track {
        display: flex;
        width: max-content;
        animation: marquee 28s linear infinite;
    }
    .beres-value-marquee:hover .beres-value-marquee__track { animation-play-state: paused; }
    @media (prefers-reduced-motion: reduce) {
        .beres-value-marquee__track { animation: none; transform: translateX(0); }
    }

    /* Responsive product grid for homepage product sections */
    .product-scroll-mobile {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1.25rem;
    }
    @media (max-width: 1024px) {
        .product-scroll-mobile { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 640px) {
        .product-scroll-mobile {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }
    }
    .beres-hero{position:relative;width:100%;overflow:hidden;background:#f8f9fa;}
    .beres-hero__track{display:flex;transition:transform .6s cubic-bezier(.4,0,.2,1);will-change:transform;}
    .beres-hero__slide{flex:0 0 100%;width:100%;aspect-ratio:3/2;background:#f8f9fa;}
    .beres-hero__slide img{width:100%;height:100%;object-fit:contain;object-position:center;display:block;}
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
                const data = await res.json().catch(() => ({}));
                if (data.cart && typeof beresCartRefresh === 'function') beresCartRefresh(data.cart);
                if (btn) btn.textContent = '✓ Ditambahkan';
                setTimeout(() => {
                    if (btn) { btn.disabled = false; btn.textContent = original; }
                    if (typeof beresCartOpen === 'function') beresCartOpen();
                }, 500);
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
    window.beresSelectVariantInline = function(btn, variantId, price) {
        const card = btn.closest('.beres-card');
        if (!card) return;

        // Update hidden input
        const hiddenInput = card.querySelector('.beres-variant-input');
        if (hiddenInput) hiddenInput.value = variantId;

        // Reset ALL sibling buttons to default state
        const siblings = btn.parentElement.querySelectorAll('button');
        siblings.forEach(b => {
            b.style.backgroundColor = '#fff';
            b.style.borderColor = '#E8F0E5';
            b.style.color = '#171717';
        });

        // Highlight ONLY the selected button
        btn.style.backgroundColor = '#2D5A27';
        btn.style.borderColor = '#2D5A27';
        btn.style.color = '#fff';

        // Update price display
        const priceEl = card.querySelector('.text-xl.font-bold');
        if (priceEl && price) priceEl.textContent = price;
    };
    window.beresSelectVariant = window.beresSelectVariantInline;

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

    {{-- ============ HERO CAROUSEL ============ --}}
    @php
        $heroBannerSetting = null;
        try {
            $heroBannerSetting = \App\Models\SiteSetting::getValue('hero_banner_image');
        } catch (\Throwable $e) {}

        $heroSlides = array_values(array_filter([
            $heroBannerSetting ?: ($c('hero.slide1_img') ?: (file_exists(public_path('images/hero-products.jpg')) ? '/images/hero-products.jpg' : '')),
            $c('hero.slide2_img'),
            $c('hero.slide3_img'),
            $c('hero.slide4_img'),
        ]));
    @endphp

    @if (!empty($heroSlides))
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
    @else
        {{-- Skeleton placeholder ketika belum ada gambar hero slide --}}
        <section class="w-full overflow-hidden bg-gray-100" aria-label="Hero banner skeleton">
            <div class="w-full aspect-[16/7] max-md:aspect-[4/5] bg-gradient-to-r from-gray-200 via-gray-100 to-gray-200 animate-pulse flex flex-col items-center justify-center text-gray-400 p-6">
                <svg class="w-12 h-12 md:w-16 md:h-16 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs md:text-sm font-medium tracking-wider uppercase text-gray-400">Banner Toko (Upload di Pengaturan)</span>
            </div>
        </section>
    @endif

    {{-- Style & script hero dipindah ke @push di bawah supaya tidak di-drop oleh Vue --}}

    {{-- ============ FEATURED PRODUCT SPOTLIGHT ============ --}}
    @if ($featuredProduct)
        @php
            $fpName  = $featuredProduct->name ?? $featuredProduct->url_key ?? 'Featured Product';
            if ($featuredProduct instanceof \App\Models\AdminProduct) {
                $fpPrice = 'Rp ' . number_format($featuredProduct->price ?? 0, 0, ',', '.');
                $fpUrl   = route('shop.admin_product.show', $featuredProduct->slug ?? '#');
                $fpImage = $featuredProduct->image_url ?? ($featuredProduct->images && $featuredProduct->images->count() ? $featuredProduct->images->first()->url : null);
                $fpDesc  = $featuredProduct->description ?? null;
            } else {
                $minP = 0;
                try { $minP = $featuredProduct->getTypeInstance()->getMinimalPrice() ?? 0; } catch (\Throwable $e) {}
                $fpPrice = 'Rp ' . number_format($minP, 0, ',', '.');
                $fpUrl   = $featuredProduct->url_key ? route('shop.product_or_category.index', $featuredProduct->url_key) : route('shop.search.index');
                $fpImage = product_image()->getProductBaseImage($featuredProduct)['medium_image_url'] ?? null;
                $fpDesc  = $featuredProduct->short_description ?? null;
            }
        @endphp
        <section class="bg-white beres-reveal">
            <div class="mx-auto max-w-[1400px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-start">
                    <a href="{{ $fpUrl }}" class="block aspect-[4/3] overflow-hidden rounded-2xl relative" style="background-color:#E8F0E5;">
                        <x-shop::product-image :image="$fpImage" :alt="$fpName" size="lg" class="w-full h-full transition-transform duration-500" style="object-fit:fill !important; transform:scale(1.02);" />
                    </a>

                    <div>
                        <h2 class="text-2xl md:text-3xl text-[#171717]" style="font-weight:600;">{{ $fpName }}</h2>
                        <p class="mt-2 text-2xl md:text-3xl" style="color:#2D5A27; font-weight:700;">{{ $fpPrice }}</p>

                        @if (!empty($fpDesc))
                            <div class="mt-6 text-sm text-[#404040] leading-relaxed text-justify prose prose-sm max-w-none">
                                @if ($featuredProduct instanceof \App\Models\AdminProduct)
                                    <p>{{ $fpDesc }}</p>
                                @else
                                    {!! $fpDesc !!}
                                @endif
                            </div>
                        @endif

                        <form action="{{ route('cart.add') }}" method="POST"
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
    @if ($newProductsDb->isNotEmpty())
        <section class="bg-white beres-reveal">
            <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
                <h2 class="text-center text-2xl md:text-3xl text-[#171717] mb-8 md:mb-10" style="font-weight:600;">{{ $c('sections.new_title', 'Produk Terbaru') }}</h2>

                <div class="product-scroll-mobile">
                    @foreach ($newProductsDb as $i => $product)
                        @include('shop::components.layouts._product-card', ['product'=>$product, 'bg'=>$bgPick($i), 'index'=>$i])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ KITS & BUNDLES ============ --}}
    @if ($bundlesDb->isNotEmpty())
        <section class="bg-white border-t beres-reveal" style="border-color:#F5F9F3;">
            <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
                <h2 class="text-center text-2xl md:text-3xl text-[#171717] mb-8 md:mb-10" style="font-weight:600;">{{ $c('sections.bundle_title', 'Paket & Bundel') }}</h2>

                <div class="product-scroll-mobile">
                    @foreach ($bundlesDb as $i => $product)
                        @include('shop::components.layouts._product-card', ['product'=>$product, 'bg'=>$bgPick($i), 'index'=>$i])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 100% NATURAL BANNER ============ --}}
    @php $naturalLink = $c('natural_banner.link', ''); @endphp
    <section class="beres-reveal" style="background-color:#2D5A27;">
        @if($naturalLink)
            <a href="{{ $naturalLink }}" class="block hover:opacity-90 transition-opacity">
        @endif
            <div class="beres-value-marquee py-6 md:py-8" aria-label="{{ $c('natural_banner.text1', '100% ALAMI') }} — {{ $c('natural_banner.text2', 'BERSERTIFIKAT LAB') }}">
                <div class="beres-value-marquee__track items-center gap-8 md:gap-16 text-white">
                    @for ($rep = 0; $rep < 6; $rep++)
                        <span class="text-lg md:text-2xl lg:text-3xl tracking-[0.15em] whitespace-nowrap" style="font-weight:700;">{{ $c('natural_banner.text1', '100% ALAMI') }}</span>
                        <span class="text-white/40 text-2xl" aria-hidden="true">✦</span>
                        <span class="text-lg md:text-2xl lg:text-3xl tracking-[0.15em] whitespace-nowrap" style="font-weight:700;">{{ $c('natural_banner.text2', 'BERSERTIFIKAT LAB') }}</span>
                        <span class="text-white/40 text-2xl" aria-hidden="true">✦</span>
                    @endfor
                </div>
            </div>
        @if($naturalLink)
            </a>
        @endif
    </section>

    {{-- ============ SHOP BY CATEGORY ============ --}}
    @php
        $homeCats = app(\Beres\Highlight\Services\HomepageHighlightService::class)
            ->getCategories($sectionLimits[\Beres\Highlight\Models\HomepageHighlight::SECTION_CATEGORIES]['limit']);
    @endphp
    @if ($homeCats->isNotEmpty())
        <section class="bg-white beres-reveal">
            <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-8">
                    <h2 class="text-xl md:text-2xl text-[#171717]" style="font-weight:600;">{{ $c('sections.cat_title', 'All Kitchen Needs') }}</h2>
                    <a href="{{ route('shop.search.index') }}" class="text-sm underline text-[#2D5A27] hover:opacity-70">See All Categories</a>
                </div>

                {{-- Horizontal scroll row — single row on desktop --}}
                <div class="flex gap-5 overflow-x-auto pb-4 scrollbar-none" style="-ms-overflow-style:none; scrollbar-width:none;">
                    @foreach ($homeCats as $i => $cat)
                        <a href="{{ route('shop.admin_category.show', $cat['slug']) }}" class="group block shrink-0 w-[150px] sm:w-[170px] md:w-[190px]">
                            <div class="aspect-square overflow-hidden transition-transform duration-500 group-hover:scale-[1.03] rounded-2xl relative flex items-center justify-center border border-[#E8F0E5] shadow-xs" style="background-color:{{ $bgPick($i) }};">
                                @if(!empty($cat['image']))
                                    <img src="{{ $cat['image'] }}" alt="{{ $cat['name'] }}" class="w-full h-full object-fill scale-[1.04] transition-transform duration-500" style="width:100% !important; height:100% !important; padding:0 !important;">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center p-4 text-center bg-gradient-to-br from-[#F5F9F3] to-[#E8F0E5] text-[#2D5A27]">
                                        <svg class="w-8 h-8 mb-2 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        <span class="text-xs font-semibold leading-tight">{{ $cat['name'] }}</span>
                                    </div>
                                @endif
                            </div>
                            <p class="mt-2.5 text-center text-[11px] md:text-xs text-white px-3 py-1.5 transition-colors group-hover:bg-[#1E3D1A] shadow-xs" style="background-color:#2D5A27; font-weight:600; border-radius:999px;">
                                {{ $cat['name'] }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ ALL TIME BEST SELLER ============ --}}
    @if ($bestSellersDb->isNotEmpty())
        <section class="bg-white border-t beres-reveal" style="border-color:#F5F9F3;">
            <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
                <h2 class="text-xl md:text-2xl text-[#171717] mb-6 md:mb-8" style="font-weight:600;">{{ $c('sections.best_title', 'Produk Terlaris') }}</h2>

                <div class="product-scroll-mobile">
                    @foreach ($bestSellersDb as $i => $product)
                        @include('shop::components.layouts._product-card', ['product'=>$product, 'bg'=>$bgPick($i), 'index'=>$i])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ CATEGORY TICKER (Marquee - Only if categories exist in DB) ============ --}}
    @php
        $tickerDbCats = \App\Models\AdminCategory::pluck('name')->all();
    @endphp
    @if(!empty($tickerDbCats))
        <section class="overflow-hidden beres-reveal" style="background-color:#2D5A27;">
            <div class="py-4 md:py-5">
                <div class="flex whitespace-nowrap animate-[marquee_35s_linear_infinite] text-white gap-10 md:gap-14">
                    @for ($rep = 0; $rep < 4; $rep++)
                        @foreach ($tickerDbCats as $t)
                            <span class="text-sm md:text-base tracking-[0.12em] uppercase" style="font-weight:500;">{{ $t }}</span>
                            <span class="text-white/40">—</span>
                        @endforeach
                    @endfor
                </div>
            </div>
        </section>
    @endif

    {{-- ============ SEEDS & SUPERFOODS ============ --}}
    @if ($superfoodsDb->isNotEmpty())
        <section class="bg-white beres-reveal">
            <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
                <div class="flex items-center justify-between mb-6 md:mb-8">
                    <h2 class="text-xl md:text-2xl text-[#171717]" style="font-weight:600;">{{ $c('sections.seed_title', 'Biji & Superfood Kami') }}</h2>
                    <a href="{{ route('shop.search.index') }}" class="text-sm underline text-[#2D5A27] hover:opacity-70">Lihat Semua</a>
                </div>

                <div class="product-scroll-mobile">
                    @foreach ($superfoodsDb as $i => $product)
                        @include('shop::components.layouts._product-card', ['product'=>$product, 'bg'=>$bgPick($i), 'index'=>$i])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Empty catalog notice when no products exist --}}
    @if ($newProductsDb->isEmpty() && $bundlesDb->isEmpty() && $bestSellersDb->isEmpty() && $superfoodsDb->isEmpty() && !$featuredProduct)
        <section class="bg-white py-10 text-center beres-reveal">
            <div class="mx-auto max-w-md px-4">
                <div class="w-16 h-16 bg-[#E8F0E5] text-[#2D5A27] rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-[#171717] mb-2">Katalog Produk Segera Hadir</h3>
                <p class="text-sm text-[#737373]">Saat ini belum ada produk aktif yang ditampilkan. Silakan tambahkan produk baru melalui panel admin.</p>
            </div>
        </section>
    @endif

    {{-- ============ TRUST BADGES with icons ============ --}}
    @if (!empty($trustBadges))
        <section class="bg-white border-t beres-reveal" style="border-color:#F5F9F3;">
            <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-5 md:py-7">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    @php
                        $badgeIcons = [
                            // Pick Up — storefront
                            '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
                            // Fastest Delivery — truck
                            '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
                            // Best Offer Zone — tag
                            '<path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
                            // Best Quality — shield/check
                            '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
                        ];
                    @endphp
                    @foreach ($trustBadges as $i => [$title, $desc])
                        <div class="p-5 md:p-6 text-center flex flex-col items-center" style="background-color:#F5F9F3;">
                            <span class="w-12 h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center mb-3" style="background-color:#E8F0E5; color:#2D5A27;">
                                <svg class="w-6 h-6 md:w-7 md:h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $badgeIcons[$i % 4] !!}</svg>
                            </span>
                            <p class="text-sm md:text-base text-[#171717]" style="font-weight:600;">{{ $title }}</p>
                            <p class="mt-1 text-xs md:text-sm text-[#737373] leading-relaxed">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

           {{-- ============ CUSTOMER REVIEWS (Only show if real approved reviews exist in DB) ============ --}}
    @if ($reviewsDb->isNotEmpty())
        <section class="beres-reveal" style="background-color:#F5F9F3;">
            <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-8">
                    <div>
                        <p class="text-xl md:text-2xl text-[#171717]" style="font-weight:600;">{{ $c('sections.review_title', 'Ulasan Pelanggan') }}</p>
                    </div>
                    <a href="{{ $c('contact.google_review_url', 'https://www.google.com/search?q=Ankesh+Online+Store') }}" target="_blank" rel="noopener" class="text-sm underline text-[#2D5A27] hover:opacity-70">Tulis ulasan</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    @foreach ($reviewsDb as $review)
                        @php
                    $revName    = $review->customer_name ?? 'Anonim';
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
                </div>
            </div>
        </section>
    @endif

    {{-- ============ GOOGLE REVIEWS (Empty State — ready for Google Business Profile integration) ============ --}}
    @php
        $googleReviews = [];
        $googleRating = 0;
        $googleReviewCount = 0;
        $googleReviewUrl = $c('contact.google_review_url', 'https://www.google.com/search?q=Ankesh+Online+Store');
    @endphp
    <section class="bg-white beres-reveal border-t" style="border-color:#E8F0E5;">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl md:text-3xl text-[#171717]" style="font-weight:600;">{{ $c('sections.google_review_title', 'Ulasan Google Pelanggan') }}</h2>
                    @if ($googleReviewCount > 0)
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex items-center gap-0.5 text-[#EAB308]">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($googleRating))
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    @elseif ($i - $googleRating < 1 && $i - $googleRating > 0)
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" opacity="0.5"/></svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-lg font-bold text-[#171717]">{{ number_format($googleRating, 1) }}</span>
                            <span class="text-sm text-[#737373]">({{ number_format($googleReviewCount) }} ulasan)</span>
                        </div>
                    @endif
                </div>
                <a href="{{ $googleReviewUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                   style="background-color:#2D5A27; border-radius:10px;"
                   onmouseover="this.style.backgroundColor='#1E3D1A';"
                   onmouseout="this.style.backgroundColor='#2D5A27';">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Tulis Ulasan
                </a>
            </div>

            {{-- Empty State --}}
            @if (empty($googleReviews))
                <div class="flex flex-col items-center justify-center py-8 md:py-10 text-center">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4" style="background-color:#F5F9F3;">
                        <svg class="w-10 h-10 text-[#2D5A27]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[#171717] mb-2">Belum ada ulasan Google</h3>
                    <p class="text-sm text-[#737373] max-w-md mb-4">Ulasan dari Google Business Profile akan muncul di sini setelah terhubung. Bagikan pengalaman belanja Anda!</p>
                    <a href="{{ $googleReviewUrl }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold transition-all"
                       style="border:1.5px solid #2D5A27; color:#2D5A27; border-radius:10px;"
                       onmouseover="this.style.backgroundColor='#E8F0E5';"
                       onmouseout="this.style.backgroundColor='transparent';">
                        Jadilah yang pertama memberi ulasan
                    </a>
                </div>
            @else
                {{-- Review Cards Horizontal Scroll --}}
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-none" style="-ms-overflow-style:none; scrollbar-width:none;">
                    @foreach ($googleReviews as $review)
                        <div class="shrink-0 w-[300px] md:w-[340px] p-5 bg-[#F9FAFB] beres-card" style="border-radius:14px;">
                            <div class="flex items-center gap-3 mb-3">
                                @if (!empty($review['avatar']))
                                    <img src="{{ $review['avatar'] }}" alt="{{ $review['name'] ?? 'Reviewer' }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <span class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm" style="background-color:#2D5A27; font-weight:600;">
                                        {{ strtoupper(mb_substr($review['name'] ?? 'A', 0, 1)) }}
                                    </span>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#171717] truncate">{{ $review['name'] ?? 'Anonim' }}</p>
                                    <p class="text-xs text-[#737373]">{{ $review['time'] ?? '' }}</p>
                                </div>
                                <div class="flex items-center gap-0.5 text-[#EAB308] shrink-0">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= ($review['rating'] ?? 5))
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-gray-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-[#404040] leading-relaxed line-clamp-4">{{ $review['text'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- See All Link --}}
                <div class="mt-8 text-center">
                    <a href="{{ $googleReviewUrl }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold transition-all"
                       style="border:1.5px solid #E8F0E5; color:#2D5A27; border-radius:10px;"
                       onmouseover="this.style.borderColor='#2D5A27';"
                       onmouseout="this.style.borderColor='#E8F0E5';">
                        Lihat Semua Ulasan
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- ============ FAQ (Only show if real FAQs exist in DB) ============ --}}
    @if ($faqsDb->isNotEmpty())
        <section class="bg-white beres-reveal">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 md:px-10 py-6 md:py-8">
                <h2 class="text-2xl md:text-3xl text-[#171717] mb-8" style="font-weight:600;">{{ $c('sections.faq_title', 'FAQ') }}</h2>

                <div class="space-y-3">
                    @foreach ($faqsDb as $i => $faq)
                        <details class="group border overflow-hidden transition-all hover:border-[#2D5A27]" style="border-color:#E8F0E5; border-radius:14px;" @if($i === 0) open @endif>
                            <summary class="flex items-center justify-between cursor-pointer list-none px-5 md:px-6 py-4 md:py-5 hover:bg-[#F5F9F3] transition-colors">
                                <span class="text-sm md:text-base text-[#171717] pr-4" style="font-weight:500;">{{ $faq->question }}</span>
                                <span class="text-2xl transition-transform duration-300 group-open:rotate-45 shrink-0 leading-none" style="color:#2D5A27;">+</span>
                            </summary>
                            <div class="px-5 md:px-6 pb-4 md:pb-5 text-sm md:text-base text-[#404040] leading-relaxed prose prose-sm max-w-none">{!! $faq->answer !!}</div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ LATEST BLOGS & ARTICLES (Only show if real blogs exist in DB) ============ --}}
    @if ($blogsDb->isNotEmpty())
        <section class="bg-gradient-to-b from-white to-[#F7FAF6] py-8 md:py-12 border-t border-[#E8F0E5]/60 beres-reveal">
            <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 md:mb-12">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-[#E8F0E5] text-[#2D5A27]">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            <span>{{ $c('sections.blog_eyebrow', 'Artikel & Wawasan') }}</span>
                        </div>
                        <h2 class="text-2xl md:text-3xl text-[#171717]" style="font-weight:600;">{{ $c('sections.blog_title', 'Artikel & Tips Terbaru') }}</h2>
                    </div>
                    <a href="{{ route('shop.blog.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#2D5A27] hover:text-[#1E3A1E] transition-colors group">
                        <span>Lihat Semua Artikel</span>
                        <span class="text-base group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach ($blogsDb as $i => $page)
                        @php
                            $blogTitle    = $page->title ?? '';
                            $blogCategory = $page->category?->name ?? 'Artikel & Tips';
                            $blogExcerpt  = \Illuminate\Support\Str::limit(strip_tags($page->content ?? ''), 120);
                            $blogUrl      = $page->slug ? route('shop.blog.show', $page->slug) : '#';
                            $blogDate     = $page->published_at ? $page->published_at->format('d F Y') : ($page->created_at ? $page->created_at->format('d F Y') : '');
                            $blogImg      = $page->thumbnail_url;
                        @endphp
                        <article class="group flex flex-col bg-white rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 h-full cursor-pointer" onclick="window.location.href='{{ $blogUrl }}';">
                            {{-- Unified 16:9 Thumbnail Banner --}}
                            <a href="{{ $blogUrl }}" class="block relative w-full overflow-hidden bg-gradient-to-br from-[#F5F9F3] via-[#E8F0E5] to-[#D5E5CE] shrink-0" style="aspect-ratio:16/9; height:200px; max-height:220px;">
                                @if ($blogImg)
                            <img src="{{ $blogImg }}" alt="{{ $blogTitle }}" class="w-full h-full object-fill scale-[1.02] group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                @else
                                    <div class="w-full h-full flex flex-col justify-between p-5 bg-gradient-to-br from-[#F5F9F3] via-[#E8F0E5] to-[#D5E5CE] group-hover:scale-105 transition-transform duration-500" style="width:100%; height:100%;">
                                        <span class="inline-block px-3 py-1 text-xs font-semibold text-[#2D5A27] bg-white/90 backdrop-blur-md rounded-full shadow-xs w-fit">
                                            {{ $blogCategory }}
                                        </span>
                                        <div class="w-10 h-10 rounded-xl bg-white/80 backdrop-blur-md flex items-center justify-center text-[#2D5A27] shadow-xs">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                    </div>
                                @endif

                                @if ($blogImg)
                                    <span class="absolute top-3 left-3 px-3 py-1 text-xs font-semibold text-[#2D5A27] bg-white/90 backdrop-blur-md rounded-full shadow-xs">
                                        {{ $blogCategory }}
                                    </span>
                                @endif
                            </a>

                            <div class="p-6 flex flex-col flex-1 justify-between">
                                <div>
                                    @if ($blogDate)
                                        <div class="flex items-center gap-2 text-xs font-medium text-zinc-500 mb-3">
                                            <svg class="w-3.5 h-3.5 text-[#2D5A27]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <span>{{ $blogDate }}</span>
                                        </div>
                                    @endif

                                    <a href="{{ $blogUrl }}" class="block">
                                        <h3 class="text-lg md:text-xl font-bold text-[#171717] group-hover:text-[#2D5A27] transition-colors leading-snug line-clamp-2">
                                            {{ $blogTitle }}
                                        </h3>
                                    </a>

                                    <p class="mt-2.5 text-sm text-zinc-600 leading-relaxed line-clamp-3">
                                        {{ $blogExcerpt }}
                                    </p>
                                </div>

                                <div class="pt-6 mt-auto">
                                    <a href="{{ $blogUrl }}"
                                       class="w-full h-10 px-4 text-xs font-bold tracking-wider uppercase transition-all duration-200 flex items-center justify-between shadow-xs"
                                       style="background-color:#F5F9F3; color:#2D5A27; border:none; border-radius:10px;"
                                       onmouseover="this.style.backgroundColor='#E8F0E5';"
                                       onmouseout="this.style.backgroundColor='#F5F9F3';">
                                        <span>Detail Selengkapnya</span>
                                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Map & Contact section ditampilkan lewat layout utama
         (shop::components.layouts.map-section) pada semua halaman,
         jadi tidak perlu di-include lagi di sini. --}}
</x-shop::layouts>
