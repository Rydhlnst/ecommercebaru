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
@endphp

{{-- Store location + map — muncul di semua page di atas footer --}}
<section id="lokasi" style="background-color:#F5F9F3;">
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-10 md:py-14">
        <div class="grid gap-8 md:gap-12 lg:grid-cols-2 items-start">

            {{-- Info kolom --}}
            <div>
                <p class="text-[11px] tracking-[0.14em] uppercase mb-3" style="color:#2D5A27;">{{ $eyebrow }}</p>
                <h2 class="text-3xl sm:text-4xl md:text-5xl text-[#171717]" style="font-weight:600; letter-spacing:-0.02em;">
                    {{ $title }}
                </h2>
                <p class="mt-4 text-sm md:text-base text-[#404040] leading-relaxed max-w-md">
                    {{ $desc }}
                </p>

                <dl class="mt-8 space-y-5">
                    <div class="flex gap-3">
                        <span class="shrink-0 mt-0.5" style="color:#2D5A27;">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </span>
                        <div>
                            <dt class="text-xs tracking-[0.14em] uppercase text-[#737373] mb-1">Alamat</dt>
                            <dd class="text-sm md:text-base text-[#171717] leading-relaxed">
                                {!! nl2br(e($address)) !!}
                            </dd>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <span class="shrink-0 mt-0.5" style="color:#2D5A27;">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </span>
                        <div>
                            <dt class="text-xs tracking-[0.14em] uppercase text-[#737373] mb-1">Jam operasional</dt>
                            <dd class="text-sm md:text-base text-[#171717] leading-relaxed">
                                {!! nl2br(e($hours)) !!}
                            </dd>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <span class="shrink-0 mt-0.5" style="color:#2D5A27;">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.6a2 2 0 01-.5 2.1L8 9.6a16 16 0 006 6l1.3-1.3a2 2 0 012.1-.5c.8.3 1.7.5 2.6.6a2 2 0 011.7 2z"/>
                            </svg>
                        </span>
                        <div>
                            <dt class="text-xs tracking-[0.14em] uppercase text-[#737373] mb-1">Kontak</dt>
                            <dd class="text-sm md:text-base text-[#171717] leading-relaxed">
                                <a href="tel:{{ preg_replace('/\D/', '', $phone) }}" class="hover:text-[#2D5A27] transition-colors">{{ $phone }}</a><br>
                                <a href="mailto:{{ $email }}" class="hover:text-[#2D5A27] transition-colors">{{ $email }}</a>
                            </dd>
                        </div>
                    </div>
                </dl>

                <a
                    href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapQuery) }}"
                    target="_blank" rel="noopener"
                    class="mt-8 inline-flex items-center gap-2 px-6 py-3 text-[13px] tracking-[0.14em] uppercase text-white transition-colors hover:opacity-90"
                    style="background-color:#2D5A27; border-radius:999px;"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="13.5" y1="4.5" x2="21" y2="12"/><polyline points="21,12 13.5,19.5"/><line x1="21" y1="12" x2="3" y2="12"/>
                    </svg>
                    Buka di Google Maps
                </a>
            </div>

            {{-- Map embed --}}
            <div class="relative w-full aspect-[4/3] md:aspect-[16/12] overflow-hidden bg-[#DCE8D6]" style="border-radius:16px;">
                <iframe
                    src="https://www.google.com/maps?q={{ urlencode($mapQuery) }}&output=embed"
                    class="absolute inset-0 w-full h-full border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="{{ $title }}"
                    allowfullscreen
                ></iframe>
            </div>
        </div>
    </div>
</section>
