{{-- Top strip only renders when channel has >1 locale/currency (see header/index.blade.php). Kept minimal to match Bellroy layout. --}}
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.before') !!}

<div class="hidden">
    {{-- Intentionally empty — utilities moved into main header row --}}
</div>

{!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.after') !!}
