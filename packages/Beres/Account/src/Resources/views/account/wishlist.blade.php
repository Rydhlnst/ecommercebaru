<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Wishlist Saya
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('shop.customer.account.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Wishlist Saya</h1>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($wishlist as $item)
                <div class="group overflow-hidden rounded-lg border border-gray-200 bg-white transition-all hover:shadow-lg">
                    <div class="relative aspect-square overflow-hidden bg-gray-100">
                        @if(isset($item['product']['images'][0]))
                            <img
                                src="{{ asset('storage/' . $item['product']['images'][0]['path']) }}"
                                alt="{{ $item['product']['name'] }}"
                                class="h-full w-full object-cover transition-transform group-hover:scale-105"
                            />
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <svg class="h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <button
                            onclick="removeFromWishlist({{ $item['product_id'] }})"
                            class="absolute right-2 top-2 rounded-full bg-white p-2 text-red-500 shadow-sm opacity-0 transition-opacity group-hover:opacity-100"
                        >
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <h3 class="line-clamp-2 text-sm font-medium text-gray-900">
                            {{ $item['product']['name'] ?? 'Produk' }}
                        </h3>
                        <p class="mt-2 text-lg font-semibold text-green-600">
                            {{ core()->formatPrice($item['product']['price'] ?? 0) }}
                        </p>
                        <div class="mt-4 flex gap-2">
                            <a
                                href="{{ route('shop.home.index') }}"
                                class="flex-1 rounded-lg bg-green-600 py-2 text-center text-sm font-medium text-white hover:bg-green-700"
                            >
                                Lihat Produk
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Wishlist Anda kosong</p>
                    <a href="{{ route('shop.search.index') }}" class="mt-4 inline-block text-sm font-medium text-green-600 hover:text-green-800">
                        Mulai Belanja
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
        <script>
            function removeFromWishlist(productId) {
                if (!confirm('Yakin ingin menghapus dari wishlist?')) {
                    return;
                }

                fetch(`/account/wishlist/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus dari wishlist');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        </script>
    @endpush
</x-shop::layouts>
