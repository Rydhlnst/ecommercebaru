<x-admin::layouts>
    <x-slot:title>
        Laporan
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-gray-900">Laporan</h1>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <!-- Revenue Report -->
            <a
                href="{{ route('admin.reports.revenue') }}"
                class="group rounded-lg border border-gray-200 bg-white p-6 transition-all hover:border-green-500 hover:shadow-lg"
            >
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Laporan Revenue</h3>
                <p class="mt-2 text-sm text-gray-500">Pendapatan harian, mingguan, dan bulanan</p>
            </a>

            <!-- Orders Report -->
            <a
                href="{{ route('admin.reports.orders') }}"
                class="group rounded-lg border border-gray-200 bg-white p-6 transition-all hover:border-blue-500 hover:shadow-lg"
            >
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Laporan Pesanan</h3>
                <p class="mt-2 text-sm text-gray-500">Jumlah pesanan dan status</p>
            </a>

            <!-- Products Report -->
            <a
                href="{{ route('admin.reports.products') }}"
                class="group rounded-lg border border-gray-200 bg-white p-6 transition-all hover:border-purple-500 hover:shadow-lg"
            >
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Laporan Produk</h3>
                <p class="mt-2 text-sm text-gray-500">Produk terlaris dan stok rendah</p>
            </a>

            <!-- Customers Report -->
            <a
                href="{{ route('admin.reports.customers') }}"
                class="group rounded-lg border border-gray-200 bg-white p-6 transition-all hover:border-orange-500 hover:shadow-lg"
            >
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Laporan Pelanggan</h3>
                <p class="mt-2 text-sm text-gray-500">Pelanggan baru dan top spenders</p>
            </a>
        </div>
    </div>
</x-admin::layouts>
