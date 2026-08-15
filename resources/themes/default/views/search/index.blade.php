<x-shop::layouts>
    <x-slot:title>
        {{ !empty($query) ? 'Hasil Pencarian: "' . $query . '"' : 'Pencarian Produk' }}
    </x-slot:title>

    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-zinc-500 mb-6 flex items-center gap-2">
            <a href="/" class="hover:text-[#2D5A27] transition-colors">Beranda</a>
            <span class="text-zinc-400">/</span>
            <span class="text-[#171717] font-medium">Pencarian</span>
            @if(!empty($query))
                <span class="text-zinc-400">/</span>
                <span class="text-zinc-500 italic">"{{ $query }}"</span>
            @endif
        </nav>

        {{-- Search Header --}}
        <div class="mb-10 max-w-3xl">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#171717] tracking-tight">
                @if(!empty($query))
                    Hasil Pencarian: <span class="text-[#2D5A27]">"{{ $query }}"</span>
                @else
                    Katalog Pencarian Produk
                @endif
            </h1>

            <p class="mt-2 text-sm text-zinc-500">
                @if($products->total() > 0)
                    Ditemukan <strong class="text-[#171717] font-semibold">{{ $products->total() }}</strong> produk yang cocok.
                @else
                    Tidak ditemukan produk yang cocok dengan kata kunci tersebut.
                @endif
            </p>

            {{-- Inline Search Form --}}
            <form action="{{ route('shop.search.index') }}" method="GET" class="mt-6 flex items-center gap-3">
                <div class="relative flex-1">
                    <input
                        type="text"
                        name="query"
                        value="{{ $query }}"
                        placeholder="Cari produk atau kategori..."
                        class="w-full h-12 pl-11 pr-4 rounded-xl border border-zinc-300 focus:border-[#2D5A27] focus:ring-1 focus:ring-[#2D5A27] text-sm transition-colors outline-none"
                    >
                    <svg class="w-5 h-5 text-zinc-400 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="h-12 px-6 bg-[#2D5A27] hover:bg-[#1E3A1E] text-white text-sm font-semibold rounded-xl transition-colors shrink-0">
                    Cari
                </button>
            </form>
        </div>

        {{-- Products Grid --}}
        @if($products->count())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                @foreach($products as $index => $product)
                    @include('shop::components.layouts._product-card', [
                        'product' => $product,
                        'index' => $index,
                        'bg' => ['#E8F0E5','#DCE8D6','#F0F5EC','#EAF1E4'][$index % 4]
                    ])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-[#F5F9F3] rounded-2xl border border-[#E8F0E5] p-8">
                <div class="w-16 h-16 bg-[#E8F0E5] text-[#2D5A27] rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-[#171717] mb-2">Produk Tidak Ditemukan</h3>
                <p class="text-sm text-zinc-500 max-w-md mx-auto mb-6">Coba periksa ejaan kata kunci Anda atau jelajahi koleksi kategori produk kami.</p>
                <a href="/" class="inline-flex items-center gap-2 px-6 py-3 bg-[#2D5A27] hover:bg-[#1E3A1E] text-white text-sm font-semibold rounded-xl transition-colors">
                    <span>Kembali ke Beranda</span>
                    <span>→</span>
                </a>
            </div>
        @endif
    </div>
</x-shop::layouts>
