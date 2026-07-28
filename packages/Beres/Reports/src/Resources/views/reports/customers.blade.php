<x-admin::layouts>
    <x-slot:title>
        Laporan Pelanggan
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.reports.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Laporan Pelanggan</h1>
            </div>
            <a
                href="{{ route('admin.reports.export', 'customers') }}?{{ http_build_query($filters) }}"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Export CSV
            </a>
        </div>

        <!-- Filters -->
        <x-admin::card class="mb-6">
            <form action="{{ route('admin.reports.customers') }}" method="GET" class="grid grid-cols-4 gap-4">
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
                    <a href="{{ route('admin.reports.customers') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </x-admin::card>

        <!-- Summary -->
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-admin::card>
                <p class="text-sm text-gray-500">Total Pelanggan</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($report['summary']['total_customers']) }}</p>
            </x-admin::card>
            <x-admin::card>
                <p class="text-sm text-gray-500">Pelanggan Baru</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($report['summary']['new_customers']) }}</p>
            </x-admin::card>
        </div>

        <!-- Top Spenders -->
        <x-admin::card class="mb-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Top Spenders</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($report['top_spenders'] as $index => $customer)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $customer['first_name'] }} {{ $customer['last_name'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $customer['email'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ core()->formatPrice($customer['orders_sum_grand_total'] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin::card>

        <!-- New Customers Chart -->
        <x-admin::card>
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Pelanggan Baru per Hari</h3>
            <div class="h-80">
                <canvas id="customersChart"></canvas>
            </div>
        </x-admin::card>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('customersChart');
            if (ctx) {
                const data = @json($report['new_customers']);
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.date),
                        datasets: [{
                            label: 'Pelanggan Baru',
                            data: data.map(d => d.count),
                            borderColor: 'rgb(249, 115, 22)',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }
        </script>
    @endpush
</x-admin::layouts>
