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
@endphp

<footer class="bg-ink text-cream mt-16">
    {{-- Newsletter --}}
    @if (core()->getConfigData('customer.settings.newsletter.subscription'))
        <div class="border-b border-cocoa">
            <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-16 md:py-20 grid gap-10 md:grid-cols-2 md:items-center">
                <div class="max-w-md">
                    <p class="text-[11px] tracking-[0.14em] uppercase text-sand mb-4">Newsletter</p>
                    <h2 class="text-4xl md:text-5xl leading-tight" style="font-weight: 500; letter-spacing: -0.02em;">
                        Resep, panen baru, dan promo — langsung ke inbox.
                    </h2>
                    <p class="mt-4 text-sm text-mist max-w-sm">
                        Berlangganan untuk info panen musiman, resep dari chef kami, dan promo eksklusif setiap dua minggu sekali.
                    </p>
                </div>

                <div>
                    <x-shop::form
                        :action="route('shop.subscription.store')"
                        class="w-full"
                        toolname="subscribe_to_newsletter"
                        tooldescription="{{ trans('shop::app.components.layouts.webmcp.subscribe-newsletter') }}"
                        toolautosubmit
                    >
                        <div class="flex items-end gap-4 border-b border-mist/40 pb-2">
                            <x-shop::form.control-group.control
                                type="email"
                                class="flex-1 bg-transparent border-0 text-cream placeholder:text-mist/60 py-3 focus:ring-0 focus:outline-none"
                                name="email"
                                rules="required|email"
                                label="Email"
                                :aria-label="trans('shop::app.components.layouts.footer.email')"
                                placeholder="your@email.placeholder"
                                toolparamdescription="{{ trans('shop::app.components.layouts.webmcp.subscribe-newsletter-email') }}"
                            />

                            <button
                                type="submit"
                                class="text-[13px] tracking-[0.14em] uppercase text-cream border-b border-cream pb-2 hover:text-sand hover:border-sand transition-colors"
                            >
                                @lang('shop::app.components.layouts.footer.subscribe') →
                            </button>
                        </div>

                        <x-shop::form.control-group.error control-name="email" />

                        <p class="mt-4 text-[11px] text-mist/60 max-w-md">
                            Placeholder — dengan berlangganan Anda menyetujui kebijakan privasi kami.
                        </p>
                    </x-shop::form>
                </div>
            </div>
        </div>
    @endif

    {{-- Link columns --}}
    <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-16">
        <div class="grid gap-10 md:grid-cols-4">
            <div class="md:col-span-1">
                <p class="text-3xl leading-none" style="font-weight: 600; letter-spacing: -0.02em;">ECommerce</p>
                <p class="mt-4 text-sm text-mist max-w-xs">
                    Pasar online untuk bahan segar dan pantry esensial. Langsung dari petani dan produsen lokal, diantar hari itu juga.
                </p>
            </div>

            <div class="md:col-span-3 grid gap-10 sm:grid-cols-2 lg:grid-cols-3" v-pre>
                @if ($customization?->options)
                    @foreach ($customization->options as $footerLinkSection)
                        @php
                            usort($footerLinkSection, fn($a,$b) => $a['sort_order'] - $b['sort_order']);
                            $head = $footerLinkSection[0]['title'] ?? 'Section';
                            $rest = array_slice($footerLinkSection, 1);
                        @endphp

                        <div>
                            <p class="text-[11px] tracking-[0.14em] uppercase text-sand mb-5">{{ $head }}</p>
                            <ul class="grid gap-3 text-sm text-mist">
                                @foreach ($rest as $link)
                                    <li>
                                        <a href="{{ $link['url'] }}" class="hover:text-cream transition-colors">
                                            {{ $link['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @else
                    @foreach ([
                        ['Belanja',    ['Buah & Sayur','Daging & Seafood','Roti & Bakery','Bundle Sarapan','Sale']],
                        ['Bantuan',    ['Cek Pesanan','Jadwal Pengiriman','Retur & Refund','Cara Menyimpan','Hubungi Kami']],
                        ['Perusahaan', ['Tentang Kami','Petani Kami','Journal & Resep','Karir','Store Locator']],
                    ] as [$title, $links])
                        <div>
                            <p class="text-[11px] tracking-[0.14em] uppercase text-sand mb-5">{{ $title }}</p>
                            <ul class="grid gap-3 text-sm text-mist">
                                @foreach ($links as $l)
                                    <li><a href="#" class="hover:text-cream transition-colors">{{ $l }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-cocoa">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-6 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] tracking-[0.14em] uppercase text-mist">
            <p>
                @if (core()->getConfigData('general.content.footer.copyright_content'))
                    {!! core()->getConfigData('general.content.footer.copyright_content') !!}
                @else
                    &copy; {{ date('Y') }} ECommerce. Hak cipta dilindungi.
                @endif
            </p>

            <div class="flex items-center gap-3">
                <span>Kami menerima —</span>
                <span class="px-2 py-1 border border-mist/40 rounded-sm">VISA</span>
                <span class="px-2 py-1 border border-mist/40 rounded-sm">MC</span>
                <span class="px-2 py-1 border border-mist/40 rounded-sm">QRIS</span>
                <span class="px-2 py-1 border border-mist/40 rounded-sm">GOPAY</span>
            </div>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
