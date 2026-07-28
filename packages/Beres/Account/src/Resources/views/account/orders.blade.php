<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Riwayat Pesanan
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('shop.customer.account.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Riwayat Pesanan</h1>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nomor Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($orders as $order)
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'canceled' => 'bg-red-100 text-red-800',
                                'shipped' => 'bg-purple-100 text-purple-800',
                            ];
                        @endphp
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                #{{ $order['increment_id'] ?? $order['id'] }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                {{ core()->formatPrice($order['grand_total']) }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($order['status']) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <a
                                    href="{{ route('shop.customer.account.orders.detail', $order['id']) }}"
                                    class="text-green-600 hover:text-green-800"
                                >
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Belum ada pesanan</p>
                                <a href="{{ route('shop.search.index') }}" class="mt-4 inline-block text-sm font-medium text-green-600 hover:text-green-800">
                                    Mulai Belanja
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-shop::layouts>
