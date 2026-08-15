@php
    $channel = core()->getCurrentChannel();
@endphp

<!-- SEO Meta Content -->
@push ('meta')
    <meta
        name="title"
        content="{{ $channel->home_seo['meta_title'] ?? '' }}"
    />

    <meta
        name="description"
        content="{{ $channel->home_seo['meta_description'] ?? '' }}"
    />

    <meta
        name="keywords"
        content="{{ $channel->home_seo['meta_keywords'] ?? '' }}"
    />
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

    {{-- HERO BANNER --}}
    @php
        $heroImage = \App\Models\SiteSetting::getValue('hero_banner_image') ?: '/images/hero-products.jpg';
    @endphp
    <section class="relative w-full bg-[#fbf9f5] overflow-hidden border-b border-mist">
        <div class="mx-auto max-w-[1600px] flex items-center justify-center">
            <img
                src="{{ $heroImage }}"
                alt="Ankesh Mart - Himalayan Salt, Chia Seeds, Almonds & More"
                class="w-full h-auto max-h-[500px] md:max-h-[580px] object-contain object-center"
                loading="eager"
            >
        </div>
    </section>

    {{-- Category tiles quick-links --}}
    <section class="bg-cream">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-16 md:py-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <p class="eyebrow mb-3">Shop by Category</p>
                    <h2 class="font-serif text-4xl md:text-5xl text-ink">Our Collections</h2>
                </div>
                <a href="{{ route('shop.product_or_category.index', 'root') }}" class="hidden md:inline-block ghost-button">View all →</a>
            </div>

            <div class="grid gap-6 md:gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $homeCategories = [
                        ['name' => 'Himalayan Salt & Gourmet Sea Salt', 'slug' => 'spices-and-salts', 'icon' => '<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714a2.25 2.25 0 00.659 1.591L19 14.5m-4.25-11.396c.251.023.501.05.75.082M5 14.5l-.94 1.41a2.25 2.25 0 00.906 3.294l2.034.678M19 14.5l.94 1.41a2.25 2.25 0 01-.906 3.294l-2.034.678"/></svg>'],
                        ['name' => 'Spices & Seasoning', 'slug' => 'recipe-mix-masalas', 'icon' => '<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>'],
                        ['name' => 'Leather Goods', 'slug' => 'root', 'icon' => '<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/></svg>'],
                        ['name' => 'Nuts & Seeds', 'slug' => 'seeds-and-super-foods', 'icon' => '<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>'],
                        ['name' => 'Pure Himalayan Honey', 'slug' => 'grannis-signature-kits', 'icon' => '<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75l-1.5.75a3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0L3 16.5m15-3.379a48.474 48.474 0 00-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 013 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 016 13.12"/></svg>'],
                        ['name' => 'Coffee & Tea', 'slug' => 'oats-with-nuts', 'icon' => '<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>'],
                    ];
                @endphp

                @foreach ($homeCategories as $category)
                    <a href="{{ route('shop.product_or_category.index', $category['slug']) }}" class="group block">
                        <div class="relative aspect-[4/3] bg-canvas overflow-hidden rounded-lg flex items-center justify-center text-ink">
                            <div class="text-stone group-hover:text-ink transition-colors">
                                {!! $category['icon'] !!}
                            </div>
                        </div>
                        <p class="mt-4 text-[13px] tracking-widelg uppercase text-ink group-hover:text-clay transition-colors text-center">
                            {{ $category['name'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CMS-driven customizations --}}
    <div class="bg-cream">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 space-y-16 md:space-y-24 py-8">
            @foreach ($customizations as $customization)
                @php ($data = $customization->options) @endphp

                @switch ($customization->type)
                    @case ($customization::IMAGE_CAROUSEL)
                        <x-shop::carousel
                            :options="$data"
                            aria-label="{{ trans('shop::app.home.index.image-carousel') }}"
                        />
                        @break

                    @case ($customization::STATIC_CONTENT)
                        @if (! empty($data['css']))
                            @push ('styles')
                                <style>{!! $data['css'] !!}</style>
                            @endpush
                        @endif
                        @if (! empty($data['html']))
                            {!! $data['html'] !!}
                        @endif
                        @break

                    @case ($customization::CATEGORY_CAROUSEL)
                        <x-shop::categories.carousel
                            :title="$data['title'] ?? ''"
                            :src="route('shop.api.categories.index', $data['filters'] ?? [])"
                            :navigation-link="route('shop.home.index')"
                            aria-label="{{ trans('shop::app.home.index.categories-carousel') }}"
                        />
                        @break

                    @case ($customization::PRODUCT_CAROUSEL)
                        <x-shop::products.carousel
                            :title="$data['title'] ?? ''"
                            :src="route('shop.api.products.index', $data['filters'] ?? [])"
                            :navigation-link="route('shop.search.index', $data['filters'] ?? [])"
                            aria-label="{{ trans('shop::app.home.index.product-carousel') }}"
                        />
                        @break
                @endswitch
            @endforeach
        </div>
    </div>

    {{-- Editorial split band --}}
    <section class="bg-canvas">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-20 md:py-28 grid gap-12 lg:grid-cols-2 items-center">
            <div class="relative aspect-[4/3] bg-mist overflow-hidden order-2 lg:order-1">
                <img
                    src="https://placehold.co/1000x750/D9C7A7/3C2E22?text=Placeholder+Story"
                    alt="Placeholder story"
                    class="absolute inset-0 w-full h-full object-cover"
                    loading="lazy"
                >
            </div>

            <div class="max-w-lg order-1 lg:order-2 lg:pl-8">
                <p class="eyebrow mb-4">Placeholder — Journal</p>
                <h2 class="font-serif text-4xl md:text-5xl leading-tight text-ink">
                    Consectetur adipiscing elit, sed do eiusmod.
                </h2>
                <p class="mt-5 text-base text-cocoa/80">
                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.
                </p>
                <a href="#" class="mt-8 inline-block ghost-button">Read the placeholder article →</a>
            </div>
        </div>
    </section>

    {{-- Google Maps Store Location Section --}}
    <section class="bg-canvas border-t border-mist py-16 md:py-24">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14">
            <div class="mb-8 text-center max-w-2xl mx-auto">
                <p class="eyebrow mb-2">Visit Our Store</p>
                <h2 class="font-serif text-3xl md:text-4xl text-ink">Store Location & Directions</h2>
                <p class="mt-3 text-sm text-stone">Find our location easily on Google Maps below.</p>
            </div>
            
            <div class="w-full h-[400px] md:h-[500px] rounded-2xl overflow-hidden shadow-lg border border-mist relative bg-mist">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.81956135000001!3d-6.194741399999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5390917b759%3A0x6b45e67356080477!2sJakarta!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Google Maps Store Location"
                    class="w-full h-full border-0"
                ></iframe>
            </div>
        </div>
    </section>

    {{-- Value props strip --}}
    <section class="bg-cream border-t border-mist">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-4 text-center">
            @foreach ([
                ['Free shipping',     'Placeholder — on orders over $99'],
                ['3-year warranty',   'Placeholder — quality guaranteed'],
                ['Easy returns',      'Placeholder — 30-day window'],
                ['Responsibly made',  'Placeholder — certified materials'],
            ] as $prop)
                <div>
                    <p class="font-serif text-2xl text-ink">{{ $prop[0] }}</p>
                    <p class="mt-2 text-sm text-stone">{{ $prop[1] }}</p>
                </div>
            @endforeach
        </div>
    </section>
</x-shop::layouts>
