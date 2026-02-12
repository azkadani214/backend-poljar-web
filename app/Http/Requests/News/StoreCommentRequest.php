<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'news_post_id' => ['required', 'string', 'exists:news_posts,id'],
            'comment' => ['required', 'string', 'max:1000'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'news_post_id.required' => 'Post ID harus diisi',
            'news_post_id.exists' => 'Post tidak ditemukan',
            'comment.required' => 'Komentar harus diisi',
            'comment.max' => 'Komentar maksimal 1000 karakter',
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