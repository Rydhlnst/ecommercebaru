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

        @stack('meta')

        <link rel="icon" type="image/png" href="/images/ankesh-mart-logo.png" />

        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;500;600;700&display=swap"
        />

        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;500;600;700&display=swap"
        />

        @stack('styles')

        <style>
            {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
        </style>

        <!-- Google Translate: hide the default banner bar -->
        <style>
            .goog-te-banner-frame, .goog-te-gadget { display: none !important; }
            body { top: 0 !important; }
        </style>

        <!-- Global: justify all description text -->
        <style>
            .prose, .prose p, .text-justify, [class*="description"] p, [class*="deskripsi"] p {
                text-align: justify;
            }
        </style>

        @if(core()->getConfigData('general.content.speculation_rules.enabled'))
            <script type="speculationrules">
                @json(core()->getSpeculationRules(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            </script>
        @endif

        <style>
            html, body, #app {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
                background-color: var(--cream, #FAFAFA);
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

        <!-- App -->
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


            <!-- Page Services Blade Component (skip on homepage — rendered inline before Google Reviews) -->
            @if ($hasFeature && !request()->is('/'))
                <x-shop::layouts.services />
            @endif

            <!-- Page Footer Blade Component -->
            @if ($hasFooter)
                <x-shop::layouts.footer />
            @endif
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
                var cookieVal = '/auto/' + lang;
                document.cookie = 'googtrans=' + cookieVal + '; expires=' + expires.toUTCString() + '; path=/';
                document.cookie = 'googtrans=' + cookieVal + '; expires=' + expires.toUTCString() + '; path=/; domain=.' + location.hostname;
                location.reload();
            }

            // Highlight active language button
            (function() {
                var match = document.cookie.match(/googtrans=\/auto\/([a-z]+)/);
                var currentLang = match ? match[1] : 'id';
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('[data-gt-lang]').forEach(function(btn) {
                        var isActive = btn.dataset.gtLang === currentLang;
                        btn.classList.toggle('font-bold', isActive);
                        btn.classList.toggle('text-ink', isActive);
                        btn.classList.toggle('text-stone', !isActive);
                    });
                });
            })();
        </script>
        <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
    </body>
</html>
