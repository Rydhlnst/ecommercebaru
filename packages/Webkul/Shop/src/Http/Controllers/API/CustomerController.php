<?php

namespace Webkul\Shop\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Webkul\Core\Repositories\SubscribersListRepository;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Shop\Http\Requests\Customer\APIProfileRequest;
use Webkul\Shop\Http\Requests\Customer\APIRegistrationRequest;
use Webkul\Shop\Http\Requests\Customer\LoginRequest;
use Webkul\Shop\Http\Resources\CustomerResource;

class CustomerController extends APIController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerGroupRepository $customerGroupRepository,
        protected SubscribersListRepository $subscriptionRepository
    ) {}

    /**
     * Login Customer
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if (! auth()->guard('customer')->attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => trans('shop::app.customers.login-form.invalid-credentials'),
            ], Response::HTTP_FORBIDDEN);
        }

        if (! auth()->guard('customer')->user()->status) {
            auth()->guard('customer')->logout();

            return response()->json([
                'message' => trans('shop::app.customers.login-form.not-activated'),
            ], Response::HTTP_FORBIDDEN);
        }

        if (! auth()->guard('customer')->user()->is_verified) {
            Cookie::queue(Cookie::make('enable-resend', 'true', 1));

            Cookie::queue(Cookie::make('email-for-resend', $request->get('email'), 1));

            auth()->guard('customer')->logout();

            return response()->json([
                'message' => trans('shop::app.customers.login-form.verify-first'),
            ], Response::HTTP_FORBIDDEN);
        }

        /**
         * Event passed to prepare cart after login.
         */
        Event::dispatch('customer.after.login', auth()->guard()->user());

        return response()->json([
            'message' => 'Login successful.',
        ]);
    }

    /**
     * Register a new customer.
     */
    public function register(APIRegistrationRequest $request): JsonResponse
    {
        $customerGroup = core()->getConfigData('customer.settings.create_new_account_options.default_group');

        $subscription = $this->subscriptionRepository->findOneWhere(['email' => $request->email]);

        $data = array_merge($request->only([
            'first_name',
            'last_name',
            'email',
            'password_confirmation',
            'is_subscribed',
        ]), [
            'password' => bcrypt($request->password),
            'api_token' => Str::random(80),
            'is_verified' => ! core()->getConfigData('customer.settings.email.verification'),
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => $customerGroup])->id,
            'channel_id' => core()->getCurrentChannel()->id,
            'token' => md5(uniqid(rand(), true)),
            'subscribed_to_news_letter' => (bool) ($request->is_subscribed ?? $subscription?->is_subscribed),
        ]);

        Event::dispatch('customer.registration.before');

        $customer = $this->customerRepository->create($data);

        if ($subscription) {
            $this->subscriptionRepository->update([
                'customer_id' => $customer->id,
            ], $subscription->id);
        }

        if (
            ! empty($data['is_subscribed'])
            && ! $subscription
        ) {
            Event::dispatch('customer.subscription.before');

            $subscription = $this->subscriptionRepository->create([
                'email' => $data['email'],
                'customer_id' => $customer->id,
                'channel_id' => core()->getCurrentChannel()->id,
                'is_subscribed' => 1,
                'token' => uniqid(),
            ]);

            Event::dispatch('customer.subscription.after', $subscription);
        }

        Event::dispatch('customer.create.after', $customer);

        Event::dispatch('customer.registration.after', $customer);

        $message = core()->getConfigData('customer.settings.email.verification')
            ? 'Registration successful. Please verify your email address.'
            : 'Registration successful.';

        return response()->json([
            'message' => $message,
            'customer' => new CustomerResource($customer),
        ], Response::HTTP_CREATED);
    }

    /**
     * Get authenticated customer profile.
     */
    public function profile(): JsonResponse
    {
        $customer = auth()->guard('customer')->user();

        return response()->json([
            'customer' => new CustomerResource($customer),
        ]);
    }

    /**
     * Update authenticated customer profile.
     */
    public function updateProfile(APIProfileRequest $request): JsonResponse
    {
        $customer = auth()->guard('customer')->user();

        $data = $request->validated();

        $data['subscribed_to_news_letter'] = $request->boolean('subscribed_to_news_letter');

        Event::dispatch('customer.update.before');

        $updatedCustomer = $this->customerRepository->update($data, $customer->id);

        if ($updatedCustomer) {
            Event::dispatch('customer.update.after', $updatedCustomer);

            return response()->json([
                'message' => 'Profile updated successfully.',
                'customer' => new CustomerResource($updatedCustomer),
            ]);
        }

        return response()->json([
            'message' => 'Failed to update profile.',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Change authenticated customer password.
     */
    public function changePassword(): JsonResponse
    {
        $request = request()->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        $customer = auth()->guard('customer')->user();

        if (! Hash::check($request['current_password'], $customer->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->customerRepository->update([
            'password' => bcrypt($request['new_password']),
        ], $customer->id);

        Event::dispatch('customer.password.update.after', $customer);

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * Logout authenticated customer.
     */
    public function logout(): JsonResponse
    {
        auth()->guard('customer')->logout();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
