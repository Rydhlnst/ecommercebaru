<x-shop::layouts>
    <x-slot:title>{{ $post->title }} — {{ config('app.name') }}</x-slot:title>

    @php
        $postDate = $post->published_at ? $post->published_at->format('d F Y') : ($post->created_at ? $post->created_at->format('d F Y') : '');
        $postCategory = $post->category?->name ?? 'Artikel & Tips';
        $postImg = $post->thumbnail_url;
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

        {{-- Hero Header Banner --}}
        <div class="rounded-2xl md:rounded-3xl overflow-hidden mb-10 border border-[#E8F0E5] shadow-xs" style="background-color:#F5F9F3;">
            @if($postImg && $post->thumbnail)
                <div class="relative w-full aspect-[16/9] md:aspect-[21/9] overflow-hidden bg-[#E8F0E5]">
                    <img src="{{ $postImg }}" alt="{{ $post->title }}" class="w-full h-full object-cover" onerror="this.parentElement.style.display='none'; document.getElementById('blog-hero-fallback').style.display='block';">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent flex flex-col justify-end p-6 sm:p-8 md:p-12 text-white">
                        <span class="inline-block px-3.5 py-1 text-xs font-bold uppercase tracking-wider bg-white/20 backdrop-blur-md rounded-full w-fit mb-3 text-white border border-white/30">
                            {{ $postCategory }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight leading-tight max-w-4xl text-white">
                            {{ $post->title }}
                        </h1>
                        @if($postDate)
                            <div class="flex items-center gap-2 text-xs md:text-sm text-white/80 mt-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>{{ $postDate }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div id="blog-hero-fallback" class="p-8 sm:p-10 md:p-14" style="{{ ($postImg && $post->thumbnail) ? 'display:none;' : 'display:block;' }}">
                <span class="inline-block px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-white rounded-full w-fit mb-4 shadow-xs" style="background-color:#2D5A27;">
                    {{ $postCategory }}
                </span>
                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-[#171717] tracking-tight leading-tight max-w-4xl">
                    {{ $post->title }}
                </h1>
                @if($postDate)
                    <div class="flex items-center gap-2 text-xs md:text-sm text-zinc-500 mt-4 font-medium">
                        <svg class="w-4 h-4 text-[#2D5A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Diterbitkan pada {{ $postDate }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Two-Column Layout (Main Article + Right Sidebar) --}}
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
            {{-- Main Article Content --}}
            <article class="w-full lg:flex-1 bg-white p-6 sm:p-8 md:p-10 rounded-2xl border border-[#E8F0E5]">
                <div class="prose prose-zinc lg:prose-lg max-w-none text-[#2c2c2c] leading-relaxed">
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

            {{-- Right Sidebar --}}
            <aside class="w-full lg:w-[360px] lg:shrink-0 space-y-6 lg:sticky lg:top-24">
                {{-- Share Box (100% Adblock-Proof Interactive Buttons) --}}
                <div class="p-6 bg-white rounded-2xl border border-[#E8F0E5] shadow-xs">
                    <h3 class="text-base font-bold text-[#171717] mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#2D5A27]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        <span>Bagikan Artikel</span>
                    </h3>
                    <div class="grid grid-cols-2 gap-2.5">
                        {{-- WhatsApp --}}
                        <button type="button"
                                onclick="beresShare('whatsapp', '{{ $currentUrl }}', '{{ addslashes($post->title) }}')"
                                class="h-10 px-3 rounded-xl bg-[#25D366] hover:bg-[#1EBE5D] text-white flex items-center justify-center gap-2 text-xs font-semibold transition-all shadow-xs cursor-pointer"
                                title="Share ke WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <span>WhatsApp</span>
                        </button>

                        {{-- Facebook --}}
                        <button type="button"
                                onclick="beresShare('facebook', '{{ $currentUrl }}', '{{ addslashes($post->title) }}')"
                                class="h-10 px-3 rounded-xl bg-[#1877F2] hover:bg-[#0D65D9] text-white flex items-center justify-center gap-2 text-xs font-semibold transition-all shadow-xs cursor-pointer"
                                title="Share ke Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            <span>Facebook</span>
                        </button>

                        {{-- Twitter/X --}}
                        <button type="button"
                                onclick="beresShare('twitter', '{{ $currentUrl }}', '{{ addslashes($post->title) }}')"
                                class="h-10 px-3 rounded-xl bg-[#171717] hover:bg-black text-white flex items-center justify-center gap-2 text-xs font-semibold transition-all shadow-xs cursor-pointer"
                                title="Share ke X">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            <span>X / Twitter</span>
                        </button>

                        {{-- Copy Link --}}
                        <button type="button"
                                onclick="beresCopyLink('{{ $currentUrl }}', this)"
                                class="h-10 px-3 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 flex items-center justify-center gap-2 text-xs font-semibold transition-all shadow-xs cursor-pointer"
                                title="Salin Tautan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span class="btn-copy-label">Salin Link</span>
                        </button>
                    </div>
                </div>

                {{-- Recent / Spotlight Posts --}}
                @if($recentPosts->isNotEmpty())
                    <div class="p-6 sm:p-7 bg-white rounded-2xl border border-[#E8F0E5] shadow-xs">
                        <h3 class="text-base font-bold text-[#171717] mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#2D5A27]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Artikel Pilihan Lainnya</span>
                        </h3>
                        <div class="space-y-4">
                            @foreach($recentPosts as $rec)
                                @php
                                    $recImg = $rec->thumbnail_url;
                                    $recDate = $rec->published_at ? $rec->published_at->format('d M Y') : ($rec->created_at ? $rec->created_at->format('d M Y') : '');
                                @endphp
                                <a href="{{ route('shop.blog.show', $rec->slug) }}" class="group flex items-center gap-4 p-3 rounded-xl hover:bg-[#F5F9F3] transition-all duration-200 border border-transparent hover:border-[#E8F0E5]">
                                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-[#F0F5EC] shrink-0 relative flex items-center justify-center">
                                        @if($recImg && $rec->thumbnail)
                                            <img src="{{ $recImg }}" alt="{{ $rec->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-[#F5F9F3] to-[#E8F0E5] flex items-center justify-center text-[#2D5A27]/60">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0 pr-1">
                                        <h4 class="text-sm font-semibold text-[#171717] group-hover:text-[#2D5A27] transition-colors leading-snug line-clamp-2">
                                            {{ $rec->title }}
                                        </h4>
                                        @if($recDate)
                                            <div class="flex items-center gap-1.5 text-xs text-zinc-400 mt-2 font-medium">
                                                <svg class="w-3.5 h-3.5 text-[#2D5A27]/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span>{{ $recDate }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
    <script>
        function beresShare(platform, url, title) {
            let shareUrl = '';
            if (platform === 'whatsapp') {
                shareUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(title + '\n' + url);
            } else if (platform === 'facebook') {
                shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
            } else if (platform === 'twitter') {
                shareUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url);
            }
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=500,scrollbars=yes,resizable=yes');
            }
        }

        function beresCopyLink(url, btn) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(function() {
                    showCopiedFeedback(btn);
                }).catch(function() {
                    fallbackCopy(url, btn);
                });
            } else {
                fallbackCopy(url, btn);
            }
        }

        function showCopiedFeedback(btn) {
            const label = btn.querySelector('.btn-copy-label');
            if (label) {
                const orig = label.textContent;
                label.textContent = 'Tersalin! ✓';
                btn.classList.add('bg-green-100', 'text-green-800');
                setTimeout(() => {
                    label.textContent = orig;
                    btn.classList.remove('bg-green-100', 'text-green-800');
                }, 2000);
            }
        }

        function fallbackCopy(url, btn) {
            const ta = document.createElement('textarea');
            ta.value = url;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try {
                document.execCommand('copy');
                showCopiedFeedback(btn);
            } catch (err) {
                prompt('Salin tautan artikel ini:', url);
            }
            document.body.removeChild(ta);
        }
    </script>
</x-shop::layouts>
