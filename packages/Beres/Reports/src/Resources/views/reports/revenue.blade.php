<x-admin::layouts>
    <x-slot:title>
        Laporan Revenue
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.reports.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Laporan Revenue</h1>
            </div>
            <a
                href="{{ route('admin.reports.export', 'revenue') }}?{{ http_build_query($filters) }}"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Export CSV
            </a>
        </div>

        <!-- Filters -->
        <x-admin::card class="mb-6">
            <form action="{{ route('admin.reports.revenue') }}" method="GET" class="grid grid-cols-4 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Periode</label>
                    <select name="period" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="daily" {{ ($filters['period'] ?? '') == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="weekly" {{ ($filters['period'] ?? '') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ ($filters['period'] ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
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
                    <a href="{{ route('admin.reports.revenue') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </x-admin::card>

        <!-- Summary -->
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-admin::card>
                <p class="text-sm text-gray-500">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900">{{ core()->formatPrice($report['summary']['total_revenue']) }}</p>
            </x-admin::card>
            <x-admin::card>
                <p class="text-sm text-gray-500">Total Pesanan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $report['summary']['total_orders'] }}</p>
            </x-admin::card>
            <x-admin::card>
                <p class="text-sm text-gray-500">Rata-rata per Pesanan</p>
                <p class="text-2xl font-bold text-gray-900">{{ core()->formatPrice($report['summary']['average_order_value']) }}</p>
            </x-admin::card>
        </div>

        <!-- Chart -->
        <x-admin::card class="mb-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Grafik Revenue</h3>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </x-admin::card>

        <!-- Data Table -->
        <x-admin::card>
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Detail Data</h3>
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
                        @forelse($report['data'] as $row)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['date'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $row['order_count'] }}</td>
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('revenueChart');
            if (ctx) {
                const data = @json($report['data']);
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.date),
                        datasets: [{
                            label: 'Revenue',
                            data: data.map(d => d.revenue),
                            backgroundColor: 'rgba(45, 90, 39, 0.5)',
                            borderColor: 'rgb(45, 90, 39)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: v => 'Rp ' + v.toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                });
            }
        </script>
    @endpush
</x-admin::layouts>
