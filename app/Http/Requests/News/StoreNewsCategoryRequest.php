<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateNewsCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category') ?? $this->route('id');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:155', Rule::unique('news_categories')->ignore($categoryId)],
            'slug' => ['nullable', 'string', 'max:155', Rule::unique('news_categories')->ignore($categoryId)],
            'color' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori harus diisi',
            'name.unique' => 'Nama kategori sudah ada',
            'name.max' => 'Nama kategori maksimal 155 karakter',
            'slug.unique' => 'Slug sudah digunakan',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}