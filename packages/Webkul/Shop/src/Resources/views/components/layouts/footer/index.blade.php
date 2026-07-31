{!! view_render_event('bagisto.shop.layout.footer.before') !!}

@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);

    $shopCategories = [
        ['name' => 'Grannis Signature Kits', 'slug' => 'grannis-signature-kits'],
        ['name' => 'Oats With Nuts', 'slug' => 'oats-with-nuts'],
        ['name' => 'Recipe Mix Masalas', 'slug' => 'recipe-mix-masalas'],
        ['name' => 'Seeds And Super Foods', 'slug' => 'seeds-and-super-foods'],
        ['name' => 'Spices And Salts', 'slug' => 'spices-and-salts'],
        ['name' => 'Desi Ghee', 'slug' => 'desi-ghee'],
        ['name' => 'Fry Mix Masala', 'slug' => 'fry-mix-masala'],
    ];
@endphp

<footer class="mt-24 bg-ink text-cream">
    <!-- Newsletter band -->
    @if (core()->getConfigData('customer.settings.newsletter.subscription'))
        <div class="border-b border-cocoa">
            <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-12 md:py-16">
                <div class="grid gap-10 md:grid-cols-2 md:items-center">
                    <div class="max-w-md">
                        <p class="text-lg font-semibold mb-2">Newsletter</p>
                        <p class="text-sm text-mist">
                            Be the first to hear about new products, exclusive events and online offers.
                        </p>
                    </div>

                    <div>
                        <x-shop::form
                            :action="route('shop.subscription.store')"
                            class="w-full"
                        >
                            <div class="flex items-center gap-3">
                                <x-shop::form.control-group.control
                                    type="email"
                                    class="flex-1 bg-cocoa/30 border border-stone rounded-lg text-cream placeholder:text-stone py-3 px-4 focus:ring-0 focus:outline-none focus:border-cream"
                                    name="email"
                                    rules="required|email"
                                    label="Email"
                                    placeholder="Email"
                                />

                                <button
                                    type="submit"
                                    class="bg-cocoa hover:bg-stone text-cream font-medium py-3 px-6 rounded-lg transition-colors"
                                >
                                    Join Us
                                </button>
                            </div>

                            <x-shop::form.control-group.error control-name="email" />
                        </x-shop::form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Footer Content -->
    <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-12 md:py-16">
        <div class="grid gap-10 md:grid-cols-5">
            <!-- Shop Links -->
            <div>
                <p class="text-lg font-semibold mb-4">SHOP</p>
                <ul class="grid gap-2 text-sm text-mist">
                    @foreach($shopCategories as $category)
                        <li>
                            <a href="{{ route('shop.product_or_category.index', $category['slug']) }}" class="hover:text-cream transition-colors">
                                {{ $category['name'] }}
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('shop.cms.page.show', 'contact-us') }}" class="hover:text-cream transition-colors">
                            Contact Us
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shop.cms.page.show', 'awards-and-certificates') }}" class="hover:text-cream transition-colors">
                            Awards and Certificates
                        </a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-cream transition-colors">
                            Blogs
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Useful Links -->
            <div>
                <p class="text-lg font-semibold mb-4">USEFUL LINKS</p>
                <ul class="grid gap-2 text-sm text-mist">
                    <li>
                        <a href="{{ route('shop.cms.page.show', 'privacy-policy') }}" class="hover:text-cream transition-colors">
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shop.cms.page.show', 'refund-policy') }}" class="hover:text-cream transition-colors">
                            Refund Policy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shop.cms.page.show', 'shipping-policy') }}" class="hover:text-cream transition-colors">
                            Shipping Policy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shop.cms.page.show', 'terms-of-service') }}" class="hover:text-cream transition-colors">
                            Terms of Service
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Details -->
            <div class="md:col-span-2">
                <p class="text-lg font-semibold mb-4">CONTACT DETAILS</p>
                <div class="grid gap-3 text-sm text-mist">
                    <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                    <div class="flex items-start gap-2">
                        <span class="mt-1">📍</span>
                        <p>235 ABBAS BLOCK MUSTAFA TOWN</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="mt-1">📞</span>
                        <p>042-33100001, +92 321 3246279, 03000974396</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="mt-1">✉️</span>
                        <p>granniskitchenofficial@gmail.com</p>
                    </div>
                </div>
            </div>

            <!-- Follow Us / Also Available On -->
            <div>
                <p class="text-lg font-semibold mb-4">ALSO AVAILABLE ON</p>
                <div class="flex flex-wrap gap-3">
                    <!-- Facebook -->
                    <a
                        href="{{ core()->getConfigData('general.social.facebook') ?: '#' }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center w-10 h-10 bg-cocoa/30 rounded-full hover:bg-cocoa transition-colors"
                        aria-label="Facebook"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>

                    <!-- YouTube -->
                    <a
                        href="{{ core()->getConfigData('general.social.youtube') ?: '#' }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center w-10 h-10 bg-cocoa/30 rounded-full hover:bg-cocoa transition-colors"
                        aria-label="YouTube"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>

                    <!-- Instagram -->
                    <a
                        href="{{ core()->getConfigData('general.social.instagram') ?: '#' }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center w-10 h-10 bg-cocoa/30 rounded-full hover:bg-cocoa transition-colors"
                        aria-label="Instagram"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>

                    <!-- Shopee -->
                    <a
                        href="{{ core()->getConfigData('general.social.shopee') ?: '#' }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center w-10 h-10 bg-cocoa/30 rounded-full hover:bg-cocoa transition-colors"
                        aria-label="Shopee"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </a>

                    <!-- Tokopedia -->
                    <a
                        href="{{ core()->getConfigData('general.social.tokopedia') ?: '#' }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center w-10 h-10 bg-cocoa/30 rounded-full hover:bg-cocoa transition-colors"
                        aria-label="Tokopedia"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </a>

                    <!-- BliBli -->
                    <a
                        href="{{ core()->getConfigData('general.social.blibli') ?: '#' }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center w-10 h-10 bg-cocoa/30 rounded-full hover:bg-cocoa transition-colors"
                        aria-label="BliBli"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                        </svg>
                    </a>

                    <!-- TikTok -->
                    <a
                        href="{{ core()->getConfigData('general.social.tiktok') ?: '#' }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center w-10 h-10 bg-cocoa/30 rounded-full hover:bg-cocoa transition-colors"
                        aria-label="TikTok"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V8.87a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.3z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom bar -->
    <div class="border-t border-cocoa">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-6 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-mist">
            <p>
                © {{ date('Y') }}, {{ config('app.name') }} Managed and Marketed by <a href="https://www.zcorebit.com/" target="_blank" class="underline hover:text-cream">Zcorebit.com</a>
            </p>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
