@php
    // Editable dari admin: Configure → Storefront → Kontak & Lokasi
    $c        = fn(string $k, string $default = '') => (string) (core()->getConfigData("beres_storefront.contact.$k") ?: $default);
    $eyebrow  = $c('eyebrow',     'Kunjungi kami');
    $title    = $c('title',       'Lokasi & kontak.');
    $desc     = $c('description', 'Mampir ke gerai fisik kami untuk pengalaman belanja langsung, atau hubungi tim kami untuk pertanyaan seputar produk & pengiriman.');
    $address  = $c('address',     "Pasar Modern BSD, Blok C-12\nJl. Letnan Sutopo No. 12, Serpong\nTangerang Selatan 15321");
    $hours    = $c('hours',       "Senin – Sabtu · 07.00 – 21.00\nMinggu · 08.00 – 20.00");
    $phone    = $c('phone',       '+62 21 555 1234');
    $email    = $c('email',       'halo@ecommerce.beres.io');
    $mapQuery = $c('map_query',   'Pasar Modern BSD, Serpong, Tangerang');
    $country  = $c('country',     'Indonesia');
@endphp

{{-- Store location + map — compact horizontal layout --}}
<section id="lokasi" class="bg-white mb-6 md:mb-8">
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-5 md:py-7">
        <div class="grid gap-6 md:gap-8 lg:grid-cols-5 items-stretch">

            {{-- Map embed — takes 3/5 width --}}
            <div class="relative w-full overflow-hidden lg:col-span-3 h-full min-h-[320px] rounded-2xl bg-zinc-100">
                @php
                    $embedUrl = str_contains($mapQuery, 'pb=') || str_contains($mapQuery, 'embed')
                        ? $mapQuery
                        : 'https://maps.google.com/maps?q=' . urlencode($mapQuery) . '&t=&z=15&ie=UTF8&iwloc=&output=embed';
                @endphp
                <iframe
                    src="{{ $embedUrl }}"
                    class="w-full h-full border-0 rounded-2xl"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="{{ $title }}"
                    allowfullscreen
                ></iframe>
                {{-- Open in Maps button --}}
                <a
                    href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapQuery) }}"
                    target="_blank" rel="noopener"
                    class="absolute top-4 left-4 inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-[#171717] bg-white hover:bg-gray-50 transition-colors shadow-md rounded-lg"
                >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Open in Maps
                </a>
            </div>

            {{-- Contact info — takes 2/5 width --}}
            <div class="lg:col-span-2 px-2 flex h-full flex-col justify-center">
                <h2 class="text-2xl md:text-3xl text-[#171717] mb-6" style="font-weight:700;">{{ $country }}</h2>

                <div class="space-y-5">
                    {{-- Address --}}
                    <div>
                        <p class="text-sm font-semibold text-[#171717] mb-1">Address</p>
                        <p class="text-sm text-[#404040] leading-relaxed">{!! nl2br(e($address)) !!}</p>
                    </div>

                    {{-- Operating Hours --}}
                    @if (!empty($hours))
                        <div>
                            <p class="text-sm font-semibold text-[#171717] mb-1">Operating Hours</p>
                            <p class="text-sm text-[#404040] leading-relaxed">{!! nl2br(e($hours)) !!}</p>
                        </div>
                    @endif

                    {{-- Phone & Email in 2 columns --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold text-[#171717] mb-1">Phone</p>
                            <a href="tel:{{ preg_replace('/\D/', '', $phone) }}" class="text-sm text-[#404040] hover:text-[#2D5A27] transition-colors break-all">{{ $phone }}</a>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[#171717] mb-1">Email</p>
                            <a href="mailto:{{ $email }}" class="text-sm text-[#404040] hover:text-[#2D5A27] transition-colors break-all">{{ $email }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
