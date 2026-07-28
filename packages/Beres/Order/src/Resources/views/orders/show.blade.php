<x-admin::layouts>
    <div class="flex gap-4 max-lg:flex-col">
        <div class="flex flex-1 flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Pesanan #{{ $order->incrementId ?? $order->id }}</h1>
                </div>
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'processing' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'canceled' => 'bg-red-100 text-red-800',
                        'shipped' => 'bg-purple-100 text-purple-800',
                    ];
                @endphp
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statuses[$order->status] ?? $order->status }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <!-- Order Info -->
                <div class="col-span-2">
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Informasi Pesanan</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Nomor Pesanan</dt>
                                <dd class="text-sm font-medium text-gray-900">#{{ $order->incrementId ?? $order->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Tanggal</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $order->createdAt ? \Carbon\Carbon::parse($order->createdAt)->format('d M Y H:i') : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Pelanggan</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $order->customerName ?? 'Guest' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Email</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $order->customerEmail ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Metode Pengiriman</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $order->shippingMethod ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Metode Pembayaran</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $order->paymentMethod ?? '-' }}</dd>
                            </div>
                        </dl>
                    </x-admin::card>

                    <!-- Order Items -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Item Pesanan</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse($order->items as $item)
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
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Tidak ada item
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Total</p>
                                <p class="text-xl font-bold text-gray-900">{{ core()->formatPrice($order->grandTotal) }}</p>
                            </div>
                        </div>
                    </x-admin::card>
                </div>

                <!-- Sidebar -->
                <div class="flex flex-col gap-4">
                    <!-- Update Status -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Update Status</h3>
                        @if(!empty($validTransitions))
                            <form id="status-form" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <div>
                                    <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        @foreach($validTransitions as $transition)
                                            <option value="{{ $transition }}">{{ $statuses[$transition] ?? $transition }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <textarea
                                        name="note"
                                        rows="3"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                        placeholder="Catatan (opsional)..."
                                    ></textarea>
                                </div>
                                <button
                                    type="submit"
                                    class="w-full rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                                >
                                    Update Status
                                </button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500">Status ini tidak dapat diubah.</p>
                        @endif
                    </x-admin::card>

                    <!-- Status History -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Riwayat Status</h3>
                        <div class="space-y-3">
                            @forelse($statusHistory as $history)
                                <div class="flex gap-3">
                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-100">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900">{{ $statuses[$history['status']] ?? $history['status'] }}</p>
                                        @if($history['old_status'])
                                            <p class="text-xs text-gray-500">Dari: {{ $statuses[$history['old_status']] ?? $history['old_status'] }}</p>
                                        @endif
                                        @if($history['note'])
                                            <p class="text-xs text-gray-500">{{ $history['note'] }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($history['created_at'])->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada riwayat</p>
                            @endforelse
                        </div>
                    </x-admin::card>

                    <!-- Activity Log -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Aktivitas</h3>
                        <div class="space-y-3">
                            @forelse($activityLog->take(5) as $log)
                                <div class="flex gap-3">
                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-100">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900">{{ ucfirst($log['action']) }}</p>
                                        <p class="text-xs text-gray-500">{{ $log['description'] ?? '' }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log['created_at'])->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada aktivitas</p>
                            @endforelse
                        </div>
                    </x-admin::card>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('status-form')?.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const orderId = {{ $order->id }};
                
                fetch(`/admin/orders/${orderId}/status`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        status: formData.get('status'),
                        note: formData.get('note'),
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal update status');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        </script>
    @endpush
</x-admin::layouts>
