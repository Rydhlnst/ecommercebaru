@props([
    'image' => null,
    'alt' => 'Produk',
    'class' => '',
    'size' => 'md', // sm, md, lg
])

@php
    $sizes = [
        'sm' => ['svg' => 'w-8 h-8', 'text' => 'text-[9px]', 'wrapper' => ''],
        'md' => ['svg' => 'w-12 h-12', 'text' => 'text-[10px]', 'wrapper' => ''],
        'lg' => ['svg' => 'w-16 h-16', 'text' => 'text-xs', 'wrapper' => ''],
    ];
    $size = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($image)
    <img src="{{ $image }}" alt="{{ $alt }}" class="{{ $class }}" loading="lazy">
@else
    <div class="flex flex-col items-center justify-center gap-2 opacity-50 {{ $class }}">
        <svg class="{{ $size['svg'] }}" viewBox="0 0 24 24" fill="none" stroke="#2D5A27" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
        </svg>
        <span class="{{ $size['text'] }} text-[#2D5A27] font-medium tracking-wide uppercase">Gambar Produk</span>
    </div>
@endif
