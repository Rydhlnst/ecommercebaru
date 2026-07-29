@php
    // Accept: $product (Bagisto Product model) ATAU plain args (name, price, compare, variants, bg, href)
    $product  = $product ?? null;
    $bg       = $bg      ?? '#E8F0E5';

    if ($product) {
        $name     = $product->name ?? $product->url_key ?? 'Product';
        $minPrice = 0;
        try { $minPrice = $product->getTypeInstance()->getMinimalPrice() ?? 0; } catch (\Throwable $e) {}
        $price    = core()->currency($minPrice);
        $compare  = null; // Bagisto special price bisa di-detect via getTypeInstance
        $variants = []; // TODO: fetch product options
        $href     = route('shop.product_or_category.index', $product->url_key ?? '#');
        $prodId   = $product->id;
        $image    = product_image()->getProductBaseImage($product)['small_image_url'] ?? null;
    } else {
        $name     = $name     ?? '';
        $price    = $price    ?? '';
        $compare  = $compare  ?? null;
        $variants = $variants ?? [];
        $href     = $href     ?? '#';
        $prodId   = null;
        $image    = null;
    }
@endphp

<div class="group border overflow-hidden" style="border-color:#E8F0E5; border-radius:16px;">
    <a href="{{ $href }}" class="block relative aspect-square overflow-hidden" style="background-color:{{ $bg }};">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" loading="lazy">
        @endif
        @if ($compare)
            <span class="absolute top-3 left-3 text-white text-[10px] tracking-[0.14em] uppercase px-2.5 py-1" style="background-color:#2D5A27; border-radius:999px;">Sale</span>
        @endif
    </a>

    <div class="p-3 md:p-4">
        <a href="{{ $href }}" class="block">
            <p class="text-xs md:text-sm text-[#171717] leading-snug min-h-[2.5em] line-clamp-2" style="font-weight:500;">{{ $name }}</p>
        </a>

        <p class="mt-1.5 text-sm" style="font-weight:600;">
            <span style="color:#2D5A27;">{{ $price }}</span>
            @if ($compare)
                <span class="text-[#737373] line-through ml-1.5 text-xs" style="font-weight:400;">{{ $compare }}</span>
            @endif
        </p>

        @if (count($variants))
            <div class="mt-2">
                <p class="text-[10px] tracking-wide text-[#737373] mb-1">Ukuran</p>
                <div class="flex flex-wrap gap-1">
                    @foreach ($variants as $i => $v)
                        <button type="button" class="px-3 py-1 text-[10px] border transition-colors
                            {{ $i === 0 ? 'text-white' : 'text-[#737373] hover:border-[#2D5A27] hover:text-[#2D5A27]' }}"
                            style="{{ $i === 0 ? 'background-color:#2D5A27; border-color:#2D5A27;' : 'border-color:#E8F0E5;' }} border-radius:999px;">
                            {{ $v }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($prodId)
            {{-- Real product — Add to Cart wired ke Bagisto API --}}
            <form
                action="{{ route('shop.api.checkout.cart.store') }}"
                method="POST"
                class="mt-3"
                onsubmit="event.preventDefault(); beresAddToCart(this);"
            >
                @csrf
                <input type="hidden" name="product_id" value="{{ $prodId }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                        class="w-full py-2.5 text-[11px] tracking-[0.14em] uppercase text-white hover:opacity-90 transition-opacity"
                        style="background-color:#2D5A27; font-weight:600; border-radius:999px;">
                    Add to Cart
                </button>
            </form>
        @else
            <a href="{{ $href }}"
               class="mt-3 block w-full py-2.5 text-[11px] tracking-[0.14em] uppercase text-white hover:opacity-90 transition-opacity text-center"
               style="background-color:#2D5A27; font-weight:600; border-radius:999px;">
                Lihat produk
            </a>
        @endif
    </div>
</div>
