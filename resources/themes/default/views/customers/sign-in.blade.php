@push('meta')
    <meta name="description" content="Masuk ke akun Anda"/>
    <meta name="robots" content="noindex, nofollow"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>Masuk</x-slot>

    <div class="min-h-screen bg-cream flex flex-col items-center justify-center px-6 py-10">
        <a
            href="{{ route('shop.home.index') }}"
            class="text-2xl text-ink mb-8"
            style="font-weight: 600; letter-spacing: -0.02em;"
            aria-label="{{ config('app.name') }}"
        >
            {{ config('app.name') }}
        </a>

        <div class="w-full max-w-sm bg-cream border border-mist p-7">
            <h1 class="text-2xl text-ink" style="font-weight: 500; letter-spacing: -0.02em;">Masuk</h1>
            <p class="mt-1.5 text-sm text-stone">Selamat datang kembali — masukkan detail Anda.</p>

            {!! view_render_event('bagisto.shop.customers.login.before') !!}

            <div class="mt-6">
                <x-shop::form :action="route('shop.customer.session.create')">
                    {!! view_render_event('bagisto.shop.customers.login_form_controls.before') !!}

                    {{-- Email --}}
                    <x-shop::form.control-group class="!mb-3">
                        <x-shop::form.control-group.label for="email" class="sr-only">Email</x-shop::form.control-group.label>
                        <div class="relative">
                            <span class="icon-email absolute left-3 top-1/2 -translate-y-1/2 text-lg text-stone pointer-events-none" aria-hidden="true"></span>
                            <x-shop::form.control-group.control
                                type="email"
                                id="email"
                                name="email"
                                rules="required|email"
                                value=""
                                class="!w-full !pl-10 !pr-3 !py-2.5 !text-sm !border !border-mist !bg-transparent !text-ink placeholder:!text-stone !rounded-none focus:!border-ink focus:!ring-0"
                                label="Email"
                                placeholder="Alamat email"
                                aria-label="Email"
                                aria-required="true"
                            />
                        </div>
                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    {{-- Password --}}
                    <x-shop::form.control-group class="!mb-2">
                        <x-shop::form.control-group.label for="password" class="sr-only">Kata Sandi</x-shop::form.control-group.label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                            <x-shop::form.control-group.control
                                type="password"
                                id="password"
                                name="password"
                                rules="required|min:6"
                                value=""
                                class="!w-full !pl-10 !pr-10 !py-2.5 !text-sm !border !border-mist !bg-transparent !text-ink placeholder:!text-stone !rounded-none focus:!border-ink focus:!ring-0"
                                label="Kata Sandi"
                                placeholder="Kata Sandi"
                                aria-label="Kata Sandi"
                                aria-required="true"
                            />
                            <button type="button" id="toggle-password"
                                class="icon-eye absolute right-3 top-1/2 -translate-y-1/2 text-lg text-stone hover:text-ink transition-colors"
                                onclick="togglePasswordVisibility()" aria-label="Tampilkan kata sandi"></button>
                        </div>
                        <x-shop::form.control-group.error control-name="password" />
                    </x-shop::form.control-group>

                    <div class="flex justify-end">
                        <a href="{{ route('shop.customers.forgot_password.create') }}"
                           class="text-[12px] text-stone hover:text-ink transition-colors">
                            Lupa kata sandi?
                        </a>
                    </div>

                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <x-shop::form.control-group class="mt-4">
                            {!! \Webkul\Customer\Facades\Captcha::render() !!}
                            <x-shop::form.control-group.error control-name="recaptcha_token" />
                        </x-shop::form.control-group>
                    @endif

                    <button type="submit"
                        class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-ink text-cream text-[12px] tracking-[0.14em] uppercase px-5 py-2.5 hover:bg-cocoa transition-colors">
                        Masuk
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>

                    {{-- Social login (event-rendered): style small icon row --}}
                    <div class="mt-5
                                [&_.flex]:justify-center [&_.flex]:gap-3
                                [&_a]:w-9 [&_a]:h-9 [&_a]:rounded-full [&_a]:border [&_a]:border-mist [&_a]:bg-cream
                                [&_a]:flex [&_a]:items-center [&_a]:justify-center [&_a]:text-ink
                                hover:[&_a]:bg-canvas
                                [&_svg]:w-4 [&_svg]:h-4">
                        {!! view_render_event('bagisto.shop.customers.login_form_controls.after') !!}
                    </div>
                </x-shop::form>
            </div>

            {!! view_render_event('bagisto.shop.customers.login.after') !!}

            @if (request()->cookie('enable-resend') && request()->cookie('email-for-resend'))
                <p class="mt-4 text-center text-sm text-stone">
                    <a class="text-ink hover:text-clay transition-colors"
                       href="{{ route('shop.customers.resend.verification_email', urlencode(request()->cookie('email-for-resend'))) }}">
                        Kirim ulang email verifikasi
                    </a>
                </p>
            @endif

            <p class="mt-6 pt-5 border-t border-mist text-center text-sm text-stone">
                Baru di sini?
                <a class="ml-1 text-ink hover:text-clay transition-colors inline-flex items-center gap-1"
                   href="{{ route('shop.customers.register.index') }}">
                    Buat akun
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
        <script>
            function togglePasswordVisibility() {
                const field = document.getElementById("password");
                const btn = document.getElementById("toggle-password");
                if (field.type === "password") {
                    field.type = "text";
                    btn.classList.remove("icon-eye"); btn.classList.add("icon-toast-info");
                } else {
                    field.type = "password";
                    btn.classList.remove("icon-toast-info"); btn.classList.add("icon-eye");
                }
            }
        </script>
    @endpush
</x-shop::layouts>
