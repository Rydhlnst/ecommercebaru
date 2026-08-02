@props([
    'title'     => null,
    'padding'   => 'p-4',
    'bg'        => 'bg-white dark:bg-gray-900',
    'border'    => 'border border-gray-200 dark:border-gray-800',
    'rounded'   => 'rounded-lg',
    'shadow'    => 'shadow-sm',
])

{{--
    Simple card wrapper for admin views. Used across Beres/* dashboards
    (Dashboard, Reports, Payment, Orders, Customers, Products, Inventory).
    Anonymous component — no PHP class needed, resolved from view path.
--}}
<div {{ $attributes->merge(['class' => trim(implode(' ', [$bg, $border, $rounded, $shadow, $padding]))]) }}>
    @if (! is_null($title))
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">{{ $title }}</h3>

            @isset($actions)
                <div>{{ $actions }}</div>
            @endisset
        </div>
    @endif

    {{ $slot }}
</div>
