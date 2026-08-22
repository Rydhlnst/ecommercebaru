<?php

namespace Webkul\Shop\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class APIRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:customers,email,NULL,id,channel_id,'.core()->getCurrentChannel()->id,
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string',
            'is_subscribed' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 6 characters.',
        ];
    }
}
