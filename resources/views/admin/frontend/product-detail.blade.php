<x-shop::layouts>
    <x-slot:title>{{ $product->name }}</x-slot:title>

    @push('styles')
        <style>
            .product-description-markdown.is-collapsed {
                max-height: 4.5rem;
                overflow: hidden;
            }

            .product-description-markdown.is-expanded {
                max-height: none;
            }

            @media (min-width: 768px) {
                .pdp-media-panel,
                .pdp-info-panel {
                    height: auto;
                }

                .pdp-media-panel {
                    align-self: start;
                }

                .pdp-info-panel {
                    overflow: visible;
                }
            }
        </style>
    @endpush

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

        <div class="grid grid-cols-1 items-stretch gap-8 md:grid-cols-2 lg:gap-10">
            {{-- Product Images --}}
            <div class="pdp-media-panel rounded-3xl border border-[#F1F1F1] bg-[#FAFAFA] p-5 shadow-sm md:p-8">
                @if($product->images->count())
                    @php $mainImage = $product->images->first(); @endphp
                    <div class="flex items-start justify-center">
                        <div class="relative aspect-[4/5] w-full max-w-[520px] overflow-hidden rounded-2xl bg-white">
                            <img src="{{ $mainImage->detail_url }}" alt="{{ $mainImage->alt_text ?: $product->name }}" class="h-full w-full" style="object-fit:{{ $mainImage->fit_mode }}; object-position:{{ $mainImage->focal_x }}% {{ $mainImage->focal_y }}%; padding:{{ $mainImage->fit_mode === 'contain' ? '1rem' : '0' }};" id="main-image">
                        </div>
                    </div>
                    @if($product->images->count() > 1)
                        <div class="mt-4 grid shrink-0 grid-cols-5 gap-2">
                            @foreach($product->images as $img)
                                <button type="button" data-image-url="{{ $img->detail_url }}" data-image-fit="{{ $img->fit_mode }}" data-image-x="{{ $img->focal_x }}" data-image-y="{{ $img->focal_y }}" onclick="setProductImage(this)" class="aspect-[4/5] rounded-lg overflow-hidden border-2 border-transparent hover:border-[#2D5A27] transition-colors bg-[#F5F9F3]">
                                <img src="{{ $img->card_url }}" alt="{{ $img->alt_text ?: $product->name }}" class="w-full h-full" style="object-fit:{{ $img->fit_mode }}; object-position:{{ $img->focal_x }}% {{ $img->focal_y }}%; padding:{{ $img->fit_mode === 'contain' ? '0.5rem' : '0' }};">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="flex min-h-0 flex-1 items-center justify-center">
                        <div class="aspect-[4/5] flex h-full max-w-full items-center justify-center rounded-2xl bg-white">
                        <span class="text-6xl text-[#C8DBBE]">🌿</span>
                        </div>
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
            <div class="pdp-info-panel flex flex-col items-start rounded-3xl bg-[#FAFAFA] p-6 text-left md:p-10">
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

                <h1 class="mb-5 text-2xl font-bold uppercase leading-tight tracking-tight text-[#171717] sm:text-3xl lg:text-4xl">{{ $product->name }}</h1>

                {{-- Price & Stock calculation --}}
                @php
                    $pdpVariations = $product->variations->count()
                        ? $product->variations->values()
                        : collect();
                    $firstVar = $pdpVariations->first();
                    $initialPrice = $firstVar ? $firstVar->price : $product->price;
                    $initialStock = $firstVar ? (int) $firstVar->stock : (int) $product->stock;
                    $isAvailable = ($initialStock > 0) || ($product->stock > 0) || ($pdpVariations->isNotEmpty() && $pdpVariations->sum('stock') > 0);
                @endphp

                <div class="mb-5 flex w-full flex-wrap items-center justify-between gap-3">
                    @if($pdpVariations->isNotEmpty())
                        <p class="text-xs text-zinc-500 mb-0.5">Mulai dari</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-3">
                        <p class="text-2xl font-bold text-[#171717] sm:text-3xl" id="pdp-price">Rp {{ number_format($initialPrice, 0, ',', '.') }}</p>
                        @if($product->compare_at_price && $product->compare_at_price > $initialPrice)
                            <p class="text-lg text-zinc-400 line-through">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</p>
                        @endif
                        @if($product->badge === 'sale')
                            <span class="rounded-md bg-red-600 px-3 py-2 text-xs font-bold uppercase text-white">SALE</span>
                        @endif
                    </div>
                    <div class="text-sm text-[#737373]" aria-label="{{ $averageRating }} dari 5 bintang dari {{ $reviewCount }} review">
                        <span class="text-amber-500">★</span> {{ number_format($averageRating, 1) }} ({{ $reviewCount }})
                    </div>
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
                    <div class="mb-6">
                        <p class="text-xs font-semibold text-[#171717] uppercase tracking-wider mb-2">Jumlah</p>
                        <div class="flex items-center border border-[#E8F0E5] rounded-xl overflow-hidden h-11 w-32 bg-white">
                            <button type="button" class="w-10 h-full flex items-center justify-center text-[#2D5A27] hover:bg-[#F5F9F3] transition-colors text-lg font-bold select-none" onclick="pdpQty(-1)" aria-label="Kurangi">−</button>
                            <input type="number" name="quantity" value="1" min="1" max="99" id="pdp-qty" class="w-12 h-full text-center text-sm border-0 border-x border-[#E8F0E5] focus:outline-none bg-transparent font-semibold" aria-label="Jumlah">
                            <button type="button" class="w-10 h-full flex items-center justify-center text-[#2D5A27] hover:bg-[#F5F9F3] transition-colors text-lg font-bold select-none" onclick="pdpQty(1)" aria-label="Tambah">+</button>
                        </div>
                    </div>

                    <div class="mb-6 grid w-full grid-cols-2 gap-3 border-y border-[#E5E5E5] py-6 text-center text-xs text-[#171717] sm:grid-cols-4">
                        <div class="flex flex-col items-center gap-2"><svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>7 Days Replacement</span></div>
                        <div class="flex flex-col items-center gap-2"><svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path d="m5 12 4 4L19 6"/><circle cx="12" cy="12" r="9"/></svg><span>100% Organic</span></div>
                        <div class="flex flex-col items-center gap-2"><svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg><span>Chemical Free</span></div>
                        <div class="flex flex-col items-center gap-2"><svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg><span>Secure Payment</span></div>
                    </div>

                    {{-- Action Buttons --}}
                    @if($isAvailable)
                        <div class="flex w-full flex-col gap-3 pt-2">
                            <button type="button"
                                    onclick="pdpBuyNow(document.getElementById('pdp-form'))"
                                    class="h-14 w-full rounded-xl bg-[#2D5A27] px-6 text-sm font-semibold uppercase tracking-wider text-white shadow-sm transition-opacity hover:opacity-90">
                                <span>Beli Sekarang</span>
                            </button>

                            <button type="submit"
                                    class="flex h-14 w-full items-center justify-center gap-2 rounded-xl border border-[#DDE7D9] bg-white px-6 text-sm font-semibold uppercase tracking-wider text-[#171717] transition-colors hover:border-[#2D5A27] hover:text-[#2D5A27]">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                <span>Tambah ke Keranjang</span>
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

        @if($product->description)
            @php
                $descriptionHtml = \Illuminate\Support\Str::markdown($product->description, [
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ]);
            @endphp
            <section class="mt-10 overflow-hidden rounded-3xl bg-[#2D5A27] px-6 py-10 text-white md:px-12 md:py-12">
                <div class="mx-auto max-w-4xl">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Product information</p>
                    <h2 class="mt-3 text-2xl font-bold md:text-3xl">Tentang {{ $product->name }}</h2>
                    <div id="product-description" class="product-description-markdown is-collapsed prose prose-sm mt-6 max-w-none text-left text-white prose-headings:text-white prose-strong:text-white prose-a:text-white prose-li:marker:text-white/70">
                        {!! $descriptionHtml !!}
                    </div>
                    <button
                        type="button"
                        id="product-description-toggle"
                        class="mt-5 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-[#2D5A27] transition-colors hover:bg-[#E8F0E5] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#2D5A27]"
                        aria-controls="product-description"
                        aria-expanded="false"
                    >
                        <span data-more-label>See more</span>
                        <span data-less-label hidden>See less</span>
                    </button>
                </div>
            </section>
        @endif

        @if($recommendations->isNotEmpty())
            <section class="mt-16 border-t border-[#E8F0E5] pt-12">
                <div class="mb-8 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#2D5A27]">Anda mungkin suka</p>
                        <h2 class="mt-2 text-2xl font-bold text-[#171717] md:text-3xl">Rekomendasi Produk</h2>
                    </div>
                    @if($product->category)
                        <a href="{{ route('shop.admin_category.show', $product->category->slug) }}" class="text-sm font-semibold text-[#2D5A27] underline">Lihat semua</a>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5 lg:gap-5">
                    @foreach($recommendations as $index => $recommendedProduct)
                        @include('shop::components.layouts._product-card', ['product' => $recommendedProduct, 'index' => $index, 'bg' => ['#E8F0E5','#DCE8D6','#F0F5EC','#EAF1E4'][$index % 4]])
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-16 border-t border-[#E8F0E5] pt-12" id="product-reviews">
            <div class="mb-8 flex flex-col justify-between gap-5 md:flex-row md:items-end">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#2D5A27]">Customer feedback</p>
                    <h2 class="mt-2 text-2xl font-bold text-[#171717] md:text-3xl">Review Produk</h2>
                </div>
                <div class="rounded-2xl bg-[#F5F9F3] px-5 py-3 text-center">
                    <p class="text-2xl font-bold text-[#171717]">{{ number_format($averageRating, 1) }} <span class="text-amber-500">★</span></p>
                    <p class="text-xs text-[#737373]">{{ $reviewCount }} review disetujui</p>
                </div>
            </div>

            @if($reviews->isNotEmpty())
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($reviews as $review)
                        <article class="rounded-2xl border border-[#E8F0E5] bg-white p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#171717]">{{ $review->customer_name ?: 'Pelanggan' }}</p>
                                    <p class="mt-1 text-xs text-[#737373]">{{ optional($review->created_at)->format('d M Y') }}</p>
                                </div>
                                <span class="text-sm tracking-wide text-amber-500">{{ str_repeat('★', max(0, min(5, (int) $review->rating))) }}</span>
                            </div>
                            @if($review->comment)
                                <p class="mt-4 text-sm leading-relaxed text-[#404040]">{{ $review->comment }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-[#C8DBBE] bg-[#FBFDF9] px-6 py-12 text-center">
                    <p class="font-semibold text-[#171717]">Belum ada review untuk produk ini.</p>
                    <p class="mt-2 text-sm text-[#737373]">Jadilah pelanggan pertama yang membagikan pengalaman.</p>
                </div>
            @endif
        </section>
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

    @push('scripts')
        <script>
            (() => {
                const description = document.getElementById('product-description');
                const toggle = document.getElementById('product-description-toggle');

                if (!description || !toggle) return;

                const moreLabel = toggle.querySelector('[data-more-label]');
                const lessLabel = toggle.querySelector('[data-less-label]');

                if (description.scrollHeight <= description.clientHeight + 2) {
                    toggle.hidden = true;
                    description.classList.remove('is-collapsed');
                    return;
                }

                toggle.addEventListener('click', () => {
                    const expanded = description.classList.toggle('is-expanded');
                    description.classList.toggle('is-collapsed', !expanded);
                    toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                    moreLabel.hidden = expanded;
                    lessLabel.hidden = !expanded;
                });
            })();
        </script>
    @endpush
</x-shop::layouts>
