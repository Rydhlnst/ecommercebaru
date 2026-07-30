@push('meta')
    <meta name="description" content="Set a new password"/>
    <meta name="robots" content="noindex, nofollow"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>Reset password</x-slot>

    <div class="min-h-screen bg-cream flex flex-col items-center justify-center px-6 py-10">
        <a href="{{ route('shop.home.index') }}" class="text-2xl text-ink mb-8"
           style="font-weight: 600; letter-spacing: -0.02em;" aria-label="{{ config('app.name') }}">
            Ankish Mart
        </a>

        <div class="w-full max-w-sm bg-cream border border-mist p-7">
            <div class="flex justify-center mb-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-mist text-ink">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>

            <h1 class="text-2xl text-ink text-center" style="font-weight: 500; letter-spacing: -0.02em;">Reset password</h1>
            <p class="mt-1.5 text-sm text-stone text-center">Create a new password to continue.</p>

            <div class="mt-6">
                <x-shop::form :action="route('shop.customers.reset_password.store')">
                    <x-shop::form.control-group.control type="hidden" name="token" :value="$token" />

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
                        <x-shop::form.control-group.label for="password" class="sr-only">New password</x-shop::form.control-group.label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                            <x-shop::form.control-group.control
                                type="password" id="password" name="password" rules="required|min:6" value=""
                                class="!w-full !pl-10 !pr-10 !py-2.5 !text-sm !border !border-mist !bg-transparent !text-ink placeholder:!text-stone !rounded-none focus:!border-ink focus:!ring-0"
                                label="New password" placeholder="New password" ref="password"
                                aria-label="New password" aria-required="true"
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
                        <x-shop::form.control-group.error control-name="password" />
                    </x-shop::form.control-group>

                    <button type="submit"
                        class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-ink text-cream text-[12px] tracking-[0.14em] uppercase px-5 py-2.5 hover:bg-cocoa transition-colors">
                        Update password
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </x-shop::form>
            </div>

            <p class="mt-6 pt-5 border-t border-mist text-center text-sm text-stone">
                <a class="text-ink hover:text-clay transition-colors inline-flex items-center gap-1"
                   href="{{ route('shop.customer.session.index') }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Back to login
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePw(id, btn) {
                const f = document.getElementById(id);
                if (f.type === "password") { f.type = "text"; btn.classList.remove('icon-eye'); btn.classList.add('icon-toast-info'); }
                else { f.type = "password"; btn.classList.remove('icon-toast-info'); btn.classList.add('icon-eye'); }
            }
        </script>
    @endpush
</x-shop::layouts>
