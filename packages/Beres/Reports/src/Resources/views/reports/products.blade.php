<x-admin::layouts>
    <x-slot:title>
        Laporan Produk
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.reports.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Laporan Produk</h1>
            </div>
            <a
                href="{{ route('admin.reports.export', 'products') }}?{{ http_build_query($filters) }}"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Export CSV
            </a>
        </div>

        <!-- Filters -->
        <x-admin::card class="mb-6">
            <form action="{{ route('admin.reports.products') }}" method="GET" class="grid grid-cols-4 gap-4">
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
                    <a href="{{ route('admin.reports.products') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </x-admin::card>

        <!-- Summary -->
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-admin::card>
                <p class="text-sm text-gray-500">Total Terjual</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($report['summary']['total_products_sold']) }}</p>
            </x-admin::card>
            <x-admin::card>
                <p class="text-sm text-gray-500">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900">{{ core()->formatPrice($report['summary']['total_revenue']) }}</p>
            </x-admin::card>
        </div>

        <!-- Top Selling Products -->
        <x-admin::card class="mb-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Produk Terlaris</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terjual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($report['top_selling'] as $index => $product)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $product['product_name'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ number_format($product['total_sold']) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ core()->formatPrice($product['total_revenue']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin::card>

        <!-- Low Stock Products -->
        <x-admin::card>
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Stok Rendah</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($report['low_stock'] as $product)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $product['name'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $product['sku'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada produk stok rendah</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-admin::card>
    </div>
</x-admin::layouts>
