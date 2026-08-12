<!-- Todays Details Vue Component -->
<v-dashboard-todays-details>
    <!-- Shimmer -->
    <x-admin::shimmer.dashboard.todays-details />
</v-dashboard-todays-details>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-todays-details-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x-admin::shimmer.dashboard.todays-details />
        </template>

        <!-- Today Sales & Orders Section -->
        <template v-else>
            <div class="flex flex-col gap-5">
                <!-- Summary Metrics Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <!-- Today's Sales -->
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">
                            <span class="icon-sales text-xl"></span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('admin::app.dashboard.index.today-sales')</span>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">@{{ report.statistics.total_sales.formatted_total }}</span>
                                <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600" :class="[report.statistics.total_sales.progress < 0 ? '!text-red-500' : '']">
                                    <span :class="[report.statistics.total_sales.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                                    @{{ Math.abs(report.statistics.total_sales.progress.toFixed(2)) }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Orders -->
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400">
                            <span class="icon-orders text-xl"></span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('admin::app.dashboard.index.today-orders')</span>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">@{{ report.statistics.total_orders.current }}</span>
                                <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600" :class="[report.statistics.total_orders.progress < 0 ? '!text-red-500' : '']">
                                    <span :class="[report.statistics.total_orders.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                                    @{{ Math.abs(report.statistics.total_orders.progress.toFixed(2)) }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Customers -->
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400">
                            <span class="icon-customer text-xl"></span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('admin::app.dashboard.index.today-customers')</span>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">@{{ report.statistics.total_customers.current }}</span>
                                <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600" :class="[report.statistics.total_customers.progress < 0 ? '!text-red-500' : '']">
                                    <span :class="[report.statistics.total_customers.progress < 0 ? 'icon-down-stat' : 'icon-up-stat']"></span>
                                    @{{ Math.abs(report.statistics.total_customers.progress.toFixed(2)) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today Orders List -->
                <div class="flex flex-col gap-3">
                    <div 
                        v-for="order in report.statistics.orders"
                        class="group flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-800 hover:shadow-md"
                    >
                        <!-- Order & Date Info -->
                        <div class="flex items-start gap-3 min-w-[200px]">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 font-bold text-gray-700 dark:text-gray-200 text-xs">
                                #@{{ order.increment_id }}
                            </div>

                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                        @{{ "@lang('admin::app.dashboard.index.order-id', ['id' => ':replace'])".replace(':replace', order.increment_id) }}
                                    </span>

                                    <!-- Status Pill -->
                                    <span 
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400': order.status === 'completed',
                                            'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400': order.status === 'processing',
                                            'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400': order.status === 'pending',
                                            'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400': order.status === 'canceled' || order.status === 'closed'
                                        }"
                                    >
                                        @{{ order.status_label }}
                                    </span>
                                </div>

                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    @{{ order.created_at }}
                                </span>
                            </div>
                        </div>

                        <!-- Amount & Payment Info -->
                        <div class="flex flex-col gap-0.5 min-w-[160px]">
                            <span class="text-base font-bold text-gray-900 dark:text-white">
                                @{{ order.formatted_base_grand_total }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                @{{ order.payment_method }} • @{{ order.channel_name }}
                            </span>
                        </div>

                        <!-- Customer Info -->
                        <div class="flex flex-col gap-0.5 min-w-[200px]">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                @{{ order.customer_name }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[220px]">
                                @{{ order.customer_email }}
                            </span>
                        </div>

                        <!-- Items Thumbnails & Action -->
                        <div class="flex items-center justify-between md:justify-end gap-3 min-w-[160px]">
                            <div class="flex items-center gap-1 overflow-hidden" v-html="order.items">
                            </div>

                            <a 
                                :href="'{{ route('admin.sales.orders.view', ':replace') }}'.replace(':replace', order.id)"
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300 transition-all hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-950 dark:hover:text-blue-400"
                                title="View Details"
                            >
                                <span class="icon-sort-right text-lg"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-todays-details', {
            template: '#v-dashboard-todays-details-template',

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

                    filters.type = 'today';

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