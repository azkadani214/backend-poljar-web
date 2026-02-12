<?php

namespace App\Http\Requests\Position;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'division_id' => ['sometimes', 'required', 'string', 'exists:divisions,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'level' => ['sometimes', 'required', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'division_id.required' => 'Divisi harus dipilih',
            'division_id.exists' => 'Divisi tidak ditemukan',
            'name.required' => 'Nama posisi harus diisi',
            'name.max' => 'Nama posisi maksimal 255 karakter',
            'level.required' => 'Level harus diisi',
            'level.integer' => 'Level harus berupa angka',
            'level.min' => 'Level minimal 1',
            'level.max' => 'Level maksimal 10',
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