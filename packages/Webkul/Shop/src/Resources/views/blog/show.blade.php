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
        <nav class="text-sm text-zinc-500 mb-6 flex items-center gap-2 flex-wrap py-2 px-4 bg-zinc-100/70 rounded-xl shadow-2xs">
            <a href="/" class="hover:text-[#2D5A27] transition-colors">Home</a>
            <span class="text-zinc-400">/</span>
            <a href="{{ route('shop.blog.index') }}" class="hover:text-[#2D5A27] transition-colors">News</a>
            <span class="text-zinc-400">/</span>
            <span class="text-zinc-800 font-medium truncate max-w-xs md:max-w-md">{{ $post->title }}</span>
        </nav>

        {{-- Two-Column Layout (Main Article + Right Sidebar) --}}
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
            {{-- Main Article Content (Clean Card with Soft Shadow, No Heavy Border) --}}
            <article class="w-full lg:flex-1 bg-white p-6 sm:p-8 md:p-10 rounded-2xl shadow-xs">
                {{-- Header Inside Article --}}
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-3 flex-wrap">
                        <span class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider text-[#2D5A27] bg-[#F5F9F3] rounded-full">
                            {{ $postCategory }}
                        </span>
                        @if($postDate)
                            <div class="flex items-center gap-1.5 text-xs text-zinc-500 font-medium">
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>{{ $postDate }}</span>
                            </div>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#171717] tracking-tight leading-tight">
                        {{ $post->title }}
                    </h1>
                </div>

                {{-- Featured Media (Standard 16:9 Video Aspect Ratio, Compact & Centered) --}}
                @if($postImg && $post->thumbnail)
                    <div class="rounded-xl md:rounded-2xl overflow-hidden mb-8 shadow-xs bg-zinc-100 aspect-video max-h-[420px] w-full flex items-center justify-center">
                        <img src="{{ $postImg }}" alt="{{ $post->title }}" class="w-full h-full object-cover object-center" loading="eager">
                    </div>
                @endif

                {{-- Article Body Content --}}
                <div class="prose prose-zinc lg:prose-lg max-w-none text-[#2c2c2c] leading-relaxed">
                    {!! $post->content !!}
                </div>

                {{-- Post Tags --}}
                @if(!empty($post->tags))
                    @php
                        $tagList = is_array($post->tags) ? $post->tags : array_filter(array_map('trim', explode(',', $post->tags)));
                    @endphp
                    @if(!empty($tagList))
                        <div class="mt-10 pt-6 border-t border-zinc-100 flex items-center gap-2 flex-wrap">
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
                {{-- Share This Box (Clean Modern Rounded Buttons, No Border, Soft Shadow) --}}
                <div class="p-6 bg-white rounded-2xl shadow-xs">
                    <h3 class="text-base font-bold text-[#171717] mb-4">
                        Share this
                    </h3>
                    <div class="flex items-center gap-3 flex-wrap">
                        {{-- Facebook --}}
                        <button type="button"
                                onclick="beresShare('facebook', '{{ $currentUrl }}', '{{ addslashes($post->title) }}')"
                                class="w-11 h-11 rounded-xl bg-[#1877F2] hover:bg-[#0D65D9] text-white flex items-center justify-center text-base transition-all shadow-xs cursor-pointer"
                                title="Share ke Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </button>

                        {{-- Instagram / WhatsApp --}}
                        <button type="button"
                                onclick="beresShare('whatsapp', '{{ $currentUrl }}', '{{ addslashes($post->title) }}')"
                                class="w-11 h-11 rounded-xl bg-gradient-to-tr from-[#f09433] via-[#dc2743] to-[#bc1888] hover:opacity-90 text-white flex items-center justify-center text-base transition-all shadow-xs cursor-pointer"
                                title="Bagikan ke Instagram / WhatsApp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </button>

                        {{-- YouTube / Video --}}
                        <button type="button"
                                onclick="beresShare('whatsapp', '{{ $currentUrl }}', '{{ addslashes($post->title) }}')"
                                class="w-11 h-11 rounded-xl bg-[#FF0000] hover:bg-[#D90000] text-white flex items-center justify-center text-base transition-all shadow-xs cursor-pointer"
                                title="Share / Tonton di YouTube">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </button>

                        {{-- TikTok / X / Copy --}}
                        <button type="button"
                                onclick="beresCopyLink('{{ $currentUrl }}', this)"
                                class="w-11 h-11 rounded-xl bg-black hover:bg-zinc-800 text-white flex items-center justify-center text-base transition-all shadow-xs cursor-pointer"
                                title="Salin Tautan / Bagikan ke TikTok">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 2.89 3.5 2.77 1.81-.1 3.25-1.52 3.4-3.32.14-1.66.07-3.33.07-5V0z"/></svg>
                        </button>
                    </div>
                </div>

                {{-- In the spotlight (Spotlight Articles) --}}
                @if($recentPosts->isNotEmpty())
                    <div class="p-6 bg-white rounded-2xl shadow-xs">
                        <h3 class="text-base font-bold text-[#171717] mb-5">
                            In the spotlight
                        </h3>
                        <div class="space-y-3.5">
                            @foreach($recentPosts as $rec)
                                @php
                                    $recImg = $rec->thumbnail_url;
                                    $recDate = $rec->published_at ? $rec->published_at->format('d M Y') : ($rec->created_at ? $rec->created_at->format('d M Y') : '');
                                @endphp
                                <a href="{{ route('shop.blog.show', $rec->slug) }}" class="group flex items-center gap-3.5 p-2 rounded-xl hover:bg-zinc-50 transition-all duration-200">
                                    <div class="w-20 h-16 rounded-xl overflow-hidden bg-zinc-100 shrink-0 relative flex items-center justify-center">
                                        @if($recImg && $rec->thumbnail)
                                            <img src="{{ $recImg }}" alt="{{ $rec->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full bg-zinc-100 flex items-center justify-center text-zinc-400">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-[#171717] group-hover:text-[#2D5A27] transition-colors leading-snug line-clamp-2">
                                            {{ $rec->title }}
                                        </h4>
                                        @if($recDate)
                                            <p class="text-xs text-zinc-400 mt-1 font-medium">{{ $recDate }}</p>
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
            btn.classList.add('ring-2', 'ring-green-500');
            setTimeout(() => {
                btn.classList.remove('ring-2', 'ring-green-500');
            }, 1500);
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
