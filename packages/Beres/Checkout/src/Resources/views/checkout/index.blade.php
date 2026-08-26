<x-shop::layouts>
    <x-slot:title>Checkout</x-slot>

    @push('styles')
        <style>
            .checkout-page {
                overflow-x: hidden;
            }

            .checkout-page .checkout-main-grid {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) minmax(300px, 380px) !important;
                gap: 2rem;
                align-items: start;
                width: 100%;
                max-width: 100%;
            }

            .checkout-page .checkout-form {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                min-width: 0;
            }

            .checkout-page .checkout-summary {
                position: sticky;
                top: 1.5rem;
                align-self: start;
                min-width: 0;
                max-width: 380px;
            }

            .checkout-page .checkout-summary-card {
                overflow: hidden;
                border: 1px solid #dcebd6;
                border-radius: 1rem;
                background: #ffffff;
                padding: 1.75rem;
            }

            .checkout-page .checkout-summary-items {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .checkout-page .checkout-summary-item {
                display: grid;
                grid-template-columns: 64px minmax(0, 1fr) auto;
                gap: 0.75rem;
                align-items: center;
                min-width: 0;
            }

            .checkout-page .checkout-summary-image {
                position: relative;
                width: 64px !important;
                min-width: 64px;
                height: 64px !important;
                overflow: hidden;
                border: 1px solid #fff;
                border-radius: 0.75rem;
                background: #fff;
            }

            .checkout-page .checkout-summary-image img {
                display: block;
                width: 100% !important;
                max-width: 100% !important;
                height: 100% !important;
                max-height: 100% !important;
                object-fit: contain !important;
            }

            .checkout-page .checkout-summary-info {
                min-width: 0;
            }

            .checkout-page .checkout-summary-name {
                overflow: hidden;
                margin: 0;
                color: #171514;
                font-size: 0.875rem;
                font-weight: 500;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .checkout-page .checkout-summary-price {
                margin: 0;
                color: #171514;
                font-size: 0.875rem;
                font-weight: 500;
                white-space: nowrap;
            }

            .checkout-page .checkout-summary-row {
                display: flex;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.4rem 0;
            }

            .checkout-page .checkout-summary-row.text-lg {
                padding: 0.75rem 0 0.35rem;
            }

            .checkout-page .checkout-submit {
                display: block;
                min-height: 52px;
                border: 0;
                border-radius: 0.75rem;
                background-color: #2d5a27 !important;
                color: #ffffff !important;
                cursor: pointer;
                font-weight: 600;
                text-align: center;
                transition: background-color 150ms ease, opacity 150ms ease;
            }

            .checkout-page .checkout-submit:hover:not(:disabled),
            .checkout-page .checkout-submit:focus-visible {
                background-color: #1e3a1e !important;
            }

            .checkout-page .checkout-submit:focus-visible {
                outline: 3px solid rgba(45, 90, 39, 0.25);
                outline-offset: 2px;
            }

            .checkout-page .checkout-submit:disabled {
                cursor: not-allowed;
                opacity: 0.5;
            }

            .checkout-page .checkout-input {
                box-sizing: border-box;
                display: block;
                width: 100%;
                max-width: 100%;
            }

            .checkout-page textarea.checkout-input {
                min-height: 112px;
                resize: vertical;
            }

            @media (max-width: 1023px) {
                .checkout-page .checkout-main-grid {
                    grid-template-columns: minmax(0, 1fr) !important;
                }

                .checkout-page .checkout-summary {
                    position: static;
                    max-width: none;
                }
            }

            @media (max-width: 480px) {
                .checkout-page .checkout-summary-card {
                    padding: 1.25rem;
                }

                .checkout-page .checkout-summary-item {
                    grid-template-columns: 56px minmax(0, 1fr);
                }

                .checkout-page .checkout-summary-image {
                    width: 56px !important;
                    min-width: 56px;
                    height: 56px !important;
                }

                .checkout-page .checkout-summary-price {
                    grid-column: 2;
                }
            }
        </style>
    @endpush

    <div class="checkout-page min-h-screen bg-[#faf9f7] py-8 sm:py-12">
        <div class="mx-auto w-full px-4 sm:px-6 md:px-10 lg:px-14" style="max-width:1600px;">
            <div class="mb-8 flex items-end justify-between border-b border-[#e6dfda] pb-5">
                <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#8d4a3d]">Beres Storefront</p><h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#171514] sm:text-4xl">Checkout</h1></div>
                <a href="{{ route('shop.home.index') }}" class="text-sm font-medium text-[#8d4a3d] hover:underline">Continue shopping</a>
            </div>
            <div id="checkout-error" class="mb-6 hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>
            <div class="checkout-main-grid">
                <form id="checkout-form" class="checkout-form" method="post" action="{{ route('shop.checkout.session.store') }}" onsubmit="return false;">
                    @csrf
                    <section class="checkout-section"><div class="mb-5 flex items-baseline justify-between gap-4"><h2 class="checkout-heading">Contact</h2><span class="text-sm text-[#746b66]">Already have an account? <a href="{{ route('shop.customer.session.index') }}" class="text-[#8d4a3d] underline">Sign in</a></span></div><input id="checkout-email" type="email" name="shipping_address[email]" placeholder="Email address" class="checkout-input" required /><label class="mt-3 flex items-center gap-2 text-sm text-[#514944]"><input type="checkbox" name="marketing_opt_in" class="accent-[#8d4a3d]" /> Email me with news and offers</label></section>
                    <section class="checkout-section"><h2 class="checkout-heading mb-5">Delivery</h2><div class="grid grid-cols-1 gap-3 sm:grid-cols-2"><input type="text" name="shipping_address[first_name]" placeholder="First name" class="checkout-input" required /><input type="text" name="shipping_address[last_name]" placeholder="Last name" class="checkout-input" required /><input type="text" name="shipping_address[address1]" placeholder="Address" class="checkout-input sm:col-span-2" required /><input type="text" name="shipping_address[address2]" placeholder="Apartment, suite, etc. (optional)" class="checkout-input sm:col-span-2" /><div class="relative"><input id="city-name" type="text" name="shipping_address[city]" placeholder="City" autocomplete="off" class="checkout-input w-full" required /><input id="city-id" type="hidden" name="shipping_address[city_id]" /><div id="city-results" class="absolute z-20 mt-1 hidden max-h-48 w-full overflow-auto rounded-xl border border-[#e6dfda] bg-white shadow-lg"></div></div><input type="text" name="shipping_address[state]" placeholder="Province / state" class="checkout-input" required /><input type="text" name="shipping_address[postcode]" placeholder="Postal code" class="checkout-input" required /><input type="text" name="shipping_address[phone]" placeholder="Phone" class="checkout-input" required /><input type="text" name="shipping_address[country]" value="ID" placeholder="Country" class="checkout-input" required /></div><label class="mt-4 flex items-center gap-2 text-sm text-[#514944]"><input type="checkbox" name="save_information" class="accent-[#8d4a3d]" /> Save this information for next time</label></section>
                    <section class="checkout-section"><h2 class="checkout-heading mb-5">Shipping method</h2><select id="courier-select" class="checkout-input" {{ $paymentMode === 'midtrans' ? 'required' : '' }}><option value="">Select a courier</option>@foreach($couriers as $code => $name)<option value="{{ $code }}">{{ $name }}</option>@endforeach</select><p class="mt-3 text-sm text-[#746b66]">Choose a city and courier to see available services. WhatsApp orders may continue without a courier selection.</p><div id="shipping-options" class="mt-4 hidden space-y-2"></div><input type="hidden" name="shipping_method" id="shipping-method" {{ $paymentMode === 'midtrans' ? 'required' : '' }} /><input type="hidden" name="shipping_cost" id="shipping-cost" value="0" /></section>
                    <section class="checkout-section"><h2 class="checkout-heading">Payment</h2><p class="mt-1 text-sm text-[#746b66]">All transactions are secure and encrypted.</p>@if($paymentMode === 'whatsapp')<input type="hidden" name="payment_method" value="whatsapp" />@if($whatsappConfigured)<div class="mt-5 rounded-xl border border-[#c9ddc2] bg-[#f3f8f0] p-4 text-sm text-[#31572c]"><strong class="block">Order via WhatsApp</strong><span>Your order will be saved and the complete order details will open in WhatsApp for admin confirmation.</span></div>@else<div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">WhatsApp checkout is not available yet. The admin must configure the WhatsApp number in Store Settings.</div>@endif @elseif($midtransActive)<label class="mt-5 flex cursor-pointer items-start gap-3 rounded-xl border border-[#8d4a3d] bg-[#fbf5f2] p-4"><input type="radio" name="payment_method" value="midtrans" class="mt-1 accent-[#8d4a3d]" required checked /><span><strong class="block text-[#171514]">Midtrans</strong><span class="text-sm text-[#746b66]">Virtual account, QRIS, e-wallets, and cards.</span></span></label>@else<div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">Online payment is temporarily unavailable. Please contact the store administrator.</div>@endif<h3 class="mt-7 text-base font-semibold text-[#171514]">Billing address</h3><label class="mt-3 flex items-center gap-3 rounded-t-xl border border-[#e6dfda] bg-[#fbf5f2] p-4"><input type="radio" name="billing_choice" value="same" checked class="accent-[#8d4a3d]" /> Same as shipping address</label><label class="flex items-center gap-3 rounded-b-xl border-x border-b border-[#e6dfda] p-4"><input type="radio" name="billing_choice" value="different" class="accent-[#8d4a3d]" /> Use a different billing address</label><textarea name="notes" rows="3" class="checkout-input mt-5" placeholder="Order notes (optional)"></textarea></section>
                </form>
                <aside class="checkout-summary"><div class="checkout-summary-card"><h2 class="mb-6 text-xl font-semibold text-[#171514]">Order summary</h2><div class="checkout-summary-items">@foreach(($cart['items'] ?? []) as $item)<div class="checkout-summary-item"><div class="checkout-summary-image">@if(!empty($item['image_url']))<img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" width="64" height="64" loading="lazy" />@endif<span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#171514] px-1 text-[10px] text-white">{{ $item['quantity'] }}</span></div><div class="checkout-summary-info"><p class="checkout-summary-name">{{ $item['name'] }}</p><p class="text-xs text-[#746b66]">{{ $item['weight_label'] ?? 'Standard' }}</p></div><p class="checkout-summary-price">Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}</p></div>@endforeach</div><div class="my-6 border-t border-[#d9d1cc]"></div><div class="space-y-3 text-sm"><div class="checkout-summary-row"><span class="text-[#746b66]">Subtotal</span><span>Rp {{ number_format($cart['subtotal'] ?? 0, 0, ',', '.') }}</span></div><div class="checkout-summary-row"><span class="text-[#746b66]">Shipping</span><span id="shipping-cost-display">Rp 0</span></div></div><div class="my-5 border-t border-[#d9d1cc]"></div><div class="checkout-summary-row text-lg font-semibold"><span>Total</span><span id="grand-total">Rp {{ number_format($cart['subtotal'] ?? 0, 0, ',', '.') }}</span></div>@php($checkoutAvailable = $paymentMode === 'whatsapp' ? $whatsappConfigured : $midtransActive)<button type="submit" form="checkout-form" id="complete-order" @disabled(!$checkoutAvailable) class="checkout-submit mt-7 w-full rounded-xl bg-[#2d5a27] px-5 py-4 font-semibold text-white transition hover:bg-[#1e3a1e] disabled:cursor-not-allowed disabled:opacity-50">{{ $paymentMode === 'whatsapp' ? 'Pesan via WhatsApp' : 'Complete order' }}</button><p class="mt-4 text-center text-xs text-[#746b66]">By placing your order, you agree to our terms and conditions.</p></div></aside>
            </div>
        </div>
    </div>
    @push('styles')<style>.checkout-section{border:1px solid #e6dfda;border-radius:1rem;background:#fff;padding:1.25rem;box-shadow:0 8px 30px rgba(60,35,20,.04)}.checkout-heading{font-size:1.25rem;font-weight:600;color:#171514}.checkout-input{border:1px solid #d9d1cc;border-radius:.75rem;background:#fff;padding:.8rem 1rem;color:#171514;outline:0}.checkout-input:focus{border-color:#8d4a3d;box-shadow:0 0 0 3px rgba(141,74,61,.12)}@media(min-width:640px){.checkout-section{padding:1.75rem}}</style>@endpush
    @push('scripts')
        <script>
            const form=document.getElementById('checkout-form'),city=document.getElementById('city-name'),cityId=document.getElementById('city-id'),cityResults=document.getElementById('city-results'),courier=document.getElementById('courier-select'),options=document.getElementById('shipping-options'),method=document.getElementById('shipping-method'),cost=document.getElementById('shipping-cost'),errorBox=document.getElementById('checkout-error'),subtotal={{ (float)($cart['subtotal']??0) }},weight={{ $cartWeight }};let searchTimer;
            city?.addEventListener('input',()=>{clearTimeout(searchTimer);cityId.value='';if(city.value.length<3)return cityResults.classList.add('hidden');searchTimer=setTimeout(()=>fetch('{{ route('api.shipping.search') }}?query='+encodeURIComponent(city.value)).then(r=>r.json()).then(data=>{cityResults.innerHTML='';(data.data||[]).slice(0,8).forEach(item=>{const b=document.createElement('button');b.type='button';b.className='block w-full px-4 py-2 text-left text-sm hover:bg-[#fbf5f2]';b.textContent=item.label||item.name||item.city_name;b.onclick=()=>{city.value=b.textContent;cityId.value=item.id||item.city_id;cityResults.classList.add('hidden');courier.dispatchEvent(new Event('change'))};cityResults.appendChild(b)});cityResults.classList.toggle('hidden',!cityResults.children.length)}).catch(()=>{}),250)});
            courier?.addEventListener('change',()=>{if(!courier.value||!cityId.value)return;options.classList.remove('hidden');options.innerHTML='<p class="text-sm text-[#746b66]">Loading shipping services…</p>';fetch('{{ route('shop.checkout.calculate_shipping') }}',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Content-Type':'application/json'},body:JSON.stringify({courier:courier.value,city_id:cityId.value,weight})}).then(r=>r.json()).then(data=>{options.innerHTML='';(data.data||[]).forEach(group=>(group.services||group.costs||[]).forEach(item=>{const label=document.createElement('label');label.className='flex cursor-pointer items-center gap-3 rounded-xl border border-[#e6dfda] p-4 hover:border-[#8d4a3d]';const value=Number(item.cost?.[0]?.value??item.cost??0);label.innerHTML='<input type="radio" name="shipping_service" value="'+courier.value+'|'+item.service+'" data-cost="'+value+'" class="accent-[#8d4a3d]" required><span class="flex-1"><strong class="block">'+item.service+'</strong><small class="text-[#746b66]">'+(item.description||'')+'</small></span><strong>Rp '+value.toLocaleString('id-ID')+'</strong>';label.querySelector('input').onchange=()=>{method.value=label.querySelector('input').value;cost.value=value;document.getElementById('shipping-cost-display').textContent='Rp '+value.toLocaleString('id-ID');document.getElementById('grand-total').textContent='Rp '+(subtotal+value).toLocaleString('id-ID')};options.appendChild(label)}));if(!options.children.length)options.innerHTML='<p class="text-sm text-red-700">No shipping service is available for this destination.</p>'}).catch(()=>options.innerHTML='<p class="text-sm text-red-700">Shipping calculation failed. Please try again.</p>')});
            form?.addEventListener('submit',async e=>{e.preventDefault();errorBox.classList.add('hidden');const button=document.getElementById('complete-order');button.disabled=true;button.textContent='Processing…';try{const csrf=document.querySelector('meta[name=csrf-token]')?.content;if(!csrf)throw Error('Checkout session expired. Please refresh and try again.');const json=async r=>{const d=await r.json().catch(()=>({}));if(!r.ok||!d.success)throw Error(d.message||'Unable to complete checkout.');return d},payload=new URLSearchParams(new FormData(form)),session=await fetch('{{ route('shop.checkout.session.store') }}',{method:'POST',credentials:'same-origin',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'},body:payload}).then(json),order=await fetch('{{ route('shop.checkout.place_order') }}',{method:'POST',credentials:'same-origin',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},body:JSON.stringify({session_id:session.data.id})}).then(json);if(order.whatsapp_url){window.location.assign(order.whatsapp_url);return}if('{{ $paymentMode }}'==='whatsapp')throw Error('WhatsApp redirect URL was not returned. Please refresh and try again.');if(order.payment_url){window.location.assign(order.payment_url);return}window.location.assign('{{ route('shop.checkout.success') }}?order_id='+order.order_id)}catch(err){errorBox.textContent=err.message;errorBox.classList.remove('hidden');button.disabled=false;button.textContent='{{ $paymentMode === 'whatsapp' ? 'Pesan via WhatsApp' : 'Complete order' }}'}});
        </script>
    @endpush
</x-shop::layouts>
