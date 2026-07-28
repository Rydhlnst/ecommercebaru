<x-admin::layouts>
    <x-slot:title>
        Pengaturan
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-gray-900">Pengaturan</h1>

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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Store Settings -->
            <x-admin::card>
                <h2 class="mb-4 text-xl font-semibold text-gray-900">Pengaturan Toko</h2>
                <form action="{{ route('admin.settings.store.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nama Toko *</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ $store['name'] }}"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                required
                            />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">URL Toko *</label>
                            <input
                                type="url"
                                name="url"
                                value="{{ $store['url'] }}"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                required
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Timezone *</label>
                                <select name="timezone" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                                    <option value="Asia/Jakarta" {{ $store['timezone'] == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar" {{ $store['timezone'] == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura" {{ $store['timezone'] == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Mata Uang *</label>
                                <select name="currency" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                                    <option value="IDR" {{ $store['currency'] == 'IDR' ? 'selected' : '' }}>IDR (Rupiah)</option>
                                    <option value="USD" {{ $store['currency'] == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Lokal Bahasa *</label>
                            <select name="locale" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                                <option value="id" {{ $store['locale'] == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                <option value="en" {{ $store['locale'] == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button
                            type="submit"
                            class="rounded-lg bg-green-600 px-6 py-2 text-sm font-medium text-white hover:bg-green-700"
                        >
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </x-admin::card>

            <!-- SMTP Settings -->
            <x-admin::card>
                <h2 class="mb-4 text-xl font-semibold text-gray-900">Pengaturan Email (SMTP)</h2>
                <form action="{{ route('admin.settings.smtp.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Driver *</label>
                            <select name="driver" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                                <option value="smtp" {{ $smtp['driver'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ $smtp['driver'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="mailgun" {{ $smtp['driver'] == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                <option value="ses" {{ $smtp['driver'] == 'ses' ? 'selected' : '' }}>SES</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Host *</label>
                                <input
                                    type="text"
                                    name="host"
                                    value="{{ $smtp['host'] }}"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Port *</label>
                                <input
                                    type="number"
                                    name="port"
                                    value="{{ $smtp['port'] }}"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Username</label>
                                <input
                                    type="text"
                                    name="username"
                                    value="{{ $smtp['username'] }}"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    value="{{ $smtp['password'] }}"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Enkripsi *</label>
                            <select name="encryption" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                                <option value="tls" {{ $smtp['encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ $smtp['encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="null" {{ $smtp['encryption'] == 'null' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button
                            type="submit"
                            class="rounded-lg bg-green-600 px-6 py-2 text-sm font-medium text-white hover:bg-green-700"
                        >
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </x-admin::card>
        </div>
    </div>
</x-admin::layouts>
