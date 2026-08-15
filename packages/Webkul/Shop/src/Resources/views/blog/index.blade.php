<x-shop::layouts>
    <x-slot:title>Artikel & Tips — {{ config('app.name') }}</x-slot:title>

    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-zinc-500 mb-6 flex items-center gap-2">
            <a href="/" class="hover:text-[#2D5A27] transition-colors">Beranda</a>
            <span class="text-zinc-400">/</span>
            <span class="text-[#171717] font-medium">Artikel & Tips</span>
        </nav>

        {{-- Page Header --}}
        <div class="mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-[#E8F0E5] text-[#2D5A27] mb-3">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span>Artikel & Wawasan</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-[#171717] tracking-tight">
                Artikel & Tips Terbaru
            </h1>
            <p class="mt-3 text-base text-zinc-600 max-w-2xl leading-relaxed">
                Panduan praktis, resep masakan, dan wawasan kesehatan untuk mendukung gaya hidup sehat keluarga Anda.
            </p>

            {{-- Categories filter --}}
            @if($categories->isNotEmpty())
                <div class="flex items-center gap-2 mt-6 overflow-x-auto pb-2" style="scrollbar-width:none;">
                    <a href="{{ route('shop.blog.index') }}"
                       class="px-4 py-2 text-xs md:text-sm font-semibold rounded-full border transition-all whitespace-nowrap {{ !request('category') ? 'bg-[#2D5A27] text-white border-[#2D5A27]' : 'bg-white text-zinc-700 border-zinc-200 hover:border-[#2D5A27]' }}">
                        Semua Kategori
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('shop.blog.index', ['category' => $cat->slug]) }}"
                           class="px-4 py-2 text-xs md:text-sm font-semibold rounded-full border transition-all whitespace-nowrap {{ request('category') === $cat->slug ? 'bg-[#2D5A27] text-white border-[#2D5A27]' : 'bg-white text-zinc-700 border-zinc-200 hover:border-[#2D5A27]' }}">
                            {{ $cat->name }} ({{ $cat->posts_count }})
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Blog Grid --}}
        @if($posts->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($posts as $post)
                    @php
                        $postDate = $post->published_at ? $post->published_at->format('d F Y') : ($post->created_at ? $post->created_at->format('d F Y') : '');
                        $postCategory = $post->category?->name ?? 'Ankesh Mart';
                        $postExcerpt = \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 130);
                        $postUrl = route('shop.blog.show', $post->slug);

                        $postImg = null;
                        if ($post->thumbnail) {
                            if (file_exists(public_path('storage/' . $post->thumbnail))) {
                                $postImg = asset('storage/' . $post->thumbnail);
                            } elseif (file_exists(public_path($post->thumbnail))) {
                                $postImg = asset($post->thumbnail);
                            } else {
                                $postImg = asset('storage/' . $post->thumbnail);
                            }
                        }
                    @endphp
                    <article class="group flex flex-col bg-white rounded-2xl border border-[#E8F0E5] overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 h-full">
                        {{-- Unified 16:9 Thumbnail Banner --}}
                        <a href="{{ $postUrl }}" class="block relative w-full overflow-hidden bg-gradient-to-br from-[#F5F9F3] via-[#E8F0E5] to-[#D5E5CE] shrink-0" style="aspect-ratio:16/9; height:200px; max-height:220px;">
                            @if($postImg)
                                <img src="{{ $postImg }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" style="width:100%; height:100%; object-fit:cover;" loading="lazy">
                            @else
                                <div class="w-full h-full flex flex-col justify-between p-5 bg-gradient-to-br from-[#F5F9F3] via-[#E8F0E5] to-[#D5E5CE] group-hover:scale-105 transition-transform duration-500" style="width:100%; height:100%;">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-[#2D5A27] bg-white/90 backdrop-blur-md rounded-full shadow-xs w-fit">
                                        {{ $postCategory }}
                                    </span>
                                    <div class="w-10 h-10 rounded-xl bg-white/80 backdrop-blur-md flex items-center justify-center text-[#2D5A27] shadow-xs">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                </div>
                            @endif

                            @if($postImg)
                                <span class="absolute top-3 left-3 px-3 py-1 text-xs font-semibold text-[#2D5A27] bg-white/90 backdrop-blur-md rounded-full shadow-xs">
                                    {{ $postCategory }}
                                </span>
                            @endif
                        </a>

                        {{-- Body --}}
                        <div class="p-6 flex flex-col flex-1">
                            @if($postDate)
                                <div class="flex items-center gap-2 text-xs font-medium text-zinc-500 mb-3">
                                    <svg class="w-3.5 h-3.5 text-[#2D5A27]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span>{{ $postDate }}</span>
                                </div>
                            @endif

                            <a href="{{ $postUrl }}" class="block">
                                <h2 class="text-lg md:text-xl font-bold text-[#171717] group-hover:text-[#2D5A27] transition-colors leading-snug line-clamp-2">
                                    {{ $post->title }}
                                </h2>
                            </a>

                            <p class="mt-2.5 text-sm text-zinc-600 leading-relaxed line-clamp-3">
                                {{ $postExcerpt }}
                            </p>

                            <div class="mt-auto pt-5 flex items-center text-xs font-bold text-[#2D5A27] group-hover:text-[#1E3A1E] transition-colors">
                                <span>Baca Selengkapnya</span>
                                <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-[#F5F9F3] rounded-2xl border border-[#E8F0E5] p-8">
                <div class="w-14 h-14 bg-[#E8F0E5] text-[#2D5A27] rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-[#171717] mb-1">Belum Ada Artikel</h3>
                <p class="text-sm text-zinc-500">Artikel terbaru seputar tips dan gaya hidup sehat akan segera kami publikasikan.</p>
            </div>
        @endif
    </div>
</x-shop::layouts>
