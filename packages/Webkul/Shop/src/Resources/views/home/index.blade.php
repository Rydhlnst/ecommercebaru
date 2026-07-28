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
    <section class="relative w-full bg-canvas overflow-hidden">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-20 md:py-28 grid gap-10 lg:grid-cols-2 items-center">
            <div class="max-w-xl">
                <p class="eyebrow mb-5">Placeholder — New Season</p>
                <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl leading-[1.02] text-ink">
                    Lorem ipsum <em class="italic text-clay">dolor</em> sit amet consectetur.
                </h1>
                <p class="mt-6 text-base md:text-lg text-cocoa/80 max-w-md">
                    Placeholder subhead — ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="#" class="primary-button">Shop the collection</a>
                    <a href="#" class="ghost-button">Read the story</a>
                </div>
            </div>

            <div class="relative aspect-[4/5] lg:aspect-[5/6] bg-mist overflow-hidden">
                <img
                    src="https://placehold.co/900x1080/EDE6D8/3C2E22?text=Placeholder+Hero"
                    alt="Placeholder hero visual"
                    class="absolute inset-0 w-full h-full object-cover"
                    loading="eager"
                >
                <div class="absolute bottom-6 left-6 right-6 md:left-8 md:bottom-8 md:right-auto md:max-w-xs bg-cream/95 backdrop-blur p-5">
                    <p class="eyebrow">Featured</p>
                    <p class="mt-2 font-serif text-xl leading-snug text-ink">Placeholder editorial caption.</p>
                    <a href="#" class="mt-3 inline-block text-[13px] tracking-widelg uppercase text-ink border-b border-ink pb-0.5">Discover →</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Category tiles quick-links (placeholder — hides if none set) --}}
    <section class="bg-cream">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-16 md:py-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <p class="eyebrow mb-3">Placeholder — Shop by category</p>
                    <h2 class="font-serif text-4xl md:text-5xl text-ink">Curated essentials.</h2>
                </div>
                <a href="#" class="hidden md:inline-block ghost-button">View all →</a>
            </div>

            <div class="grid gap-4 md:gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (['Bags & Luggage', 'Wallets', 'Tech', 'Accessories'] as $i => $label)
                    <a href="#" class="group block">
                        <div class="relative aspect-square bg-canvas overflow-hidden">
                            <img
                                src="https://placehold.co/600x600/EDE6D8/3C2E22?text={{ urlencode($label) }}"
                                alt="{{ $label }} placeholder"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
                                loading="lazy"
                            >
                        </div>
                        <p class="mt-4 text-[13px] tracking-widelg uppercase text-ink group-hover:text-clay transition-colors">
                            {{ $label }}
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
