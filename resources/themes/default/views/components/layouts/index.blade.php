@props([
    'hasHeader'  => true,
    'hasFeature' => true,
    'hasFooter'  => true,
])

<!DOCTYPE html>

<html
    lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()->direction }}"
>
    <head>

        {!! view_render_event('bagisto.shop.layout.head.before') !!}

        {{-- ============ Nama tab browser — editable dari dashboard ============ --}}
        @php
            $seoSiteName  = trim((string) core()->getConfigData('beres_storefront.seo.site_name'));
            $seoHomeTitle = trim((string) core()->getConfigData('beres_storefront.seo.home_title'));
            $seoSuffix    = trim((string) core()->getConfigData('beres_storefront.seo.title_suffix'));

            $channel  = core()->getCurrentChannel();
            $siteName = $seoSiteName !== '' ? $seoSiteName : ($channel->name ?? config('app.name'));

            if (isset($title) && trim((string) $title) !== '') {
                $pageTitle = trim((string) $title);
            } elseif ($seoHomeTitle !== '') {
                $pageTitle = $seoHomeTitle;
            } elseif (trim((string) ($channel->home_seo['meta_title'] ?? '')) !== '') {
                $pageTitle = $channel->home_seo['meta_title'];
            } else {
                $pageTitle = $siteName;
            }

            if ($seoSuffix !== '' && ! str_contains($pageTitle, $seoSuffix) && ! str_contains($pageTitle, $siteName)) {
                $pageTitle .= ' '.$seoSuffix;
            }
        @endphp

        <title>{{ $pageTitle }}</title>

        <meta charset="UTF-8">

        <meta
            http-equiv="X-UA-Compatible"
            content="IE=edge"
        >
        <meta
            http-equiv="content-language"
            content="{{ app()->getLocale() }}"
        >

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >
        <meta
            name="base-url"
            content="{{ url()->to('/') }}"
        >
        <meta
            name="currency"
            content="{{ core()->getCurrentCurrency()->toJson() }}"
        >
        <meta
            name="generator"
            content="{{ config('app.name') }}"
        >
        <link rel="icon" type="image/png" href="{{ asset('images/ankesh-mart-logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/ankesh-mart-logo.png') }}">

        {{-- ============ Global SEO defaults (page @push('meta') can override) ============ --}}
        @php
            $channel      = core()->getCurrentChannel();
            $siteName     = $channel->name ?? config('app.name');
            $defaultTitle = $title ?? ($channel->home_seo['meta_title'] ?? $siteName);
            $defaultDesc  = $channel->home_seo['meta_description']
                ?? 'Pasar online untuk bahan segar dan pantry esensial — langsung dari petani dan produsen lokal, diantar hari itu juga.';
            $defaultKeys  = $channel->home_seo['meta_keywords']
                ?? 'belanja bahan segar, pasar online, sayur, buah, daging, roti, minuman, bumbu, snack sehat';
            $ogImage      = $channel->logo_url ?? null;
            $canonicalUrl = url()->current();
        @endphp

        <meta name="description" content="{{ $defaultDesc }}">
        <meta name="keywords"    content="{{ $defaultKeys }}">
        <meta name="robots"      content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
        <meta name="theme-color" content="#171717">
        <meta name="author"      content="{{ $siteName }}">

        <link rel="canonical" href="{{ $canonicalUrl }}">

        {{-- Open Graph --}}
        <meta property="og:site_name"  content="{{ $siteName }}">
        <meta property="og:type"       content="website">
        <meta property="og:locale"     content="{{ str_replace('-', '_', app()->getLocale()) }}">
        <meta property="og:title"      content="{{ $defaultTitle }}">
        <meta property="og:description" content="{{ $defaultDesc }}">
        <meta property="og:url"        content="{{ $canonicalUrl }}">
        @if ($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif

        {{-- Twitter Card --}}
        <meta name="twitter:card"        content="summary_large_image">
        <meta name="twitter:title"       content="{{ $defaultTitle }}">
        <meta name="twitter:description" content="{{ $defaultDesc }}">
        @if ($ogImage)<meta name="twitter:image" content="{{ $ogImage }}">@endif

        {{-- hreflang alternates (only if channel has >1 locale) --}}
        @if (($locales = $channel->locales()->get()) && $locales->count() > 1)
            @foreach ($locales as $locale)
                <link rel="alternate" hreflang="{{ $locale->code }}" href="{{ $canonicalUrl }}?locale={{ $locale->code }}">
            @endforeach
            <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">
        @endif

        {{-- Organization JSON-LD (site-wide) --}}
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $siteName,
            'url'      => url('/'),
            'logo'     => $ogImage ?? url('/'),
            'sameAs'   => array_values(array_filter([
                core()->getConfigData('general.content.social.facebook_link'),
                core()->getConfigData('general.content.social.instagram_link'),
                core()->getConfigData('general.content.social.twitter_link'),
                core()->getConfigData('general.content.social.linked_in_link'),
                core()->getConfigData('general.content.social.youtube_link'),
            ])),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
        </script>
        {{-- ============ end Global SEO defaults ============ --}}

        @stack('meta')

        <link rel="icon" type="image/png" href="/images/ankesh-mart-logo.png" />

        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

        <link
            rel="preconnect"
            href="https://fonts.googleapis.com"
            crossorigin
        />

        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin
        />

        <link
            rel="preload" as="style"
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap"
        />

        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap"
        />

        @stack('styles')

        <style>
            {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
        </style>

        <!-- Google Translate: hide the default banner bar -->
        <style>
            .goog-te-banner-frame, .goog-te-gadget, #goog-gt-tt { display: none !important; }
            body { top: 0 !important; }
        </style>

        <!-- Custom scrollbar -->
        <style>
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: var(--cream); }
            ::-webkit-scrollbar-thumb { background: var(--mist); border-radius: 3px; }
            ::-webkit-scrollbar-thumb:hover { background: var(--clay); }
            * { scrollbar-width: thin; scrollbar-color: var(--mist) var(--cream); }
        </style>

        <style>
            /* Bellroy-inspired tokens */
            :root {
                --ink:    #171717;
                --cocoa:  #404040;
                --clay:   #A3A3A3;
                --cream:  #FAFAFA;
                --canvas: #F4F4F4;
                --sand:   #E5E5E5;
                --mist:   #D4D4D4;
                --stone:  #737373;
            }

            html, body {
                background-color: var(--cream);
                color: var(--ink);
                font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
                font-weight: 400;
                letter-spacing: -0.005em;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background-color: var(--ink);
                color: var(--cream);
                padding: 14px 28px;
                border-radius: 999px;
                font-size: 13px;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                font-weight: 500;
                transition: background-color 0.3s ease;
            }
            .btn-primary:hover {
                background-color: var(--cocoa);
                color: var(--cream);
            }

            a { color: inherit; text-decoration: none; }
            a:hover { color: var(--clay); }

            /* Scroll-reveal for sections */
            .beres-reveal { opacity: 0; transform: translateY(24px); transition: opacity .7s ease-out, transform .7s cubic-bezier(.2,.7,.2,1); }
            .beres-reveal.is-visible { opacity: 1; transform: none; }
            @media (prefers-reduced-motion: reduce) { .beres-reveal { opacity: 1; transform: none; transition: none; } }

            /* Product card polish */
            .beres-card { transition: transform .35s cubic-bezier(.2,.7,.2,1), box-shadow .35s ease, border-color .35s ease; }
            .beres-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(45,90,39,0.08); border-color: #C8DBBE !important; }

            /* Button ripple-ish press */
            .beres-btn { position: relative; transition: transform .15s ease, background-color .25s ease, opacity .2s ease; }
            .beres-btn:active { transform: scale(.97); }

            /* Fade page-in on load */
            @keyframes beresFadeIn { from { opacity: 0; } to { opacity: 1; } }
            main#main { animation: beresFadeIn .4s ease-out; }
        </style>

        {{-- Search debounce + normalize (case-insensitive, strip symbols) --}}
        <script>
        (function() {
            var debounceTimer;
            function normalizeQuery(q) {
                return q.toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, ' ').trim();
            }
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('form[role="search"]').forEach(function(form) {
                    var input = form.querySelector('input[name="query"]');
                    if (!input) return;

                    form.addEventListener('submit', function(e) {
                        var val = normalizeQuery(input.value);
                        if (!val) { e.preventDefault(); return; }
                        input.value = val;
                    });

                    var timer;
                    input.addEventListener('input', function() {
                        clearTimeout(timer);
                        timer = setTimeout(function() {
                            var val = normalizeQuery(input.value);
                            if (val && val.length >= 2) {
                                input.value = val;
                                form.submit();
                            }
                        }, 500);
                    });

                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            clearTimeout(timer);
                            var val = normalizeQuery(input.value);
                            if (val) { input.value = val; }
                        }
                    });
                });
            });
        })();
        </script>

        @if(core()->getConfigData('general.content.speculation_rules.enabled'))
            <script type="speculationrules">
                @json(core()->getSpeculationRules(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            </script>
        @endif

        <style>
            html, body, #app {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
                background-color: #2D5A27 !important;
            }
        </style>

        {!! view_render_event('bagisto.shop.layout.head.after') !!}

    </head>

    <body>
        {!! view_render_event('bagisto.shop.layout.body.before') !!}

        <a
            href="#main"
            class="skip-to-main-content-link"
        >
            Skip to main content
        </a>

        <!-- Ankesh Mart -->
        <div id="app">
            <!-- Flash Message Blade Component -->
            <x-shop::flash-group />

            <!-- Confirm Modal Blade Component -->
            <x-shop::modal.confirm />

            <!-- Page Header Blade Component -->
            @if ($hasHeader)
                <x-shop::layouts.header />
            @endif

            @if(
                core()->getConfigData('general.gdpr.settings.enabled')
                && core()->getConfigData('general.gdpr.cookie.enabled')
            )
                <x-shop::layouts.cookie />
            @endif

            {!! view_render_event('bagisto.shop.layout.content.before') !!}

            <!-- Page Content Blade Component -->
            <main id="main" class="bg-cream">
                {{ $slot }}
            </main>

            {!! view_render_event('bagisto.shop.layout.content.after') !!}


            <!-- Page Services Blade Component -->
            @if ($hasFeature)
                <x-shop::layouts.services />
            @endif

            <!-- Page Footer Blade Component -->
            @if ($hasFooter)
                <x-shop::layouts.footer />
            @endif

            <!-- Mobile Bottom Navigation -->
            @include('shop::components.layouts.bottom-nav')

            <!-- Custom session cart drawer (AdminProduct catalogue) -->
            @include('shop::components.layouts.cart-drawer')

            <!-- Fullscreen Search Overlay (mobile-first, also works on desktop) -->
            <v-search-overlay></v-search-overlay>
        </div>

        {!! view_render_event('bagisto.shop.layout.body.after') !!}

        <!-- WebMCP Tool Registration For AI Agents -->
        <x-shop::layouts.webmcp />

        @stack('scripts')

        {{-- Global scroll-reveal observer — defined globally so mountApp() can call it AFTER Vue re-renders the DOM --}}
        <script>
            window.initReveal = function(){
                var els = document.querySelectorAll('.beres-reveal');
                if (!els.length) return;
                if (!('IntersectionObserver' in window)) {
                    els.forEach(function(e){ e.classList.add('is-visible'); });
                    return;
                }
                var io = new IntersectionObserver(function(entries){
                    entries.forEach(function(en){
                        if (en.isIntersecting) {
                            en.target.classList.add('is-visible');
                            io.unobserve(en.target);
                        }
                    });
                }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
                els.forEach(function(e){ io.observe(e); });
            };
        </script>

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.before') !!}

        {{-- ═══════════════════════════════════════════════════════════════
             Fullscreen Search Overlay — Alpine-free, pure Vue 3 component
             ═══════════════════════════════════════════════════════════════ --}}
        <script type="text/x-template" id="v-search-overlay-template">
            <div
                v-show="isOpen"
                class="search-overlay"
                @keydown.escape.window="close"
            >
                {{-- Overlay backdrop --}}
                <div class="search-overlay__backdrop" @click="close"></div>

                {{-- Overlay panel --}}
                <div class="search-overlay__panel">
                    {{-- Search header --}}
                    <div class="search-overlay__header">
                        <div class="search-overlay__input-wrap">
                            <svg class="search-overlay__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input
                                ref="searchInput"
                                v-model="query"
                                type="text"
                                placeholder="Cari produk..."
                                class="search-overlay__input"
                                @input="onInput"
                                @keydown.enter.prevent="goToSearch"
                                autocomplete="off"
                            />
                            <button
                                v-if="query.length > 0"
                                @click="query = ''; results = []; suggestions = []"
                                class="search-overlay__clear"
                                aria-label="Hapus"
                            >&times;</button>
                        </div>
                        <button @click="close" class="search-overlay__close" aria-label="Tutup">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Results body --}}
                    <div class="search-overlay__body" ref="resultsBody">
                        {{-- Loading --}}
                        <div v-if="isLoading" class="search-overlay__loading">
                            <div class="search-overlay__spinner"></div>
                        </div>

                        {{-- Suggestions (typo correction) --}}
                        <div v-else-if="suggestions.length" class="search-overlay__suggestions">
                            <p class="text-sm text-[#737373] mb-3">
                                Mungkin yang Anda maksud:
                                <button
                                    v-for="s in suggestions"
                                    :key="s"
                                    @click="query = s; doSearch()"
                                    class="ml-2 text-[#2D5A27] font-semibold hover:underline"
                                >@{{ s }}</button>
                            </p>
                        </div>

                        {{-- Product results --}}
                        <div v-else-if="results.length" class="search-overlay__products">
                            <div
                                v-for="product in results"
                                :key="product.id"
                                class="search-overlay__product"
                                @click="goToProduct(product)"
                            >
                                <div class="search-overlay__product-img">
                                    <img
                                        :src="product.base_image?.small_image_url || product.base_image?.medium_image_url || ''"
                                        :alt="product.name"
                                        loading="lazy"
                                    />
                                </div>
                                <div class="search-overlay__product-info">
                                    <p class="search-overlay__product-name">@{{ product.name }}</p>
                                    <p class="search-overlay__product-price" v-html="product.price_html || product.min_price"></p>
                                </div>
                                <svg class="search-overlay__product-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                            </div>

                            {{-- View all results link --}}
                            <button
                                @click="goToSearch"
                                class="search-overlay__view-all"
                            >
                                Lihat semua hasil untuk "<span class="font-semibold">@{{ query }}</span>"
                            </button>
                        </div>

                        {{-- Empty state --}}
                        <div v-else-if="query.length >= 2 && !isLoading" class="search-overlay__empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#D4D4D4" stroke-width="1.5" width="48" height="48">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <p class="text-[#737373] mt-3">Produk tidak ditemukan</p>
                        </div>

                        {{-- Initial hint --}}
                        <div v-else class="search-overlay__hint">
                            <p class="text-sm text-[#737373]">Ketik untuk mulai mencari</p>
                        </div>
                    </div>
                </div>
            </div>
        </script>

        {{-- ═══════════════════════════════════════════════════════════════
             Search Overlay Styles
             ═══════════════════════════════════════════════════════════════ --}}
        <style>
            .search-overlay {
                position: fixed;
                inset: 0;
                z-index: 9999;
                display: flex;
                align-items: flex-start;
                justify-content: center;
            }
            .search-overlay__backdrop {
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                animation: searchFadeIn .2s ease-out;
            }
            .search-overlay__panel {
                position: relative;
                width: 100%;
                max-width: 640px;
                height: 100dvh;
                background: #fff;
                display: flex;
                flex-direction: column;
                animation: searchSlideIn .25s cubic-bezier(.2,.7,.2,1);
                box-shadow: 0 8px 40px rgba(0,0,0,0.12);
            }
            @media (max-width: 640px) {
                .search-overlay__panel {
                    max-width: 100%;
                }
            }
            .search-overlay__header {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 16px 20px;
                border-bottom: 1px solid #E8F0E5;
                background: #fff;
                flex-shrink: 0;
            }
            .search-overlay__input-wrap {
                flex: 1;
                display: flex;
                align-items: center;
                gap: 10px;
                background: #F4F4F4;
                border-radius: 12px;
                padding: 0 14px;
                height: 48px;
            }
            .search-overlay__icon {
                width: 20px;
                height: 20px;
                flex-shrink: 0;
                color: #737373;
            }
            .search-overlay__input {
                flex: 1;
                border: none;
                background: none;
                outline: none;
                font-size: 16px;
                color: #171717;
                font-family: inherit;
                padding: 0;
            }
            .search-overlay__input::placeholder {
                color: #A3A3A3;
            }
            .search-overlay__clear {
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #D4D4D4;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                font-size: 14px;
                color: #fff;
                line-height: 1;
                flex-shrink: 0;
                transition: background .15s;
            }
            .search-overlay__clear:hover {
                background: #A3A3A3;
            }
            .search-overlay__close {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: none;
                border: 1px solid #E8F0E5;
                border-radius: 10px;
                cursor: pointer;
                color: #404040;
                flex-shrink: 0;
                transition: background .15s, border-color .15s;
            }
            .search-overlay__close:hover {
                background: #F4F4F4;
                border-color: #D4D4D4;
            }
            .search-overlay__body {
                flex: 1;
                overflow-y: auto;
                overscroll-behavior: contain;
                padding: 16px 20px;
                -webkit-overflow-scrolling: touch;
            }
            .search-overlay__loading {
                display: flex;
                justify-content: center;
                padding: 40px 0;
            }
            .search-overlay__spinner {
                width: 28px;
                height: 28px;
                border: 3px solid #E8F0E5;
                border-top-color: #2D5A27;
                border-radius: 50%;
                animation: searchSpin .6s linear infinite;
            }
            .search-overlay__products {
                display: flex;
                flex-direction: column;
            }
            .search-overlay__product {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 12px;
                border-radius: 12px;
                cursor: pointer;
                transition: background .15s;
            }
            .search-overlay__product:hover {
                background: #F5F9F3;
            }
            .search-overlay__product-img {
                width: 52px;
                height: 52px;
                border-radius: 10px;
                overflow: hidden;
                flex-shrink: 0;
                background: #E8F0E5;
            }
            .search-overlay__product-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .search-overlay__product-info {
                flex: 1;
                min-width: 0;
            }
            .search-overlay__product-name {
                font-size: 14px;
                font-weight: 600;
                color: #171717;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                margin: 0;
            }
            .search-overlay__product-price {
                font-size: 13px;
                color: #2D5A27;
                font-weight: 600;
                margin: 2px 0 0;
            }
            .search-overlay__product-arrow {
                width: 16px;
                height: 16px;
                flex-shrink: 0;
                color: #D4D4D4;
            }
            .search-overlay__view-all {
                display: block;
                width: 100%;
                text-align: center;
                padding: 14px;
                margin-top: 8px;
                background: #F5F9F3;
                border: 1px solid #E8F0E5;
                border-radius: 12px;
                cursor: pointer;
                font-size: 14px;
                color: #2D5A27;
                font-weight: 500;
                transition: background .15s, border-color .15s;
            }
            .search-overlay__view-all:hover {
                background: #E8F0E5;
                border-color: #C8DBBE;
            }
            .search-overlay__empty,
            .search-overlay__hint,
            .search-overlay__suggestions {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 60px 20px;
                text-align: center;
            }
            @keyframes searchFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes searchSlideIn {
                from { transform: translateY(-20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            @keyframes searchSpin {
                to { transform: rotate(360deg); }
            }
        </style>

        @pushOnce('scripts')
        <script type="module">
            app.component('v-search-overlay', {
                template: '#v-search-overlay-template',

                data() {
                    return {
                        isOpen: false,
                        query: '',
                        results: [],
                        suggestions: [],
                        isLoading: false,
                        debounceTimer: null,
                        abortController: null,
                    };
                },

                methods: {
                    open() {
                        this.isOpen = true;
                        this.$nextTick(() => {
                            if (this.$refs.searchInput) {
                                this.$refs.searchInput.focus();
                            }
                        });
                        document.body.style.overflow = 'hidden';
                    },

                    close() {
                        this.isOpen = false;
                        this.query = '';
                        this.results = [];
                        this.suggestions = [];
                        this.isLoading = false;
                        document.body.style.overflow = '';
                    },

                    onInput() {
                        clearTimeout(this.debounceTimer);
                        if (this.abortController) {
                            this.abortController.abort();
                        }
                        if (this.query.length < 2) {
                            this.results = [];
                            this.suggestions = [];
                            return;
                        }
                        this.isLoading = true;
                        this.debounceTimer = setTimeout(() => {
                            this.doSearch();
                        }, 350);
                    },

                    doSearch() {
                        this.isLoading = true;
                        this.results = [];
                        this.suggestions = [];

                        if (this.abortController) {
                            this.abortController.abort();
                        }
                        this.abortController = new AbortController();

                        const url = `{{ route('shop.api.products.index') }}?query=${encodeURIComponent(this.query)}&limit=8`;

                        fetch(url, {
                            signal: this.abortController.signal,
                            headers: { 'Accept': 'application/json' }
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.isLoading = false;
                            this.results = data.data || [];
                        })
                        .catch(err => {
                            if (err.name !== 'AbortError') {
                                this.isLoading = false;
                            }
                        });
                    },

                    goToProduct(product) {
                        const urlKey = product.url_key || product.sku;
                        window.location.href = '/' + urlKey;
                    },

                    goToSearch() {
                        if (this.query.trim()) {
                            window.location.href = `{{ route('shop.search.index') }}?query=${encodeURIComponent(this.query.trim())}`;
                        }
                    },
                },

                mounted() {
                    window.__searchOverlayOpen = () => this.open();
                },
            });
        </script>
        @endPushOnce

        <script>

            /**
             * Mount the application as soon as the DOM is ready instead of waiting
             * for the `load` event. All `Vue` components are registered through
             * deferred `type="module"` scripts, which always finish executing
             * before `DOMContentLoaded` fires, so every component is available
             * by the time `app.mount()` runs. Mounting on `DOMContentLoaded`
             * avoids blocking the storefront behind every image/font download.
             */
            function mountApp() {
                app.mount("#app");
                // Run reveal AFTER Vue has cleared and re-rendered #app, so the
                // IntersectionObserver watches the live DOM elements, not stale ones.
                if (typeof window.initReveal === 'function') window.initReveal();

                // Re-trigger Google Translate after Vue renders dynamic content
                var match = document.cookie.match(/googtrans=\/auto\/([a-z]+)/);
                if (match && match[1] !== 'en') {
                    setTimeout(function() {
                        var gtFrame = document.querySelector('.goog-te-menu-frame');
                        if (gtFrame) {
                            var val = '/auto/' + match[1];
                            document.cookie = 'googtrans=' + val + '; expires=Thu, 01 Jan 2030 00:00:00 UTC; path=/';
                            document.cookie = 'googtrans=' + val + '; expires=Thu, 01 Jan 2030 00:00:00 UTC; path=/; domain=.' + location.hostname;
                            location.reload();
                        }
                    }, 2000);
                }
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", mountApp);
            } else {
                mountApp();
            }
        </script>

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.after') !!}

        <script type="text/javascript">
            {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
        </script>

        <!-- Google Translate -->
        <div id="google_translate_element" style="display:none"></div>
        <script>
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'id',
                    includedLanguages: 'en,id',
                    autoDisplay: false
                }, 'google_translate_element');
            }

            function setGoogleTranslateLang(lang) {
                var expires = new Date();
                expires.setFullYear(expires.getFullYear() + 1);
                var val = '/auto/' + lang;
                document.cookie = 'googtrans=' + val + '; expires=' + expires.toUTCString() + '; path=/';
                document.cookie = 'googtrans=' + val + '; expires=' + expires.toUTCString() + '; path=/; domain=.' + location.hostname;
                location.reload();
            }

            (function() {
                var match = document.cookie.match(/googtrans=\/auto\/([a-z]+)/);
                var currentLang = match ? match[1] : 'id';
                var flags = {
                    en: '<svg class="w-5 h-3.5 rounded-sm" viewBox="0 0 640 480"><path fill="#012169" d="M0 0h640v480H0z"/><path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 80 480H0v-60l239-178L0 64V0z"/><path fill="#C8102E" d="m424 0 244 181 32-1h78v62L457 241l217 158v61h-80L377 301 240 480h-20v-60l239-178L0 64V0z"/><path fill="#FFF" d="M241 0v480h160V0zM0 160v160h640V160z"/><path fill="#C8102E" d="M0 193v96h640v-96zM273 0v480h96V0z"/></svg>',
                    id: '<svg class="w-5 h-3.5 rounded-sm" viewBox="0 0 640 480"><path fill="#e70011" d="M0 0h640v240H0z"/><path fill="#fff" d="M0 240h640v240H0z"/></svg>'
                };
                document.addEventListener('DOMContentLoaded', function() {
                    var flagEl = document.getElementById('current-lang-flag');
                    if (flagEl && flags[currentLang]) {
                        flagEl.outerHTML = flags[currentLang].replace('class="', 'id="current-lang-flag" class="');
                    }
                });
            })();
        </script>
        <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>
    </body>
</html>
