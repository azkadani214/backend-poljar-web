<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreNewsPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news_posts,slug'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:published,draft,scheduled'],
            'published_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'photo_alt_text' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:news_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'seo' => ['nullable', 'array'],
            'seo.meta_title' => ['nullable', 'string', 'max:60'],
            'seo.meta_description' => ['nullable', 'string', 'max:160'],
            'seo.keywords' => ['nullable', 'array'],
            'seo.keywords.*' => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul harus diisi',
            'slug.unique' => 'Slug sudah digunakan',
            'body.required' => 'Konten harus diisi',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
            'scheduled_for.after' => 'Jadwal publish harus di masa depan',
            'cover_photo.image' => 'File harus berupa gambar',
            'cover_photo.mimes' => 'Format gambar: jpg, jpeg, png, webp',
            'cover_photo.max' => 'Ukuran gambar maksimal 2MB',
            'categories.*.exists' => 'Kategori tidak valid',
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
