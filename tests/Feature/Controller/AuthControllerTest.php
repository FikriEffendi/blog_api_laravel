<?php

use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

describe('AuthController @register', function () {

    it('normal kondisi', function () {
        Mail::fake();

        $form = [
            'name' => "Fikri",
            'email' => "Fikri@gmail.com",
            'password' => "123456789",
            'password_confirmation' => "123456789"
        ];

        $this->postJson(route('auth.register'), $form)
            ->assertStatus(201)
            ->assertJson([
                'message' => 'OTP dikirim ke email. Silakan konfirmasi untuk menyelesaikan registrasi.'
            ]);

        $this->assertDatabaseHas('registration_otps', [
            'email' => $form['email'],
            'name' => $form['name'],
        ]);

        $this->assertDatabaseCount('registration_otps', 1);
    });
});
