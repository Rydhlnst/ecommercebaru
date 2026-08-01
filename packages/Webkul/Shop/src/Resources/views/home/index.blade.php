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

    {{-- HERO --}}
    <section class="relative w-full overflow-hidden">
        <img
            src="/images/hero-products.jpg"
            alt="Ankesh Mart - Himalayan Salt, Chia Seeds, Almonds & More"
            class="w-full h-auto object-cover max-h-[600px]"
            loading="eager"
        >
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
                        ['name' => 'Himalayan Salt & Gourmet Sea Salt', 'slug' => 'spices-and-salts', 'icon' => '🧂'],
                        ['name' => 'Spices & Seasoning', 'slug' => 'recipe-mix-masalas', 'icon' => '🌶️'],
                        ['name' => 'Leather Goods', 'slug' => 'root', 'icon' => '👜'],
                        ['name' => 'Nuts & Seeds', 'slug' => 'seeds-and-super-foods', 'icon' => '🥜'],
                        ['name' => 'Pure Himalayan Honey', 'slug' => 'grannis-signature-kits', 'icon' => '🍯'],
                        ['name' => 'Coffee & Tea', 'slug' => 'oats-with-nuts', 'icon' => '☕'],
                    ];
                @endphp

                @foreach ($homeCategories as $category)
                    <a href="{{ route('shop.product_or_category.index', $category['slug']) }}" class="group block">
                        <div class="relative aspect-[4/3] bg-canvas overflow-hidden rounded-lg">
                            <div class="absolute inset-0 flex items-center justify-center text-6xl">
                                {{ $category['icon'] }}
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
