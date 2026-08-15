<x-shop::layouts>
    <x-slot:title>{{ $post->title }} — {{ config('app.name') }}</x-slot:title>

    @php
        $postDate = $post->published_at ? $post->published_at->format('d F Y') : ($post->created_at ? $post->created_at->format('d F Y') : '');
        $postCategory = $post->category?->name ?? 'Artikel & Wawasan';

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
        $currentUrl = url()->current();
        $shareTitle = urlencode($post->title);
    @endphp

    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-8 md:py-12">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-zinc-500 mb-6 flex items-center gap-2 flex-wrap">
            <a href="/" class="hover:text-[#2D5A27] transition-colors">Beranda</a>
            <span class="text-zinc-400">/</span>
            <a href="{{ route('shop.blog.index') }}" class="hover:text-[#2D5A27] transition-colors">Artikel & Tips</a>
            <span class="text-zinc-400">/</span>
            <span class="text-[#171717] font-medium truncate max-w-xs md:max-w-md">{{ $post->title }}</span>
        </nav>

        {{-- Featured Header Banner --}}
        <div class="relative w-full rounded-2xl md:rounded-3xl overflow-hidden bg-gradient-to-br from-[#F5F9F3] via-[#E8F0E5] to-[#D5E5CE] mb-10 shadow-sm">
            @if($postImg)
                <div class="relative aspect-[16/9] md:aspect-[21/9] w-full overflow-hidden">
                    <img src="{{ $postImg }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent flex flex-col justify-end p-6 md:p-12 text-white">
                        <span class="inline-block px-3.5 py-1 text-xs font-semibold uppercase tracking-wider bg-white/20 backdrop-blur-md rounded-full w-fit mb-3 text-white">
                            {{ $postCategory }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight leading-tight max-w-4xl text-white">
                            {{ $post->title }}
                        </h1>
                        @if($postDate)
                            <div class="flex items-center gap-2 text-xs md:text-sm text-white/80 mt-3">
                                <span>{{ $postDate }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="p-8 md:p-14">
                    <span class="inline-block px-3.5 py-1 text-xs font-semibold uppercase tracking-wider bg-[#2D5A27] text-white rounded-full w-fit mb-3">
                        {{ $postCategory }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-[#171717] tracking-tight leading-tight max-w-4xl">
                        {{ $post->title }}
                    </h1>
                    @if($postDate)
                        <div class="flex items-center gap-2 text-xs md:text-sm text-zinc-500 mt-4">
                            <span>{{ $postDate }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Content & Sidebar --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
            {{-- Main Content (Left 8 cols) --}}
            <article class="lg:col-span-8">
                <div class="prose prose-zinc lg:prose-lg max-w-none leading-relaxed text-[#2c2c2c]">
                    {!! $post->content !!}
                </div>

                {{-- Post Tags --}}
                @if(!empty($post->tags))
                    @php
                        $tagList = is_array($post->tags) ? $post->tags : array_filter(array_map('trim', explode(',', $post->tags)));
                    @endphp
                    @if(!empty($tagList))
                        <div class="mt-10 pt-6 border-t border-zinc-200 flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-bold uppercase tracking-wider text-zinc-400 mr-2">Tag:</span>
                            @foreach($tagList as $tag)
                                <span class="px-3 py-1 bg-zinc-100 text-zinc-700 text-xs font-medium rounded-lg">#{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                @endif
            </article>

            {{-- Sidebar (Right 4 cols) --}}
            <aside class="lg:col-span-4 space-y-8 sticky top-24">
                {{-- Share Box --}}
                <div class="p-6 bg-white rounded-2xl border border-[#E8F0E5] shadow-xs">
                    <h3 class="text-base font-bold text-[#171717] mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#2D5A27]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        <span>Bagikan Artikel</span>
                    </h3>
                    <div class="flex items-center gap-3">
                        {{-- WhatsApp --}}
                        <a href="https://wa.me/?text={{ $shareTitle }}%20{{ urlencode($currentUrl) }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Share ke WhatsApp">
                            <i class="fab fa-whatsapp text-lg"></i>
                        </a>
                        {{-- Facebook --}}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($currentUrl) }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Share ke Facebook">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </a>
                        {{-- Twitter/X --}}
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($currentUrl) }}&text={{ $shareTitle }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-zinc-800 hover:bg-black text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Share ke X">
                            <i class="fab fa-x-twitter text-lg"></i>
                        </a>
                        {{-- Copy Link --}}
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $currentUrl }}'); alert('Link artikel berhasil disalin!');" class="w-10 h-10 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Salin Tautan">
                            <i class="fas fa-link text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Recent / Spotlight Posts --}}
                @if($recentPosts->isNotEmpty())
                    <div class="p-6 bg-white rounded-2xl border border-[#E8F0E5] shadow-xs">
                        <h3 class="text-base font-bold text-[#171717] mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#2D5A27]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Artikel Pilihan Lainnya</span>
                        </h3>
                        <div class="space-y-4">
                            @foreach($recentPosts as $rec)
                                @php
                                    $recImg = null;
                                    if ($rec->thumbnail) {
                                        if (file_exists(public_path('storage/' . $rec->thumbnail))) {
                                            $recImg = asset('storage/' . $rec->thumbnail);
                                        } elseif (file_exists(public_path($rec->thumbnail))) {
                                            $recImg = asset($rec->thumbnail);
                                        } else {
                                            $recImg = asset('storage/' . $rec->thumbnail);
                                        }
                                    }
                                    $recDate = $rec->published_at ? $rec->published_at->format('d M Y') : ($rec->created_at ? $rec->created_at->format('d M Y') : '');
                                @endphp
                                <a href="{{ route('shop.blog.show', $rec->slug) }}" class="group flex items-start gap-3.5 p-2 rounded-xl hover:bg-[#F5F9F3] transition-colors">
                                    <div class="w-20 h-16 rounded-lg overflow-hidden bg-zinc-100 shrink-0 relative aspect-[4/3]">
                                        @if($recImg)
                                            <img src="{{ $recImg }}" alt="{{ $rec->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full bg-[#E8F0E5] flex items-center justify-center text-[#2D5A27]">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs md:text-sm font-semibold text-[#171717] group-hover:text-[#2D5A27] transition-colors leading-snug line-clamp-2">
                                            {{ $rec->title }}
                                        </h4>
                                        @if($recDate)
                                            <p class="text-[11px] text-zinc-400 mt-1">{{ $recDate }}</p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-shop::layouts>
