<x-shop::layouts>
    <x-slot:title>{{ $category->name }}</x-slot:title>

    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-zinc-500 mb-6 flex items-center gap-2">
            <a href="/" class="hover:text-[#2D5A27] transition-colors">Beranda</a>
            <span class="text-zinc-400">/</span>
            <span class="text-[#171717] font-medium">{{ $category->name }}</span>
        </nav>

        {{-- Category Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#171717] mb-2">{{ $category->name }}</h1>
            <p class="text-zinc-500">{{ $category->products_count }} produk</p>
        </div>

        {{-- Products Grid --}}
        @if($products->count())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                @foreach($products as $index => $product)
                    @include('shop::components.layouts._product-card', ['product' => $product, 'index' => $index, 'bg' => ['#E8F0E5','#DCE8D6','#F0F5EC','#EAF1E4'][$index % 4]])
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-zinc-500 text-lg">Belum ada produk di kategori ini.</p>
            </div>
        @endif
    </div>
</x-shop::layouts>
