<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmOtpRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendOtpRequest;
use App\Models\RegistrationOtp;
use App\Models\User;
use App\Services\OtpService;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function __construct(private RegistrationService $registrationService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $request->validated();

        $this->registrationService->initiateRegistration(
            $request->name,
            $request->email,
            $request->password,
        );

        return response()->json([
            'message' => 'OTP dikirim ke email. Silakan konfirmasi untuk menyelesaikan registrasi.',
        ], 201);
    }

    public function confirm(ConfirmOtpRequest $request): JsonResponse
    {
        $request->validated();

        $result = $this->registrationService->completeRegistration(
            $request->email,
            $request->otp,
        );

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'token' => $result['token'],
            'user' => $result['user'],
        ]);
    }

    public function resend(ResendOtpRequest $request): JsonResponse
    {
        $request->validated();

        $this->registrationService->resendOtp($request->email);

        return response()->json([
            'message' => 'OTP baru telah dikirim ke email.',
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $request->validated();

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        $token->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
