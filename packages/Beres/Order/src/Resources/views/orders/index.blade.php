<x-admin::layouts>
    <div class="flex gap-4 max-lg:flex-col">
        <div class="flex flex-1 flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Pesanan</h1>
                <div class="flex gap-2">
                    <a
                        href="{{ route('admin.orders.export') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Export CSV
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <x-admin::card>
                <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Semua</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ ($filters['status'] ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email Pelanggan</label>
                        <input
                            type="email"
                            name="customer_email"
                            value="{{ $filters['customer_email'] ?? '' }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Cari email..."
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Tanggal</label>
                        <div class="flex gap-2">
                            <input
                                type="date"
                                name="date_from"
                                value="{{ $filters['date_from'] ?? '' }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            />
                            <input
                                type="date"
                                name="date_to"
                                value="{{ $filters['date_to'] ?? '' }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            />
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            Filter
                        </button>
                        <a
                            href="{{ route('admin.orders.index') }}"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </x-admin::card>

            <!-- Orders Table -->
            <x-admin::card>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($orders['data'] ?? [] as $order)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        #{{ $order['id'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $order['increment_id'] ?? $order['id'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $order['customer']['full_name'] ?? 'Guest' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ core()->formatPrice($order['grand_total']) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'processing' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'canceled' => 'bg-red-100 text-red-800',
                                                'shipped' => 'bg-purple-100 text-purple-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statuses[$order['status']] ?? $order['status'] }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y H:i') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <a href="{{ route('admin.orders.show', $order['id']) }}" class="text-blue-600 hover:text-blue-800">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada pesanan ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($orders['last_page']) && $orders['last_page'] > 1)
                    <div class="mt-4 flex justify-center">
                        @for($i = 1; $i <= $orders['last_page']; $i++)
                            <a
                                href="{{ route('admin.orders.index', array_merge($filters, ['page' => $i])) }}"
                                class="mx-1 rounded-lg px-3 py-2 text-sm
                                    {{ ($orders['current_page'] ?? 1) == $i ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                            >
                                {{ $i }}
                            </a>
                        @endfor
                    </div>
                @endif
            </x-admin::card>
        </div>
    </div>
</x-admin::layouts>
