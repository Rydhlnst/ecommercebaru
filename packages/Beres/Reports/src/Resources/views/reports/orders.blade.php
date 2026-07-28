<x-admin::layouts>
    <x-slot:title>
        Laporan Pesanan
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.reports.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Laporan Pesanan</h1>
            </div>
            <a
                href="{{ route('admin.reports.export', 'orders') }}?{{ http_build_query($filters) }}"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Export CSV
            </a>
        </div>

        <!-- Filters -->
        <x-admin::card class="mb-6">
            <form action="{{ route('admin.reports.orders') }}" method="GET" class="grid grid-cols-4 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Filter</button>
                    <a href="{{ route('admin.reports.orders') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </x-admin::card>

        <!-- Summary -->
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-admin::card>
                <p class="text-sm text-gray-500">Total Pesanan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $report['summary']['total_orders'] }}</p>
            </x-admin::card>
            <x-admin::card>
                <p class="text-sm text-gray-500">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900">{{ core()->formatPrice($report['summary']['total_revenue']) }}</p>
            </x-admin::card>
        </div>

        <!-- Status Breakdown -->
        <x-admin::card class="mb-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Status Pesanan</h3>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach($report['status_breakdown'] as $status)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">{{ ucfirst($status['status']) }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $status['count'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-admin::card>

        <!-- Data Table -->
        <x-admin::card>
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Detail Harian</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($report['daily_orders'] as $row)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['date'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $row['count'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ core()->formatPrice($row['revenue']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-admin::card>
    </div>
</x-admin::layouts>
