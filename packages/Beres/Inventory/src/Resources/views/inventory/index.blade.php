<x-admin::layouts>
    <div class="flex gap-4 max-lg:flex-col">
        <div class="flex flex-1 flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Inventori</h1>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-4 gap-4">
                <x-admin::card>
                    <p class="text-sm text-gray-500">Total Produk</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats->totalProducts }}</p>
                </x-admin::card>
                <x-admin::card>
                    <p class="text-sm text-gray-500">Total Stok</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats->totalStock }}</p>
                </x-admin::card>
                <x-admin::card>
                    <p class="text-sm text-gray-500">Stok Rendah</p>
                    <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $stats->lowStockProducts }}</p>
                </x-admin::card>
                <x-admin::card>
                    <p class="text-sm text-gray-500">Habis Stok</p>
                    <p class="mt-1 text-2xl font-bold text-red-600">{{ $stats->outOfStockProducts }}</p>
                </x-admin::card>
            </div>

            <!-- Filters -->
            <x-admin::card>
                <form action="{{ route('admin.inventory.index') }}" method="GET" class="grid grid-cols-4 gap-4">
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
                        <label class="mb-1 block text-sm font-medium text-gray-700">Filter</label>
                        <select name="low_stock" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Semua</option>
                            <option value="1" {{ ($filters['low_stock'] ?? '') == '1' ? 'selected' : '' }}>Stok Rendah</option>
                            <option value="0" {{ ($filters['out_of_stock'] ?? '') == '1' ? 'selected' : '' }}>Habis Stok</option>
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
                            href="{{ route('admin.inventory.index') }}"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </x-admin::card>

            <!-- Inventory Table -->
            <x-admin::card>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($products['data'] ?? [] as $product)
                                @php
                                    $stock = $product['inventories']->sum('qty') ?? 0;
                                @endphp
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        #{{ $product['id'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $product['name'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $product['sku'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ $stock }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($stock == 0)
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-red-100 text-red-800">
                                                Habis Stok
                                            </span>
                                        @elseif($stock < 10)
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                Stok Rendah
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-green-100 text-green-800">
                                                Tersedia
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <button
                                            onclick="openAdjustModal({{ $product['id'] }}, '{{ $product['name'] }}')"
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            Adjust
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
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
                                href="{{ route('admin.inventory.index', array_merge($filters, ['page' => $i])) }}"
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

    <!-- Adjust Stock Modal -->
    <div id="adjust-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Adjust Stok</h3>
            <form id="adjust-form">
                @csrf
                <input type="hidden" name="product_id" id="adjust-product-id" />
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Produk</label>
                    <p id="adjust-product-name" class="text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Aksi</label>
                    <select name="action" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                        <option value="add">Tambah Stok</option>
                        <option value="subtract">Kurangi Stok</option>
                        <option value="set">Atur Stok</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Jumlah</label>
                    <input
                        type="number"
                        name="quantity"
                        min="0"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        required
                    />
                </div>
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea
                        name="note"
                        rows="2"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        placeholder="Catatan (opsional)..."
                    ></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        onclick="closeAdjustModal()"
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
            function openAdjustModal(productId, productName) {
                document.getElementById('adjust-product-id').value = productId;
                document.getElementById('adjust-product-name').textContent = productName;
                document.getElementById('adjust-modal').classList.remove('hidden');
                document.getElementById('adjust-modal').classList.add('flex');
            }

            function closeAdjustModal() {
                document.getElementById('adjust-modal').classList.add('hidden');
                document.getElementById('adjust-modal').classList.remove('flex');
            }

            document.getElementById('adjust-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('{{ route("admin.inventory.adjust") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: formData.get('product_id'),
                        action: formData.get('action'),
                        quantity: formData.get('quantity'),
                        note: formData.get('note'),
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal adjust stok');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        </script>
    @endpush
</x-admin::layouts>
