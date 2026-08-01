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

        <title>{{ $title ?? '' }}</title>

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

        @if(core()->getConfigData('general.content.speculation_rules.enabled'))
            <script type="speculationrules">
                @json(core()->getSpeculationRules(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            </script>
        @endif

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

        <!-- Beres Commerce -->
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

            <!-- Store Location + Map (semua page) -->
            @include('shop::components.layouts.map-section')

            <!-- Page Footer Blade Component -->
            @if ($hasFooter)
                <x-shop::layouts.footer />
            @endif

            <!-- Mobile Bottom Navigation -->
            @include('shop::components.layouts.bottom-nav')
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
                    pageLanguage: 'en',
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
                var currentLang = match ? match[1] : 'en';
                var langLabels = { en: 'EN', id: 'ID' };
                document.addEventListener('DOMContentLoaded', function() {
                    var label = document.getElementById('current-lang-label');
                    if (label) label.textContent = langLabels[currentLang] || 'EN';

                    document.querySelectorAll('select[data-lang-select]').forEach(function(sel) {
                        sel.value = currentLang;
                    });
                });
            })();
        </script>
        <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>
    </body>
</html>
