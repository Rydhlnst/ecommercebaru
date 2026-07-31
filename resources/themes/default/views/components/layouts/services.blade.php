{!! view_render_event('bagisto.shop.layout.features.before') !!}

<!--
    The ThemeCustomizationRepository repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'services_content',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]); 
@endphp

<!-- Features -->
@if ($customization)
    <div class="bg-cream border-t border-mist" v-pre>
        <div class="mx-auto max-w-[1600px] px-6 md:px-10 lg:px-14 py-12 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach (($customization->options['services'] ?? []) as $service)
                <div class="flex flex-col items-center gap-3">
                    <span
                        class="{{ $service['service_icon'] }} flex items-center justify-center w-12 h-12 rounded-full border border-ink text-2xl text-ink"
                        role="presentation"
                    ></span>

                    <div>
                        <p class="font-serif text-lg text-ink">{{ $service['title'] }}</p>
                        <p class="mt-1 text-xs text-stone max-w-[220px]">{{ $service['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{!! view_render_event('bagisto.shop.layout.features.after') !!}