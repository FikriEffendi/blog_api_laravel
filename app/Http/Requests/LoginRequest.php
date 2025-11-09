<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $email = $this->input('email');
                $password = $this->input('password');

                $user = User::where('email', $email)->firstOrFail();

                if ($user && !Hash::check($password, $user->password)) {
                    $validator->errors()->add('password', 'Password salah.');
                }
            },
        ];
    }
}
