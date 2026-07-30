@push('meta')
    <meta name="description" content="Create your account"/>
    <meta name="robots" content="noindex, nofollow"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>Create account</x-slot>

    <div class="min-h-screen bg-cream flex flex-col items-center justify-center px-6 py-10">
        <a href="{{ route('shop.home.index') }}" class="text-2xl text-ink mb-8"
           style="font-weight: 600; letter-spacing: -0.02em;" aria-label="{{ config('app.name') }}">
            Ankish Mart
        </a>

        <div class="w-full max-w-sm bg-cream border border-mist p-7">
            <h1 class="text-2xl text-ink" style="font-weight: 500; letter-spacing: -0.02em;">Create account</h1>
            <p class="mt-1.5 text-sm text-stone">Join us — takes less than a minute.</p>

            <div class="mt-6">
                <x-shop::form :action="route('shop.customers.register.store')">
                    {!! view_render_event('bagisto.shop.customers.signup_form_controls.before') !!}

                    <div class="grid grid-cols-2 gap-2">
                        <x-shop::form.control-group class="!mb-3">
                            <x-shop::form.control-group.label for="first_name" class="sr-only">First name</x-shop::form.control-group.label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                                <x-shop::form.control-group.control
                                    type="text" id="first_name" name="first_name" rules="required" :value="old('first_name')"
                                    class="!w-full !pl-10 !pr-3 !py-2.5 !text-sm !border !border-mist !bg-transparent !text-ink placeholder:!text-stone !rounded-none focus:!border-ink focus:!ring-0"
                                    label="First name" placeholder="First name" aria-label="First name" aria-required="true"
                                />
                            </div>
                            <x-shop::form.control-group.error control-name="first_name" />
                        </x-shop::form.control-group>

                        <x-shop::form.control-group class="!mb-3">
                            <x-shop::form.control-group.label for="last_name" class="sr-only">Last name</x-shop::form.control-group.label>
                            <x-shop::form.control-group.control
                                type="text" id="last_name" name="last_name" rules="required" :value="old('last_name')"
                                class="!w-full !px-3 !py-2.5 !text-sm !border !border-mist !bg-transparent !text-ink placeholder:!text-stone !rounded-none focus:!border-ink focus:!ring-0"
                                label="Last name" placeholder="Last name" aria-label="Last name" aria-required="true"
                            />
                            <x-shop::form.control-group.error control-name="last_name" />
                        </x-shop::form.control-group>
                    </div>

                    <x-shop::form.control-group class="!mb-3">
                        <x-shop::form.control-group.label for="email" class="sr-only">Email</x-shop::form.control-group.label>
                        <div class="relative">
                            <span class="icon-email absolute left-3 top-1/2 -translate-y-1/2 text-lg text-stone pointer-events-none" aria-hidden="true"></span>
                            <x-shop::form.control-group.control
                                type="email" id="email" name="email" rules="required|email" :value="old('email')"
                                class="!w-full !pl-10 !pr-3 !py-2.5 !text-sm !border !border-mist !bg-transparent !text-ink placeholder:!text-stone !rounded-none focus:!border-ink focus:!ring-0"
                                label="Email" placeholder="Email address" aria-label="Email" aria-required="true"
                            />
                        </div>
                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    <x-shop::form.control-group class="!mb-3">
                        <x-shop::form.control-group.label for="password" class="sr-only">Password</x-shop::form.control-group.label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                            <x-shop::form.control-group.control
                                type="password" id="password" name="password" rules="required|min:6" :value="old('password')"
                                class="!w-full !pl-10 !pr-10 !py-2.5 !text-sm !border !border-mist !bg-transparent !text-ink placeholder:!text-stone !rounded-none focus:!border-ink focus:!ring-0"
                                label="Password" placeholder="Password" ref="password"
                                aria-label="Password" aria-required="true"
                            />
                            <button type="button" class="icon-eye absolute right-3 top-1/2 -translate-y-1/2 text-lg text-stone hover:text-ink"
                                onclick="togglePw('password', this)" aria-label="Show password"></button>
                        </div>
                        <x-shop::form.control-group.error control-name="password" />
                    </x-shop::form.control-group>

                    <x-shop::form.control-group class="!mb-2">
                        <x-shop::form.control-group.label for="password_confirmation" class="sr-only">Confirm password</x-shop::form.control-group.label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <x-shop::form.control-group.control
                                type="password" id="password_confirmation" name="password_confirmation" rules="confirmed:@password" value=""
                                class="!w-full !pl-10 !pr-10 !py-2.5 !text-sm !border !border-mist !bg-transparent !text-ink placeholder:!text-stone !rounded-none focus:!border-ink focus:!ring-0"
                                label="Confirm password" placeholder="Confirm password"
                                aria-label="Confirm password" aria-required="true"
                            />
                            <button type="button" class="icon-eye absolute right-3 top-1/2 -translate-y-1/2 text-lg text-stone hover:text-ink"
                                onclick="togglePw('password_confirmation', this)" aria-label="Show password"></button>
                        </div>
                        <x-shop::form.control-group.error control-name="password_confirmation" />
                    </x-shop::form.control-group>

                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <x-shop::form.control-group>
                            {!! \Webkul\Customer\Facades\Captcha::render() !!}
                            <x-shop::form.control-group.error control-name="recaptcha_token" />
                        </x-shop::form.control-group>
                    @endif

                    @if (core()->getConfigData('customer.settings.create_new_account_options.news_letter'))
                        <label class="mt-2 flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_subscribed" class="w-4 h-4 accent-ink" />
                            <span class="text-[12px] text-stone">Subscribe to newsletter</span>
                        </label>
                    @endif

                    @if(core()->getConfigData('general.gdpr.settings.enabled') && core()->getConfigData('general.gdpr.agreement.enabled'))
                        <label class="mt-2 flex items-start gap-2 cursor-pointer">
                            <x-shop::form.control-group.control type="checkbox" name="agreement" id="agreement" value="0" rules="required" class="w-4 h-4 accent-ink mt-0.5" />
                            <span class="text-[12px] text-stone" v-pre>
                                {{ core()->getConfigData('general.gdpr.agreement.agreement_label') }}
                            </span>
                        </label>
                        <x-shop::form.control-group.error control-name="agreement" />
                    @endif

                    <button type="submit"
                        class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-ink text-cream text-[12px] tracking-[0.14em] uppercase px-5 py-2.5 hover:bg-cocoa transition-colors">
                        Sign up
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>

                    {!! view_render_event('bagisto.shop.customers.signup_form_controls.after') !!}
                </x-shop::form>
            </div>

            <p class="mt-6 pt-5 border-t border-mist text-center text-sm text-stone">
                Already have an account?
                <a class="ml-1 text-ink hover:text-clay transition-colors inline-flex items-center gap-1"
                   href="{{ route('shop.customer.session.index') }}">
                    Login
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
        <script>
            function togglePw(id, btn) {
                const f = document.getElementById(id);
                if (f.type === "password") { f.type = "text"; btn.classList.remove('icon-eye'); btn.classList.add('icon-toast-info'); }
                else { f.type = "password"; btn.classList.remove('icon-toast-info'); btn.classList.add('icon-eye'); }
            }
        </script>
    @endpush
</x-shop::layouts>
