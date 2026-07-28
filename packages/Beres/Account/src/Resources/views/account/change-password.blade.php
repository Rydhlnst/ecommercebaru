<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Ganti Password
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('shop.customer.account.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Ganti Password</h1>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <form action="{{ route('shop.customer.account.change_password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Password Lama *</label>
                        <input
                            type="password"
                            name="current_password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                            required
                        />
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Password Baru *</label>
                        <input
                            type="password"
                            name="password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                            required
                        />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Konfirmasi Password Baru *</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                            required
                        />
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
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-shop::layouts>
