<?php

namespace App\Validation;

use App\Models\RegistrationOtp;
use Illuminate\Validation\Validator;

class EnsureOtpExpired
{
    public function __construct(private ?string $email) {}

    public function __invoke(Validator $validator): void
    {
        $pending = RegistrationOtp::where('email', $this->email)->firstOrFail();

        if ($pending->otp_expires_at && !$pending->otp_expires_at->isPast()) {
            $validator->errors()->add(
                'email',
                'OTP belum kedaluwarsa. Harap tunggu hingga masa berlaku berakhir sebelum meminta OTP baru.'
            );
        }
    }
}
