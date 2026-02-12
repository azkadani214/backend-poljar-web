<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'string', 'exists:users,id'],
            'division_id' => ['required', 'string', 'exists:divisions,id'],
            'position_id' => ['required', 'string', 'exists:positions,id'],
            'is_active' => ['nullable', 'boolean'],
            'period' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User harus dipilih',
            'user_id.exists' => 'User tidak ditemukan',
            'division_id.required' => 'Divisi harus dipilih',
            'division_id.exists' => 'Divisi tidak ditemukan',
            'position_id.required' => 'Posisi harus dipilih',
            'position_id.exists' => 'Posisi tidak ditemukan',
            'is_active.boolean' => 'Status aktif harus berupa true/false',
            'period.max' => 'Period maksimal 50 karakter',
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