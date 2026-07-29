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

        @if ($favicon = core()->getCurrentChannel()->favicon_url)
            <link rel="icon" sizes="16x16" href="{{ $favicon }}" />
        @endif

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
    </body>
</html>
