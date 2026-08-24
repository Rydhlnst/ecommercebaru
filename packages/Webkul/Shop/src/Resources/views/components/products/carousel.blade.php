<v-products-carousel
    src="{{ $src }}"
    title="{{ $title }}"
    navigation-link="{{ $navigationLink ?? '' }}"
>
    <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
</v-products-carousel>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-products-carousel-template"
    >
        <div
            class="container mt-20 max-lg:px-8 max-md:mt-8 max-sm:mt-7 max-sm:!px-4"
            v-if="! isLoading && products.length"
        >
            <div class="flex justify-between">
                <h2 class="font-dmserif text-3xl max-md:text-2xl max-sm:text-xl">
                    @{{ title }}
                </h2>

                <div class="flex items-center justify-between gap-8">
                    <a
                        :href="navigationLink"
                        class="hidden max-lg:flex"
                        v-if="navigationLink"
                    >
                        <p class="items-center text-xl max-md:text-base max-sm:text-sm">
                            @lang('shop::app.components.products.carousel.view-all')

                            <span class="icon-arrow-right text-2xl max-md:text-lg max-sm:text-sm"></span>
                        </p>
                    </a>

                </div>
            </div>

            <div
                class="product-recommendation-grid mt-10 grid grid-cols-2 gap-4 max-md:mt-5 sm:grid-cols-3 lg:grid-cols-5 lg:gap-6"
            >
                <x-shop::products.card
                    v-for="product in products"
                    :key="product.id"
                />
            </div>

            <a
                :href="navigationLink"
                class="secondary-button mx-auto mt-5 block w-max rounded-2xl px-11 py-3 text-center text-base max-lg:mt-0 max-lg:hidden max-lg:py-3.5 max-md:rounded-lg"
                :aria-label="title"
                v-if="navigationLink"
            >
                @lang('shop::app.components.products.carousel.view-all')
            </a>
        </div>

        <!-- Product Card Listing -->
        <template v-if="isLoading">
            <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
        </template>

        <div
            v-else-if="hasError"
            class="container mt-10 max-lg:px-8 max-sm:!px-4"
            role="status"
        >
            <div class="flex items-center justify-between gap-4 border border-gray-200 px-4 py-3 text-sm text-gray-600">
                <span>Unable to load products right now.</span>

                <button
                    type="button"
                    class="font-medium text-navy hover:underline"
                    @click="getProducts"
                >
                    Try again
                </button>
            </div>
        </div>
    </script>

    @pushOnce('styles')
        <style>
            .product-recommendation-grid > :nth-child(n + 6) { display: none; }

            @media (max-width: 1024px) {
                .product-recommendation-grid > :nth-child(n + 4) { display: none; }
            }

            @media (max-width: 640px) {
                .product-recommendation-grid > :nth-child(n + 3) { display: none; }
            }
        </style>
    @endPushOnce

    <script type="module">
        app.component('v-products-carousel', {
            template: '#v-products-carousel-template',

            props: [
                'src',
                'title',
                'navigationLink',
            ],

            data() {
                return {
                    isLoading: true,

                    products: [],

                    hasError: false,

                };
            },

            mounted() {
                this.getProducts();
            },

            methods: {
                getProducts() {
                    this.isLoading = true;
                    this.hasError = false;

                    this.$axios.get(this.src)
                        .then(response => {
                            this.products = response.data.data ?? [];
                        }).catch(() => {
                            this.products = [];
                            this.hasError = true;
                        }).finally(() => {
                            this.isLoading = false;
                        });
                },

            },
        });
    </script>
@endPushOnce
