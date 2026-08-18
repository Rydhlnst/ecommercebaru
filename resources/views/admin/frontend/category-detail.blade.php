<x-shop::layouts>
    <x-slot:title>{{ $category->name }}</x-slot:title>

    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-zinc-500 mb-6 flex items-center gap-2">
            <a href="/" class="hover:text-[#2D5A27] transition-colors">Beranda</a>
            <span class="text-zinc-400">/</span>
            <span class="text-[#171717] font-medium">{{ $category->name }}</span>
        </nav>

        {{-- Collection Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#171717] mb-2">{{ $category->name }}</h1>
            <p class="text-zinc-500">{{ $products->total() }} produk</p>
        </div>

        <div class="flex items-start gap-10 max-lg:gap-6 max-md:flex-col">
            @include('admin.frontend._catalog-filters', ['action' => url()->current(), 'maxPrice' => $maxPrice])

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

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="rounded-2xl border border-[#E8F0E5] bg-[#F5F9F3] py-16 text-center">
                        <p class="text-lg text-zinc-500">Tidak ada produk yang sesuai filter.</p>
                    </div>
                @endif
            </main>
        </div>
    </div>
</x-shop::layouts>
