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

    $svgIcons = [
        'fas fa-truck-fast' => '<path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'fas fa-rotate-left' => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>',
        'fas fa-shield-halved' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'fas fa-headset' => '<path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>',
        'fas fa-credit-card' => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        'fas fa-leaf' => '<path d="M11 20A7 7 0 0 1 9.8 6.9C15.5 4.9 17 3.5 19 2c1 2 2 4.5 1 8-1 3.5-5.1 5.7-9 6z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
        'fas fa-award' => '<circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>',
        'fas fa-box-open' => '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
        'fas fa-clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
    ];
@endphp

@if (!empty($servicesList))
    <div class="bg-[#2D5A27] border-t border-[#1E3D1A]" v-pre>
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 md:px-10 lg:px-14 py-10 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach ($servicesList as $service)
                @php
                    $iconClass = $service['icon'] ?? 'fas fa-shield-halved';
                    $svgPath = $svgIcons[$iconClass] ?? $svgIcons['fas fa-shield-halved'];
                @endphp
                <div class="flex flex-col items-center gap-3 group">
                    <span
                        class="flex items-center justify-center w-14 h-14 rounded-full bg-white shadow-xs group-hover:scale-110 transition-transform duration-300"
                        role="presentation"
                    >
                        <svg class="w-6 h-6 text-[#2D5A27]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $svgPath !!}</svg>
                    </span>

                    <div>
                        <p class="font-bold text-base text-white">{{ $service['title'] ?? '' }}</p>
                        @if(!empty($service['description']))
                            <p class="mt-1 text-xs text-white max-w-[240px] leading-relaxed">{{ $service['description'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{!! view_render_event('bagisto.shop.layout.features.after') !!}
