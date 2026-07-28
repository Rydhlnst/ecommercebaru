<x-admin::layouts>
    <div class="flex gap-4 max-lg:flex-col">
        <div class="flex flex-1 flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Pelanggan</h1>
                <div class="flex gap-2">
                    <a
                        href="{{ route('admin.customers.export') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Export CSV
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <x-admin::card>
                <form action="{{ route('admin.customers.index') }}" method="GET" class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ $filters['name'] ?? '' }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Cari pelanggan..."
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ $filters['email'] ?? '' }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Cari email..."
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Semua</option>
                            <option value="1" {{ ($filters['status'] ?? '') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ ($filters['status'] ?? '') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            Filter
                        </button>
                        <a
                            href="{{ route('admin.customers.index') }}"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </x-admin::card>

            <!-- Customers Table -->
            <x-admin::card>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesanan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Gabung</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($customers['data'] ?? [] as $customer)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        #{{ $customer['id'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $customer['first_name'] }} {{ $customer['last_name'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $customer['email'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $customer['phone'] ?? '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $customer['orders_count'] ?? 0 }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                            {{ $customer['status'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $customer['status'] ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($customer['created_at'])->format('d M Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <a href="{{ route('admin.customers.show', $customer['id']) }}" class="text-blue-600 hover:text-blue-800">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada pelanggan ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($customers['last_page']) && $customers['last_page'] > 1)
                    <div class="mt-4 flex justify-center">
                        @for($i = 1; $i <= $customers['last_page']; $i++)
                            <a
                                href="{{ route('admin.customers.index', array_merge($filters, ['page' => $i])) }}"
                                class="mx-1 rounded-lg px-3 py-2 text-sm
                                    {{ ($customers['current_page'] ?? 1) == $i ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
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
