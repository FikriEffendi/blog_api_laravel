<?php

namespace App\Http\Requests;

use App\Models\RegistrationOtp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ConfirmOtpRequest extends FormRequest
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
            //
            'email' => ['required', 'email', 'exists:registration_otps,email'],
            'otp' => ['required', 'digits:6', 'exists:registration_otps,otp_code'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $email = $this->input('email');
                $otp = $this->input('otp');

                $pending = RegistrationOtp::where('email', $email)->where('otp_code', $otp)->firstOrFail();

                if ($pending?->otp_expires_at?->isPast()) {
                    $validator->errors()->add('otp', 'Kode OTP kedaluwarsa.');
                }
            },
        ];
    }
}
