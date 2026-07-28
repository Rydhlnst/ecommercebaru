<x-admin::layouts>
    <div class="flex gap-4 max-lg:flex-col">
        <div class="flex flex-1 flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Pembayaran</h1>
                <div class="flex gap-2">
                    <a
                        href="{{ route('admin.payments.export') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Export CSV
                    </a>
                </div>
            </div>

            <!-- Payment Info -->
            <x-admin::card>
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Gateway</p>
                        <p class="text-lg font-semibold text-gray-900">Midtrans</p>
                    </div>
                    <div class="ml-auto">
                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold bg-green-100 text-green-800">
                            {{ config('midtrans.environment') === 'production' ? 'Production' : 'Sandbox' }}
                        </span>
                    </div>
                </div>
            </x-admin::card>

            <!-- Transactions Table -->
            <x-admin::card>
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Riwayat Transaksi</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fraud</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($transactions as $transaction)
                                @php
                                    $statusColors = [
                                        'settlement' => 'bg-green-100 text-green-800',
                                        'pending'    => 'bg-yellow-100 text-yellow-800',
                                        'cancel'     => 'bg-red-100 text-red-800',
                                        'expire'     => 'bg-red-100 text-red-800',
                                        'deny'       => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        #{{ $transaction['id'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        #{{ $transaction['order_id'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $transaction['payment_method'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ core()->formatPrice($transaction['gross_amount']) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $statusColors[$transaction['status']] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statuses[$transaction['status']] ?? $transaction['status'] }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $transaction['fraud_status'] ?? '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada transaksi ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-admin::card>
        </div>
    </div>
</x-admin::layouts>
