<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Checkout
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-gray-900">Checkout</h1>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form id="checkout-form" class="space-y-6">
                    @csrf

                    <!-- Shipping Address -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-4 text-xl font-semibold text-gray-900">Alamat Pengiriman</h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Nama Depan *</label>
                                <input
                                    type="text"
                                    name="shipping_address[first_name]"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Nama Belakang *</label>
                                <input
                                    type="text"
                                    name="shipping_address[last_name]"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Email *</label>
                                <input
                                    type="email"
                                    name="shipping_address[email]"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Telepon *</label>
                                <input
                                    type="text"
                                    name="shipping_address[phone]"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>
                            <div class="col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Alamat *</label>
                                <textarea
                                    name="shipping_address[address1]"
                                    rows="2"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                ></textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Kota *</label>
                                <input
                                    type="text"
                                    name="shipping_address[city]"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Provinsi *</label>
                                <input
                                    type="text"
                                    name="shipping_address[state]"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Kode Pos *</label>
                                <input
                                    type="text"
                                    name="shipping_address[postcode]"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Negara *</label>
                                <input
                                    type="text"
                                    name="shipping_address[country]"
                                    value="ID"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-4 text-xl font-semibold text-gray-900">Metode Pengiriman</h2>

                        <div class="mb-4">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Kurir *</label>
                            <select
                                id="courier-select"
                                name="shipping_method"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                required
                            >
                                <option value="">Pilih Kurir</option>
                                @foreach($couriers as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="shipping-options" class="hidden">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Layanan *</label>
                            <div id="shipping-options-list" class="space-y-2">
                                <!-- Shipping options will be loaded here -->
                            </div>
                        </div>

                        <input type="hidden" name="shipping_cost" id="shipping-cost" value="0" />
                    </div>

                    <!-- Payment Method -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-4 text-xl font-semibold text-gray-900">Metode Pembayaran</h2>

                        <div class="space-y-3">
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-4 hover:border-green-500">
                                <input type="radio" name="payment_method" value="midtrans" class="text-green-600 focus:ring-green-500" required />
                                <div>
                                    <p class="font-medium text-gray-900">Midtrans</p>
                                    <p class="text-sm text-gray-500">VA, E-Wallet, Kartu Kredit</p>
                                </div>
                            </label>

                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-4 hover:border-green-500">
                                <input type="radio" name="payment_method" value="bank_transfer" class="text-green-600 focus:ring-green-500" />
                                <div>
                                    <p class="font-medium text-gray-900">Transfer Bank</p>
                                    <p class="text-sm text-gray-500">BCA, Mandiri, BRI, BNI</p>
                                </div>
                            </label>

                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-4 hover:border-green-500">
                                <input type="radio" name="payment_method" value="cod" class="text-green-600 focus:ring-green-500" />
                                <div>
                                    <p class="font-medium text-gray-900">Bayar di Tempat (COD)</p>
                                    <p class="text-sm text-gray-500">Bayar saat barang diterima</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-4 text-xl font-semibold text-gray-900">Catatan</h2>
                        <textarea
                            name="notes"
                            rows="3"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                            placeholder="Catatan untuk pesanan (opsional)"
                        ></textarea>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="sticky top-4 rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Ringkasan Pesanan</h2>

                    <!-- Cart Items -->
                    <div class="mb-4 space-y-3">
                        @foreach(($cart['items'] ?? []) as $item)
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded bg-gray-100">
                                    @if(!empty($item['image_url']))
                                        <img
                                            src="{{ $item['image_url'] }}"
                                            alt="{{ $item['name'] }}"
                                            class="h-full w-full object-cover"
                                        />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $item['name'] }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $item['quantity'] }}</p>
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-4" />

                    <!-- Summary -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="text-gray-900">Rp {{ number_format($cart['subtotal'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Pengiriman</span>
                            <span id="shipping-cost-display" class="text-gray-900">Rp 0</span>
                        </div>
                        <hr class="my-2" />
                        <div class="flex justify-between text-lg font-semibold">
                            <span class="text-gray-900">Total</span>
                            <span id="grand-total" class="text-green-600">Rp {{ number_format($cart['subtotal'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button
                        type="submit"
                        form="checkout-form"
                        class="mt-6 w-full rounded-lg bg-green-600 px-6 py-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                        Bayar Sekarang
                    </button>

                    <p class="mt-4 text-center text-xs text-gray-500">
                        Dengan melakukan pemesanan, Anda menyetujui Syarat & Ketentuan kami.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Courier selection
            document.getElementById('courier-select')?.addEventListener('change', function() {
                const courier = this.value;
                const optionsDiv = document.getElementById('shipping-options');
                const optionsList = document.getElementById('shipping-options-list');

                if (!courier) {
                    optionsDiv.classList.add('hidden');
                    return;
                }

                // Fetch shipping options
                fetch('{{ route("shop.checkout.calculate_shipping") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        courier: courier,
                        city_id: 501, // Default city, should be dynamic
                        weight: 1000, // Default weight, should be dynamic
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        optionsDiv.classList.remove('hidden');
                        optionsList.innerHTML = '';

                        data.data.forEach(service => {
                            if (service.costs) {
                                service.costs.forEach(cost => {
                                    const label = document.createElement('label');
                                    label.className = 'flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-3 hover:border-green-500';
                                    label.innerHTML = `
                                        <input type="radio" name="shipping_method" value="${cost.service}" class="text-green-600 focus:ring-green-500" />
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">${cost.service}</p>
                                            <p class="text-sm text-gray-500">${cost.description || ''}</p>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">Rp ${cost.cost[0].value.toLocaleString('id-ID')}</p>
                                    `;
                                    optionsList.appendChild(label);
                                });
                            }
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            // Form submission
            document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Create session
                fetch('{{ route("shop.checkout.session.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData)),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Place order
                        return fetch('{{ route("shop.checkout.place_order") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                session_id: data.data.id,
                            }),
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.payment_url) {
                            window.location.href = data.payment_url;
                        } else {
                            window.location.href = '{{ route("shop.checkout.success") }}?order_id=' + data.order_id;
                        }
                    } else {
                        alert(data.message || 'Gagal melakukan pemesanan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
            });
        </script>
    @endpush
</x-shop::layouts>
