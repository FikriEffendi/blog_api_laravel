<?php

use App\Http\Controllers\AuthController;
use App\Http\Requests\RegisterRequest;
use App\Models\RegistrationOtp;
use App\Services\RegistrationService;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Providers\AppServiceProvider;

uses(TestCase::class);

describe('register', function () {

    it('mengembalikan 201 dan memanggil initiateRegistration dengan data yang benar', function () {
        $service = Mockery::mock(RegistrationService::class);
        $service->shouldReceive('initiateRegistration')
            ->once()
            ->with('John Doe', 'john@example.com', 'Password123');

        $controller = new AuthController($service);

        $request = new class extends RegisterRequest {
            public function rules(): array
            {
                return [];
            }
            public function after(): array
            {
                return [];
            }
        };
        $request->setContainer(app());
        $request->setMethod('POST');
        $request->replace([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $request->validateResolved();

        $response = $controller->register($request);

        expect($response->status())->toBe(201)
            ->and($response->getData(true)['message'])->toBe('OTP dikirim ke email. Silakan konfirmasi untuk menyelesaikan registrasi.');
    });

    it('user unique email', function () {
        $request = new RegisterRequest();
        $rules = $request->rules();
        expect($rules['email'])->toContain('unique:users,email');
    });

    it('rate limit register', function () {
        $provider = new AppServiceProvider(app());
        $provider->boot();

        $request = Request::create('/register', 'POST');
        $request->server->set('REMOTE_ADDR', '203.0.113.10');

        $limiter = RateLimiter::limiter('otp');
        $limit = $limiter($request);
        if (is_array($limit)) {
            $limit = $limit[0];
        }

        expect($limit)->toBeInstanceOf(Limit::class);
        expect($limit->maxAttempts)->toBe(3)
            ->and($limit->decaySeconds)->toBe(30 * 60)
            ->and($limit->key)->toBe('203.0.113.10');
    });
});
