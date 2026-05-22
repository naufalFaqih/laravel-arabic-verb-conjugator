<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchTranslateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'texts' => 'required|array',
            'texts.*' => 'required|string',
            'source' => 'nullable|string|in:ar,id,en',
            'target' => 'nullable|string|in:ar,id,en',
        ];
    }
}
