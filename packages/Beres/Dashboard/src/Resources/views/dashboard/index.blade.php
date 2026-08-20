<x-admin::layouts>
    <div class="flex gap-4 max-lg:flex-col">
        <div class="flex flex-1 flex-col gap-4">
            <!-- Today's Revenue Card -->
            <x-admin::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Revenue Hari Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ core()->formatPrice($metrics->todayRevenue) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </x-admin::card>

            <!-- Orders Summary Cards -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <!-- Today's Orders -->
                <x-admin::card>
                    <p class="text-sm text-gray-500">Pesanan Hari Ini</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $metrics->todayOrders }}</p>
                </x-admin::card>

                <!-- Pending Orders -->
                <x-admin::card>
                    <p class="text-sm text-gray-500">Menunggu</p>
                    <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $metrics->pendingOrders }}</p>
                </x-admin::card>

                <!-- Paid Orders -->
                <x-admin::card>
                    <p class="text-sm text-gray-500">Dibayar</p>
                    <p class="mt-1 text-2xl font-bold text-blue-600">{{ $metrics->paidOrders }}</p>
                </x-admin::card>

                <!-- Cancelled Orders -->
                <x-admin::card>
                    <p class="text-sm text-gray-500">Dibatalkan</p>
                    <p class="mt-1 text-2xl font-bold text-red-600">{{ $metrics->cancelledOrders }}</p>
                </x-admin::card>
            </div>

            <!-- Revenue Chart -->
            <x-admin::card>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Grafik Revenue Bulanan</h3>
                    <button
                        type="button"
                        class="text-sm text-blue-600 hover:text-blue-800"
                        onclick="refreshChart()"
                    >
                        Refresh
                    </button>
                </div>
                <div class="h-80" id="revenueChart">
                    <canvas id="revenueCanvas"></canvas>
                </div>
            </x-admin::card>

            <!-- Recent Orders -->
            <x-admin::card>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Pesanan Terbaru</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Lihat Semua →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($metrics->recentOrders as $order)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        #{{ $order['id'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $order['customer']['full_name'] ?? 'Guest' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ core()->formatPrice($order['grand_total']) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                            @if($order['status'] === 'processing') bg-blue-100 text-blue-800
                                            @elseif($order['status'] === 'completed') bg-green-100 text-green-800
                                            @elseif($order['status'] === 'canceled') bg-red-100 text-red-800
                                            @elseif($order['status'] === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($order['status']) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada pesanan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-admin::card>
        </div>

        <!-- Right Sidebar -->
        <div class="flex w-80 flex-col gap-4 max-lg:w-full">
            <!-- Total Customers -->
            <x-admin::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Pelanggan</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $metrics->totalCustomers }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </x-admin::card>

            <!-- Top Selling Products -->
            <x-admin::card>
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Produk Terlaris</h3>
                <div class="space-y-3">
                    @forelse($metrics->topSellingProducts->take(5) as $product)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded bg-gray-100">
                                    @if(isset($product['images'][0]))
                                        <img
                                            src="{{ asset('storage/' . $product['images'][0]['path']) }}"
                                            alt="{{ $product['name'] }}"
                                            class="h-full w-full object-contain p-1"
                                        />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $product['name'] }}</p>
                                    <p class="text-xs text-gray-500">Terjual: {{ $product['total_sold'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada data penjualan.</p>
                    @endforelse
                </div>
            </x-admin::card>

            <!-- Latest Activity -->
            <x-admin::card>
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                <div class="space-y-3">
                    @forelse($metrics->latestActivity as $activity)
                        <div class="flex gap-3">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-100">
                                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-900">{{ $activity['description'] ?? $activity['action'] }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </x-admin::card>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let revenueChart = null;

            function initChart() {
                const ctx = document.getElementById('revenueCanvas');
                if (!ctx) return;

                const chartData = @json($chartData);

                revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Revenue',
                            data: chartData.data,
                            borderColor: '#2D5A27',
                            backgroundColor: 'rgba(45, 90, 39, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function refreshChart() {
                fetch('{{ route("admin.dashboard.chart") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && revenueChart) {
                            revenueChart.data.labels = data.data.labels;
                            revenueChart.data.datasets[0].data = data.data.data;
                            revenueChart.update();
                        }
                    });
            }

            document.addEventListener('DOMContentLoaded', initChart);
        </script>
    @endpush
</x-admin::layouts>
