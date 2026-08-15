{!! view_render_event('bagisto.shop.layout.features.before') !!}

@php
    use App\Models\SiteSetting;

    $servicesList = [];
    try {
        $featuresRaw = SiteSetting::getValue('service_features');
        if ($featuresRaw) {
            $servicesList = json_decode($featuresRaw, true) ?: [];
        }
    } catch (\Throwable $e) {
        $servicesList = [];
    }
@endphp

@if (!empty($servicesList))
    <div class="bg-[#F5F9F3] border-t border-[#E8F0E5]" v-pre>
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-10 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach ($servicesList as $service)
                <div class="flex flex-col items-center gap-3 group">
                    <span
                        class="flex items-center justify-center w-14 h-14 rounded-full bg-white border border-[#E8F0E5] text-2xl text-[#2D5A27] shadow-xs group-hover:scale-110 transition-transform duration-300"
                        role="presentation"
                    >
                        <i class="{{ $service['icon'] ?? 'fas fa-shield-alt' }}"></i>
                    </span>

                    <div>
                        <p class="font-bold text-base text-[#171717]">{{ $service['title'] ?? '' }}</p>
                        @if(!empty($service['description']))
                            <p class="mt-1 text-xs text-zinc-500 max-w-[240px] leading-relaxed">{{ $service['description'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{!! view_render_event('bagisto.shop.layout.features.after') !!}