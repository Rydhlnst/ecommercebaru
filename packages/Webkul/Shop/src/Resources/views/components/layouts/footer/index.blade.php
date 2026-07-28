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

<footer class="mt-24 bg-ink text-cream">
    <!-- Newsletter band -->
    @if (core()->getConfigData('customer.settings.newsletter.subscription'))
        <div class="border-b border-cocoa">
            <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-16 md:py-20 grid gap-10 md:grid-cols-2 md:items-center">
                <div class="max-w-md">
                    <p class="text-[11px] tracking-widelg uppercase text-sand mb-4">Placeholder — Newsletter</p>
                    <h2 class="font-serif text-4xl md:text-5xl leading-tight">
                        Lorem ipsum dolor sit amet consectetur.
                    </h2>
                    <p class="mt-4 text-sm text-mist max-w-sm">
                        Placeholder subhead. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.
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
                                class="text-[13px] tracking-widelg uppercase text-cream border-b border-cream pb-2 hover:text-sand hover:border-sand transition-colors"
                            >
                                Subscribe →
                            </button>
                        </div>

                        <x-shop::form.control-group.error control-name="email" />

                        <p class="mt-4 text-[11px] text-mist/60 max-w-md">
                            Placeholder — by subscribing you agree to our privacy policy.
                        </p>
                    </x-shop::form>
                </div>
            </div>
        </div>
    @endif

    <!-- Link columns -->
    <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-16">
        <div class="grid gap-10 md:grid-cols-4">
            <!-- Brand -->
            <div class="md:col-span-1">
                <p class="font-serif text-3xl leading-none">Placeholder</p>
                <p class="mt-4 text-sm text-mist max-w-xs">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Placeholder brand tagline.
                </p>
            </div>

            <!-- CMS columns (desktop) -->
            <div class="md:col-span-3 grid gap-10 sm:grid-cols-2 lg:grid-cols-3 max-1060:hidden" v-pre>
                @if ($customization?->options)
                    @foreach ($customization->options as $footerLinkSection)
                        @php
                            usort($footerLinkSection, fn($a,$b) => $a['sort_order'] - $b['sort_order']);
                            $head = $footerLinkSection[0]['title'] ?? 'Section';
                            $rest = array_slice($footerLinkSection, 1);
                        @endphp

                        <div>
                            <p class="text-[11px] tracking-widelg uppercase text-sand mb-5">{{ $head }}</p>
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
                @endif

                @if (! $customization?->options)
                    @for ($i = 0; $i < 3; $i++)
                        <div>
                            <p class="text-[11px] tracking-widelg uppercase text-sand mb-5">Placeholder Section</p>
                            <ul class="grid gap-3 text-sm text-mist">
                                <li><a href="#" class="hover:text-cream">Placeholder link one</a></li>
                                <li><a href="#" class="hover:text-cream">Placeholder link two</a></li>
                                <li><a href="#" class="hover:text-cream">Placeholder link three</a></li>
                                <li><a href="#" class="hover:text-cream">Placeholder link four</a></li>
                            </ul>
                        </div>
                    @endfor
                @endif
            </div>

            <!-- Mobile accordion -->
            <div class="md:col-span-3 hidden max-1060:block">
                <x-shop::accordion :is-active="false" class="!w-full !border-0 border-t border-cocoa">
                    <x-slot:header class="!bg-transparent !text-cream text-[13px] tracking-widelg uppercase py-4">
                        @lang('shop::app.components.layouts.footer.footer-content')
                    </x-slot>

                    <x-slot:content class="!bg-transparent !p-4 grid gap-6 sm:grid-cols-2">
                        @if ($customization?->options)
                            @foreach ($customization->options as $footerLinkSection)
                                <ul class="grid gap-2 text-sm text-mist" v-pre>
                                    @foreach ($footerLinkSection as $link)
                                        <li>
                                            <a href="{{ $link['url'] }}" class="hover:text-cream">
                                                {{ $link['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        @endif
                    </x-slot>
                </x-shop::accordion>
            </div>
        </div>
    </div>

    <!-- Bottom bar -->
    <div class="border-t border-cocoa">
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-6 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] tracking-widelg uppercase text-mist">
            {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

            <p>
                @if (core()->getConfigData('general.content.footer.copyright_content'))
                    {!! core()->getConfigData('general.content.footer.copyright_content') !!}
                @else
                    @lang('shop::app.components.layouts.footer.footer-text', ['current_year'=> date('Y') ])
                @endif
            </p>

            {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}

            <div class="flex items-center gap-3">
                <span>We accept —</span>
                <span class="px-2 py-1 border border-mist/40 rounded-sm">VISA</span>
                <span class="px-2 py-1 border border-mist/40 rounded-sm">MC</span>
                <span class="px-2 py-1 border border-mist/40 rounded-sm">AMEX</span>
                <span class="px-2 py-1 border border-mist/40 rounded-sm">PAY</span>
            </div>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
