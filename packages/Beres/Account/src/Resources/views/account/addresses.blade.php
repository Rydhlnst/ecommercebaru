<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Alamat Saya
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('shop.customer.account.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Alamat Saya</h1>
            </div>
            <button
                onclick="openAddAddressModal()"
                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
            >
                + Tambah Alamat
            </button>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-50 p-4 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($addresses as $address)
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $address['first_name'] }} {{ $address['last_name'] }}</p>
                            <p class="text-sm text-gray-500">{{ $address['phone'] }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                onclick="openEditAddressModal({{ json_encode($address) }})"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.294-8.294z"></path>
                                </svg>
                            </button>
                            <form action="{{ route('shop.customer.account.addresses.delete', $address['id']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 4h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>{{ $address['address1'] }}</p>
                        <p>{{ $address['city'] }}, {{ $address['state'] }}</p>
                        <p>{{ $address['postcode'] }} {{ $address['country'] }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.414 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Belum ada alamat tersimpan</p>
                    <button
                        onclick="openAddAddressModal()"
                        class="mt-4 text-sm font-medium text-green-600 hover:text-green-800"
                    >
                        + Tambah Alamat Pertama
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Add Address Modal -->
    <div id="add-address-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="mx-4 w-full max-w-lg rounded-lg bg-white p-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Tambah Alamat Baru</h3>
            <form action="{{ route('shop.customer.account.addresses.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nama Depan *</label>
                        <input type="text" name="first_name" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nama Belakang *</label>
                        <input type="text" name="last_name" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required />
                    </div>
                    <div class="col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Telepon *</label>
                        <input type="text" name="phone" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required />
                    </div>
                    <div class="col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Alamat *</label>
                        <textarea name="address1" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Kota *</label>
                        <input type="text" name="city" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Provinsi *</label>
                        <input type="text" name="state" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Kode Pos *</label>
                        <input type="text" name="postcode" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Negara *</label>
                        <input type="text" name="country" value="ID" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required />
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" onclick="closeAddAddressModal()" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openAddAddressModal() {
                document.getElementById('add-address-modal').classList.remove('hidden');
                document.getElementById('add-address-modal').classList.add('flex');
            }

            function closeAddAddressModal() {
                document.getElementById('add-address-modal').classList.add('hidden');
                document.getElementById('add-address-modal').classList.remove('flex');
            }
        </script>
    @endpush
</x-shop::layouts>
