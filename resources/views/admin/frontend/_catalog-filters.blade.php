@php
    $filterAction = $action ?? url()->current();
    $filterMaxPrice = (float) ($maxPrice ?? 0);
    $filterSort = request()->query('sort', 'featured');
@endphp

<aside class="catalog-filter-panel">
    <div class="hidden md:block">
        <p class="catalog-filter-title">Filter:</p>
    </div>

    <details class="md:hidden" open>
        <summary class="catalog-filter-mobile-toggle">Filter produk <span aria-hidden="true">⌄</span></summary>
        <div class="pt-5">
            @include('admin.frontend._catalog-filter-form')
        </div>
    </details>

    <div class="hidden md:block">
        @include('admin.frontend._catalog-filter-form')
    </div>
</aside>

@once
    @push('styles')
        <style>
            .catalog-filter-panel { width: 260px; flex: 0 0 260px; }
            .catalog-filter-title { color: #171717; font-size: 1.5rem; font-weight: 700; margin-bottom: 2.25rem; }
            .catalog-filter-group { border-top: 1px solid #E8F0E5; padding: 1.25rem 0; }
            .catalog-filter-group:first-child { border-top: 0; padding-top: 0; }
            .catalog-filter-heading { align-items: center; color: #171717; display: flex; font-size: 1.05rem; font-weight: 700; justify-content: space-between; }
            .catalog-filter-heading span { color: #737373; font-size: 1rem; }
            .catalog-filter-label { align-items: center; color: #404040; display: flex; font-size: .95rem; gap: .75rem; }
            .catalog-filter-switch { appearance: none; background: #D6DAD5; border-radius: 999px; cursor: pointer; height: 1.7rem; position: relative; transition: background .2s; width: 3.25rem; }
            .catalog-filter-switch::after { background: #fff; border-radius: 50%; content: ''; height: 1.3rem; left: .2rem; position: absolute; top: .2rem; transition: transform .2s; width: 1.3rem; }
            .catalog-filter-switch:checked { background: #2D5A27; }
            .catalog-filter-switch:checked::after { transform: translateX(1.55rem); }
            .catalog-filter-price-note { color: #737373; font-size: .85rem; line-height: 1.5; margin-top: 1rem; }
            .catalog-filter-price-fields { align-items: center; display: grid; gap: .5rem; grid-template-columns: 1fr 1fr; margin-top: 1rem; }
            .catalog-filter-price-fields input { border: 1px solid #DDE7D9; border-radius: .75rem; color: #171717; min-width: 0; padding: .75rem; width: 100%; }
            .catalog-filter-price-fields input:focus { border-color: #2D5A27; outline: 2px solid #E8F0E5; }
            .catalog-filter-submit { background: #2D5A27; border-radius: 999px; color: #fff; font-size: .8rem; font-weight: 700; letter-spacing: .08em; margin-top: 1.25rem; padding: .75rem 1rem; text-transform: uppercase; width: 100%; }
            .catalog-filter-submit:hover { background: #1E3D1A; }
            .catalog-filter-mobile-toggle { align-items: center; border: 1px solid #E8F0E5; border-radius: .75rem; color: #171717; cursor: pointer; display: flex; font-weight: 700; justify-content: space-between; padding: .8rem 1rem; }
            @media (max-width: 767px) { .catalog-filter-panel { flex: 1 1 100%; width: 100%; } }
        </style>
    @endpush
@endonce
