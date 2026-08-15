{!! view_render_event('bagisto.shop.layout.footer.before') !!}

@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);

    $footerOptions = $customization?->translate(app()->getLocale())['options'] ?? [];

    // Social media icon map
    $socialIcons = [
        'facebook'  => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'instagram' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
        'youtube'   => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        'tiktok'    => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
        'twitter'   => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>',
        'whatsapp'  => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
    ];

    function detectSocialIcon($url) {
        global $socialIcons;
        $lower = strtolower($url);
        foreach ($socialIcons as $key => $svg) {
            if (str_contains($lower, $key)) return $svg;
        }
        return null;
    }
@endphp

<footer class="text-cream" style="background-color:#2D5A27;">
    <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-16">
        <div class="grid gap-12 lg:grid-cols-5">

            {{-- Newsletter --}}
            <div class="lg:col-span-1">
                <img src="/images/ankesh-mart-logo.png" alt="{{ config('app.name', 'Ankesh Mart') }}" width="131" height="44" class="block mb-4 brightness-0 invert">
                <p class="text-sm font-semibold text-cream mb-3">Newsletter</p>
                <p class="text-sm text-mist/80 mb-6 leading-relaxed">
                    {{ \App\Models\SiteSetting::getValue('footer_newsletter_text') ?: 'Jadilah yang pertama mendengar tentang produk baru, acara eksklusif, dan penawaran online.' }}
                </p>
                <x-shop::form :action="route('shop.subscription.store')" class="w-full">
                    <div class="flex items-stretch w-full h-12 rounded-lg overflow-hidden border border-cream/30 focus-within:border-cream bg-white/5 transition-colors">
                        <input
                            type="email"
                            name="email"
                            class="flex-1 min-w-0 bg-transparent text-cream placeholder:text-mist/50 px-4 text-sm focus:outline-none focus:ring-0 border-0 m-0 rounded-none h-full"
                            placeholder="E-mail"
                            required
                            aria-label="E-mail"
                        />
                        <button
                            type="submit"
                            class="h-full px-7 bg-cream text-[#2D5A27] text-sm font-bold hover:bg-[#E8F0E5] transition-colors shrink-0 flex items-center justify-center border-0 m-0 rounded-none cursor-pointer"
                        >
                            Join
                        </button>
                    </div>
                </x-shop::form>
            </div>

            {{-- Column 1 — About Us / Custom Links --}}
            @php
                $col1Title = \App\Models\SiteSetting::getValue('footer_col1_title') ?: 'About Us';
                $col1Raw = \App\Models\SiteSetting::getValue('footer_col1_links');
                $col1Items = [];
                if ($col1Raw !== null) {
                    if (trim($col1Raw) !== '') {
                        foreach (preg_split("/\r?\n/", $col1Raw) as $line) {
                            $line = trim($line);
                            if ($line === '') continue;
                            [$title, $url] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
                            if ($title !== '') {
                                $col1Items[] = ['title' => $title, 'url' => $url ?: '#'];
                            }
                        }
                    }
                } else {
                    $col1Items = [
                        ['title' => 'About Us', 'url' => '/about-us'],
                        ['title' => 'Contact Us', 'url' => '/contact-us'],
                        ['title' => 'Customer Service', 'url' => '/customer-service'],
                        ['title' => 'What\'s New', 'url' => '/whats-new'],
                        ['title' => 'Terms & Conditions', 'url' => '/page/terms-conditions'],
                    ];
                }
            @endphp
            @if(!empty($col1Items))
                <div>
                    <p class="text-sm font-semibold text-cream mb-4">{{ $col1Title }}</p>
                    <ul class="grid gap-3 text-sm text-mist/80">
                        @foreach ($col1Items as $item)
                            <li><a href="{{ $item['url'] }}" class="hover:text-cream transition-colors">{{ $item['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Column 2 — Privacy Policy / Custom Links --}}
            @php
                $col2Title = \App\Models\SiteSetting::getValue('footer_col2_title') ?: 'Privacy Policy';
                $col2Raw = \App\Models\SiteSetting::getValue('footer_col2_links');
                $col2Items = [];
                if ($col2Raw !== null) {
                    if (trim($col2Raw) !== '') {
                        foreach (preg_split("/\r?\n/", $col2Raw) as $line) {
                            $line = trim($line);
                            if ($line === '') continue;
                            [$title, $url] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
                            if ($title !== '') {
                                $col2Items[] = ['title' => $title, 'url' => $url ?: '#'];
                            }
                        }
                    }
                } else {
                    $col2Items = [
                        ['title' => 'Payment Policy', 'url' => '/page/payment-policy'],
                        ['title' => 'Shipping Policy', 'url' => '/page/shipping-policy'],
                        ['title' => 'Refund Policy', 'url' => '/page/refund-policy'],
                        ['title' => 'Return Policy', 'url' => '/page/return-policy'],
                        ['title' => 'FAQ', 'url' => '/faq'],
                    ];
                }
            @endphp
            @if(!empty($col2Items))
                <div>
                    <p class="text-sm font-semibold text-cream mb-4">{{ $col2Title }}</p>
                    <ul class="grid gap-3 text-sm text-mist/80">
                        @foreach ($col2Items as $item)
                            <li><a href="{{ $item['url'] }}" class="hover:text-cream transition-colors">{{ $item['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Column 3 — Contact Details --}}
            @if (! empty($footerOptions['column_3']))
                @php
                    usort($footerOptions['column_3'], fn($a, $b) => ($a['sort_order'] ?? 0) - ($b['sort_order'] ?? 0));
                    $col3Header = $footerOptions['column_3'][0]['title'] ?? 'Detail Kontak';
                    $col3Links = array_slice($footerOptions['column_3'], 1);
                @endphp
                <div>
                    <p class="text-sm font-semibold text-cream mb-4">{{ $col3Header }}</p>
                    <div class="text-sm text-mist/80 space-y-4">
                        @foreach ($col3Links as $link)
                            @php $url = $link['url'] ?? ''; @endphp
                            @if (str_starts_with($url, 'mailto:'))
                                <div class="flex items-start gap-3">
                                    <span class="icon-email text-lg mt-0.5 text-mist/80"></span>
                                    <a href="{{ $url }}" class="hover:text-cream transition-colors">{{ $link['title'] }}</a>
                                </div>
                            @elseif (str_starts_with($url, 'tel:'))
                                <div class="flex items-start gap-3">
                                    <span class="icon-support text-lg mt-0.5 text-mist/80"></span>
                                    <a href="{{ $url }}" class="hover:text-cream transition-colors">{{ $link['title'] }}</a>
                                </div>
                            @else
                                <div class="flex items-start gap-3">
                                    <span class="icon-location text-lg mt-0.5 text-mist/80"></span>
                                    <p>{!! nl2br(e($link['title'])) !!}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                @php
                    $storeAddress = \App\Models\SiteSetting::getValue('store_address');
                    $storePhone = \App\Models\SiteSetting::getValue('store_phone') ?: \App\Models\SiteSetting::getValue('store_whatsapp');
                    $storeEmail = \App\Models\SiteSetting::getValue('store_email');
                @endphp
                @if($storeAddress || $storePhone || $storeEmail)
                    <div>
                        <p class="text-sm font-semibold text-cream mb-4">DETAIL KONTAK</p>
                        <div class="text-sm text-mist/80 space-y-4">
                            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Ankesh Mart') }}</p>
                            @if($storeAddress)
                                <div class="flex items-start gap-3">
                                    <span class="icon-location text-lg mt-0.5 text-mist/80"></span>
                                    <p>{!! nl2br(e($storeAddress)) !!}</p>
                                </div>
                            @endif
                            @if($storePhone)
                                <div class="flex items-start gap-3">
                                    <span class="icon-support text-lg mt-0.5 text-mist/80"></span>
                                    <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $storePhone) }}" class="hover:text-cream transition-colors">{{ $storePhone }}</a></p>
                                </div>
                            @endif
                            @if($storeEmail)
                                <div class="flex items-start gap-3">
                                    <span class="icon-email text-lg mt-0.5 text-mist/80"></span>
                                    <p><a href="mailto:{{ $storeEmail }}" class="hover:text-cream transition-colors">{{ $storeEmail }}</a></p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- Column 4 — Social Media --}}
            @if (! empty($footerOptions['column_4']))
                @php
                    usort($footerOptions['column_4'], fn($a, $b) => ($a['sort_order'] ?? 0) - ($b['sort_order'] ?? 0));
                    $col4Header = $footerOptions['column_4'][0]['title'] ?? 'Ikuti kami';
                    $col4Links = array_slice($footerOptions['column_4'], 1);
                @endphp
                <div>
                    <p class="text-sm font-semibold text-cream mb-4">{{ $col4Header }}</p>
                    <div class="flex items-center gap-4">
                        @foreach ($col4Links as $link)
                            @php $icon = detectSocialIcon($link['url'] ?? ''); @endphp
                            @if ($icon)
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="text-mist/80 hover:text-cream transition-colors" aria-label="{{ $link['title'] }}">
                                    {!! $icon !!}
                                </a>
                            @else
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="text-sm text-mist/80 hover:text-cream transition-colors">
                                    {{ $link['title'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                @php
                    $storeIg = \App\Models\SiteSetting::getValue('store_instagram');
                    $storeFb = \App\Models\SiteSetting::getValue('store_facebook');
                    $storeYt = \App\Models\SiteSetting::getValue('store_youtube');
                    $storeTt = \App\Models\SiteSetting::getValue('store_tiktok');
                    $storeWa = \App\Models\SiteSetting::getValue('store_whatsapp');
                    $hasSocial = $storeIg || $storeFb || $storeYt || $storeTt || $storeWa;
                @endphp
                @if($hasSocial)
                    <div>
                        <p class="text-sm font-semibold text-cream mb-4">Ikuti kami</p>
                        <div class="flex items-center gap-4">
                            @if($storeWa)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storeWa) }}" target="_blank" rel="noopener" class="text-mist/80 hover:text-cream transition-colors" aria-label="WhatsApp">
                                    {!! $socialIcons['whatsapp'] !!}
                                </a>
                            @endif
                            @if($storeIg)
                                <a href="{{ $storeIg }}" target="_blank" rel="noopener" class="text-mist/80 hover:text-cream transition-colors" aria-label="Instagram">
                                    {!! $socialIcons['instagram'] !!}
                                </a>
                            @endif
                            @if($storeFb)
                                <a href="{{ $storeFb }}" target="_blank" rel="noopener" class="text-mist/80 hover:text-cream transition-colors" aria-label="Facebook">
                                    {!! $socialIcons['facebook'] !!}
                                </a>
                            @endif
                            @if($storeTt)
                                <a href="{{ $storeTt }}" target="_blank" rel="noopener" class="text-mist/80 hover:text-cream transition-colors" aria-label="TikTok">
                                    {!! $socialIcons['tiktok'] !!}
                                </a>
                            @endif
                            @if($storeYt)
                                <a href="{{ $storeYt }}" target="_blank" rel="noopener" class="text-mist/80 hover:text-cream transition-colors" aria-label="YouTube">
                                    {!! $socialIcons['youtube'] !!}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-white/10">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-5 text-center text-sm text-mist/60">
            &copy; {{ date('Y') }}, {{ config('app.name') }}
        </div>
    </div>
</footer>

@push('scripts')
<style>
    html, body {
        background-color: #2D5A27 !important;
    }
</style>
@endpush

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
