<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Auth Routes
 */
Route::prefix('auth')->controller(AuthController::class)->name('auth.')->group(function () {
    Route::post('register', 'register')->name('register')
        ->middleware('throttle:otp');
    Route::post('register/resend', 'resend')->name('register.resend')
        ->middleware('throttle:otp');
    Route::post('register/confirm', 'confirm')->name('register.confirm');
    Route::post('login', 'login')->name('login')
        ->middleware('throttle:login');
    Route::post('logout', 'logout')->name('logout')
        ->middleware('auth:sanctum');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
