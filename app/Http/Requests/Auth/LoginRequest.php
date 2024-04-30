<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    /**
     * Login Api Rules.
     *
     * @return array<string, Rule|string[]|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:dns'],
            'password' => ['required', 'string']
        ];
    }
}
