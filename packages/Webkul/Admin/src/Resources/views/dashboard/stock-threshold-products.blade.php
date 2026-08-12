<!-- Stock Threshold Products Vue Component -->
<v-dashboard-stock-threshold-products>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.stock-threshold-products />
</v-dashboard-stock-threshold-products>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-stock-threshold-products-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.stock-threshold-products />
        </template>

        <!-- Low Stock Alert Cards Section -->
        <template v-else>
            <!-- Stock Threshold Products List -->
            <div
                class="flex flex-col gap-3"
                v-if="report.statistics.length"
            >
                <div
                    v-for="product in report.statistics"
                    class="group flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm transition-all hover:border-amber-300 dark:hover:border-amber-800 hover:shadow-md"
                >
                    <!-- Product Image & Details -->
                    <div class="flex items-center gap-3">
                        <template v-if="product.image">
                            <img
                                class="h-14 w-14 shrink-0 rounded-lg object-cover border border-gray-200 dark:border-gray-800"
                                :src="product.image"
                                :alt="product.name"
                            >
                        </template>

                        <template v-else>
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 dark:border-gray-800 dark:bg-gray-950 text-gray-400">
                                <span class="icon-product text-2xl"></span>
                            </div>
                        </template>

                        <div class="flex flex-col gap-0.5">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1">
                                @{{ product.name }}
                            </h4>

                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-xs font-mono text-gray-600 dark:text-gray-400">
                                    SKU: @{{ product.sku }}
                                </span>

                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                    @{{ product.formatted_price }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Status & Edit Action -->
                    <div class="flex items-center justify-between sm:justify-end gap-4">
                        <div class="flex flex-col items-start sm:items-end gap-1">
                            <span 
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                :class="[product.total_qty <= 0 ? 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400']"
                            >
                                <span class="h-1.5 w-1.5 rounded-full" :class="[product.total_qty <= 0 ? 'bg-red-500' : 'bg-amber-500']"></span>
                                @{{ "@lang('admin::app.dashboard.index.total-stock', ['total_stock' => ':replace'])".replace(':replace', product.total_qty) }}
                            </span>
                        </div>

                        <a 
                            :href="'{{ route('admin.catalog.products.edit', ':replace') }}'.replace(':replace', product.id)"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300 transition-all hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-950 dark:hover:text-amber-400"
                            title="Edit Product"
                        >
                            <span class="icon-edit text-lg"></span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Empty Product Design -->
            <div
                class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 dark:border-gray-800 p-8 text-center bg-gray-50/50 dark:bg-gray-900/50"
                v-else
            >
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 mb-3">
                    <span class="icon-uncheck text-2xl"></span>
                </div>
                
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                    @lang('admin::app.dashboard.index.empty-threshold')
                </h4>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('admin::app.dashboard.index.empty-threshold-description')
                </p>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-stock-threshold-products', {
            template: '#v-dashboard-stock-threshold-products-template',

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

                    filters.type = 'stock-threshold-products';

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