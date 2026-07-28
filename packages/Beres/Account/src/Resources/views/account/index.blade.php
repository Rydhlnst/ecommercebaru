<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Akun Saya
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-gray-900">Akun Saya</h1>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Stats Cards -->
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Pesanan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Belanja</p>
                        <p class="text-2xl font-bold text-gray-900">{{ core()->formatPrice($stats['total_spent'] ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Wishlist</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['wishlist_count'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a
                href="{{ route('shop.customer.account.profile') }}"
                class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-green-500 hover:shadow-md"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Profil</p>
                    <p class="text-xs text-gray-500">Edit informasi akun</p>
                </div>
            </a>

            <a
                href="{{ route('shop.customer.account.orders') }}"
                class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-green-500 hover:shadow-md"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Pesanan</p>
                    <p class="text-xs text-gray-500">Riwayat pesanan</p>
                </div>
            </a>

            <a
                href="{{ route('shop.customer.account.addresses') }}"
                class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-green-500 hover:shadow-md"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.414 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Alamat</p>
                    <p class="text-xs text-gray-500">Kelola alamat</p>
                </div>
            </a>

            <a
                href="{{ route('shop.customer.account.wishlist') }}"
                class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-green-500 hover:shadow-md"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Wishlist</p>
                    <p class="text-xs text-gray-500">Produk favorit</p>
                </div>
            </a>
        </div>
    </div>
</x-shop::layouts>
