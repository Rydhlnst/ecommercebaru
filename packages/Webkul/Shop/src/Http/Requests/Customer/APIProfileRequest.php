<?php

namespace Webkul\Shop\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Core\Rules\PhoneNumber;

class APIProfileRequest extends FormRequest
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
        $id = auth()->guard('customer')->user()->id;

        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:Other,Male,Female',
            'date_of_birth' => 'nullable|date|before:today',
            'email' => 'required|email|unique:customers,email,'.$id,
            'phone' => ['required', new PhoneNumber, 'unique:customers,phone,'.$id],
            'subscribed_to_news_letter' => 'nullable|boolean',
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
            'email.unique' => 'This email address is already taken.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.unique' => 'This phone number is already taken.',
            'gender.required' => 'The gender field is required.',
            'gender.in' => 'The gender must be Other, Male, or Female.',
        ];
    }
}
