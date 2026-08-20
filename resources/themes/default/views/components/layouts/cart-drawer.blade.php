@php
    /**
     * Custom slide-out cart drawer + shared cart JS.
     * Rendered once per page via the shop layout. Vanilla JS (no Vue/Alpine dep).
     */
@endphp

{{-- ============================= DRAWER ============================= --}}
<div id="beres-cart-overlay" onclick="beresCartClose()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:60; opacity:0; transition:opacity .25s ease;"></div>

<aside id="beres-cart-drawer" role="dialog" aria-label="Keranjang" aria-modal="true"
       style="position:fixed; top:0; right:0; height:100vh; width:100%; max-width:420px; background:#fff; z-index:61;
              transform:translateX(100%); transition:transform .3s ease; display:flex; flex-direction:column; box-shadow:-4px 0 24px rgba(0,0,0,.12);">
    <header style="padding:18px 20px; border-bottom:1px solid #E8F0E5; display:flex; align-items:center; justify-content:space-between;">
        <h2 style="font-size:18px; font-weight:700; color:#171717; margin:0;">Keranjang</h2>
        <button type="button" onclick="beresCartClose()" aria-label="Tutup" style="background:none; border:0; cursor:pointer; font-size:22px; color:#737373; line-height:1;">&times;</button>
    </header>

    <div id="beres-cart-items" style="flex:1; overflow-y:auto; padding:16px 20px;">
        {{-- Lines rendered by beresCartRender() --}}
    </div>

    <footer id="beres-cart-footer" style="padding:18px 20px; border-top:1px solid #E8F0E5; background:#FAFAFA;" class="hidden">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <span style="font-size:14px; color:#737373;">Subtotal</span>
            <span id="beres-cart-subtotal" style="font-size:18px; font-weight:700; color:#171717;">Rp 0</span>
        </div>
        <a href="{{ route('shop.checkout.index') }}"
           style="display:block; text-align:center; padding:14px; background:#2D5A27; color:#fff; font-size:13px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; border-radius:8px; text-decoration:none;">
            Checkout
        </a>
    </footer>
</aside>

{{-- ============================= SHARED JS ============================= --}}
@push('scripts')
<script>
(function () {
    // ---- Drawer open/close ----
    window.beresCartState = { count: 0, items_qty: 0, subtotal: 0, items: [] };

    window.beresCartOpen = function () {
        var drawer = document.getElementById('beres-cart-drawer');
        var overlay = document.getElementById('beres-cart-overlay');
        if (!drawer) return;
        drawer.style.transform = 'translateX(0)';
        if (overlay) { overlay.style.display = 'block'; requestAnimationFrame(function(){ overlay.style.opacity = '1'; }); }
        document.body.style.overflow = 'hidden';
    };

    window.beresCartClose = function () {
        var drawer = document.getElementById('beres-cart-drawer');
        var overlay = document.getElementById('beres-cart-overlay');
        if (!drawer) return;
        drawer.style.transform = 'translateX(100%)';
        if (overlay) { overlay.style.opacity = '0'; setTimeout(function(){ overlay.style.display = 'none'; }, 250); }
        document.body.style.overflow = '';
    };

    window.beresCartFormatPrice = function (n) {
        return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID');
    };

    window.beresCartEscapeHtml = function (s) {
        var d = document.createElement('div');
        d.textContent = s == null ? '' : String(s);
        return d.innerHTML;
    };

    // ---- Render the cart snapshot into the drawer + badge ----
    window.beresCartRender = function (cart) {
        if (!cart) cart = window.beresCartState;
        window.beresCartState = cart;

        // Badge (header + mobile)
        document.querySelectorAll('.beres-cart-count').forEach(function (el) {
            el.textContent = String(cart.items_qty || 0);
            el.style.display = (cart.items_qty > 0) ? '' : 'none';
        });

        // Subtotal + footer
        var sub = document.getElementById('beres-cart-subtotal');
        if (sub) sub.textContent = window.beresCartFormatPrice(cart.subtotal);
        var footer = document.getElementById('beres-cart-footer');
        if (footer) footer.classList.toggle('hidden', !cart.items || cart.items.length === 0);

        // Lines
        var host = document.getElementById('beres-cart-items');
        if (!host) return;

        if (!cart.items || cart.items.length === 0) {
            host.innerHTML = '<div style="text-align:center; padding:48px 16px; color:#737373;">' +
                '<div style="font-size:40px; margin-bottom:12px;">🛒</div>' +
                '<p style="font-size:14px;">Keranjang masih kosong.</p></div>';
            return;
        }

        host.innerHTML = cart.items.map(function (it) {
            var img = it.image_url
                ? '<img src="' + it.image_url + '" alt="" style="width:56px; height:56px; object-fit:contain; padding:4px; border-radius:8px; background:#F5F9F3;">'
                : '<div style="width:56px; height:56px; border-radius:8px; background:#F5F9F3; display:flex; align-items:center; justify-content:center; color:#C8DBBE;">🌿</div>';

            return '<div style="display:flex; gap:12px; padding:14px 0; border-bottom:1px solid #F0F0F0;" data-key="' + it.key + '">' +
                '<a href="' + (it.product_url || '#') + '">' + img + '</a>' +
                '<div style="flex:1; min-width:0;">' +
                    '<p style="font-size:13px; font-weight:600; color:#171717; margin:0 0 2px; line-height:1.35;">' + window.beresCartEscapeHtml(it.name) + '</p>' +
                    '<p style="font-size:12px; color:#737373; margin:0 0 8px;">' + window.beresCartFormatPrice(it.price) + '</p>' +
                    '<div style="display:flex; align-items:center; justify-content:space-between;">' +
                        '<div style="display:flex; align-items:center; border:1px solid #E8F0E5; border-radius:6px; overflow:hidden;">' +
                            '<button type="button" onclick="beresCartStep(\'' + it.key + '\', -1)" style="width:28px; height:28px; border:0; background:#fff; color:#2D5A27; font-size:16px; cursor:pointer;">−</button>' +
                            '<span style="min-width:28px; text-align:center; font-size:13px; font-weight:600; color:#171717;">' + it.quantity + '</span>' +
                            '<button type="button" onclick="beresCartStep(\'' + it.key + '\', 1)" style="width:28px; height:28px; border:0; background:#fff; color:#2D5A27; font-size:16px; cursor:pointer;">+</button>' +
                        '</div>' +
                        '<button type="button" onclick="beresCartRemoveLine(\'' + it.key + '\')" style="font-size:11px; color:#B91C1C; background:none; border:0; cursor:pointer; text-decoration:underline;">Hapus</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }).join('');
    };

    // Apply a fresh cart snapshot (from add/update/remove responses or initial fetch)
    window.beresCartRefresh = function (cart) {
        window.beresCartRender(cart);
    };

    // ---- Mutations ----
    window.beresCartStep = function (key, delta) {
        var line = (window.beresCartState.items || []).find(function (i) { return i.key === key; });
        if (!line) return;
        beresCartMutate('{{ route('cart.update') }}', { key: key, quantity: Math.max(1, line.quantity + delta) });
    };

    window.beresCartRemoveLine = function (key) {
        beresCartMutate('{{ route('cart.remove') }}', { key: key });
    };

    window.beresCartMutate = function (url, payload) {
        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) { if (data && data.cart) window.beresCartRefresh(data.cart); })
        .catch(function () {});
    };

    // ---- Add-to-cart (used by product cards on every page) ----
    // Only define if not already provided (home page ships its own copy).
    if (typeof window.beresAddToCart !== 'function') {
        window.beresAddToCart = async function (form) {
            var btn = form.querySelector('button[type="submit"]');
            var original = btn ? btn.textContent : '';
            if (btn) { btn.disabled = true; btn.textContent = 'Menambahkan…'; }

            var productId = form.querySelector('[name="product_id"]').value;
            var qtyEl = form.querySelector('[name="quantity"]');
            var quantity = parseInt((qtyEl ? qtyEl.value : 1) || 1, 10);
            var token = (document.querySelector('meta[name="csrf-token"]')?.content) || (form.querySelector('[name="_token"]')?.value);

            var variantInput = form.querySelector('.beres-variant-input');
            var selectedVariantId = variantInput ? parseInt(variantInput.value, 10) : null;

            var payload = { product_id: productId, quantity: quantity };
            if (selectedVariantId && !isNaN(selectedVariantId)) {
                payload.selected_configurable_option = selectedVariantId;
            }

            try {
                var res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });
                var data = await res.json().catch(function () { return ({}); });

                if (res.ok && data.success) {
                    if (data.cart) window.beresCartRefresh(data.cart);
                    if (btn) btn.textContent = '✓ Ditambahkan';
                    setTimeout(function () {
                        if (btn) { btn.disabled = false; btn.textContent = original; }
                        window.beresCartOpen();
                    }, 500);
                } else {
                    alert(data.message || 'Gagal menambahkan ke keranjang. Coba lagi.');
                    if (btn) { btn.disabled = false; btn.textContent = original; }
                }
            } catch (e) {
                alert('Gagal terhubung ke server.');
                if (btn) { btn.disabled = false; btn.textContent = original; }
            }
        };
    }

    if (typeof window.beresBuyNow !== 'function') {
        window.beresBuyNow = async function (form) {
            var productId = form.querySelector('[name="product_id"]').value;
            var qtyEl = form.querySelector('[name="quantity"]');
            var quantity = parseInt((qtyEl ? qtyEl.value : 1) || 1, 10);
            var token = (document.querySelector('meta[name="csrf-token"]')?.content) || (form.querySelector('[name="_token"]')?.value);

            var variantInput = form.querySelector('.beres-variant-input');
            var selectedVariantId = variantInput ? parseInt(variantInput.value, 10) : null;

            var payload = { product_id: productId, quantity: quantity };
            if (selectedVariantId && !isNaN(selectedVariantId)) {
                payload.selected_configurable_option = selectedVariantId;
            }

            try {
                var res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });
                var data = await res.json().catch(function () { return ({}); });

                if (res.ok && data.success) {
                    window.location.href = '{{ route("shop.checkout.onepage.index") }}';
                } else {
                    alert(data.message || 'Gagal melanjutkan ke pembayaran.');
                }
            } catch (e) {
                alert('Gagal terhubung ke server.');
            }
        };
    }

    // ---- Qty stepper + variant selector (cards) — guarded ----
    if (typeof window.beresQty !== 'function') {
        window.beresQty = function (btn, delta) {
            var input = btn.parentElement.querySelector('input[name="quantity"]');
            if (!input) return;
            var v = parseInt(input.value || 1, 10) + delta;
            if (v < 1) v = 1;
            if (v > 99) v = 99;
            input.value = v;
        };
    }

    // ---- Variant inline buttons (product cards) — updates hidden input + price ----
    if (typeof window.beresSelectVariantInline !== 'function') {
        window.beresSelectVariantInline = function (btn, variantId, price) {
            var card = btn.closest('.beres-card');
            if (!card) return;

            // Update hidden input
            var hidden = card.querySelector('.beres-variant-input');
            if (hidden) hidden.value = variantId;

            // Toggle active state on ALL sibling buttons in the same row
            var row = btn.parentElement;
            row.querySelectorAll('.beres-variant-btn').forEach(function (b) {
                b.style.backgroundColor = '#fff';
                b.style.color = '#171717';
                b.style.borderColor = '#E8F0E5';
                b.classList.remove('beres-variant-active');
            });
            btn.style.backgroundColor = '#2D5A27';
            btn.style.color = '#fff';
            btn.style.borderColor = '#2D5A27';
            btn.classList.add('beres-variant-active');

            // Update displayed price
            var priceEl = card.querySelector('.text-xl.font-bold');
            if (priceEl && price) priceEl.textContent = price;
        };
    }

    if (typeof window.beresSelectVariant !== 'function') {
        window.beresSelectVariant = function (btn, variantId, price) {
            var card = btn.closest('.beres-card');
            if (!card) return;
            var hidden = card.querySelector('.beres-variant-input');
            if (hidden) hidden.value = variantId;
            var siblings = btn.parentElement.querySelectorAll('button');
            siblings.forEach(function (b) {
                b.style.backgroundColor = ''; b.style.borderColor = '#E8F0E5';
                b.classList.remove('text-white'); b.classList.add('text-[#171717]');
            });
            btn.style.backgroundColor = '#2D5A27'; btn.style.borderColor = '#2D5A27';
            btn.classList.add('text-white'); btn.classList.remove('text-[#171717]');
            var priceEl = card.querySelector('.text-xl.font-bold');
            if (priceEl && price) priceEl.textContent = price;
        };
    }

    if (typeof window.beresToggleWishlist !== 'function') {
        window.beresToggleWishlist = async function (btn, productId) {
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            var svg = btn.querySelector('svg');
            var filled = btn.getAttribute('data-active') === '1';
            try {
                var res = await fetch('{{ url("/api/customer/wishlist") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                    credentials: 'same-origin',
                    body: JSON.stringify({ product_id: productId }),
                });
                if (res.status === 401 || res.redirected) {
                    window.location.href = '{{ route("shop.customer.session.create") }}';
                    return;
                }
                if (res.ok) {
                    btn.setAttribute('data-active', filled ? '0' : '1');
                    if (svg) svg.setAttribute('fill', filled ? 'none' : '#2D5A27');
                }
            } catch (e) {}
        };
    }

    // ---- Initial load ----
    function initBeresCart() {
        fetch('{{ route("cart.show") }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) { if (data && data.cart) window.beresCartRender(data.cart); })
            .catch(function () { window.beresCartRender({ count: 0, items_qty: 0, subtotal: 0, items: [] }); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') window.beresCartClose(); });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBeresCart);
    } else {
        initBeresCart();
    }
})();
</script>
@endpush
