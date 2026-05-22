<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchVerbRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'verb' => 'required|string|min:1|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'verb.required' => 'Parameter verb is required',
        ];
    }
}
