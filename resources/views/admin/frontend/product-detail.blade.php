<x-shop::layouts>
    <x-slot:title>{{ $product->name }}</x-slot:title>

    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-zinc-500 mb-6 flex items-center gap-2">
            <a href="/" class="hover:text-[#2D5A27] transition-colors">Beranda</a>
            <span class="text-zinc-400">/</span>
            @if($product->category)
                <a href="{{ route('shop.admin_category.show', $product->category->slug) }}" class="hover:text-[#2D5A27] transition-colors">{{ $product->category->name }}</a>
                <span class="text-zinc-400">/</span>
            @endif
            <span class="text-[#171717] font-medium">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-14">
            {{-- Product Images --}}
            <div>
                @if($product->images->count())
                    <div class="aspect-square rounded-2xl overflow-hidden bg-[#F5F9F3] border border-[#E8F0E5] mb-4">
                        @php $mainImage = $product->images->first(); @endphp
                        <img src="{{ $mainImage->detail_url }}" alt="{{ $mainImage->alt_text ?: $product->name }}" class="w-full h-full" style="object-fit:{{ $mainImage->fit_mode }}; object-position:{{ $mainImage->focal_x }}% {{ $mainImage->focal_y }}%; padding:{{ $mainImage->fit_mode === 'contain' ? '1rem' : '0' }};" id="main-image">
                    </div>
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($product->images as $img)
                                <button type="button" data-image-url="{{ $img->detail_url }}" data-image-fit="{{ $img->fit_mode }}" data-image-x="{{ $img->focal_x }}" data-image-y="{{ $img->focal_y }}" onclick="setProductImage(this)" class="aspect-square rounded-lg overflow-hidden border-2 border-transparent hover:border-[#2D5A27] transition-colors bg-[#F5F9F3]">
                                <img src="{{ $img->card_url }}" alt="{{ $img->alt_text ?: $product->name }}" class="w-full h-full" style="object-fit:{{ $img->fit_mode }}; object-position:{{ $img->focal_x }}% {{ $img->focal_y }}%; padding:{{ $img->fit_mode === 'contain' ? '0.5rem' : '0' }};">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="aspect-square rounded-2xl bg-[#F5F9F3] border border-[#E8F0E5] flex items-center justify-center">
                        <span class="text-6xl text-[#C8DBBE]">🌿</span>
                    </div>
                @endif
            </div>

            <script>
                function setProductImage(button) {
                    const image = document.getElementById('main-image');
                    image.src = button.dataset.imageUrl;
                    image.style.objectFit = button.dataset.imageFit || 'cover';
                    image.style.objectPosition = `${button.dataset.imageX || 50}% ${button.dataset.imageY || 50}%`;
                    image.style.padding = button.dataset.imageFit === 'contain' ? '1rem' : '0';
                }
            </script>

            {{-- Product Info --}}
            <div class="flex flex-col items-start text-left">
                {{-- Badge (Strictly fit-content width, never full width) --}}
                @if($product->badge)
                    <div class="mb-3">
                        <span class="inline-block px-3 py-1 text-[11px] font-bold tracking-wider uppercase text-white rounded"
                              style="width:fit-content; max-width:max-content; background-color:{{ $product->badge === 'sale' ? '#B91C1C' : ($product->badge === 'habis_terjual' ? '#737373' : '#2D5A27') }};">
                            {{ $product->badge === 'new' ? 'BARU' : ($product->badge === 'sale' ? 'SALE' : 'HABIS TERJUAL') }}
                        </span>
                    </div>
                @endif

                @if($product->category)
                    <p class="text-xs font-bold uppercase tracking-wider text-[#2D5A27] mb-1.5">{{ $product->category->name }}</p>
                @endif

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-[#171717] mb-4 leading-tight">{{ $product->name }}</h1>

                {{-- Price & Stock calculation --}}
                @php
                    $pdpVariations = ($product->has_variations && $product->variations->count())
                        ? $product->variations->values()
                        : collect();
                    $firstVar = $pdpVariations->first();
                    $initialPrice = $firstVar ? $firstVar->price : $product->price;
                    $initialStock = $firstVar ? (int) $firstVar->stock : (int) $product->stock;
                    $isAvailable = ($initialStock > 0) || ($product->stock > 0) || ($pdpVariations->isNotEmpty() && $pdpVariations->sum('stock') > 0);
                @endphp

                <div class="mb-4">
                    @if($pdpVariations->isNotEmpty())
                        <p class="text-xs text-zinc-500 mb-0.5">Mulai dari</p>
                    @endif
                    <p class="text-2xl sm:text-3xl font-bold text-[#171717]" id="pdp-price">Rp {{ number_format($initialPrice, 0, ',', '.') }}</p>
                </div>

                {{-- Stock status --}}
                <div class="mb-6" id="pdp-stock">
                    @if($initialStock > 0)
                        <span class="inline-flex items-center gap-1.5 text-sm text-green-700 font-semibold bg-green-50 px-2.5 py-1 rounded-md border border-green-200">
                            <span>✓</span>
                            <span>Stok tersedia ({{ $initialStock }})</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-sm text-red-700 font-semibold bg-red-50 px-2.5 py-1 rounded-md border border-red-200">
                            <span>✗</span>
                            <span>Stok habis</span>
                        </span>
                    @endif
                </div>

                {{-- Description --}}
                @if($product->description)
                    <div class="prose prose-sm text-zinc-700 text-justify max-w-none mb-6 leading-relaxed">
                        {!! $product->description !!}
                    </div>
                @endif

                {{-- Variants (weight) --}}
                @if($pdpVariations->isNotEmpty())
                    <div class="mb-6 w-full">
                        <p class="text-sm font-semibold text-[#171717] mb-2">Pilih Berat / Varian</p>
                        <div class="flex flex-wrap items-center gap-2" id="pdp-variants">
                            @foreach($pdpVariations as $i => $var)
                                <button type="button"
                                        class="pdp-variant-btn px-5 py-2 text-sm font-medium border transition-all {{ $i === 0 ? 'text-white border-[#2D5A27] bg-[#2D5A27]' : 'text-[#171717] border-[#E8F0E5] bg-white hover:border-[#2D5A27]' }}"
                                        style="border-radius:8px; {{ $i === 0 ? 'background-color:#2D5A27; color:#fff; border-color:#2D5A27;' : 'background-color:#fff; color:#171717; border-color:#E8F0E5;' }}"
                                        data-variant-id="{{ $var->id }}"
                                        data-price="{{ number_format($var->price, 0, ',', '.') }}"
                                        data-stock="{{ (int) $var->stock }}"
                                        onclick="pdpSelectVariant(this)">
                                    {{ $var->weight_label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Add to Cart & Buy Now Form --}}
                <form action="{{ route('cart.add') }}" method="POST" class="w-full mt-2" id="pdp-form" onsubmit="event.preventDefault(); pdpAddToCart(this);">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    @if($pdpVariations->isNotEmpty())
                        <input type="hidden" name="selected_configurable_option" value="{{ $pdpVariations->first()->id }}" class="beres-variant-input" id="pdp-variant-input">
                    @endif

                    {{-- Quantity Stepper --}}
                    <div class="mb-4">
                        <p class="text-xs font-semibold text-[#171717] uppercase tracking-wider mb-2">Jumlah</p>
                        <div class="flex items-center border border-[#E8F0E5] rounded-xl overflow-hidden h-11 w-32 bg-white">
                            <button type="button" class="w-10 h-full flex items-center justify-center text-[#2D5A27] hover:bg-[#F5F9F3] transition-colors text-lg font-bold select-none" onclick="pdpQty(-1)" aria-label="Kurangi">−</button>
                            <input type="number" name="quantity" value="1" min="1" max="99" id="pdp-qty" class="w-12 h-full text-center text-sm border-0 border-x border-[#E8F0E5] focus:outline-none bg-transparent font-semibold" aria-label="Jumlah">
                            <button type="button" class="w-10 h-full flex items-center justify-center text-[#2D5A27] hover:bg-[#F5F9F3] transition-colors text-lg font-bold select-none" onclick="pdpQty(1)" aria-label="Tambah">+</button>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    @if($isAvailable)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <button type="submit"
                                    class="h-12 px-6 flex items-center justify-center gap-2 font-semibold text-sm uppercase tracking-wider text-white hover:opacity-90 transition-opacity shadow-sm"
                                    style="background-color:#2D5A27; border-radius:12px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                <span>Tambah ke Keranjang</span>
                            </button>

                            <button type="button"
                                    onclick="pdpBuyNow(document.getElementById('pdp-form'))"
                                    class="h-12 px-6 flex items-center justify-center gap-2 font-semibold text-sm uppercase tracking-wider text-white bg-[#171717] hover:bg-black transition-colors shadow-sm"
                                    style="border-radius:12px;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Beli Sekarang</span>
                            </button>
                        </div>
                    @else
                        <button type="button" disabled class="w-full h-12 text-sm font-semibold uppercase tracking-wider text-zinc-500 bg-zinc-100 rounded-xl cursor-not-allowed">
                            Stok Habis Terjual
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script>
        window.pdpSelectVariant = function (btn) {
            const wrap = document.getElementById('pdp-variants');
            if (!wrap) return;

            wrap.querySelectorAll('button').forEach(b => {
                b.style.backgroundColor = '#fff';
                b.style.color = '#171717';
                b.style.borderColor = '#E8F0E5';
            });

            btn.style.backgroundColor = '#2D5A27';
            btn.style.color = '#fff';
            btn.style.borderColor = '#2D5A27';

            const id = btn.getAttribute('data-variant-id');
            const price = btn.getAttribute('data-price');
            const stock = parseInt(btn.getAttribute('data-stock') || 0, 10);

            const hidden = document.getElementById('pdp-variant-input');
            if (hidden) hidden.value = id;

            const priceEl = document.getElementById('pdp-price');
            if (priceEl) priceEl.textContent = 'Rp ' + price;

            const stockEl = document.getElementById('pdp-stock');
            if (stockEl) {
                stockEl.innerHTML = stock > 0
                    ? '<span class="inline-flex items-center gap-1.5 text-sm text-green-700 font-semibold bg-green-50 px-2.5 py-1 rounded-md border border-green-200"><span>✓</span><span>Stok tersedia (' + stock + ')</span></span>'
                    : '<span class="inline-flex items-center gap-1.5 text-sm text-red-700 font-semibold bg-red-50 px-2.5 py-1 rounded-md border border-red-200"><span>✗</span><span>Stok habis</span></span>';
            }
        };

        window.pdpQty = function (delta) {
            const input = document.getElementById('pdp-qty');
            if (!input) return;
            let v = parseInt(input.value || 1, 10) + delta;
            if (v < 1) v = 1;
            if (v > 99) v = 99;
            input.value = v;
        };

        window.pdpAddToCart = async function (form) {
            const btn = form.querySelector('button[type="submit"]');
            const original = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<span>Menambahkan…</span>'; }

            const productId = form.querySelector('[name="product_id"]').value;
            const quantity  = parseInt(document.getElementById('pdp-qty')?.value || 1, 10);
            const token     = document.querySelector('meta[name="csrf-token"]')?.content
                           || form.querySelector('[name="_token"]')?.value;

            const payload = { product_id: productId, quantity };
            const variantInput = document.getElementById('pdp-variant-input');
            if (variantInput) payload.selected_configurable_option = parseInt(variantInput.value, 10);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok && data.success) {
                    if (data.cart && typeof window.beresCartRefresh === 'function') window.beresCartRefresh(data.cart);
                    if (btn) btn.innerHTML = '<span>✓ Ditambahkan</span>';
                    setTimeout(() => {
                        if (btn) { btn.disabled = false; btn.innerHTML = original; }
                        if (typeof window.beresCartOpen === 'function') window.beresCartOpen();
                    }, 500);
                } else {
                    alert(data.message || 'Gagal menambahkan ke keranjang. Coba lagi.');
                    if (btn) { btn.disabled = false; btn.innerHTML = original; }
                }
            } catch (e) {
                alert('Gagal terhubung ke server.');
                if (btn) { btn.disabled = false; btn.innerHTML = original; }
            }
        };

        window.pdpBuyNow = async function (form) {
            const productId = form.querySelector('[name="product_id"]').value;
            const quantity  = parseInt(document.getElementById('pdp-qty')?.value || 1, 10);
            const token     = document.querySelector('meta[name="csrf-token"]')?.content
                           || form.querySelector('[name="_token"]')?.value;

            const payload = { product_id: productId, quantity };
            const variantInput = document.getElementById('pdp-variant-input');
            if (variantInput) payload.selected_configurable_option = parseInt(variantInput.value, 10);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });

                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    window.location.href = "{{ route('shop.checkout.onepage.index') }}";
                } else {
                    alert(data.message || 'Gagal melanjutkan ke pembayaran.');
                }
            } catch (e) {
                alert('Gagal terhubung ke server.');
            }
        };
    </script>
</x-shop::layouts>
