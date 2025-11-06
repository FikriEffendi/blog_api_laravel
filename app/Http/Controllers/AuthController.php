<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\RegistrationOtp;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $code = $this->otpService->generateCode(6);
        $expiresAt = Carbon::now()->addMinutes(10);

        RegistrationOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'otp_code' => $code,
                'otp_expires_at' => $expiresAt,
            ]
        );

        $this->otpService->send($request->email, $code, 10);

        return response()->json([
            'message' => 'OTP dikirim ke email. Silakan konfirmasi untuk menyelesaikan registrasi.',
        ], 201);
    }

    public function confirm()
    {
        // 
    }
}
