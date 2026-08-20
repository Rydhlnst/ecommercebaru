@php
    // Accept: $product (Bagisto Product OR AdminProduct) ATAU plain args
    $product  = $product ?? null;
    $bg       = $bg      ?? '#E8F0E5';
    $index    = $index   ?? 0;
    $imageScale = $imageScale ?? 1.02;

    $inStock  = true;
    $compare  = null;
    $salePrice = null;
    $isNew    = false;
    $href     = $href    ?? route('shop.search.index');
    $badge    = null;
    $description = null;
    $childVariants = [];

    if ($product) {
        // Detect model type: AdminProduct vs Bagisto Product
        $isAdminProduct = $product instanceof \App\Models\AdminProduct;

        $name     = $product->name ?? 'Produk';
        $prodId   = $product->id;
        $isNew    = $product->created_at && $product->created_at->diffInDays(now()) <= 14;

        if ($isAdminProduct) {
            // --- AdminProduct (simple model) ---
            $priceNum = (float) ($product->price ?? 0);
            $price    = 'Rp ' . number_format($priceNum, 0, ',', '.');
            $vars     = $product->variations;
            $hasVars  = $vars && $vars->count() > 0;

            // Compare at price (strikethrough / sale)
            $compareAt = (float) ($product->compare_at_price ?? 0);
            if ($compareAt > 0 && $compareAt > $priceNum) {
                $compare   = 'Rp ' . number_format($compareAt, 0, ',', '.');
                $salePrice = $price;
            }

            // Availability must follow actual stock, not only the display badge.
            $inStock = $hasVars
                ? (int) $vars->sum('stock') > 0
                : (int) ($product->stock ?? 0) > 0;
            if ($product->badge === 'habis_terjual') {
                $inStock = false;
            }

            $href     = route('shop.admin_product.show', $product->slug);
            $image    = $product->images->count()
                            ? $product->images->first()->card_url
                            : null;
            $imageFit = 'contain';
            $imageX   = $product->images->first()?->focal_x ?? 50;
            $imageY   = $product->images->first()?->focal_y ?? 50;

            // Badge
            $badge = ($product->badge && $product->badge !== 'habis_terjual') ? $product->badge : ($isNew ? 'new' : null);

            // Description
            $description = $product->description ?? null;

            // Variations from the database.
            if ($hasVars) {
                foreach ($vars as $i => $var) {
                    $vPriceNum = (float) ($var->price > 0 ? $var->price : $priceNum);
                    $vCompareAt = (float) ($var->compare_at_price ?? 0);
                    $childVariants[] = [
                        'id'            => $var->id,
                        'label'         => $var->weight_label,
                        'price'         => 'Rp ' . number_format($vPriceNum, 0, ',', '.'),
                        'regular_price' => ($vCompareAt > $vPriceNum) ? 'Rp ' . number_format($vCompareAt, 0, ',', '.') : null,
                        'special_price' => $vPriceNum,
                    ];
                }
                if ($priceNum == 0 && count($childVariants)) {
                    $price = $childVariants[0]['price'];
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
            $imageFit = 'cover';
            $imageX   = 50;
            $imageY   = 50;
            $badge    = null;
            $description = $product->short_description ?? null;

            $isConfigurable = $product->type === 'configurable';
            $superAttrId    = null;
            if ($isConfigurable) {
                try {
                    foreach ($product->variants as $child) {
                        $childType = $child->getTypeInstance();
                        $cMinPrice  = 0;
                        $regPrice  = null;
                        try {
                            $cMinPrice = $childType->getMinimalPrice() ?? $child->price ?? 0;
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
                            'price'         => 'Rp ' . number_format($cMinPrice, 0, ',', '.'),
                            'regular_price' => $regPrice,
                            'special_price' => $cMinPrice,
                        ];
                    }
                    $superAttrs = $product->super_attributes()->first();
                    if ($superAttrs) $superAttrId = $superAttrs->attribute_id ?? $superAttrs->id ?? 35;
                } catch (\Throwable $e) {}
            }

        }
    } else {
        $name     = $name     ?? 'Produk Segar';
        $price    = $price    ?? 'Rp 100.000';
        $compare  = $compare  ?? null;
        $href     = $href     ?? route('shop.search.index');
        $prodId   = 1;
        $image    = null;
        $badge    = null;
        $description = null;
        $isConfigurable = false;
        $superAttrId    = null;
        $childVariants  = [];
    }
@endphp

<div class="beres-card group bg-white overflow-hidden flex flex-col justify-between" style="border:1px solid #E8F0E5; border-radius:16px; height:100%; min-height:100%;" @if($superAttrId) data-super-attr-id="{{ $superAttrId }}" @endif>
    {{-- Product Image Container (Standardized 4:5 Aspect Ratio) --}}
    <a href="{{ $href }}" class="block relative w-full overflow-hidden shrink-0" style="aspect-ratio:4 / 5 !important; height:auto !important; min-height:0; background-color:#F5F9F3;">
        <x-shop::product-image :image="$image" :alt="$name" class="absolute inset-0 block transition-transform duration-500" style="width:100% !important; height:100% !important; display:block !important; object-fit:contain !important; object-position:center; padding:1rem !important; transform:scale(1);" />

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
                <span class="text-white text-xs tracking-wider uppercase px-4 py-2" style="background-color:rgba(0,0,0,0.7); border-radius:999px; font-weight:600;">Habis</span>
            </div>
        @endif
    </a>

    {{-- Product Info --}}
    <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between">
        <div>
            {{-- Product Name --}}
            <a href="{{ $href }}" class="block">
                <h3 class="text-base font-semibold text-[#171717] leading-snug transition-colors hover:text-[#2D5A27]">{{ $name }}</h3>
            </a>

            {{-- Badge --}}
            @if($badge)
                <div class="mt-1.5">
                    <span class="inline-block text-[10px] tracking-wider uppercase px-2 py-0.5 text-white font-bold"
                          style="width:fit-content; max-width:max-content; border-radius:4px; background-color:{{ $badge === 'sale' ? '#B91C1C' : ($badge === 'habis_terjual' ? '#737373' : '#2D5A27') }};">
                        {{ $badge === 'new' ? 'New' : ($badge === 'sale' ? 'Sale' : 'Habis') }}
                    </span>
                </div>
            @endif

            {{-- Description --}}
            @if (!empty($description))
                <p class="mt-2 text-xs text-[#737373] leading-relaxed line-clamp-2">{!! Str::limit(strip_tags($description), 80) !!}</p>
            @endif

            {{-- Price with sale --}}
            <div class="mt-3 mb-2 flex items-center gap-2">
                @if ($compare)
                    <span class="text-xl font-bold text-[#171717]">{{ $salePrice }}</span>
                    <span class="text-sm text-[#737373] line-through">{{ $compare }}</span>
                    <span class="text-[10px] tracking-wider uppercase px-2 py-0.5 text-white font-bold" style="background-color:#B91C1C; border-radius:4px;">Sale</span>
                @else
                    <span class="text-xl font-bold text-[#171717]">{{ $price }}</span>
                @endif
            </div>
        </div>

        {{-- Actions: Variant Pills + Counter + Add to Cart + Buy Now --}}
        @if ($prodId && $inStock)
            <form
                action="{{ route('cart.add') }}"
                method="POST"
                class="mt-4 pt-1 flex flex-col gap-3"
                onsubmit="event.preventDefault(); beresAddToCart(this);"
            >
                @csrf
                <input type="hidden" name="product_id" value="{{ $prodId }}">

                {{-- Weight variant buttons (horizontal scroll) --}}
                @if (count($childVariants))
                    <div>
                        <input type="hidden" name="selected_configurable_option" value="{{ $childVariants[0]['id'] }}" class="beres-variant-input">
                        <p class="text-xs font-semibold text-[#171717] mb-1.5">Pilih Varian / Berat:</p>
                        <div class="beres-variant-row flex items-center gap-2 overflow-x-auto pb-1" style="scrollbar-width:none; -ms-overflow-style:none;">
                            @foreach ($childVariants as $i => $v)
                                <button type="button"
                                        class="beres-variant-btn shrink-0 px-3 py-1 text-xs font-semibold border transition-all whitespace-nowrap {{ $i === 0 ? 'beres-variant-active' : '' }}"
                                        style="{{ $i === 0 ? 'background-color:#2D5A27; color:#fff; border-color:#2D5A27;' : 'background-color:#fff; color:#171717; border-color:#E8F0E5;' }} border-radius:999px;"
                                        onclick="beresSelectVariantInline(this, '{{ $v['id'] }}', '{{ $v['price'] }}')"
                                        data-variant-id="{{ $v['id'] }}">
                                    {{ $v['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Quantity Stepper --}}
                <div class="flex items-center justify-between bg-[#F5F9F3] border border-[#E8F0E5] rounded-xl px-3.5 h-10">
                    <span class="text-xs font-semibold text-[#171717]">Jumlah</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="w-7 h-7 flex items-center justify-center text-[#2D5A27] hover:bg-white rounded-md transition-colors font-bold text-sm leading-none cursor-pointer" onclick="beresQty(this, -1)" aria-label="Kurangi">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="99" class="w-8 h-7 text-center text-xs border-0 focus:outline-none bg-transparent font-bold leading-none text-[#171717]" aria-label="Jumlah">
                        <button type="button" class="w-7 h-7 flex items-center justify-center text-[#2D5A27] hover:bg-white rounded-md transition-colors font-bold text-sm leading-none cursor-pointer" onclick="beresQty(this, 1)" aria-label="Tambah">+</button>
                    </div>
                </div>

                {{-- Action Buttons: 2 Side-by-Side Horizontal Buttons --}}
                <div class="grid grid-cols-2 gap-2.5 pt-0.5">
                    {{-- Button 1: Add to Cart --}}
                    <button type="submit"
                            class="h-10 px-2 text-[11px] font-bold tracking-wider uppercase text-white transition-all flex items-center justify-center gap-1.5 whitespace-nowrap shadow-xs cursor-pointer"
                            style="background-color:#2D5A27; border:1.5px solid #2D5A27; border-radius:10px;"
                            onmouseover="this.style.backgroundColor='#1E3D1A'; this.style.borderColor='#1E3D1A';"
                            onmouseout="this.style.backgroundColor='#2D5A27'; this.style.borderColor='#2D5A27';">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        <span>Keranjang</span>
                    </button>

                    {{-- Button 2: Buy Now (Borderless White with Green Text and Muted Green Hover) --}}
                    <button type="button"
                            onclick="beresBuyNow(this.form)"
                            class="h-10 px-2 text-[11px] font-bold tracking-wider uppercase transition-all duration-200 flex items-center justify-center gap-1.5 whitespace-nowrap shadow-xs cursor-pointer"
                            style="background-color:#FFFFFF; color:#2D5A27; border:none; border-radius:10px;"
                            onmouseover="this.style.backgroundColor='#E8F0E5';"
                            onmouseout="this.style.backgroundColor='#FFFFFF';">
                        <svg class="w-3.5 h-3.5 shrink-0 text-[#2D5A27]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Beli Sekarang</span>
                    </button>
                </div>
            </form>
        @elseif ($prodId && !$inStock)
            <button type="button" disabled
                    class="mt-4 w-full h-11 text-xs font-bold tracking-wider uppercase text-zinc-500 cursor-not-allowed bg-zinc-100 rounded-xl">
                Habis Terjual
            </button>
        @else
            <a href="{{ $href }}"
               class="beres-btn mt-4 block w-full h-11 text-xs font-bold tracking-wider uppercase text-white hover:opacity-90 text-center transition-opacity flex items-center justify-center"
               style="background-color:#2D5A27; border-radius:10px;">
                Lihat Produk
            </a>
        @endif
    </div>
</div>
