<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Detail Pesanan #{{ $order->increment_id ?? $order->id }}
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('shop.customer.account.orders') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Detail Pesanan #{{ $order->increment_id ?? $order->id }}</h1>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Order Info -->
            <div class="lg:col-span-2">
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Informasi Pesanan</h2>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Nomor Pesanan</dt>
                            <dd class="text-sm font-medium text-gray-900">#{{ $order->increment_id ?? $order->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Tanggal</dt>
                            <dd class="text-sm text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'canceled' => 'bg-red-100 text-red-800',
                                        'shipped' => 'bg-purple-100 text-purple-800',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Metode Pembayaran</dt>
                            <dd class="text-sm text-gray-900">{{ $order->payment_method ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Order Items -->
                <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Item Pesanan</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Qty</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $item->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ core()->formatPrice($item->price) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ $item->qty_ordered }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ core()->formatPrice($item->total) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <div class="flex justify-end">
                            <div class="w-64 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Subtotal</span>
                                    <span class="text-gray-900">{{ core()->formatPrice($order->subtotal) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Pengiriman</span>
                                    <span class="text-gray-900">{{ core()->formatPrice($order->shipping_amount) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Pajak</span>
                                    <span class="text-gray-900">{{ core()->formatPrice($order->tax_amount) }}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-2 text-lg font-semibold">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-gray-900">{{ core()->formatPrice($order->grand_total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="flex flex-col gap-6">
                <!-- Shipping Info -->
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Informasi Pengiriman</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500">Metode</dt>
                            <dd class="text-sm text-gray-900">{{ $order->shipping_method ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Biaya</dt>
                            <dd class="text-sm text-gray-900">{{ core()->formatPrice($order->shipping_amount) }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Shipping Address -->
                @if($order->shipping_address)
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Alamat Pengiriman</h2>
                        <div class="text-sm text-gray-600">
                            <p>{{ $order->shipping_address->first_name }} {{ $order->shipping_address->last_name }}</p>
                            <p>{{ $order->shipping_address->address1 }}</p>
                            <p>{{ $order->shipping_address->city }}, {{ $order->shipping_address->state }}</p>
                            <p>{{ $order->shipping_address->postcode }} {{ $order->shipping_address->country }}</p>
                            <p class="mt-2">Telp: {{ $order->shipping_address->phone }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-shop::layouts>
