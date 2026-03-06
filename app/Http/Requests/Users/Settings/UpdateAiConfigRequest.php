<?php

namespace App\Http\Requests\Users\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAiConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow enabling if they actually have a key configured system-wide
        if ($this->input('use_ai_description') && ! config('project.openrouter_key')) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'use_ai_description' => ['required', 'boolean'],
        ];
    }
}
