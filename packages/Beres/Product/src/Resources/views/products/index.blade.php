<x-admin::layouts>
    <div class="flex gap-4 max-lg:flex-col">
        <div class="flex flex-1 flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Produk</h1>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        onclick="openImportModal()"
                    >
                        Import CSV
                    </button>
                    <a
                        href="{{ route('admin.products.export') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Export CSV
                    </a>
                    <a
                        href="{{ route('admin.products.create') }}"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                    >
                        + Tambah Produk
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <x-admin::card>
                <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ $filters['name'] ?? '' }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Cari produk..."
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">SKU</label>
                        <input
                            type="text"
                            name="sku"
                            value="{{ $filters['sku'] ?? '' }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Cari SKU..."
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
                            href="{{ route('admin.products.index') }}"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </x-admin::card>

            <!-- Products Table -->
            <x-admin::card>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300" />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($products['data'] ?? [] as $product)
                                <tr>
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="product_ids[]" value="{{ $product['id'] }}" class="product-checkbox rounded border-gray-300" />
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        #{{ $product['id'] }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="h-10 w-10 overflow-hidden rounded bg-gray-100">
                                            @if(isset($product['images'][0]))
                                                <img src="{{ asset('storage/' . $product['images'][0]['path']) }}" alt="" class="h-full w-full object-contain p-1" />
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-gray-400">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $product['name'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $product['sku'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ core()->formatPrice($product['price']) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $product['quantity'] ?? 0 }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                            {{ $product['status'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product['status'] ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <a href="{{ route('admin.products.show', $product['id']) }}" class="text-blue-600 hover:text-blue-800">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada produk ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($products['last_page']) && $products['last_page'] > 1)
                    <div class="mt-4 flex justify-center">
                        @for($i = 1; $i <= $products['last_page']; $i++)
                            <a
                                href="{{ route('admin.products.index', array_merge($filters, ['page' => $i])) }}"
                                class="mx-1 rounded-lg px-3 py-2 text-sm
                                    {{ ($products['current_page'] ?? 1) == $i ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                            >
                                {{ $i }}
                            </a>
                        @endfor
                    </div>
                @endif
            </x-admin::card>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="import-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Import Produk</h3>
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">File CSV</label>
                    <input
                        type="file"
                        name="file"
                        accept=".csv"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        required
                    />
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        onclick="closeImportModal()"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openImportModal() {
                document.getElementById('import-modal').classList.remove('hidden');
                document.getElementById('import-modal').classList.add('flex');
            }

            function closeImportModal() {
                document.getElementById('import-modal').classList.add('hidden');
                document.getElementById('import-modal').classList.remove('flex');
            }

            // Select all checkbox
            document.getElementById('select-all')?.addEventListener('change', function() {
                document.querySelectorAll('.product-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        </script>
    @endpush
</x-admin::layouts>
