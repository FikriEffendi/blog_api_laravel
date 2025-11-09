<?php

namespace App\Http\Requests;

use App\Validation\EnsureOtpExpired;
use Illuminate\Foundation\Http\FormRequest;

class ResendOtpRequest extends FormRequest
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
            'email' => 'required|email|exists:registration_otps,email',
        ];
    }

    public function after(): array
    {
        return [
            new EnsureOtpExpired($this->input('email')),
        ];
    }
}
