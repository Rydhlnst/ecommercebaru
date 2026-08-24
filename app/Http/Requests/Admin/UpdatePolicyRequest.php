<?php

namespace App\Http\Requests\Admin;

use App\Support\PolicyPages;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    public function rules(): array
    {
        return collect(PolicyPages::settingKeys())
            ->mapWithKeys(fn (string $key) => [$key => ['required', 'string', 'max:65535']])
            ->all();
    }

    protected function prepareForValidation(): void
    {
        $this->merge(
            collect(PolicyPages::settingKeys())
                ->mapWithKeys(fn (string $key) => [$key => PolicyPages::sanitize((string) $this->input($key, ''))])
                ->all()
        );
    }
}
