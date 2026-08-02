<v-product-card
    {{ $attributes }}
    :product="product"
>
</v-product-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-card-template"
    >
        <!-- Grid Card -->
        <div
            class="group relative flex flex-col w-full"
            v-if="mode != 'list'"
        >
            <div class="relative overflow-hidden bg-canvas aspect-[4/5]">
                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

                <a
                    :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)"
                    :aria-label="product.name"
                    class="block absolute inset-0"
                >
                    <x-shop::media.images.lazy
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 group-hover:opacity-0"
                        ::src="product.base_image.medium_image_url"
                        ::srcset="`
                            ${product.base_image.small_image_url} 150w,
                            ${product.base_image.medium_image_url} 300w,
                        `"
                        sizes="(max-width: 768px) 50vw, 25vw"
                        ::key="product.id"
                        ::index="product.id"
                        width="600"
                        height="750"
                        ::alt="product.name"
                    />

                    <img
                        class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 group-hover:opacity-100 scale-[1.02]"
                        :src="(product.images && product.images[1]) ? product.images[1].medium_image_url : product.base_image.large_image_url"
                        :alt="product.name"
                        loading="lazy"
                    />
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

                <!-- Badges -->
                <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                    <span
                        v-if="product.on_sale"
                        class="bg-ink text-cream text-[10px] tracking-widelg uppercase px-2.5 py-1"
                    >
                        @lang('shop::app.components.products.card.sale')
                    </span>
                    <span
                        v-else-if="product.is_new"
                        class="bg-cream text-ink text-[10px] tracking-widelg uppercase px-2.5 py-1"
                    >
                        @lang('shop::app.components.products.card.new')
                    </span>
                </div>

                <!-- Wishlist / Compare (top-right) -->
                <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 max-lg:opacity-100">
                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}
                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                        <button
                            class="w-9 h-9 rounded-full bg-cream/95 flex items-center justify-center text-lg hover:bg-cream transition-colors"
                            :class="product.is_wishlist ? 'icon-heart-fill text-clay' : 'icon-heart text-ink'"
                            @click="addToWishlist()"
                            :aria-label="'@lang('shop::app.components.products.card.add-to-wishlist')'"
                        ></button>
                    @endif
                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}
                </div>

                <!-- Quick add-to-cart bar (desktop hover) -->
                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                    <div class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out max-md:hidden">
                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}
                        <button
                            class="w-full bg-ink text-cream text-[12px] tracking-widelg uppercase py-3 hover:bg-cocoa transition-colors disabled:opacity-60"
                            :disabled="! product.is_saleable || isAddingToCart"
                            @click="addToCart()"
                        >
                            @lang('shop::app.components.products.card.add-to-cart')
                        </button>
                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                    </div>
                @endif

                <!-- Quick add-to-cart icon (mobile/tablet) -->
                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                    <div class="absolute bottom-3 right-3 min-md:hidden">
                        <button
                            class="w-10 h-10 bg-[#2D5A27] text-white rounded-full flex items-center justify-center hover:bg-[#1E3D1A] transition-colors disabled:opacity-60 shadow-md"
                            :disabled="! product.is_saleable || isAddingToCart"
                            @click="addToCart()"
                            aria-label="@lang('shop::app.components.products.card.add-to-cart')"
                        >
                            <span class="icon-cart text-lg"></span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Info -->
            <div class="pt-4 pb-6 grid gap-1.5">
                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}
                <a
                    :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)"
                    class="text-[14px] text-ink font-normal hover:text-clay transition-colors break-words"
                >
                    @{{ product.name }}
                </a>
                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}
                <div
                    class="text-[14px] text-cocoa font-medium"
                    v-html="product.price_html"
                ></div>
                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}
                @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                    <x-shop::products.ratings
                        class="text-xs text-stone"
                        ::average="product.ratings.average"
                        ::total="product.ratings.total"
                        ::rating="false"
                        v-if="product.ratings.total"
                    />
                @else
                    <x-shop::products.ratings
                        class="text-xs text-stone"
                        ::average="product.ratings.average"
                        ::total="product.reviews.total"
                        ::rating="false"
                        v-if="product.reviews.total"
                    />
                @endif
                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}
            </div>
        </div>

        <!-- List Card -->
        <div
            class="relative flex gap-6 border-b border-mist pb-8 max-sm:flex-wrap"
            v-else
        >
            <div class="group relative w-[220px] h-[275px] flex-shrink-0 overflow-hidden bg-canvas">
                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}
                <a :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)">
                    <x-shop::media.images.lazy
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
                        ::src="product.base_image.medium_image_url"
                        ::key="product.id"
                        ::index="product.id"
                        width="291"
                        height="300"
                        ::alt="product.name"
                    />
                </a>
                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

                <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                    <span v-if="product.on_sale" class="bg-ink text-cream text-[10px] tracking-widelg uppercase px-2.5 py-1">
                        @lang('shop::app.components.products.card.sale')
                    </span>
                    <span v-else-if="product.is_new" class="bg-cream text-ink text-[10px] tracking-widelg uppercase px-2.5 py-1">
                        @lang('shop::app.components.products.card.new')
                    </span>
                </div>
            </div>

            <div class="grid content-start gap-3 flex-1">
                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}
                <a
                    :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)"
                    class="font-serif text-2xl text-ink hover:text-clay transition-colors"
                >
                    @{{ product.name }}
                </a>
                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}
                <div class="text-lg text-cocoa font-medium" v-html="product.price_html"></div>
                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}
                <div class="text-sm text-stone">
                    <template v-if="! product.ratings.total">
                        @lang('shop::app.components.products.card.review-description')
                    </template>
                    <template v-else>
                        @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                            <x-shop::products.ratings ::average="product.ratings.average" ::total="product.ratings.total" ::rating="false" />
                        @else
                            <x-shop::products.ratings ::average="product.ratings.average" ::total="product.reviews.total" ::rating="false" />
                        @endif
                    </template>
                </div>
                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

                <div class="flex items-center gap-4 mt-2">
                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}
                        <button
                            class="primary-button max-sm:w-10 max-sm:h-10 max-sm:p-0 max-sm:rounded-full max-sm:flex max-sm:items-center max-sm:justify-center"
                            :disabled="! product.is_saleable || isAddingToCart"
                            @click="addToCart()"
                        >
                            <span class="max-sm:hidden">@lang('shop::app.components.products.card.add-to-cart')</span>
                            <span class="icon-cart text-lg sm:hidden"></span>
                        </button>
                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                    @endif

                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                        <button
                            class="text-2xl text-ink"
                            :class="product.is_wishlist ? 'icon-heart-fill text-clay' : 'icon-heart'"
                            @click="addToWishlist()"
                            aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                        ></button>
                    @endif
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-product-card', {
            template: '#v-product-card-template',

            props: ['mode', 'product'],

            data() {
                return {
                    isCustomer: '{{ auth()->guard('customer')->check() }}',
                    isAddingToCart: false,
                }
            },

            methods: {
                addToWishlist() {
                    if (this.isCustomer) {
                        this.$axios.post(`{{ route('shop.api.customers.account.wishlist.store') }}`, {
                                product_id: this.product.id
                            })
                            .then(response => {
                                this.product.is_wishlist = ! this.product.is_wishlist;
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
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message});
                            });
                        return;
                    }

                    let items = this.getStorageValue() ?? [];
                    if (items.length) {
                        if (! items.includes(productId)) {
                            items.push(productId);
                            localStorage.setItem('compare_items', JSON.stringify(items));
                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });
                        } else {
                            this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.components.products.card.already-in-compare')" });
                        }
                    } else {
                        localStorage.setItem('compare_items', JSON.stringify([productId]));
                        this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });
                    }
                },

                getStorageValue(key) {
                    let value = localStorage.getItem('compare_items');
                    if (! value) return [];
                    return JSON.parse(value);
                },

                addToCart() {
                    this.isAddingToCart = true;
                    this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                            'quantity': 1,
                            'product_id': this.product.id,
                        })
                        .then(response => {
                            if (response.data.message) {
                                this.$emitter.emit('update-mini-cart', response.data.data);
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                            }
                            this.isAddingToCart = false;
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            if (error.response.data.redirect_uri) {
                                window.location.href = error.response.data.redirect_uri;
                            }
                            this.isAddingToCart = false;
                        });
                },
            },
        });
    </script>
@endpushOnce
