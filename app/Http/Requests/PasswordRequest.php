<?php

namespace App\Http\Requests;

use App\Rules\CurrentPasswordCheckRule;
use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => ['required', 'min:6', new CurrentPasswordCheckRule],
            'password' => [
                'required',
                'min:8',          // Minimum length of 8 characters
                'confirmed',      // Must match password confirmation
                'different:old_password',  // New password must be different from old password
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])/', // Requires at least one uppercase letter, one lowercase letter, one digit, and one special character
            ],
            'password_confirmation' => ['required', 'min:8'],  // Password confirmation should also have a minimum length
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'old_password' => __('current password'),
        ];
    }
}
