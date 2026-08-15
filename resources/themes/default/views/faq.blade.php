<x-shop::layouts>
    <x-slot:title>Frequently Asked Questions (FAQ) — {{ config('app.name') }}</x-slot>

    <div class="bg-gradient-to-b from-[#F5F9F3] to-white py-12 md:py-20 border-b border-[#E8F0E5]/60">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 md:px-10 text-center">
            <span class="inline-block px-3.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-[#E8F0E5] text-[#2D5A27] mb-4">
                Pusat Bantuan
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-[#171717] tracking-tight">
                Pertanyaan yang Sering Diajukan (FAQ)
            </h1>
            <p class="mt-4 text-sm md:text-base text-[#555555] max-w-xl mx-auto leading-relaxed">
                Temukan jawaban cepat untuk pertanyaan seputar produk, pemesanan, pengiriman, dan layanan kami.
            </p>
        </div>
    </div>

    <section class="bg-white py-12 md:py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 md:px-10">
            @if ($faqs->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($faqs as $i => $faq)
                        <details class="group border overflow-hidden transition-all hover:border-[#2D5A27] bg-white" style="border-color:#E8F0E5; border-radius:14px;" @if($i === 0) open @endif>
                            <summary class="flex items-center justify-between cursor-pointer list-none px-5 md:px-6 py-4 md:py-5 hover:bg-[#F5F9F3] transition-colors">
                                <span class="text-base md:text-lg text-[#171717] pr-4 font-semibold">{{ $faq->question }}</span>
                                <span class="text-2xl transition-transform duration-300 group-open:rotate-45 shrink-0 leading-none text-[#2D5A27]">+</span>
                            </summary>
                            <div class="px-5 md:px-6 pb-5 pt-1 text-sm md:text-base text-[#404040] leading-relaxed prose prose-sm max-w-none border-t border-[#E8F0E5]/50 mt-1">
                                {!! $faq->answer !!}
                            </div>
                        </details>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-[#F5F9F3] rounded-2xl border border-[#E8F0E5] p-8">
                    <p class="text-base text-[#555555]">Belum ada data FAQ yang ditambahkan.</p>
                </div>
            @endif

            <div class="mt-12 p-6 md:p-8 rounded-2xl bg-[#F5F9F3] border border-[#E8F0E5] flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-lg font-bold text-[#171717]">Punya pertanyaan lain?</h3>
                    <p class="text-sm text-[#555555] mt-1">Tim dukungan pelanggan kami siap membantu Anda kapan saja.</p>
                </div>
                @php
                    $storeWa = \App\Models\SiteSetting::getValue('store_whatsapp') ?: \App\Models\SiteSetting::getValue('store_phone');
                @endphp
                @if($storeWa)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storeWa) }}" target="_blank" rel="noopener" class="px-6 py-3 bg-[#2D5A27] text-white font-semibold text-sm rounded-xl hover:bg-[#1E3A1E] transition-colors shrink-0 shadow-sm">
                        Hubungi via WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </section>
</x-shop::layouts>
