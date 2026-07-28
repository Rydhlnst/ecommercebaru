<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Edit Profil
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('shop.customer.account.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Profil</h1>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <form action="{{ route('shop.customer.account.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Nama Depan *</label>
                        <input
                            type="text"
                            name="first_name"
                            value="{{ old('first_name', $customer->first_name) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                            required
                        />
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Nama Belakang *</label>
                        <input
                            type="text"
                            name="last_name"
                            value="{{ old('last_name', $customer->last_name) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                            required
                        />
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Email *</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $customer->email) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                            required
                        />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Telepon</label>
                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $customer->phone) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                        />
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <a
                        href="{{ route('shop.customer.account.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="rounded-lg bg-green-600 px-6 py-2 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-shop::layouts>
