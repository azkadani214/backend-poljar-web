<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:published,draft,scheduled'],
            'published_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'photo_alt_text' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:blog_categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['string', 'max:50'],
            'seo' => ['nullable', 'array'],
            'seo.meta_title' => ['nullable', 'string'],
            'seo.meta_description' => ['nullable', 'string'],
            'seo.keywords' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul artikel harus diisi',
            'body.required' => 'Konten artikel harus diisi',
            'status.required' => 'Status harus dipilih',
            'category_ids.*.exists' => 'Kategori tidak valid',
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
