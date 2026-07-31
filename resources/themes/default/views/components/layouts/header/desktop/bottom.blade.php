{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.before') !!}

<div class="w-full bg-cream">
    {{-- Row 1: logo | search bar | icons --}}
    <div class="w-full px-6 md:px-10 lg:px-14 flex items-center gap-6 pt-5 pb-3">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a
            href="{{ route('shop.home.index') }}"
            aria-label="{{ config('app.name') }}"
            class="shrink-0"
        >
            <img
                src="/images/ankesh-mart-logo.png"
                alt="{{ config('app.name') }}"
                width="131"
                height="44"
                class="block"
            >
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}

        {{-- Search bar --}}
        <div class="flex-1">
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.before') !!}

            <form
                action="{{ route('shop.search.index') }}"
                class="relative max-w-4xl"
                role="search"
                toolname="search_products"
                tooldescription="{{ trans('shop::app.components.layouts.webmcp.search-products') }}"
                toolautosubmit
            >
                <label for="organic-search" class="sr-only">@lang('shop::app.components.layouts.header.desktop.bottom.search')</label>

                <input
                    id="organic-search"
                    type="text"
                    name="query"
                    value="{{ request('query') }}"
                    toolparamdescription="{{ trans('shop::app.components.layouts.webmcp.search-products-query') }}"
                    class="block w-full text-[14px] text-ink placeholder:text-stone bg-transparent border border-mist rounded-none px-5 py-3 pr-14 transition-colors hover:border-ink focus:border-ink focus:ring-0 focus:outline-none"
                    minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                    maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                    placeholder="What are you looking for?"
                    aria-label="Search"
                    aria-required="true"
                    pattern="[^\\]+"
                    required
                >

                <button
                    type="submit"
                    class="absolute right-3 top-1/2 -translate-y-1/2 icon-search text-2xl text-stone hover:text-ink transition-colors"
                    aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.submit')"
                ></button>

                @if (core()->getConfigData('catalog.products.settings.image_search'))
                    @include('shop::search.images.index')
                @endif
            </form>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.after') !!}
        </div>

        {{-- Right icons --}}
        <div class="flex items-center gap-5 shrink-0">
            <!-- EN/ID Language Toggle -->
            <div class="flex items-center gap-1 select-none">
                <button type="button" data-gt-lang="en" onclick="setGoogleTranslateLang('en')" class="px-1 py-0.5 text-stone transition-colors hover:text-ink">EN</button>
                <span class="text-stone/50 text-xs">/</span>
                <button type="button" data-gt-lang="id" onclick="setGoogleTranslateLang('id')" class="px-1 py-0.5 text-stone transition-colors hover:text-ink">ID</button>
            </div>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.before') !!}
            @if(core()->getConfigData('catalog.products.settings.compare_option'))
                <a href="{{ route('shop.compare.index') }}" aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.compare')">
                    <span class="inline-block text-xl cursor-pointer icon-compare text-ink" role="presentation"></span>
                </a>
            @endif
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.after') !!}

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.before') !!}
            @if(core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                @include('shop::checkout.cart.mini-cart')
            @endif
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.after') !!}

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.before') !!}
            <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                <x-slot:toggle>
                    <span
                        class="inline-block text-xl cursor-pointer icon-users text-ink"
                        role="button"
                        aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.profile')"
                        tabindex="0"
                    ></span>
                </x-slot>

                @guest('customer')
                    <x-slot:content>
                        <div class="grid gap-2.5">
                            <p class="font-serif text-xl">
                                @lang('shop::app.components.layouts.header.desktop.bottom.welcome-guest')
                            </p>
                            <p class="text-sm text-stone">
                                @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                            </p>
                        </div>
                        <p class="w-full mt-3 border-t border-mist"></p>

                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.before') !!}
                        <div class="flex gap-3 mt-6">
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_in_button.before') !!}
                            <a href="{{ route('shop.customer.session.create') }}" class="primary-button">
                                @lang('shop::app.components.layouts.header.desktop.bottom.sign-in')
                            </a>
                            <a href="{{ route('shop.customers.register.index') }}" class="secondary-button">
                                @lang('shop::app.components.layouts.header.desktop.bottom.sign-up')
                            </a>
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_up_button.after') !!}
                        </div>

                        @if (core()->getConfigData('sales.eu_withdrawal.general.enabled', core()->getCurrentChannelCode()))
                            <a href="{{ route('shop.eu-withdrawal.guest.lookup') }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-medium text-ink hover:underline">
                                @lang('shop::app.eu_withdrawal.guest_dropdown.link')
                            </a>
                        @endif
                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.after') !!}
                    </x-slot>
                @endguest

                @auth('customer')
                    <x-slot:content class="!p-0">
                        <div class="grid gap-2.5 p-5 pb-0">
                            <p class="font-serif text-xl" v-pre>
                                @lang('shop::app.components.layouts.header.desktop.bottom.welcome')'
                                {{ auth()->guard('customer')->user()->first_name }}
                            </p>
                            <p class="text-sm text-stone">
                                @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                            </p>
                        </div>
                        <p class="w-full mt-3 border-t border-mist"></p>
                        <div class="mt-2.5 grid gap-1 pb-2.5">
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.before') !!}
                            <a class="px-5 py-2 text-sm cursor-pointer hover:bg-canvas" href="{{ route('shop.customers.account.profile.index') }}">
                                @lang('shop::app.components.layouts.header.desktop.bottom.profile')
                            </a>
                            <a class="px-5 py-2 text-sm cursor-pointer hover:bg-canvas" href="{{ route('shop.customers.account.orders.index') }}">
                                @lang('shop::app.components.layouts.header.desktop.bottom.orders')
                            </a>
                            @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                <a class="px-5 py-2 text-sm cursor-pointer hover:bg-canvas" href="{{ route('shop.customers.account.wishlist.index') }}">
                                    @lang('shop::app.components.layouts.header.desktop.bottom.wishlist')
                                </a>
                            @endif
                            @auth('customer')
                                <x-shop::form method="DELETE" action="{{ route('shop.customer.session.destroy') }}" id="customerLogout" />
                                <a class="px-5 py-2 text-sm cursor-pointer hover:bg-canvas" href="{{ route('shop.customer.session.destroy') }}"
                                   onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                                    @lang('shop::app.components.layouts.header.desktop.bottom.logout')
                                </a>
                            @endauth
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.after') !!}
                        </div>
                    </x-slot>
                @endauth
            </x-shop::dropdown>
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.after') !!}
        </div>
    </div>

    {{-- Row 2: horizontal category nav --}}
    @php
        $navPath    = request()->path();
        $isNavHome  = ($navPath === '/' || $navPath === '');
        $navActive  = 'text-[#2D5A27] border-b-2 border-[#2D5A27] pb-1 font-semibold';
        $navInactive= 'text-stone hover:text-[#2D5A27] transition-colors';

        $navItems = [
            ['Featured',         route('shop.home.index'),         $isNavHome],
            ['Buah & Sayur',     route('shop.search.index', ['category' => 'buah-sayur']),   str_contains($navPath, 'buah')   || str_contains($navPath, 'sayur')],
            ['Daging & Seafood', route('shop.search.index', ['category' => 'daging']),       str_contains($navPath, 'daging') || str_contains($navPath, 'seafood')],
            ['Roti & Bakery',    route('shop.search.index', ['category' => 'roti']),         str_contains($navPath, 'roti')   || str_contains($navPath, 'bakery')],
            ['Minuman',          route('shop.search.index', ['category' => 'minuman']),      str_contains($navPath, 'minuman')],
            ['Bumbu & Rempah',   route('shop.search.index', ['category' => 'bumbu']),        str_contains($navPath, 'bumbu')  || str_contains($navPath, 'rempah')],
            ['Snack Sehat',      route('shop.search.index', ['category' => 'snack']),        str_contains($navPath, 'snack')],
        ];
    @endphp
    <nav class="w-full px-6 md:px-10 lg:px-14 pb-4 border-t border-mist" aria-label="Category navigation">
        <ul class="flex items-center gap-8 lg:gap-12 text-[14px] text-ink">
            @foreach ($navItems as [$label, $href, $active])
                <li>
                    <a href="{{ $href }}" class="{{ $active ? $navActive : $navInactive }}">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

</div>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-desktop-category-template"
    >
        <!-- Loading State -->
        <div
            class="flex items-center gap-5"
            v-if="isLoading"
        >
            <span
                class="w-20 h-6 rounded shimmer"
                role="presentation"
            ></span>

            <span
                class="w-20 h-6 rounded shimmer"
                role="presentation"
            ></span>

            <span
                class="w-20 h-6 rounded shimmer"
                role="presentation"
            ></span>
        </div>

        <!-- Default category layout -->
        <div
            class="flex items-center"
            v-else-if="'{{ core()->getConfigData('general.design.categories.category_view') }}' !== 'sidebar'"
        >
            <div
                class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-ink"
                v-for="category in categories"
            >
                <span>
                    <a
                        :href="category.url"
                        class="inline-block px-5 uppercase"
                    >
                        @{{ category.name }}
                    </a>
                </span>

                <div
                    class="pointer-events-none absolute top-[78px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-y-auto border border-b-0 border-l-0 border-r-0 border-t border-[#F3F3F3] bg-white p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in ltr:-left-9 rtl:-right-9"
                    v-if="category.children && category.children.length"
                >
                    <div class="flex justify-between gap-x-[70px]">
                        <div
                            class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                            v-for="pairCategoryChildren in pairCategoryChildren(category)"
                        >
                            <template v-for="secondLevelCategory in pairCategoryChildren">
                                <p class="font-medium text-ink">
                                    <a :href="secondLevelCategory.url">
                                        @{{ secondLevelCategory.name }}
                                    </a>
                                </p>

                                <ul
                                    class="grid grid-cols-[1fr] gap-3"
                                    v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                >
                                    <li
                                        class="text-sm font-medium text-zinc-500"
                                        v-for="thirdLevelCategory in secondLevelCategory.children"
                                    >
                                        <a :href="thirdLevelCategory.url">
                                            @{{ thirdLevelCategory.name }}
                                        </a>
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar category layout -->
        <div v-else>
            <!-- Categories Navigation -->
            <div class="flex items-center">
                <!-- "All" button for opening the category drawer -->
                <div
                    class="flex h-[77px] cursor-pointer items-center border-b-4 border-transparent hover:border-b-4 hover:border-ink"
                    @click="toggleCategoryDrawer"
                >
                    <span class="flex items-center gap-1 px-5 uppercase">
                        <span class="text-xl icon-hamburger"></span>

                        @lang('shop::app.components.layouts.header.desktop.bottom.all')
                    </span>
                </div>

                <!-- Show only first 4 categories in main navigation -->
                <div
                    class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-ink"
                    v-for="category in categories.slice(0, 4)"
                >
                    <span>
                        <a
                            :href="category.url"
                            class="inline-block px-5 uppercase"
                        >
                            @{{ category.name }}
                        </a>
                    </span>

                    <!-- Dropdown for each category -->
                    <div
                        class="pointer-events-none absolute top-[78px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-y-auto border border-b-0 border-l-0 border-r-0 border-t border-[#F3F3F3] bg-white p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in ltr:-left-9 rtl:-right-9"
                        v-if="category.children && category.children.length"
                    >
                        <div class="flex justify-between gap-x-[70px]">
                            <div
                                class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                                v-for="pairCategoryChildren in pairCategoryChildren(category)"
                            >
                                <template v-for="secondLevelCategory in pairCategoryChildren">
                                    <p class="font-medium text-ink">
                                        <a :href="secondLevelCategory.url">
                                            @{{ secondLevelCategory.name }}
                                        </a>
                                    </p>

                                    <ul
                                        class="grid grid-cols-[1fr] gap-3"
                                        v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                    >
                                        <li
                                            class="text-sm font-medium text-zinc-500"
                                            v-for="thirdLevelCategory in secondLevelCategory.children"
                                        >
                                            <a :href="thirdLevelCategory.url">
                                                @{{ thirdLevelCategory.name }}
                                            </a>
                                        </li>
                                    </ul>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Drawer Integration -->
            <x-shop::drawer
                position="left"
                width="400px"
                ::is-active="isDrawerActive"
                @toggle="onDrawerToggle"
                @close="onDrawerClose"
            >
                <x-slot:toggle></x-slot>

                <x-slot:header class="border-b border-gray-200">
                    <div class="flex items-center justify-between w-full">
                        <p class="text-xl font-medium">
                            @lang('shop::app.components.layouts.header.desktop.bottom.categories')
                        </p>
                    </div>
                </x-slot>

                <x-slot:content class="!px-0">
                    <!-- Wrapper with transition effects -->
                    <div class="relative h-full overflow-hidden">
                        <!-- Sliding container -->
                        <div
                            class="flex h-full transition-transform duration-300"
                            :class="{
                                'ltr:translate-x-0 rtl:translate-x-0': currentViewLevel !== 'third',
                                'ltr:-translate-x-full rtl:translate-x-full': currentViewLevel === 'third'
                            }"
                        >
                            <!-- First level view -->
                            <div class="h-[calc(100vh-74px)] w-full flex-shrink-0 overflow-auto">
                                <div class="py-4">
                                    <div
                                        v-for="category in categories"
                                        :key="category.id"
                                        :class="{'mb-2': category.children && category.children.length}"
                                    >
                                        <div class="flex items-center justify-between px-6 py-2 transition-colors duration-200 cursor-pointer hover:bg-gray-100">
                                            <a
                                                :href="category.url"
                                                class="text-base font-medium text-black"
                                            >
                                                @{{ category.name }}
                                            </a>
                                        </div>

                                        <!-- Second Level Categories -->
                                        <div v-if="category.children && category.children.length" >
                                            <div
                                                v-for="secondLevelCategory in category.children"
                                                :key="secondLevelCategory.id"
                                            >
                                                <div
                                                    class="flex items-center justify-between px-6 py-2 transition-colors duration-200 cursor-pointer hover:bg-gray-100"
                                                    @click="showThirdLevel(secondLevelCategory, category, $event)"
                                                >
                                                    <a
                                                        :href="secondLevelCategory.url"
                                                        class="text-sm font-normal"
                                                    >
                                                        @{{ secondLevelCategory.name }}
                                                    </a>

                                                    <span
                                                        v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                                        class="icon-arrow-right rtl:icon-arrow-left"
                                                    ></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Third level view -->
                            <div
                                class="flex-shrink-0 w-full h-full"
                                v-if="currentViewLevel === 'third'"
                            >
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <button
                                        @click="goBackToMainView"
                                        class="flex items-center justify-center gap-2 focus:outline-none"
                                        aria-label="Go back"
                                    >
                                        <span class="text-lg icon-arrow-left rtl:icon-arrow-right"></span>

                                        <p class="text-base font-medium text-black">
                                            @lang('shop::app.components.layouts.header.desktop.bottom.back-button')
                                        </p>
                                    </button>
                                </div>

                                <!-- Third Level Content -->
                                <div class="py-4">
                                    <div
                                        v-for="thirdLevelCategory in currentSecondLevelCategory?.children"
                                        :key="thirdLevelCategory.id"
                                        class="mb-2"
                                    >
                                        <a
                                            :href="thirdLevelCategory.url"
                                            class="block px-6 py-2 text-sm transition-colors duration-200 hover:bg-gray-100"
                                        >
                                            @{{ thirdLevelCategory.name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-shop::drawer>
        </div>
    </script>

    <script type="module">
        app.component('v-desktop-category', {
            template: '#v-desktop-category-template',

            data() {
                return {
                    isLoading: true,
                    categories: [],
                    isDrawerActive: false,
                    currentViewLevel: 'main',
                    currentSecondLevelCategory: null,
                    currentParentCategory: null
                }
            },

            mounted() {
                this.initCategories();
            },

            methods: {
                initCategories() {
                    try {
                        const stored = localStorage.getItem('categories');

                        if (stored) {
                            this.categories = JSON.parse(stored);
                            this.isLoading = false;

                            return;
                        }

                    } catch (e) {}

                    this.getCategories();
                },

                getCategories() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then(response => {
                            this.isLoading = false;
                            this.categories = response.data.data;
                            localStorage.setItem('categories', JSON.stringify(this.categories));
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                pairCategoryChildren(category) {
                    if (! category.children) return [];

                    return category.children.reduce((result, value, index, array) => {
                        if (index % 2 === 0) {
                            result.push(array.slice(index, index + 2));
                        }
                        return result;
                    }, []);
                },

                toggleCategoryDrawer() {
                    this.isDrawerActive = !this.isDrawerActive;
                    if (this.isDrawerActive) {
                        this.currentViewLevel = 'main';
                    }
                },

                onDrawerToggle(event) {
                    this.isDrawerActive = event.isActive;
                },

                onDrawerClose(event) {
                    this.isDrawerActive = false;
                },

                showThirdLevel(secondLevelCategory, parentCategory, event) {
                    if (secondLevelCategory.children && secondLevelCategory.children.length) {
                        this.currentSecondLevelCategory = secondLevelCategory;
                        this.currentParentCategory = parentCategory;
                        this.currentViewLevel = 'third';

                        if (event) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    }
                },

                goBackToMainView() {
                    this.currentViewLevel = 'main';
                }
            },
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}
