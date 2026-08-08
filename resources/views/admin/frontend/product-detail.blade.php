@extends('shop::layouts.index')

@section('title', $product->name)

@section('content')
<x-shop::layouts>
    <div class="mx-auto max-w-[1200px] px-4 py-8">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-zinc-500 mb-6">
            <a href="/" class="hover:text-[#2D5A27]">Beranda</a>
            <span class="mx-2">/</span>
            @if($product->category)
                <a href="{{ route('shop.admin_category.show', $product->category->slug) }}" class="hover:text-[#2D5A27]">{{ $product->category->name }}</a>
                <span class="mx-2">/</span>
            @endif
            <span class="text-[#171717]">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
            {{-- Product Images --}}
            <div>
                @if($product->images->count())
                    <div class="aspect-square rounded-2xl overflow-hidden bg-[#F5F9F3] mb-4">
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" id="main-image">
                    </div>
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($product->images as $img)
                                <button onclick="document.getElementById('main-image').src='{{ asset('storage/' . $img->image_path) }}'" class="aspect-square rounded-lg overflow-hidden border-2 border-transparent hover:border-[#2D5A27] transition-colors">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="aspect-square rounded-2xl bg-[#F5F9F3] flex items-center justify-center">
                        <span class="text-6xl text-[#C8DBBE]">🌿</span>
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="flex flex-col">
                @if($product->badge)
                    <span class="inline-block self-start text-[10px] tracking-[0.14em] uppercase px-3 py-1 text-white font-bold mb-3"
                          style="border-radius:4px; background-color:{{ $product->badge === 'sale' ? '#B91C1C' : ($product->badge === 'habis_terjual' ? '#737373' : '#2D5A27') }};">
                        {{ $product->badge === 'new' ? 'Baru' : ($product->badge === 'sale' ? 'Sale' : 'Habis Terjual') }}
                    </span>
                @endif

                @if($product->category)
                    <p class="text-sm text-[#2D5A27] font-medium mb-1">{{ $product->category->name }}</p>
                @endif

                <h1 class="text-2xl lg:text-3xl font-bold text-[#171717] mb-4">{{ $product->name }}</h1>

                {{-- Price --}}
                <div class="mb-6">
                    @if($product->has_variations && $product->variations->count())
                        <p class="text-sm text-zinc-500 mb-1">Mulai dari</p>
                    @endif
                    <p class="pdp-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>

                {{-- Stock --}}
                <div class="mb-6">
                    @if($product->stock > 0)
                        <span class="text-sm text-green-600 font-medium">✓ Stok tersedia ({{ $product->stock }})</span>
                    @else
                        <span class="text-sm text-red-500 font-medium">✗ Stok habis</span>
                    @endif
                </div>

                {{-- Description --}}
                @if($product->description)
                    <div class="prose prose-zinc max-w-none mb-6">
                        {!! $product->description !!}
                    </div>
                @endif

                {{-- Add to Cart --}}
                @if($product->stock > 0)
                    <form action="{{ route('shop.api.checkout.cart.store') }}" method="POST" class="mt-auto" onsubmit="event.preventDefault(); beresAddToCart(this);">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="pdp-btn-primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            Tambah ke Keranjang
                        </button>
                    </form>
                @endif

                {{-- WhatsApp --}}
                @php $wa = \App\Models\SiteSetting::getValue('store_whatsapp'); @endphp
                @if($wa)
                    <a href="https://wa.me/{{ $wa }}?text=Halo, saya tertarik dengan {{ urlencode($product->name) }}" target="_blank" class="pdp-btn-whatsapp mt-3">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Hubungi via WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-shop::layouts>
@endsection
