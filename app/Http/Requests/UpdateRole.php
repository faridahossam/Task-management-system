<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRole extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string',
            'permissions_ids' => 'nullable|array',
            'permissions_ids.*' => 'nullable|exists:permissions,id',
            'user_ids' => 'array|nullable',
            'users_ids.*' => 'nullable|exists:users,id',
        ];
    }
}
