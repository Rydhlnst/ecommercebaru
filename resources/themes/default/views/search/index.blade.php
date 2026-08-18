<x-shop::layouts>
    <x-slot:title>
        {{ !empty($query) ? 'Hasil Pencarian: "' . $query . '"' : 'Pencarian Produk' }}
    </x-slot:title>

    <div class="mx-auto max-w-[1600px] px-4 py-8 sm:px-6 md:px-10 md:py-12 lg:px-14">
        <nav class="mb-6 flex items-center gap-2 text-sm text-zinc-500">
            <a href="/" class="transition-colors hover:text-[#2D5A27]">Beranda</a>
            <span class="text-zinc-400">/</span>
            <span class="font-medium text-[#171717]">Pencarian</span>
            @if(!empty($query))
                <span class="text-zinc-400">/</span>
                <span class="italic text-zinc-500">"{{ $query }}"</span>
            @endif
        </nav>

        <div class="mb-10 max-w-3xl">
            <h1 class="text-2xl font-bold tracking-tight text-[#171717] sm:text-3xl md:text-4xl">
                @if(!empty($query))
                    Hasil Pencarian: <span class="text-[#2D5A27]">"{{ $query }}"</span>
                @else
                    Katalog Pencarian Produk
                @endif
            </h1>
            <p class="mt-2 text-sm text-zinc-500">
                @if($products->total() > 0)
                    Ditemukan <strong class="font-semibold text-[#171717]">{{ $products->total() }}</strong> produk yang cocok.
                @else
                    Tidak ditemukan produk yang cocok dengan kata kunci tersebut.
                @endif
            </p>
            <form action="{{ route('shop.search.index') }}" method="GET" class="mt-6 flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="text" name="query" value="{{ $query }}" placeholder="Cari produk atau kategori..." class="h-12 w-full rounded-xl border border-zinc-300 pl-11 pr-4 text-sm outline-none transition-colors focus:border-[#2D5A27] focus:ring-1 focus:ring-[#2D5A27]">
                    <svg class="absolute left-3.5 top-3.5 h-5 w-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="h-12 shrink-0 rounded-xl bg-[#2D5A27] px-6 text-sm font-semibold text-white transition-colors hover:bg-[#1E3A1E]">Cari</button>
            </form>
        </div>

        <div class="flex items-start gap-10 max-lg:gap-6 max-md:flex-col">
            @include('admin.frontend._catalog-filters', ['action' => url()->current(), 'query' => $query, 'maxPrice' => $maxPrice])

            <main class="min-w-0 flex-1">
                <div class="mb-6 flex items-center justify-between gap-4 border-b border-[#E8F0E5] pb-4">
                    <p class="text-sm text-[#737373]">Menampilkan <strong class="text-[#171717]">{{ $products->count() }}</strong> dari {{ $products->total() }} produk</p>
                    <div class="hidden items-center gap-2 text-[#737373] md:flex" aria-label="Tampilan katalog">
                        <span class="rounded-lg bg-[#F5F9F3] px-3 py-2 text-lg text-[#2D5A27]" aria-hidden="true">▦</span>
                        <span class="px-2 py-2 text-lg" aria-hidden="true">☷</span>
                    </div>
                </div>

                @if($products->count())
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:gap-6">
                        @foreach($products as $index => $product)
                            @include('shop::components.layouts._product-card', ['product' => $product, 'index' => $index, 'bg' => ['#E8F0E5','#DCE8D6','#F0F5EC','#EAF1E4'][$index % 4]])
                        @endforeach
                    </div>
                    <div class="mt-10">{{ $products->links() }}</div>
                @else
                    <div class="rounded-2xl border border-[#E8F0E5] bg-[#F5F9F3] p-8 py-20 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#E8F0E5] text-2xl text-[#2D5A27]">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-[#171717]">Produk Tidak Ditemukan</h3>
                        <p class="mx-auto mb-6 max-w-md text-sm text-zinc-500">Coba ubah kata kunci atau filter pencarian Anda.</p>
                        <a href="{{ route('shop.search.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#2D5A27] px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#1E3A1E]">Reset pencarian</a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</x-shop::layouts>
