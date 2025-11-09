<?php

namespace App\Http\Requests;

use App\Models\RegistrationOtp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $email = $this->input('email');

                $pending = RegistrationOtp::where('email', $email)->firstOrFail();

                // Jika ada OTP aktif (belum kedaluwarsa) untuk email yang sama, blok proses register
                if ($pending->otp_expires_at && !$pending->otp_expires_at->isPast()) {
                    $validator->errors()->add(
                        'email',
                        'OTP belum kedaluwarsa. Harap tunggu hingga masa berlaku berakhir sebelum meminta OTP baru.'
                    );
                }
            },
        ];
    }
}
