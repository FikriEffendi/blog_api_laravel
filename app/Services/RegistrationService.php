<?php

namespace App\Services;

use App\Models\RegistrationOtp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Cache;

class RegistrationService
{

    public function initiateRegistration(string $name, string $email, string $password): void
    {
        $code = str_pad((string) random_int(0, 10 ** 6 - 1), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(1);

        RegistrationOtp::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'otp_code' => $code,
                'otp_expires_at' => $expiresAt,
            ]
        );

        Mail::to($email)->queue(new OtpMail($code, 1));
    }

    public function completeRegistration(string $email, string $otp): array
    {
        $pending = RegistrationOtp::where('email', $email)->firstOrFail();

        if ($pending->otp_code !== $otp) {
            throw new InvalidArgumentException('Kode OTP salah.');
        }

        if ($pending->otp_expires_at->isPast()) {
            throw new InvalidArgumentException('Kode OTP kedaluwarsa.');
        }

        $user = new User([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
        ]);
        $user->email_verified_at = now();
        $user->save();

        $pending->delete();

        $token = $user->createToken('auth')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function resendOtp(string $email): void
    {
        $pending = RegistrationOtp::where('email', $email)->firstOrFail();

        // Hanya boleh resend jika OTP sebelumnya sudah expired
        if ($pending->otp_expires_at && !$pending->otp_expires_at->isPast()) {
            throw new InvalidArgumentException('OTP belum kedaluwarsa. Harap tunggu hingga masa berlaku berakhir sebelum meminta OTP baru.');
        }

        $code = str_pad((string) random_int(0, 10 ** 6 - 1), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(1);

        $pending->update([
            'otp_code' => $code,
            'otp_expires_at' => $expiresAt,
        ]);

        Mail::to($email)->queue(new OtpMail($code, 1));
    }
}
