<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'desc' => ['nullable', 'string'],
            'stocks' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'srp' => ['required', 'numeric', 'min:0.01'],
            'expired_at' => ['nullable'],
            'procurement' => ['required', 'integer'],
            'cover_image' => ['nullable', 'image', 'max:2048']            
        ];
    }
}
