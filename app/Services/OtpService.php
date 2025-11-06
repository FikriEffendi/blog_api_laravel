<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class OtpService
{
    public function generateCode(int $length = 6): string
    {
        return str_pad((string) random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);
    }

    public function send(string $email, string $code, int $ttlMinutes = 10): void
    {
        Mail::to($email)->send(new OtpMail($code, $ttlMinutes));
    }
}
