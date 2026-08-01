@php
    $current = request()->path();
    $isActive = fn($paths) => collect((array) $paths)->contains(fn($p) => $current === $p || str_starts_with($current, trim($p, '/')));

    $items = [
        [
            'label'  => core()->getConfigData('beres_storefront.bottom_nav.label_shop') ?: 'Beranda',
            'href'   => route('shop.home.index'),
            'active' => $current === '/' || $current === '' || $isActive(['products', 'categories']),
            'svg'    => '<path d="M3 3h5v5H3zM10 3h5v5h-5zM17 3h4v5h-4zM3 10h5v5H3zM10 10h5v5h-5zM17 10h4v5h-4zM3 17h5v4H3zM10 17h5v4h-5zM17 17h4v4h-4z"/>',
        ],
        [
            'label'  => core()->getConfigData('beres_storefront.bottom_nav.label_cart') ?: 'Keranjang',
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
        [
            'label'  => core()->getConfigData('beres_storefront.bottom_nav.label_search') ?: 'Cari',
            'href'   => route('shop.search.index'),
            'active' => $isActive(['search']),
            'svg'    => '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
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
<div class="beres-bottom-nav-spacer" style="display:none; height:0;" aria-hidden="true"></div>

@push('scripts')
<style>
    .beres-bottom-nav { display: none !important; }
    .beres-bottom-nav-spacer { display: none !important; height: 0 !important; margin: 0 !important; padding: 0 !important; }
    @media (max-width: 767px) {
        .beres-bottom-nav { display: block !important; }
        .beres-bottom-nav-spacer { display: block !important; height: 62px !important; }
    }
</style>
@endpush
