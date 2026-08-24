<?php

namespace App\Http\Requests\Admin;

use App\Models\AdminProduct;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveAdminProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        $hasVariations = $this->boolean('has_variations');
        $variationArrayRules = $hasVariations
            ? ['required', 'array', 'min:1', 'max:20']
            : ['nullable', 'array', 'max:20'];
        $variationValueRules = $hasVariations
            ? ['required']
            : ['nullable'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:admin_categories,id'],
            'badge' => ['nullable', 'in:new,sale,habis_terjual'],
            'description' => ['nullable', 'string', 'max:65535'],
            'is_featured' => ['nullable', 'boolean'],
            'has_variations' => ['nullable', 'boolean'],
            'price' => $hasVariations ? ['nullable', 'decimal:0,2', 'min:0', 'max:9999999999999'] : ['required', 'decimal:0,2', 'min:0', 'max:9999999999999'],
            'compare_at_price' => ['nullable', 'decimal:0,2', 'min:0', 'max:9999999999999'],
            'stock' => $hasVariations ? ['nullable', 'integer', 'min:0', 'max:2147483647'] : ['required', 'integer', 'min:0', 'max:2147483647'],
            'status' => ['nullable', 'in:active,inactive'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
            'image_meta' => ['nullable', 'json'],
            'remove_image_ids' => ['nullable', 'array', 'max:5'],
            'remove_image_ids.*' => ['integer', 'distinct'],
            'variation_weight' => $variationArrayRules,
            'variation_weight.*' => [...$variationValueRules, 'integer', 'min:1', 'max:1000000'],
            'variation_price' => $variationArrayRules,
            'variation_price.*' => [...$variationValueRules, 'decimal:0,2', 'min:0', 'max:9999999999999'],
            'variation_compare_at_price' => ['nullable', 'array', 'max:20'],
            'variation_compare_at_price.*' => ['nullable', 'decimal:0,2', 'min:0', 'max:9999999999999'],
            'variation_stock' => $variationArrayRules,
            'variation_stock.*' => [...$variationValueRules, 'integer', 'min:0', 'max:2147483647'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'compare_at_price' => $this->filled('compare_at_price') ? $this->input('compare_at_price') : null,
            'variation_compare_at_price' => array_map(
                fn ($value) => ($value === null || trim((string) $value) === '') ? null : $value,
                (array) $this->input('variation_compare_at_price', [])
            ),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateComparePrices($validator);
            $this->validateVariations($validator);
            $this->validateImageMetadata($validator);
        });
    }

    private function validateComparePrices(Validator $validator): void
    {
        if ($this->boolean('has_variations')) {
            return;
        }

        $price = $this->input('price');
        $compareAtPrice = $this->input('compare_at_price');

        if (is_numeric($price) && is_numeric($compareAtPrice) && (float) $compareAtPrice <= (float) $price) {
            $validator->errors()->add('compare_at_price', 'The compare-at price must be greater than the selling price.');
        }
    }

    private function validateVariations(Validator $validator): void
    {
        if (! $this->boolean('has_variations')) {
            return;
        }

        $weights = array_values((array) $this->input('variation_weight', []));
        $prices = array_values((array) $this->input('variation_price', []));
        $stocks = array_values((array) $this->input('variation_stock', []));
        $compareAtPrices = array_values((array) $this->input('variation_compare_at_price', []));

        if (count($weights) !== count($prices) || count($prices) !== count($stocks)) {
            $validator->errors()->add('variation_price', 'Each variation requires a weight, selling price, and stock value.');

            return;
        }

        $normalizedWeights = collect($weights)
            ->filter(fn ($weight) => is_numeric($weight))
            ->map(fn ($weight) => (int) $weight);

        if ($normalizedWeights->count() !== $normalizedWeights->unique()->count()) {
            $validator->errors()->add('variation_weight', 'Each variation weight must be unique.');
        }

        foreach ($prices as $index => $price) {
            $compareAtPrice = $compareAtPrices[$index] ?? null;

            if (is_numeric($price) && is_numeric($compareAtPrice) && (float) $compareAtPrice <= (float) $price) {
                $validator->errors()->add("variation_compare_at_price.$index", 'The compare-at price must be greater than the selling price.');
            }
        }
    }

    private function validateImageMetadata(Validator $validator): void
    {
        $rawMetadata = $this->input('image_meta');

        if ($rawMetadata === null || $rawMetadata === '') {
            return;
        }

        $metadata = json_decode($rawMetadata, true);

        if (! is_array($metadata) || ! array_is_list($metadata)) {
            return;
        }

        if (count($metadata) > 5) {
            $validator->errors()->add('image_meta', 'A product can have at most five images.');
        }

        $imageIds = [];
        $fileIndexes = [];
        $removeImageIds = collect((array) $this->input('remove_image_ids', []))
            ->filter(fn ($id) => filter_var($id, FILTER_VALIDATE_INT) !== false && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($metadata as $index => $item) {
            if (! is_array($item)) {
                $validator->errors()->add("image_meta.$index", 'Invalid image metadata.');

                continue;
            }

            $imageId = $item['id'] ?? null;
            $fileIndex = $item['file_index'] ?? null;

            if ($imageId === null && $fileIndex === null) {
                $validator->errors()->add("image_meta.$index", 'Each image must reference an existing image or uploaded file.');
            }

            if ($imageId !== null && (filter_var($imageId, FILTER_VALIDATE_INT) === false || (int) $imageId < 1)) {
                $validator->errors()->add("image_meta.$index.id", 'Invalid image reference.');
            } elseif ($imageId !== null) {
                $imageIds[] = (int) $imageId;
            }

            if ($fileIndex !== null && (filter_var($fileIndex, FILTER_VALIDATE_INT) === false || (int) $fileIndex < 0)) {
                $validator->errors()->add("image_meta.$index.file_index", 'Invalid uploaded image reference.');
            } elseif ($fileIndex !== null) {
                $fileIndexes[] = (int) $fileIndex;
            }

            if (isset($item['fit_mode']) && ! in_array($item['fit_mode'], ['cover', 'contain'], true)) {
                $validator->errors()->add("image_meta.$index.fit_mode", 'Invalid image fit mode.');
            }

            foreach (['focal_x', 'focal_y'] as $field) {
                if (isset($item[$field]) && (! is_numeric($item[$field]) || $item[$field] < 0 || $item[$field] > 100)) {
                    $validator->errors()->add("image_meta.$index.$field", 'Image focal points must be between 0 and 100.');
                }
            }

            if (isset($item['alt_text']) && (! is_string($item['alt_text']) || mb_strlen($item['alt_text']) > 255)) {
                $validator->errors()->add("image_meta.$index.alt_text", 'Image alt text must not exceed 255 characters.');
            }
        }

        if (count($imageIds) !== count(array_unique($imageIds))) {
            $validator->errors()->add('image_meta', 'Each existing image may only be included once.');
        }

        if (count($fileIndexes) !== count(array_unique($fileIndexes))) {
            $validator->errors()->add('image_meta', 'Each uploaded image may only be included once.');
        }

        if (array_intersect($imageIds, $removeImageIds) !== []) {
            $validator->errors()->add('remove_image_ids', 'An image cannot be kept and removed in the same update.');
        }

        $files = array_values($this->file('images', []));

        if ($fileIndexes !== [] && (max($fileIndexes) >= count($files) || count($fileIndexes) !== count($files))) {
            $validator->errors()->add('image_meta', 'Image metadata does not match the uploaded files.');
        }

        $routeValue = $this->route('id');

        if ($routeValue === null || ($imageIds === [] && $removeImageIds === [])) {
            return;
        }

        $product = AdminProduct::query()
            ->where('slug', $routeValue)
            ->orWhere('id', $routeValue)
            ->first();

        if (! $product) {
            return;
        }

        $allowedImageIds = $product->images()->pluck('id')->all();

        if (array_diff([...$imageIds, ...$removeImageIds], $allowedImageIds) !== []) {
            $validator->errors()->add('image_meta', 'One or more selected images do not belong to this product.');
        }
    }
}
