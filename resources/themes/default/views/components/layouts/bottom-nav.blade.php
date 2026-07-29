@php
    $current = request()->path();
    $isActive = fn($paths) => collect((array) $paths)->contains(fn($p) => $current === $p || str_starts_with($current, trim($p, '/')));

    $items = [
        [
            'label'  => core()->getConfigData('beres_storefront.bottom_nav.label_home') ?: 'Home',
            'href'   => route('shop.home.index'),
            'active' => $current === '/' || $current === '',
            'svg'    => '<path d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/>',
        ],
        [
            'label'  => core()->getConfigData('beres_storefront.bottom_nav.label_shop') ?: 'Shop',
            'href'   => route('shop.search.index'),
            'active' => $isActive(['search', 'products', 'categories']),
            'svg'    => '<path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 4h13"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>',
        ],
        [
            'label'  => core()->getConfigData('beres_storefront.bottom_nav.label_cart') ?: 'Cart',
            'href'   => route('shop.checkout.cart.index'),
            'active' => $isActive(['checkout/cart', 'checkout']),
            'svg'    => '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>',
        ],
        [
            'label'  => core()->getConfigData('beres_storefront.bottom_nav.label_account') ?: 'Akun',
            'href'   => auth('customer')->check() ? route('shop.customers.account.profile.index') : route('shop.customer.session.create'),
            'active' => $isActive(['customer', 'account']),
            'svg'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>',
        ],
    ];
@endphp

{{-- Mobile bottom nav — inline layout supaya pasti render tanpa perlu Tailwind grid class --}}
<nav
    aria-label="Bottom navigation"
    style="
        display: none;
        position: fixed;
        bottom: 0; left: 0; right: 0;
        z-index: 40;
        background: #ffffff;
        border-top: 1px solid #E8F0E5;
        padding-bottom: env(safe-area-inset-bottom);
        box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
    "
    class="beres-bottom-nav"
>
    <ul style="display: grid; grid-template-columns: repeat(4, 1fr); margin: 0; padding: 0; list-style: none;">
        @foreach ($items as $item)
            <li style="text-align: center;">
                <a
                    href="{{ $item['href'] }}"
                    style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        gap: 4px;
                        padding: 10px 4px;
                        text-decoration: none;
                        font-size: 10px;
                        letter-spacing: 0.02em;
                        position: relative;
                        color: {{ $item['active'] ? '#2D5A27' : '#404040' }};
                        font-weight: {{ $item['active'] ? '600' : '500' }};
                    "
                    aria-label="{{ $item['label'] }}"
                    aria-current="{{ $item['active'] ? 'page' : 'false' }}"
                >
                    <svg style="width:22px; height:22px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['svg'] !!}</svg>
                    <span>{{ $item['label'] }}</span>
                    @if ($item['active'])
                        <span style="position:absolute; top:0; left:50%; transform:translateX(-50%); width:28px; height:2px; background:#2D5A27; border-radius:0 0 2px 2px;"></span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</nav>

{{-- Spacer supaya konten tidak ke-tumpuk bottom nav (mobile only) --}}
<div class="beres-bottom-nav-spacer" style="display:none; height:68px;" aria-hidden="true"></div>

@push('styles')
<style>
    @media (max-width: 767px) {
        .beres-bottom-nav { display: block !important; }
        .beres-bottom-nav-spacer { display: block !important; }
    }
</style>
@endpush
