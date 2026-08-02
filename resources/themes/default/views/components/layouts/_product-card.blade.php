@php
    // Accept: $product (Bagisto Product model) ATAU plain args (name, price, compare, variants, bg, href)
    $product  = $product ?? null;
    $bg       = $bg      ?? '#E8F0E5';

    $inStock  = true;
    $compare  = null;
    $isNew    = false;

    if ($product) {
        $name     = $product->name ?? $product->url_key ?? 'Produk';
        $minPrice = 0;
        try { $minPrice = $product->getTypeInstance()->getMinimalPrice() ?? 0; } catch (\Throwable $e) {}
        $price    = core()->currency($minPrice);
        try { $inStock = $product->getTypeInstance()->isSaleable(); } catch (\Throwable $e) {}
        // Special price detection
        try {
            $regularPrice = $product->getTypeInstance()->getRegularPrice() ?? null;
            if ($regularPrice && $regularPrice > $minPrice) $compare = core()->currency($regularPrice);
        } catch (\Throwable $e) {}
        $isNew    = $product->created_at && $product->created_at->diffInDays(now()) <= 14;
        $variants = [];
        $href     = $product->url_key
                        ? route('shop.product_or_category.index', $product->url_key)
                        : route('shop.search.index');
        $prodId   = $product->id;
        $image    = product_image()->getProductBaseImage($product)['small_image_url'] ?? null;

        // For configurable products: get child variants (weight options)
        $isConfigurable = $product->type === 'configurable';
        $childVariants  = [];
        if ($isConfigurable) {
            try {
                $children = $product->getTypeInstance()->getChildrenItems();
                foreach ($children as $child) {
                    $childVariants[] = [
                        'id'    => $child->id,
                        'label' => $child->name,
                        'price' => core()->currency($child->price),
                    ];
                }
            } catch (\Throwable $e) {}
        }
    } else {
        $name     = $name     ?? '';
        $price    = $price    ?? '';
        $compare  = $compare  ?? null;
        $variants = $variants ?? [];
        $href     = $href     ?? route('shop.search.index');
        $prodId   = null;
        $image    = null;
        $isConfigurable = false;
        $childVariants  = [];
    }
@endphp

<div class="beres-card group border overflow-hidden bg-white" style="border-color:#E8F0E5; border-radius:16px;">
    <a href="{{ $href }}" class="block relative aspect-square overflow-hidden" style="background-color:{{ $bg }};">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.05]" loading="lazy">
        @endif

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
            @if ($compare)
                <span class="text-white text-[10px] tracking-[0.14em] uppercase px-2.5 py-1" style="background-color:#B91C1C; border-radius:999px; font-weight:600;">Diskon</span>
            @endif
            @if ($isNew && !$compare)
                <span class="text-white text-[10px] tracking-[0.14em] uppercase px-2.5 py-1" style="background-color:#2D5A27; border-radius:999px; font-weight:600;">Baru</span>
            @endif
        </div>

        {{-- Wishlist button --}}
        @if ($prodId)
            <button type="button"
                    onclick="event.preventDefault(); event.stopPropagation(); beresToggleWishlist(this, {{ $prodId }});"
                    class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center bg-white/90 hover:bg-white transition-colors shadow-sm opacity-0 group-hover:opacity-100"
                    style="border-radius:999px;"
                    aria-label="Tambah ke wishlist">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2D5A27" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                </svg>
            </button>
        @endif

        {{-- Out of stock overlay --}}
        @if (!$inStock)
            <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                <span class="text-white text-xs tracking-[0.2em] uppercase px-4 py-2" style="background-color:rgba(0,0,0,0.7); border-radius:999px; font-weight:600;">Habis</span>
            </div>
        @endif
    </a>

    <div class="p-3 md:p-4">
        <a href="{{ $href }}" class="block">
            <p class="text-xs md:text-sm text-[#171717] leading-snug min-h-[2.5em] line-clamp-2 transition-colors group-hover:text-[#2D5A27]" style="font-weight:500;">{{ $name }}</p>
        </a>

        <p class="mt-1.5 text-sm" style="font-weight:600;">
            <span style="color:#2D5A27;">{{ $price }}</span>
            @if ($compare)
                <span class="text-[#737373] line-through ml-1.5 text-xs" style="font-weight:400;">{{ $compare }}</span>
            @endif
        </p>

        {{-- Weight variant selector for configurable products --}}
        @if (count($childVariants))
            <div class="mt-2">
                <p class="text-[10px] tracking-wide text-[#737373] mb-1">Ukuran</p>
                <div class="flex flex-wrap gap-1">
                    @foreach ($childVariants as $i => $v)
                        <button type="button"
                                class="beres-btn px-3 py-1 text-[10px] border transition-colors
                                {{ $i === 0 ? 'text-white' : 'text-[#737373] hover:border-[#2D5A27] hover:text-[#2D5A27]' }}"
                                style="{{ $i === 0 ? 'background-color:#2D5A27; border-color:#2D5A27;' : 'border-color:#E8F0E5;' }} border-radius:999px;"
                                onclick="beresSelectVariant(this, {{ $v['id'] }}, '{{ $v['price'] }}')"
                                data-variant-id="{{ $v['id'] }}">
                            {{ $v['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        @elseif (count($variants))
            <div class="mt-2">
                <p class="text-[10px] tracking-wide text-[#737373] mb-1">Ukuran</p>
                <div class="flex flex-wrap gap-1">
                    @foreach ($variants as $i => $v)
                        <button type="button" class="beres-btn px-3 py-1 text-[10px] border transition-colors
                            {{ $i === 0 ? 'text-white' : 'text-[#737373] hover:border-[#2D5A27] hover:text-[#2D5A27]' }}"
                            style="{{ $i === 0 ? 'background-color:#2D5A27; border-color:#2D5A27;' : 'border-color:#E8F0E5;' }} border-radius:999px;">
                            {{ $v }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($prodId && $inStock)
            {{-- Qty stepper + Add to Cart --}}
            <form
                action="{{ route('shop.api.checkout.cart.store') }}"
                method="POST"
                class="mt-3 flex items-stretch gap-2"
                onsubmit="event.preventDefault(); beresAddToCart(this);"
            >
                @csrf
                <input type="hidden" name="product_id" value="{{ $prodId }}">
                @if (count($childVariants))
                    <input type="hidden" name="selected_configurable_option" value="{{ $childVariants[0]['id'] }}" class="beres-variant-input">
                @endif

                <div class="flex items-center border" style="border-color:#E8F0E5; border-radius:999px;">
                    <button type="button" class="beres-btn w-7 h-full flex items-center justify-center text-[#2D5A27] hover:bg-[#F5F9F3]" style="border-radius:999px 0 0 999px;" onclick="beresQty(this, -1)" aria-label="Kurangi">−</button>
                    <input type="number" name="quantity" value="1" min="1" max="99" class="w-8 text-center text-[11px] border-0 focus:outline-none bg-transparent" style="font-weight:600; color:#171717;" aria-label="Jumlah">
                    <button type="button" class="beres-btn w-7 h-full flex items-center justify-center text-[#2D5A27] hover:bg-[#F5F9F3]" style="border-radius:0 999px 999px 0;" onclick="beresQty(this, 1)" aria-label="Tambah">+</button>
                </div>

                <button type="submit"
                        class="beres-btn flex-1 py-2.5 text-[11px] tracking-[0.14em] uppercase text-white hover:opacity-90"
                        style="background-color:#2D5A27; font-weight:600; border-radius:999px;">
                    Tambah ke Keranjang
                </button>
            </form>
        @elseif ($prodId && !$inStock)
            <button type="button" disabled
                    class="mt-3 w-full py-2.5 text-[11px] tracking-[0.14em] uppercase text-[#737373] cursor-not-allowed"
                    style="background-color:#F5F5F5; border-radius:999px; font-weight:600;">
                Habis
            </button>
        @else
            <a href="{{ $href }}"
               class="beres-btn mt-3 block w-full py-2.5 text-[11px] tracking-[0.14em] uppercase text-white hover:opacity-90 text-center"
               style="background-color:#2D5A27; font-weight:600; border-radius:999px;">
                Lihat produk
            </a>
        @endif
    </div>
</div>
