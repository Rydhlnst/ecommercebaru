@php
    $current = request()->path();
    $isActive = fn($paths) => collect((array) $paths)->contains(fn($p) => $current === $p || str_starts_with($current, trim($p, '/')));

    $items = [
        [
            'label'  => 'Home',
            'href'   => route('shop.home.index'),
            'active' => $current === '/' || $current === '',
            'svg'    => '<path d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/>',
        ],
        [
            'label'  => 'Shop',
            'href'   => route('shop.search.index'),
            'active' => $isActive(['search', 'products', 'categories']),
            'svg'    => '<path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 4h13"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>',
        ],
        [
            'label'  => 'Cart',
            'href'   => route('shop.checkout.cart.index'),
            'active' => $isActive(['checkout/cart', 'checkout']),
            'badge'  => true,
            'svg'    => '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>',
        ],
        [
            'label'  => 'Akun',
            'href'   => auth('customer')->check() ? route('shop.customers.account.profile.index') : route('shop.customer.session.create'),
            'active' => $isActive(['customer', 'account']),
            'svg'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>',
        ],
    ];
@endphp

{{-- Bottom nav — mobile only, fixed above safe-area --}}
<nav
    class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t"
    style="border-color:#E8F0E5; padding-bottom: env(safe-area-inset-bottom);"
    aria-label="Bottom navigation"
>
    <ul class="grid grid-cols-4">
        @foreach ($items as $item)
            <li>
                <a
                    href="{{ $item['href'] }}"
                    @isset($item['onclick']) onclick="{{ $item['onclick'] }}; return false;" @endisset
                    class="flex flex-col items-center justify-center gap-1 py-2.5 text-[10px] tracking-wide relative transition-colors
                        {{ $item['active'] ? '' : 'hover:text-[#2D5A27]' }}"
                    style="{{ $item['active'] ? 'color:#2D5A27;' : 'color:#404040;' }}"
                    aria-label="{{ $item['label'] }}"
                    aria-current="{{ $item['active'] ? 'page' : 'false' }}"
                >
                    <svg
                        class="w-6 h-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >{!! $item['svg'] !!}</svg>

                    <span style="font-weight:{{ $item['active'] ? '600' : '500' }};">{{ $item['label'] }}</span>

                    @if (!empty($item['badge']))
                        <span
                            v-if="$root.cart && $root.cart.items_qty > 0"
                            v-cloak
                            class="absolute top-1.5 right-6 min-w-[16px] h-4 px-1 rounded-full text-[9px] text-white flex items-center justify-center"
                            style="background-color:#2D5A27; font-weight:600;"
                            v-text="$root.cart.items_qty"
                        ></span>
                    @endif

                    @if ($item['active'])
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5" style="background-color:#2D5A27;"></span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</nav>

{{-- Spacer supaya konten tidak ke-tumpuk bottom nav --}}
<div class="md:hidden h-[68px]" aria-hidden="true" style="padding-bottom: env(safe-area-inset-bottom);"></div>
