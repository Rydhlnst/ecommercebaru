@php
    // Accept: $product (Bagisto Product OR AdminProduct) ATAU plain args
    $product  = $product ?? null;
    $bg       = $bg      ?? '#E8F0E5';
    $index    = $index   ?? 0;

    $inStock  = true;
    $compare  = null;
    $salePrice = null;
    $isNew    = false;

    if ($product) {
        // Detect model type: AdminProduct vs Bagisto Product
        $isAdminProduct = $product instanceof \App\Models\AdminProduct;

        $name     = $product->name ?? 'Produk';
        $prodId   = $product->id;
        $isNew    = $product->created_at && $product->created_at->diffInDays(now()) <= 14;

        if ($isAdminProduct) {
            // --- AdminProduct (simple model) ---
            $price    = 'Rp ' . number_format($product->price ?? 0, 0, ',', '.');
            $inStock  = ($product->stock ?? 0) > 0;
            $href     = route('shop.admin_product.show', $product->slug);
            $image    = $product->images->count()
                            ? (file_exists(public_path('storage/'.$product->images->first()->image_path)) ? asset('storage/'.$product->images->first()->image_path) : asset($product->images->first()->image_path))
                            : null;

            // Badge
            $badge = $product->badge ?? null;

            // Description
            $description = $product->description ?? null;

            // Variations
            $childVariants = [];
            $vars = $product->variations;
            if ($vars && $vars->count()) {
                foreach ($vars as $i => $var) {
                    $childVariants[] = [
                        'id'            => $var->id,
                        'label'         => $var->weight ? $var->weight_label : 'Var '.($i+1),
                        'price'         => 'Rp ' . number_format($var->price, 0, ',', '.'),
                        'regular_price' => null,
                        'special_price' => $var->price,
                    ];
                }
            }

            $isConfigurable = false;
            $superAttrId    = null;
        } else {
            // --- Bagisto Product (EAV model) ---
            $minPrice = 0;
            try { $minPrice = $product->getTypeInstance()->getMinimalPrice() ?? 0; } catch (\Throwable $e) {}
            $price    = 'Rp ' . number_format($minPrice, 0, ',', '.');
            try { $inStock = $product->getTypeInstance()->isSaleable(); } catch (\Throwable $e) {}

            try {
                $regularPrice = $product->getTypeInstance()->getRegularPrice() ?? null;
                if ($regularPrice && $regularPrice > $minPrice) {
                    $compare = 'Rp ' . number_format($regularPrice, 0, ',', '.');
                    $salePrice = $price;
                }
            } catch (\Throwable $e) {}

            $href     = $product->url_key
                            ? route('shop.product_or_category.index', $product->url_key)
                            : route('shop.search.index');
            $image    = product_image()->getProductBaseImage($product)['small_image_url'] ?? null;
            $badge    = null;
            $description = $product->short_description ?? null;

            $isConfigurable = $product->type === 'configurable';
            $childVariants  = [];
            $superAttrId    = null;
            if ($isConfigurable) {
                try {
                    foreach ($product->variants as $child) {
                        $childType = $child->getTypeInstance();
                        $minPrice  = 0;
                        $regPrice  = null;
                        try {
                            $minPrice = $childType->getMinimalPrice() ?? $child->price ?? 0;
                            $regPrice = $childType->getRegularPrice();
                        } catch (\Throwable $e) {}

                        $label = $child->name;
                        if (! empty($child->net_weight) && ! is_numeric($child->net_weight)) {
                            $label = $child->net_weight;
                        } elseif (str_contains($child->name, ' - ')) {
                            $parts = explode(' - ', $child->name);
                            $label = trim(end($parts));
                        }

                        $childVariants[] = [
                            'id'            => $child->id,
                            'label'         => $label,
                            'price'         => 'Rp ' . number_format($minPrice, 0, ',', '.'),
                            'regular_price' => $regPrice,
                            'special_price' => $minPrice,
                        ];
                    }
                    $superAttrs = $product->super_attributes()->first();
                    if ($superAttrs) $superAttrId = $superAttrs->attribute_id ?? $superAttrs->id ?? 35;
                } catch (\Throwable $e) {}
            }
        }
    } else {
        $name     = $name     ?? '';
        $price    = $price    ?? '';
        $compare  = $compare  ?? null;
        $variants = $variants ?? [];
        $href     = $href     ?? route('shop.search.index');
        $prodId   = 1;
        $image    = null;
        $badge    = null;
        $description = null;
        $isConfigurable = false;
        $childVariants  = [];
        $superAttrId    = null;
    }

    // Ensure childVariants is always populated with variant pills for selection on every card
    if (empty($childVariants)) {
        $variantList = !empty($variants) ? (array) $variants : ['500g', '1kg'];
        foreach ($variantList as $i => $vLabel) {
            $childVariants[] = [
                'id'            => 'var_' . ($i + 1),
                'label'         => is_array($vLabel) ? ($vLabel['label'] ?? 'Varian ' . ($i+1)) : (string) $vLabel,
                'price'         => $price,
                'regular_price' => null,
                'special_price' => null,
            ];
        }
    }
@endphp

<div class="beres-card group bg-white overflow-hidden flex flex-col justify-between" style="border:1px solid #E8F0E5; border-radius:16px;" @if($superAttrId) data-super-attr-id="{{ $superAttrId }}" @endif>
    {{-- Product Image --}}
    <a href="{{ $href }}" class="block relative aspect-[4/5] overflow-hidden" style="background-color:{{ $bg }};">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" loading="lazy">
        @endif

        {{-- Numbered badge --}}
        <span class="absolute top-3 left-3 w-7 h-7 flex items-center justify-center bg-white text-[#171717] text-xs font-bold" style="border-radius:999px; box-shadow:0 1px 4px rgba(0,0,0,0.1);">
            {{ $index + 1 }}
        </span>

        {{-- Quick view button --}}
        @if ($prodId)
            <button type="button"
                    onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ $href }}';"
                    class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center bg-white/90 hover:bg-white transition-colors shadow-sm"
                    style="border-radius:999px;"
                    aria-label="Lihat produk">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2D5A27" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        @endif

        {{-- Out of stock overlay --}}
        @if (!$inStock)
            <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                <span class="text-white text-xs tracking-[0.2em] uppercase px-4 py-2" style="background-color:rgba(0,0,0,0.7); border-radius:999px; font-weight:600;">@lang('shop::app.products.out-of-stock', [], 'Habis')</span>
            </div>
        @endif
    </a>

    {{-- Product Info --}}
    <div class="p-4 flex flex-col flex-1 justify-between">
        <div>
            {{-- Product Name --}}
            <a href="{{ $href }}" class="block">
                <h3 class="text-base font-semibold text-[#171717] leading-snug transition-colors hover:text-[#2D5A27]">{{ $name }}</h3>
            </a>

            {{-- Badge --}}
            @if($badge)
                <div class="mt-1">
                    <span class="inline-block text-[10px] tracking-[0.14em] uppercase px-2 py-0.5 text-white font-bold"
                          style="width:fit-content; max-width:max-content; border-radius:4px; background-color:{{ $badge === 'sale' ? '#B91C1C' : ($badge === 'habis_terjual' ? '#737373' : '#2D5A27') }};">
                        {{ $badge === 'new' ? 'New' : ($badge === 'sale' ? 'Sale' : 'Habis') }}
                    </span>
                </div>
            @endif

            {{-- Description --}}
            @if (!empty($description))
                <p class="mt-1.5 text-xs text-[#737373] leading-relaxed line-clamp-2">{!! Str::limit(strip_tags($description), 80) !!}</p>
            @endif

            {{-- Price with sale --}}
            <div class="mt-2 flex items-center gap-2">
                @if ($compare)
                    {{-- Sale price --}}
                    <span class="text-xl font-bold text-[#171717]">{{ $salePrice }}</span>
                    <span class="text-sm text-[#737373] line-through">{{ $compare }}</span>
                    <span class="text-[10px] tracking-[0.14em] uppercase px-2 py-0.5 text-white font-bold" style="background-color:#B91C1C; border-radius:4px;">Sale</span>
                @else
                    <span class="text-xl font-bold text-[#171717]">{{ $price }}</span>
                @endif
            </div>
        </div>

        {{-- Actions: Counter + Add to Cart + Buy Now --}}
        @if ($prodId && $inStock)
            <form
                action="{{ route('cart.add') }}"
                method="POST"
                class="mt-3 space-y-2"
                onsubmit="event.preventDefault(); beresAddToCart(this);"
            >
                @csrf
                <input type="hidden" name="product_id" value="{{ $prodId }}">

                {{-- Weight variant buttons (above quantity, horizontal scroll) --}}
                @if (count($childVariants))
                    <input type="hidden" name="selected_configurable_option" value="{{ $childVariants[0]['id'] }}" class="beres-variant-input">
                    <p class="text-xs font-medium text-[#171717] mb-1">@lang('shop::app.products.select-variant', [], 'Pilih Varian / Berat')</p>
                    <div class="beres-variant-row flex items-center gap-1.5 mb-2 overflow-x-auto pb-1" style="scrollbar-width:none; -ms-overflow-style:none;">
                        @foreach ($childVariants as $i => $v)
                            <button type="button"
                                    class="beres-variant-btn shrink-0 px-3 py-1 text-xs font-medium border transition-all whitespace-nowrap {{ $i === 0 ? 'beres-variant-active' : '' }}"
                                    style="{{ $i === 0 ? 'background-color:#2D5A27; color:#fff; border-color:#2D5A27;' : 'background-color:#fff; color:#171717; border-color:#E8F0E5;' }} border-radius:999px;"
                                    onclick="beresSelectVariantInline(this, '{{ $v['id'] }}', '{{ $v['price'] }}')"
                                    data-variant-id="{{ $v['id'] }}">
                                {{ $v['label'] }}
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Row 1: Quantity + Add to cart --}}
                <div class="flex items-center gap-2">
                    <div class="flex items-center border border-[#E8F0E5] rounded-lg overflow-hidden h-9 bg-white">
                        <button type="button" class="w-8 h-full flex items-center justify-center text-[#2D5A27] hover:bg-[#F5F9F3] transition-colors text-base leading-none" onclick="beresQty(this, -1)" aria-label="Kurangi">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="99" class="w-8 h-full text-center text-xs border-0 border-x border-[#E8F0E5] focus:outline-none bg-transparent font-semibold leading-none" aria-label="Jumlah">
                        <button type="button" class="w-8 h-full flex items-center justify-center text-[#2D5A27] hover:bg-[#F5F9F3] transition-colors text-base leading-none" onclick="beresQty(this, 1)" aria-label="Tambah">+</button>
                    </div>

                    <button type="submit"
                            class="flex-1 h-9 text-xs font-semibold tracking-[0.05em] uppercase text-white hover:opacity-90 transition-opacity flex items-center justify-center gap-1.5"
                            style="background-color:#2D5A27; border-radius:8px;">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        <span>@lang('shop::app.products.add-to-cart', [], 'Add to Cart')</span>
                    </button>
                </div>

                {{-- Row 2: Buy Now direct checkout --}}
                <button type="button"
                        onclick="beresBuyNow(this.form)"
                        class="w-full h-8 text-[11px] font-bold tracking-[0.08em] uppercase text-[#171717] hover:text-white bg-[#E8F0E5] hover:bg-[#171717] transition-colors flex items-center justify-center gap-1.5"
                        style="border-radius:8px;">
                    <svg class="w-3 h-3 text-[#2D5A27]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>@lang('shop::app.products.buy-now', [], 'Beli Sekarang')</span>
                </button>
            </form>
        @elseif ($prodId && !$inStock)
            <button type="button" disabled
                    class="mt-3 w-full h-10 text-sm font-semibold tracking-[0.05em] uppercase text-[#737373] cursor-not-allowed"
                    style="background-color:#F5F5F5; border-radius:8px;">
                @lang('shop::app.products.out-of-stock', [], 'Habis')
            </button>
        @else
            <a href="{{ $href }}"
               class="beres-btn mt-3 block w-full h-10 text-sm font-semibold tracking-[0.05em] uppercase text-white hover:opacity-90 text-center transition-opacity"
               style="background-color:#2D5A27; border-radius:8px;">
                Lihat Produk
            </a>
        @endif
    </div>
</div>
