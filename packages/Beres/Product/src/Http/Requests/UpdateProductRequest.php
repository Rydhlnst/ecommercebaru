<?php

namespace Beres\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productId = $this->route('id');

        return [
            'name'              => 'required|string|max:255',
            'sku'               => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'url_key'           => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'url_key')->ignore($productId),
            ],
            'price'             => 'required|numeric|min:0',
            'special_price'     => 'nullable|numeric|min:0|lt:price',
            'quantity'          => 'required|integer|min:0',
            'status'            => 'required|boolean',
            'visible_individually' => 'required|boolean',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
            'meta_keywords'     => 'nullable|string|max:255',
            'attribute_family_id' => 'required|exists:attribute_families,id',
            'categories'        => 'required|array|min:1',
            'categories.*'      => 'exists:categories,id',
            'images'            => 'nullable|array',
            'images.*'          => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'sku.unique'    => 'SKU sudah digunakan.',
            'url_key.unique' => 'URL Key sudah digunakan.',
            'price.min'     => 'Harga minimal 0.',
            'special_price.lt' => 'Harga spesial harus lebih kecil dari harga normal.',
            'categories.required' => 'Pilih minimal satu kategori.',
        ];
    }
}
