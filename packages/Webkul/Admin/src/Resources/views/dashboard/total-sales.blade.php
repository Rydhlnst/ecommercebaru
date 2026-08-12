<!-- Total Sales Vue Component -->
<v-dashboard-total-sales>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.total-sales />
</v-dashboard-total-sales>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-total-sales-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.total-sales />
        </template>

        <!-- Total Sales Section -->
        <template v-else>
            <div class="p-5 flex flex-col gap-4">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            @lang('admin::app.dashboard.index.total-sales')
                        </span>

                        <!-- Total Order Revenue -->
                        <span class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            @{{ report.statistics.total_sales.formatted_total }}
                        </span>
                    </div>

                    <div class="flex flex-col items-end gap-1">
                        <!-- Orders Time Duration Badge -->
                        <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                            @{{ report.date_range }}
                        </span>

                        <!-- Total Orders -->
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            @{{ "@lang('admin::app.dashboard.index.order')".replace(':total_orders', report.statistics.total_orders.current ?? 0) }}
                        </span>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="mt-2">
                    <x-admin::charts.bar
                        ::labels="chartLabels"
                        ::datasets="chartDatasets"
                        ::aspect-ratio="1.5"
                    />
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-total-sales', {
            template: '#v-dashboard-total-sales-template',

            data() {
                return {
                    report: [],

                    isLoading: true,
                }
            },

            computed: {
                chartLabels() {
                    return this.report.statistics.over_time.map(({ label }) => label);
                },

                chartDatasets() {
                    return [{
                        data: this.report.statistics.over_time.map(({ total }) => total),
                        barThickness: 8,
                        borderRadius: 4,
                        backgroundColor: '#3b82f6',
                    }];
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

                    filters.type = 'total-sales';

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