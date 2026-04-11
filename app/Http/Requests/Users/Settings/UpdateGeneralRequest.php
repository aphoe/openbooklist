<?php

namespace App\Http\Requests\Users\Settings;

use App\Services\LanguageOptionsService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGeneralRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email:dns,spoof,rfc',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'language' => ['required', 'string', Rule::in(app(LanguageOptionsService::class)->codes())],
        ];
    }
}
