<!-- Over Details Vue Component -->
<v-dashboard-overall-details>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.over-all-details />
</v-dashboard-overall-details>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-overall-details-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.over-all-details />
        </template>

        <!-- Total Sales Section & Grid -->
        <template v-else>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                <!-- Total Sales Card -->
                <div class="flex flex-col justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-950 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            @lang('admin::app.dashboard.index.total-sales')
                        </span>

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400">
                            <span class="icon-sales text-xl"></span>
                        </div>
                    </div>

                    <div class="mt-3 flex items-baseline justify-between gap-2">
                        <p class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            @{{ report.statistics.total_sales.formatted_total }}
                        </p>

                        <!-- Sales Percentage Badge -->
                        <div
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                            :class="[report.statistics.total_sales.progress < 0 ? 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400']"
                        >
                            <span :class="[report.statistics.total_sales.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                            <span>@{{ Math.abs(report.statistics.total_sales.progress.toFixed(2)) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Total Orders Card -->
                <div class="flex flex-col justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-950 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            @lang('admin::app.dashboard.index.total-orders')
                        </span>

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
                            <span class="icon-orders text-xl"></span>
                        </div>
                    </div>

                    <div class="mt-3 flex items-baseline justify-between gap-2">
                        <p class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            @{{ report.statistics.total_orders.current }}
                        </p>

                        <!-- Order Percentage Badge -->
                        <div
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                            :class="[report.statistics.total_orders.progress < 0 ? 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400']"
                        >
                            <span :class="[report.statistics.total_orders.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                            <span>@{{ Math.abs(report.statistics.total_orders.progress.toFixed(2)) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Total Customers Card -->
                <div class="flex flex-col justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-950 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            @lang('admin::app.dashboard.index.total-customers')
                        </span>

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400">
                            <span class="icon-customer text-xl"></span>
                        </div>
                    </div>

                    <div class="mt-3 flex items-baseline justify-between gap-2">
                        <p class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            @{{ report.statistics.total_customers.current }}
                        </p>

                        <!-- Customers Percentage Badge -->
                        <div
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                            :class="[report.statistics.total_customers.progress < 0 ? 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400']"
                        >
                            <span :class="[report.statistics.total_customers.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                            <span>@{{ Math.abs(report.statistics.total_customers.progress.toFixed(2)) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Average Sales Card -->
                <div class="flex flex-col justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-950 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            @lang('admin::app.dashboard.index.average-sale')
                        </span>

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-950/60 dark:text-sky-400">
                            <span class="icon-attribute text-xl"></span>
                        </div>
                    </div>

                    <div class="mt-3 flex items-baseline justify-between gap-2">
                        <p class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            @{{ report.statistics.avg_sales.formatted_total }}
                        </p>

                        <!-- Avg Sales Percentage Badge -->
                        <div
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                            :class="[report.statistics.avg_sales.progress < 0 ? 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400']"
                        >
                            <span :class="[report.statistics.avg_sales.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                            <span>@{{ Math.abs(report.statistics.avg_sales.progress.toFixed(2)) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Unpaid Invoices Card -->
                <div class="flex flex-col justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-950 shadow-sm transition-all hover:shadow-md sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            @lang('admin::app.dashboard.index.total-unpaid-invoices')
                        </span>

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400">
                            <span class="icon-pending text-xl"></span>
                        </div>
                    </div>

                    <div class="mt-3 flex items-baseline justify-between gap-2">
                        <p class="text-xl font-bold tracking-tight text-amber-600 dark:text-amber-400">
                            @{{ report.statistics.total_unpaid_invoices.formatted_total }}
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-overall-details', {
            template: '#v-dashboard-overall-details-template',

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

                    filters.type = 'over-all';

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