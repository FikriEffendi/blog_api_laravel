<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmOtpRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\RegistrationOtp;
use App\Models\User;
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
        $request->validated();

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

    public function confirm(ConfirmOtpRequest $request): JsonResponse
    {
        $request->validated();

        $pending = RegistrationOtp::where('email', $request->email)->firstOrFail();

        if ($pending->otp_code !== $request->otp) {
            return response()->json(['message' => 'Kode OTP salah.'], 422);
        }

        if ($pending->otp_expires_at->isPast()) {
            return response()->json(['message' => 'Kode OTP kedaluwarsa.'], 422);
        }

        $user = new User([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password, // hashed; cast 'hashed' tidak akan rehash
        ]);
        $user->email_verified_at = now();
        $user->save();

        $pending->delete();

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'token' => $token,
            'user' => $user,
        ]);
    }
}
