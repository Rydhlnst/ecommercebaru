@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('productViewHelper', 'Webkul\Product\Helpers\View')

@php
    $avgRatings = $reviewHelper->getAverageRating($product);

    $percentageRatings = $reviewHelper->getPercentageRating($product);

    $customAttributeValues = $productViewHelper->getAdditionalData($product);

    $attributeData = collect($customAttributeValues)->filter(fn ($item) => ! empty($item['value']));

    $categoryEyebrow = $product->categories->first()?->name ?? '';
@endphp

{{-- Product-page specific styles --}}
@push('styles')
<style>
    /* Green CTA overrides — scoped to product page */
    .pdp-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background-color: #2D5A27;
        color: #ffffff;
        border: 2px solid #2D5A27;
        padding: 14px 24px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.01em;
        width: 100%;
        transition: background-color .25s ease, transform .15s ease;
        cursor: pointer;
    }
    .pdp-btn-primary:hover { background-color: #234820; border-color: #234820; }
    .pdp-btn-primary:active { transform: scale(.97); }
    .pdp-btn-primary:disabled { opacity: .6; cursor: not-allowed; transform: none; }

    .pdp-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background-color: transparent;
        color: #2D5A27;
        border: 2px solid #2D5A27;
        padding: 14px 24px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.01em;
        width: 100%;
        transition: background-color .25s ease, color .25s ease, transform .15s ease;
        cursor: pointer;
    }
    .pdp-btn-secondary:hover { background-color: #2D5A27; color: #ffffff; }
    .pdp-btn-secondary:active { transform: scale(.97); }
    .pdp-btn-secondary:disabled { opacity: .6; cursor: not-allowed; }

    .pdp-btn-whatsapp {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background-color: #25D366;
        color: #ffffff;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        width: 100%;
        text-decoration: none;
        transition: background-color .25s ease, transform .15s ease;
    }
    .pdp-btn-whatsapp:hover { background-color: #1ebe5a; color: #fff; }
    .pdp-btn-whatsapp:active { transform: scale(.97); }

    .pdp-price { color: #2D5A27; font-size: 2rem; font-weight: 700; line-height: 1.1; }
    .pdp-price del, .pdp-price s { color: #737373; font-size: 1.1rem; font-weight: 400; }

    .pdp-trust li {
        display: flex; flex-direction: column; align-items: center; gap: 6px;
        font-size: 11px; letter-spacing: .06em; text-transform: uppercase; color: #737373; text-align: center;
    }
    .pdp-trust li svg { width: 22px; height: 22px; color: #2D5A27; }
</style>
@endpush

<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="{{ trim($product->meta_description) != "" ? $product->meta_description : \Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}"/>

    <meta name="keywords" content="{{ $product->meta_keywords }}"/>

    @if (core()->getConfigData('catalog.rich_snippets.products.enable'))
        <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getProductJsonLd($product) !!}
        </script>
    @endif

    <?php $productBaseImage = product_image()->getProductBaseImage($product); ?>

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $product->name }}" />
    <meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />
    <meta name="twitter:image:alt" content="" />
    <meta name="twitter:image" content="{{ $productBaseImage['medium_image_url'] }}" />
    <meta property="og:type" content="og:product" />
    <meta property="og:title" content="{{ $product->name }}" />
    <meta property="og:image" content="{{ $productBaseImage['medium_image_url'] }}" />
    <meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />
    <meta property="og:url" content="{{ route('shop.product_or_category.index', $product->url_key) }}" />
@endPush

<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ trim($product->meta_title) != "" ? $product->meta_title : $product->name }}
    </x-slot>

    {!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}

    <!-- Breadcrumbs -->
    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        <div class="flex justify-center px-7 max-lg:hidden">
            <x-shop::breadcrumbs
                name="product"
                :entity="$product"
            />
        </div>
    @endif

    <!-- Product Information Vue Component -->
    <v-product>
        <x-shop::shimmer.products.view />
    </v-product>

    <!-- Tabs / Accordions Section -->
    <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 mt-16 md:mt-24">
        <div class="max-1180:hidden">
            <x-shop::tabs position="center" ref="productTabs">
                {!! view_render_event('bagisto.shop.products.view.description.before', ['product' => $product]) !!}

                <x-shop::tabs.item
                    id="descritpion-tab"
                    class="container mt-[60px] !p-0"
                    :title="trans('shop::app.products.view.description')"
                    :is-selected="true"
                >
                    <div class="container mt-[60px] max-1180:px-5">
                        <p class="text-lg text-zinc-500 max-1180:text-sm">
                            {!! $product->description !!}
                        </p>
                    </div>
                </x-shop::tabs.item>

                {!! view_render_event('bagisto.shop.products.view.description.after', ['product' => $product]) !!}

                @if(count($attributeData))
                    <x-shop::tabs.item
                        id="information-tab"
                        class="container mt-[60px] !p-0"
                        :title="trans('shop::app.products.view.additional-information')"
                        :is-selected="false"
                    >
                        <div class="container mt-[60px] max-1180:px-5">
                            <div class="mt-8 grid max-w-max grid-cols-[auto_1fr] gap-4">
                                @foreach ($customAttributeValues as $customAttributeValue)
                                    @if (! empty($customAttributeValue['value']))
                                        <div class="grid">
                                            <p class="text-base text-black">{{ $customAttributeValue['label'] }}</p>
                                        </div>

                                        @if ($customAttributeValue['type'] == 'file')
                                            <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}" download="{{ $customAttributeValue['label'] }}">
                                                <span class="icon-download text-2xl"></span>
                                            </a>
                                        @elseif ($customAttributeValue['type'] == 'image')
                                            <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}" download="{{ $customAttributeValue['label'] }}">
                                                <img class="min-h-5 min-w-5 h-5 w-5" src="{{ Storage::url($customAttributeValue['value']) }}" />
                                            </a>
                                        @else
                                            <div class="grid">
                                                <p class="text-base text-zinc-500">{{ $customAttributeValue['value'] }}</p>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </x-shop::tabs.item>
                @endif

                <x-shop::tabs.item
                    id="review-tab"
                    class="container mt-[60px] !p-0"
                    :title="trans('shop::app.products.view.review')"
                    :is-selected="false"
                >
                    @include('shop::products.view.reviews')
                </x-shop::tabs.item>
            </x-shop::tabs>
        </div>
    </div>

    <!-- Mobile accordions -->
    <div class="container mt-6 grid gap-3 !p-0 max-1180:px-5 1180:hidden">
        <x-shop::accordion class="max-md:border-none" :is-active="true">
            <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2">
                <p class="text-base font-medium 1180:hidden">@lang('shop::app.products.view.description')</p>
            </x-slot>
            <x-slot:content class="max-sm:px-0">
                <div class="mb-5 text-lg text-zinc-500 max-1180:text-sm max-md:mb-1 max-md:px-4">
                    {!! $product->description !!}
                </div>
            </x-slot>
        </x-shop::accordion>

        @if (count($attributeData))
            <x-shop::accordion class="max-md:border-none" :is-active="false">
                <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2">
                    <p class="text-base font-medium 1180:hidden">@lang('shop::app.products.view.additional-information')</p>
                </x-slot>
                <x-slot:content class="max-sm:px-0">
                    <div class="container max-1180:px-5">
                        <div class="grid max-w-max grid-cols-[auto_1fr] gap-4 text-lg text-zinc-500 max-1180:text-sm">
                            @foreach ($customAttributeValues as $customAttributeValue)
                                @if (! empty($customAttributeValue['value']))
                                    <div class="grid">
                                        <p class="text-base text-black" v-pre>{{ $customAttributeValue['label'] }}</p>
                                    </div>

                                    @if ($customAttributeValue['type'] == 'file')
                                        <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}" download="{{ $customAttributeValue['label'] }}">
                                            <span class="icon-download text-2xl"></span>
                                        </a>
                                    @elseif ($customAttributeValue['type'] == 'image')
                                        <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}" download="{{ $customAttributeValue['label'] }}">
                                            <img class="min-h-5 min-w-5 h-5 w-5" src="{{ Storage::url($customAttributeValue['value']) }}" alt="Product Image" />
                                        </a>
                                    @else
                                        <div class="grid">
                                            <p class="text-base text-zinc-500" v-pre>{{ $customAttributeValue['value'] ?? '-' }}</p>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </x-slot>
            </x-shop::accordion>
        @endif

        <x-shop::accordion class="max-md:border-none" :is-active="false">
            <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2" id="review-accordian-button">
                <p class="text-base font-medium">@lang('shop::app.products.view.review')</p>
            </x-slot>
            <x-slot:content>
                @include('shop::products.view.reviews')
            </x-slot>
        </x-shop::accordion>
    </div>

    <v-product-associations></v-product-associations>

    {!! view_render_event('bagisto.shop.products.view.after', ['product' => $product]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-product-template"
        >
            <x-shop::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    ref="formData"
                    @submit="handleSubmit($event, addToCart)"
                >
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="is_buy_now" v-model="is_buy_now">

                    <div class="mx-auto max-w-[1600px] px-4 md:px-10 lg:px-14 py-6 lg:py-10">
                        <div class="grid gap-8 lg:grid-cols-[1.3fr_1fr] lg:gap-14 items-start">

                            {{-- ── GALLERY ─────────────────────────────────── --}}
                            @include('shop::products.view.gallery')

                            {{-- ── INFO PANEL ──────────────────────────────── --}}
                            <div class="lg:sticky lg:top-24 lg:self-start">

                                {!! view_render_event('bagisto.shop.products.name.before', ['product' => $product]) !!}

                                {{-- Eyebrow: category name --}}
                                @if ($categoryEyebrow)
                                    <p class="text-[11px] font-semibold uppercase tracking-[.12em] text-[#2D5A27] mb-2">
                                        {{ $categoryEyebrow }}
                                    </p>
                                @endif

                                {{-- Product name + wishlist --}}
                                <div class="flex items-start justify-between gap-4">
                                    <h1 class="text-2xl md:text-3xl lg:text-[2rem] font-bold leading-tight text-ink" v-pre>
                                        {{ $product->name }}
                                    </h1>

                                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                        <button
                                            type="button"
                                            class="shrink-0 flex items-center justify-center w-10 h-10 rounded-full border border-mist bg-cream text-xl transition-colors hover:border-[#2D5A27]"
                                            aria-label="@lang('shop::app.products.view.add-to-wishlist')"
                                            :class="isWishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                                            @click="addToWishlist"
                                        ></button>
                                    @endif
                                </div>

                                {!! view_render_event('bagisto.shop.products.name.after', ['product' => $product]) !!}

                                {{-- Star rating --}}
                                {!! view_render_event('bagisto.shop.products.rating.before', ['product' => $product]) !!}

                                @if ($totalRatings = $reviewHelper->getTotalFeedback($product))
                                    <div class="mt-2 w-max cursor-pointer" role="button" tabindex="0" @click="scrollToReview">
                                        <x-shop::products.ratings
                                            class="transition-all hover:border-gray-400"
                                            :average="$avgRatings"
                                            :total="$totalRatings"
                                            ::rating="true"
                                        />
                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.products.rating.after', ['product' => $product]) !!}

                                {{-- Price --}}
                                {!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

                                <div class="mt-5 pdp-price flex items-baseline gap-3 flex-wrap">
                                    {!! $product->getTypeInstance()->getPriceHtml() !!}
                                </div>

                                @if (\Webkul\Tax\Facades\Tax::isInclusiveTaxProductPrices())
                                    <span class="text-sm text-zinc-500">(@lang('shop::app.products.view.tax-inclusive'))</span>
                                @endif

                                @if (count($product->getTypeInstance()->getCustomerGroupPricingOffers()))
                                    <div class="mt-2 grid gap-1">
                                        @foreach ($product->getTypeInstance()->getCustomerGroupPricingOffers() as $offer)
                                            <p class="text-sm text-zinc-500 [&>*]:text-black">{!! $offer !!}</p>
                                        @endforeach
                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.products.price.after', ['product' => $product]) !!}

                                {{-- Short description --}}
                                {!! view_render_event('bagisto.shop.products.short_description.before', ['product' => $product]) !!}

                                <div class="mt-5 pt-5 border-t border-mist text-[15px] text-cocoa/80 leading-relaxed">
                                    {!! $product->short_description !!}
                                </div>

                                {!! view_render_event('bagisto.shop.products.short_description.after', ['product' => $product]) !!}

                                {{-- Product type-specific options --}}
                                @include('shop::products.view.types.simple')
                                @include('shop::products.view.types.configurable')
                                @include('shop::products.view.types.grouped')
                                @include('shop::products.view.types.bundle')
                                @include('shop::products.view.types.downloadable')
                                @include('shop::products.view.types.booking')

                                {{-- Actions: qty + CTA --}}
                                <div class="mt-7 flex flex-col gap-3">

                                    {{-- Qty + Add to Cart row --}}
                                    <div class="flex gap-3 items-stretch">
                                        {!! view_render_event('bagisto.shop.products.view.quantity.before', ['product' => $product]) !!}

                                        @if ($product->getTypeInstance()->showQuantityBox())
                                            <x-shop::quantity-changer
                                                name="quantity"
                                                value="1"
                                                class="gap-x-4 rounded-lg border border-mist px-4 py-3 bg-cream shrink-0"
                                            />
                                        @endif

                                        {!! view_render_event('bagisto.shop.products.view.quantity.after', ['product' => $product]) !!}

                                        @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                            {!! view_render_event('bagisto.shop.products.view.add_to_cart.before', ['product' => $product]) !!}

                                            <button
                                                type="submit"
                                                class="pdp-btn-primary flex-1"
                                                :disabled="isStoring.addToCart || {{ $product->isSaleable(1) ? 'false' : 'true' }}"
                                                @click="is_buy_now=0;"
                                            >
                                                <span v-if="isStoring.addToCart">
                                                    <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" opacity=".25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>
                                                </span>
                                                <span v-else>@lang('shop::app.products.view.add-to-cart')</span>
                                            </button>

                                            {!! view_render_event('bagisto.shop.products.view.add_to_cart.after', ['product' => $product]) !!}
                                        @else
                                            <button type="button" class="pdp-btn-primary flex-1" @click="$refs.contactUsModal.open()">
                                                @lang('shop::app.components.layouts.footer.contact-us')
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Buy Now --}}
                                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                        {!! view_render_event('bagisto.shop.products.view.buy_now.before', ['product' => $product]) !!}

                                        @if (core()->getConfigData('catalog.products.storefront.buy_now_button_display'))
                                            <button
                                                type="submit"
                                                class="pdp-btn-secondary"
                                                :disabled="isStoring.buyNow || {{ $product->isSaleable(1) ? 'false' : 'true' }}"
                                                @click="is_buy_now=1;"
                                            >
                                                <span v-if="isStoring.buyNow">
                                                    <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" opacity=".25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>
                                                </span>
                                                <span v-else>@lang('shop::app.products.view.buy-now')</span>
                                            </button>
                                        @endif

                                        {!! view_render_event('bagisto.shop.products.view.buy_now.after', ['product' => $product]) !!}
                                    @endif

                                    {{-- WhatsApp quick order --}}
                                    @php
                                        $waNumber = core()->getConfigData('beres_storefront.contact.whatsapp_number') ?: '';
                                        $waText   = urlencode('Halo! Saya ingin memesan: ' . $product->name . ' — ' . route('shop.product_or_category.index', $product->url_key));
                                    @endphp
                                    @if ($waNumber)
                                        <a
                                            href="https://wa.me/{{ preg_replace('/\D/', '', $waNumber) }}?text={{ $waText }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="pdp-btn-whatsapp"
                                        >
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.555 4.122 1.524 5.855L.057 23.57a.5.5 0 0 0 .613.612l5.783-1.465A11.944 11.944 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 0 1-5.015-1.374l-.36-.214-3.723.944.963-3.637-.234-.374A9.818 9.818 0 1 1 12 21.818z"/>
                                            </svg>
                                            Pesan via WhatsApp
                                        </a>
                                    @endif
                                </div>

                                {{-- Trust badges --}}
                                <ul class="pdp-trust mt-7 pt-6 border-t border-mist grid grid-cols-3 gap-3">
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                            <rect x="1" y="8" width="22" height="13" rx="2"/>
                                        </svg>
                                        Gratis Ongkir
                                    </li>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        </svg>
                                        Produk Asli
                                    </li>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <polyline points="1 4 1 10 7 10"/>
                                            <path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
                                        </svg>
                                        Mudah Dikembalikan
                                    </li>
                                </ul>

                                {!! view_render_event('bagisto.shop.products.view.additional_actions.before', ['product' => $product]) !!}

                                {{-- Compare + Share row --}}
                                <div class="mt-6 flex items-center gap-6 text-sm text-stone flex-wrap">
                                    {!! view_render_event('bagisto.shop.products.view.compare.before', ['product' => $product]) !!}

                                    @if (core()->getConfigData('catalog.products.settings.compare_option'))
                                        <div
                                            class="flex items-center gap-2 cursor-pointer hover:text-ink transition-colors"
                                            role="button"
                                            tabindex="0"
                                            @click="is_buy_now=0; addToCompare({{ $product->id }})"
                                        >
                                            <span class="icon-compare text-xl" role="presentation"></span>
                                            @lang('shop::app.products.view.compare')
                                        </div>
                                    @endif

                                    {!! view_render_event('bagisto.shop.products.view.compare.after', ['product' => $product]) !!}
                                </div>

                                {!! view_render_event('bagisto.shop.products.view.additional_actions.after', ['product' => $product]) !!}
                            </div>
                            {{-- end INFO PANEL --}}
                        </div>
                    </div>
                </form>
            </x-shop::form>

            <!-- Contact Us Modal -->
            <x-shop::modal ref="contactUsModal">
                <x-slot:header>
                    <h2 class="text-lg font-semibold max-md:text-base">
                        @lang('shop::app.products.view.contact-us.title')
                    </h2>
                </x-slot>

                <x-slot:content>
                    <x-shop::form :action="route('shop.home.contact_us.send_mail')">
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.products.view.contact-us.name')
                            </x-shop::form.control-group.label>
                            <x-shop::form.control-group.control
                                type="text" name="name" rules="required"
                                :value="old('name')"
                                :label="trans('shop::app.products.view.contact-us.name')"
                                :placeholder="trans('shop::app.products.view.contact-us.name')"
                                aria-required="true"
                            />
                            <x-shop::form.control-group.error control-name="name" />
                        </x-shop::form.control-group>

                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.products.view.contact-us.email')
                            </x-shop::form.control-group.label>
                            <x-shop::form.control-group.control
                                type="email" name="email" rules="required|email"
                                :value="old('email')"
                                :label="trans('shop::app.products.view.contact-us.email')"
                                :placeholder="trans('shop::app.products.view.contact-us.email')"
                                aria-required="true"
                            />
                            <x-shop::form.control-group.error control-name="email" />
                        </x-shop::form.control-group>

                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label>
                                @lang('shop::app.products.view.contact-us.phone-number')
                            </x-shop::form.control-group.label>
                            <x-shop::form.control-group.control
                                type="text" name="contact" rules="phone"
                                :value="old('contact')"
                                :label="trans('shop::app.products.view.contact-us.phone-number')"
                                :placeholder="trans('shop::app.products.view.contact-us.phone-number')"
                            />
                            <x-shop::form.control-group.error control-name="contact" />
                        </x-shop::form.control-group>

                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.products.view.contact-us.desc')
                            </x-shop::form.control-group.label>
                            <x-shop::form.control-group.control
                                type="textarea" name="message" rules="required"
                                :label="trans('shop::app.products.view.contact-us.message')"
                                :placeholder="trans('shop::app.products.view.contact-us.describe-here')"
                                aria-required="true" rows="6"
                            />
                            <x-shop::form.control-group.error control-name="message" />
                        </x-shop::form.control-group>

                        @if (core()->getConfigData('customer.captcha.credentials.status'))
                            <x-shop::form.control-group class="mt-5">
                                {!! \Webkul\Customer\Facades\Captcha::render() !!}
                                <x-shop::form.control-group.error control-name="recaptcha_token" />
                            </x-shop::form.control-group>
                        @endif

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="pdp-btn-primary !w-auto px-8">
                                @lang('shop::app.products.view.contact-us.submit')
                            </button>
                        </div>
                    </x-shop::form>
                </x-slot>
            </x-shop::modal>
        </script>

        <script type="module">
            app.component('v-product', {
                template: '#v-product-template',

                data() {
                    return {
                        isWishlist: false,
                        isCustomer: '{{ auth()->guard('customer')->check() }}',
                        is_buy_now: 0,
                        isStoring: {
                            addToCart: false,
                            buyNow: false,
                        },
                    }
                },

                mounted() {
                    this.checkWishlistStatus();
                },

                methods: {
                    addToCart(params) {
                        const operation = this.is_buy_now ? 'buyNow' : 'addToCart';
                        this.isStoring[operation] = true;

                        let formData = new FormData(this.$refs.formData);
                        this.ensureQuantity(formData);

                        this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', formData, {
                                headers: { 'Content-Type': 'multipart/form-data' }
                            })
                            .then(response => {
                                if (response.data.message) {
                                    this.$emitter.emit('update-mini-cart', response.data.data);
                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                                    if (response.data.redirect) window.location.href = response.data.redirect;
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }
                                this.isStoring[operation] = false;
                            })
                            .catch(error => {
                                this.isStoring[operation] = false;
                                this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.message });
                            });
                    },

                    checkWishlistStatus() {
                        if (this.isCustomer) {
                            this.$axios.get('{{ route('shop.api.customers.account.wishlist.index') }}')
                                .then(response => {
                                    const wishlistItems = response.data.data || [];
                                    this.isWishlist = Boolean(wishlistItems.find(item => item.product.id == "{{ $product->id }}")?.product?.is_wishlist);
                                })
                                .catch(error => {});
                        }
                    },

                    addToWishlist() {
                        if (this.isCustomer) {
                            this.$axios.post('{{ route('shop.api.customers.account.wishlist.store') }}', {
                                    product_id: "{{ $product->id }}"
                                })
                                .then(response => {
                                    this.isWishlist = ! this.isWishlist;
                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                })
                                .catch(error => {});
                        } else {
                            window.location.href = "{{ route('shop.customer.session.index')}}";
                        }
                    },

                    addToCompare(productId) {
                        if (this.isCustomer) {
                            this.$axios.post('{{ route("shop.api.compare.store") }}', { 'product_id': productId })
                                .then(response => {
                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                })
                                .catch(error => {
                                    if ([400, 422].includes(error.response.status)) {
                                        this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.data.message });
                                        return;
                                    }
                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                                });
                            return;
                        }

                        let existingItems = this.getStorageValue(this.getCompareItemsStorageKey()) ?? [];
                        if (existingItems.length) {
                            if (! existingItems.includes(productId)) {
                                existingItems.push(productId);
                                this.setStorageValue(this.getCompareItemsStorageKey(), existingItems);
                                this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.products.view.already-in-compare')" });
                            }
                        } else {
                            this.setStorageValue(this.getCompareItemsStorageKey(), [productId]);
                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                        }
                    },

                    scrollToReview() {
                        let accordianElement = document.querySelector('#review-accordian-button');
                        if (accordianElement) { accordianElement.click(); accordianElement.scrollIntoView({ behavior: 'smooth' }); }
                        let tabElement = document.querySelector('#review-tab-button');
                        if (tabElement) { tabElement.click(); tabElement.scrollIntoView({ behavior: 'smooth' }); }
                    },

                    ensureQuantity(formData) {
                        if (! formData.has('quantity')) formData.append('quantity', 1);
                    },

                    getCompareItemsStorageKey() { return 'compare_items'; },
                    setStorageValue(key, value) { localStorage.setItem(key, JSON.stringify(value)); },
                    getStorageValue(key) {
                        let value = localStorage.getItem(key);
                        if (value) value = JSON.parse(value);
                        return value;
                    },
                },
            });
        </script>

        <script
            type="text/x-template"
            id="v-product-associations-template"
        >
            <div ref="carouselWrapper">
                <template v-if="isVisible">
                    <x-shop::products.carousel
                        :title="trans('shop::app.products.view.related-product-title')"
                        :src="route('shop.api.products.related.index', ['id' => $product->id])"
                    />
                    <x-shop::products.carousel
                        :title="trans('shop::app.products.view.up-sell-title')"
                        :src="route('shop.api.products.up-sell.index', ['id' => $product->id])"
                    />
                </template>
            </div>
        </script>

        <script type="module">
            app.component('v-product-associations', {
                template: '#v-product-associations-template',

                data() {
                    return { isVisible: false };
                },

                mounted() {
                    const observer = new IntersectionObserver(
                        (entries) => {
                            entries.forEach((entry) => {
                                if (entry.isIntersecting) {
                                    this.isVisible = true;
                                    observer.unobserve(entry.target);
                                }
                            });
                        },
                        { threshold: 0.1 }
                    );
                    observer.observe(this.$refs.carouselWrapper);
                }
            });
        </script>

        @if (core()->getConfigData('customer.captcha.credentials.status'))
            {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
        @endif
    @endPushOnce
</x-shop::layouts>
