<!-- Top Selling Products Vue Component -->
<v-dashboard-top-customers>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.top-customers />
</v-dashboard-top-customers>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-top-customers-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.top-customers />
        </template>

        <!-- Top Customers Leaderboard -->
        <template v-else>
            <div class="flex flex-col">
                <div class="flex items-center justify-between p-4 bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="icon-customer text-lg text-purple-600 dark:text-purple-400"></span>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                            @lang('admin::app.dashboard.index.customer-with-most-sales')
                        </h4>
                    </div>

                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                        @{{ report.date_range }}
                    </span>
                </div>

                <!-- Customer Items List -->
                <div
                    class="divide-y divide-gray-100 dark:divide-gray-800"
                    v-if="report.statistics.length"
                >
                    <a 
                        :href="customer.id ? '{{ route('admin.customers.customers.view', ':id') }}'.replace(':id', customer.id) : '#'"
                        class="flex items-center gap-3 p-3.5 transition-all hover:bg-purple-50/50 dark:hover:bg-purple-950/30 group"
                        v-for="(customer, index) in report.statistics"
                        :key="customer.id"
                    >
                        <!-- Avatar Badge with Initials -->
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 font-bold text-xs">
                            @{{ customer.full_name ? customer.full_name.charAt(0).toUpperCase() : 'C' }}
                        </div>

                        <!-- Customer Details -->
                        <div class="flex flex-1 flex-col gap-0.5 overflow-hidden">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                @{{ customer.full_name }}
                            </span>

                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                @{{ customer.email }}
                            </span>
                        </div>

                        <!-- Order Count & Spend Total -->
                        <div class="flex flex-col items-end gap-0.5 shrink-0">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                @{{ customer.formatted_total }}
                            </span>

                            <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-[11px] font-medium text-gray-600 dark:text-gray-400" v-if="customer.orders">
                                @{{ "@lang('admin::app.dashboard.index.order-count')".replace(':count', customer.orders) }}
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Empty State -->
                <div
                    class="flex flex-col items-center justify-center p-8 text-center"
                    v-else
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 mb-2">
                        <span class="icon-customer text-2xl"></span>
                    </div>

                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('admin::app.dashboard.index.add-customer')
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        @lang('admin::app.dashboard.index.customer-info')
                    </p>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-top-customers', {
            template: '#v-dashboard-top-customers-template',

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

                    filters.type = 'top-customers';

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