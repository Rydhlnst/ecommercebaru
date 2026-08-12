<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.dashboard.index.title')
    </x-slot>

    <!-- Header & Filter Bar -->
    <div class="flex items-center justify-between gap-4 mb-6 max-sm:flex-wrap">
        <div class="grid gap-1">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                <span>Welcome back, {{ auth()->guard('admin')->user()->name }}</span>
                <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-950 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10">Admin</span>
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Here's what's happening with your store performance today.
            </p>
        </div>

        <!-- Actions / Filters -->
        <v-dashboard-filters>
            <!-- Shimmer -->
            <div class="flex gap-2">
                <div class="shimmer h-[40px] w-[130px] rounded-lg"></div>
                <div class="shimmer h-[40px] w-[140px] rounded-lg"></div>
                <div class="shimmer h-[40px] w-[140px] rounded-lg"></div>
            </div>
        </v-dashboard-filters>
    </div>

    <!-- Body Component Grid -->
    <div class="mt-4 flex gap-6 max-xl:flex-col">
        <!-- Main Content Area (Left Stream) -->
        <div class="flex flex-col flex-1 gap-6 max-xl:w-full">
            {!! view_render_event('bagisto.admin.dashboard.overall_details.before') !!}

            <!-- Overall Details -->
            <x-admin::card title="{{ __('admin::app.dashboard.index.overall-details') }}">
                @include('admin::dashboard.over-all-details')
            </x-admin::card>

            {!! view_render_event('bagisto.admin.dashboard.overall_details.after') !!}

            {!! view_render_event('bagisto.admin.dashboard.todays_details.before') !!}

            <!-- Today's Details -->
            <x-admin::card title="{{ __('admin::app.dashboard.index.today-details') }}">
                @include('admin::dashboard.todays-details')
            </x-admin::card>

            {!! view_render_event('bagisto.admin.dashboard.todays_details.after') !!}

            {!! view_render_event('bagisto.admin.dashboard.stock_threshold.before') !!}

            <!-- Stock Threshold Alerts -->
            <x-admin::card title="{{ __('admin::app.dashboard.index.stock-threshold') }}">
                @include('admin::dashboard.stock-threshold-products')
            </x-admin::card>
            
            {!! view_render_event('bagisto.admin.dashboard.stock_threshold.after') !!}
        </div>

        <!-- Sidebar Analytics Area (Right Stream) -->
        <div class="flex w-[380px] max-w-full flex-col gap-6 max-xl:w-full">
            {!! view_render_event('bagisto.admin.dashboard.store_stats.before') !!}

            <!-- Store Stats Side Panel -->
            <x-admin::card title="{{ __('admin::app.dashboard.index.store-stats') }}" padding="p-0">
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    <!-- Total Sales Graph -->
                    @include('admin::dashboard.total-sales')

                    <!-- Top Selling Products -->
                    @include('admin::dashboard.top-selling-products')

                    <!-- Top Customers -->
                    @include('admin::dashboard.top-customers')
                </div>
            </x-admin::card>

            {!! view_render_event('bagisto.admin.dashboard.store_stats.after') !!}
        </div>
    </div>
    
    @pushOnce('scripts')
        <script
            type="module"
            src="{{ bagisto_asset('js/chart.js') }}"
        >
        </script>

        <script
            type="text/x-template"
            id="v-dashboard-filters-template"
        >
            <div class="flex items-center gap-2 flex-wrap">
                <template v-if="channels.length > 2">
                    <x-admin::dropdown position="bottom-right">
                        <x-slot:toggle>
                            <button
                                type="button"
                                class="inline-flex w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                            >
                                <span class="flex items-center gap-1.5">
                                    <span class="icon-store text-base text-gray-400"></span>
                                    @{{ channels.find(channel => channel.code == filters.channel).name }}
                                </span>
                                
                                <span class="text-xl icon-sort-down text-gray-400"></span>
                            </button>
                        </x-slot>

                        <x-slot:menu class="!p-1 shadow-lg border-gray-200 dark:border-gray-800 rounded-lg">
                            <x-admin::dropdown.menu.item
                                v-for="channel in channels"
                                ::class="{'bg-blue-50 text-blue-600 font-semibold dark:bg-blue-950 dark:text-blue-300': channel.code == filters.channel}"
                                @click="filters.channel = channel.code"
                            >
                                @{{ channel.name }}
                            </x-admin::dropdown.menu.item>
                        </x-slot>
                    </x-admin::dropdown>
                </template>

                <x-admin::flat-picker.date class="!w-[140px]" ::allow-input="false">
                    <input
                        class="flex min-h-[40px] w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm transition-all hover:border-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-700"
                        v-model="filters.start"
                        placeholder="@lang('admin::app.dashboard.index.start-date')"
                    />
                </x-admin::flat-picker.date>

                <x-admin::flat-picker.date class="!w-[140px]" ::allow-input="false">
                    <input
                        class="flex min-h-[40px] w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm transition-all hover:border-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-700"
                        v-model="filters.end"
                        placeholder="@lang('admin::app.dashboard.index.end-date')"
                    />
                </x-admin::flat-picker.date>
            </div>
        </script>

        <script type="module">
            app.component('v-dashboard-filters', {
                template: '#v-dashboard-filters-template',

                data() {
                    return {
                        channels: [
                            {
                                name: "@lang('admin::app.dashboard.index.all-channels')",
                                code: ''
                            },
                            ...@json(core()->getAllChannels()),
                        ],
                        
                        filters: {
                            channel: '',

                            start: "{{ $startDate->format('Y-m-d') }}",
                            
                            end: "{{ $endDate->format('Y-m-d') }}",
                        }
                    }
                },

                watch: {
                    filters: {
                        handler() {
                            this.$emitter.emit('reporting-filter-updated', this.filters);
                        },

                        deep: true
                    }
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
