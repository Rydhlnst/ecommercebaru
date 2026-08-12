<!-- Top Selling Products Vue Component -->
<v-dashboard-top-selling-products>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.top-selling-products />
</v-dashboard-top-selling-products>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-top-selling-products-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.top-selling-products />
        </template>

        <!-- Top Selling Products Leaderboard -->
        <template v-else>
            <div class="flex flex-col">
                <div class="flex items-center justify-between p-4 bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="icon-product text-lg text-blue-600 dark:text-blue-400"></span>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                            @lang('admin::app.dashboard.index.top-selling-products')
                        </h4>
                    </div>

                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                        @{{ report.date_range }}
                    </span>
                </div>

                <!-- Product Items List -->
                <div
                    class="divide-y divide-gray-100 dark:divide-gray-800"
                    v-if="report.statistics.length"
                >
                    <a
                        :href="'{{ route('admin.catalog.products.edit', ':id') }}'.replace(':id', item.id)"
                        class="flex items-center gap-3 p-3.5 transition-all hover:bg-blue-50/50 dark:hover:bg-blue-950/30 group"
                        v-for="(item, index) in report.statistics"
                        :key="item.id"
                    >
                        <!-- Rank Badge -->
                        <span 
                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                            :class="{
                                'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300': index === 0,
                                'bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300': index === 1,
                                'bg-amber-800/10 text-amber-900 dark:bg-amber-900/30 dark:text-amber-400': index === 2,
                                'bg-gray-100 text-gray-500 dark:bg-gray-900 dark:text-gray-400': index > 2
                            }"
                        >
                            #@{{ index + 1 }}
                        </span>

                        <!-- Product Thumbnail -->
                        <img
                            v-if="item.images?.length"
                            class="h-11 w-11 shrink-0 rounded-lg object-cover border border-gray-200 dark:border-gray-800"
                            :src="item.images[0]?.url"
                            :alt="item.name"
                        />

                        <div
                            v-else
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 dark:border-gray-800 dark:bg-gray-950 text-gray-400"
                        >
                            <span class="icon-product text-xl"></span>
                        </div>

                        <!-- Product Name & Sales Details -->
                        <div class="flex flex-1 flex-col gap-0.5 overflow-hidden">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                @{{ item.name }}
                            </span>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">
                                    @{{ item.formatted_price }}
                                </span>

                                <span class="font-bold text-gray-900 dark:text-white bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 px-2 py-0.5 rounded-full">
                                    @{{ item.formatted_revenue }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Empty State -->
                <div
                    class="flex flex-col items-center justify-center p-8 text-center"
                    v-else
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 mb-2">
                        <span class="icon-product text-2xl"></span>
                    </div>

                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('admin::app.dashboard.index.add-product')
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        @lang('admin::app.dashboard.index.product-info')
                    </p>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-top-selling-products', {
            template: '#v-dashboard-top-selling-products-template',

            data() {
                return {
                    report: [],

                    isLoading: true,
                }
            },

            mounted() {
                this.getStats({});

                this.$emitter.on('reporting-filter-updated', this.getStats);
            },

            methods: {
                getStats(filters) {
                    this.isLoading = true;

                    var filters = Object.assign({}, filters);

                    filters.type = 'top-selling-products';

                    this.$axios.get("{{ route('admin.dashboard.stats') }}", {
                            params: filters
                        })
                        .then(response => {
                            this.report = response.data;

                            this.isLoading = false;
                        })
                        .catch(error => {});
                }
            }
        });
    </script>
@endPushOnce