<x-admin::layouts>
    <div class="flex gap-4 max-lg:flex-col">
        <div class="flex flex-1 flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $customer->getFullName() }}</h1>
                </div>
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                    {{ $customer->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $customer->isActive() ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <!-- Customer Info -->
                <div class="col-span-2">
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Informasi Pelanggan</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Email</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $customer->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Telepon</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $customer->phone ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Grup</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $customer->group ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Tanggal Gabung</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $customer->createdAt ? \Carbon\Carbon::parse($customer->createdAt)->format('d M Y') : '-' }}</dd>
                            </div>
                        </dl>
                    </x-admin::card>

                    <!-- Addresses -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Alamat</h3>
                        @if(!empty($customer->addresses))
                            <div class="grid grid-cols-2 gap-4">
                                @foreach($customer->addresses as $address)
                                    <div class="rounded-lg border border-gray-200 p-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $address['first_name'] }} {{ $address['last_name'] }}
                                        </p>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $address['address1'] ?? '' }}
                                            {{ $address['address2'] ?? '' }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $address['city'] ?? '' }}, {{ $address['state'] ?? '' }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $address['postcode'] ?? '' }} {{ $address['country'] ?? '' }}
                                        </p>
                                        <p class="mt-2 text-sm text-gray-600">
                                            Telp: {{ $address['phone'] ?? '-' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Belum ada alamat</p>
                        @endif
                    </x-admin::card>

                    <!-- Order History -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Riwayat Pesanan</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse($customer->orders ?? [] as $order)
                                        <tr>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                                #{{ $order['id'] }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                                {{ core()->formatPrice($order['grand_total']) }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                                    @if($order['status'] === 'processing') bg-blue-100 text-blue-800
                                                    @elseif($order['status'] === 'completed') bg-green-100 text-green-800
                                                    @elseif($order['status'] === 'canceled') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($order['status']) }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Belum ada pesanan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-admin::card>
                </div>

                <!-- Sidebar -->
                <div class="flex flex-col gap-4">
                    <!-- Statistics -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Statistik</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Total Pesanan</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats->totalOrders }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Belanja</p>
                                <p class="text-2xl font-bold text-gray-900">{{ core()->formatPrice($stats->totalSpent) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Rata-rata Pesanan</p>
                                <p class="text-2xl font-bold text-gray-900">{{ core()->formatPrice($stats->averageOrderValue) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pesanan Terakhir</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $stats->lastOrderDate ? \Carbon\Carbon::parse($stats->lastOrderDate)->format('d M Y H:i') : '-' }}
                                </p>
                            </div>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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

                    <!-- Notes -->
                    <x-admin::card>
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Catatan</h3>
                            <button
                                type="button"
                                onclick="openNoteModal()"
                                class="text-sm text-blue-600 hover:text-blue-800"
                            >
                                + Tambah
                            </button>
                        </div>
                        <div class="space-y-3">
                            @forelse($notes as $note)
                                <div class="rounded-lg border border-gray-200 p-3">
                                    <p class="text-sm text-gray-900">{{ $note['note'] }}</p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $note['admin']['name'] ?? 'Admin' }} - {{ \Carbon\Carbon::parse($note['created_at'])->diffForHumans() }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada catatan</p>
                            @endforelse
                        </div>
                    </x-admin::card>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div id="note-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Tambah Catatan</h3>
            <form id="note-form">
                @csrf
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea
                        name="note"
                        rows="4"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        placeholder="Tulis catatan..."
                        required
                    ></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        onclick="closeNoteModal()"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openNoteModal() {
                document.getElementById('note-modal').classList.remove('hidden');
                document.getElementById('note-modal').classList.add('flex');
            }

            function closeNoteModal() {
                document.getElementById('note-modal').classList.add('hidden');
                document.getElementById('note-modal').classList.remove('flex');
            }

            document.getElementById('note-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('{{ route("admin.customers.notes.store", $customer->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        note: formData.get('note'),
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        </script>
    @endpush
</x-admin::layouts>
