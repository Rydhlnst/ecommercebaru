@push('meta')
    <meta name="description" content="Atur ulang kata sandi Anda"/>
    <meta name="robots" content="noindex, nofollow"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>Lupa Kata Sandi</x-slot>

    <div class="min-h-screen bg-cream flex flex-col items-center justify-center px-6 py-10">
        <a href="{{ route('shop.home.index') }}" class="text-2xl text-ink mb-8"
           style="font-weight: 600; letter-spacing: -0.02em;" aria-label="{{ config('app.name') }}">
            {{ config('app.name') }}
        </a>

        <div class="w-full max-w-sm bg-cream border border-mist p-7">
            <div class="flex justify-center mb-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-mist text-ink">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </span>
            </div>

            <h1 class="text-2xl text-ink text-center" style="font-weight: 500; letter-spacing: -0.02em;">Lupa kata sandi?</h1>
            <p class="mt-1.5 text-sm text-stone text-center">Masukkan email Anda dan kami akan mengirimkan tautan reset.</p>

            <div class="mt-6">
                <x-shop::form :action="route('shop.customers.forgot_password.store')">
                    <x-shop::form.control-group class="!mb-3">
                        <x-shop::form.control-group.label for="email" class="sr-only">Email</x-shop::form.control-group.label>
                        <div class="relative">
                            <span class="icon-email absolute left-3 top-1/2 -translate-y-1/2 text-lg text-stone pointer-events-none" aria-hidden="true"></span>
                            <x-shop::form.control-group.control
                                type="email" id="email" name="email" rules="required|email" value=""
                                class="!w-full !pl-10 !pr-3 !py-2.5 !text-sm !border !border-mist !bg-transparent !text-ink placeholder:!text-stone !rounded-none focus:!border-ink focus:!ring-0"
                                label="Email" placeholder="Alamat email" aria-label="Email" aria-required="true"
                            />
                        </div>
                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <x-shop::form.control-group class="mt-3">
                            {!! \Webkul\Customer\Facades\Captcha::render() !!}
                            <x-shop::form.control-group.error control-name="recaptcha_token" />
                        </x-shop::form.control-group>
                    @endif

                    <button type="submit"
                        class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-ink text-cream text-[12px] tracking-[0.14em] uppercase px-5 py-2.5 hover:bg-cocoa transition-colors">
                        Kirim tautan reset
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </x-shop::form>
            </div>

            <p class="mt-6 pt-5 border-t border-mist text-center text-sm text-stone">
                <a class="text-ink hover:text-clay transition-colors inline-flex items-center gap-1"
                   href="{{ route('shop.customer.session.index') }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Kembali ke login
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>
