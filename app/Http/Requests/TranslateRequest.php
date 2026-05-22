<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => 'required|string',
            'source' => 'nullable|string|in:ar,id,en',
            'target' => 'nullable|string|in:ar,id,en',
            'force' => 'nullable|boolean',
        ];
    }
}
