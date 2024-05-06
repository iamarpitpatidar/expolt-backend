<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()?->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $this->route('user')->id],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['nullable', 'integer', 'in:0,1'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ];
    }
}
