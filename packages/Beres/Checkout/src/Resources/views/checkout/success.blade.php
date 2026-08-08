<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Pesanan Berhasil
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Success Icon -->
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="mb-4 text-3xl font-bold text-gray-900">Pesanan Berhasil!</h1>
            <p class="mb-8 text-lg text-gray-600">
                Terima kasih telah berbelanja di {{ config('app.name') }}.
            </p>

            @if($order)
                <!-- Order Details -->
                <div class="mx-auto max-w-md rounded-lg border border-gray-200 bg-white p-6 text-left">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Detail Pesanan</h2>

                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Nomor Pesanan</dt>
                            <dd class="text-sm font-medium text-gray-900">#{{ $order->increment_id ?? $order->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Tanggal</dt>
                            <dd class="text-sm text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Total</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ core()->formatPrice($order->grand_total) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
                                <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">
                                    Menunggu Pembayaran
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-center gap-4">
                    <a
                        href="{{ route('shop.home.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Lanjut Belanja
                    </a>
                    <a
                        href="{{ route('shop.customer.account.orders') }}"
                        class="rounded-lg bg-green-600 px-6 py-3 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Lihat Pesanan
                    </a>
                </div>
            @else
                <div class="mt-8">
                    <a
                        href="{{ route('shop.home.index') }}"
                        class="rounded-lg bg-green-600 px-6 py-3 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Kembali ke Beranda
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-shop::layouts>
