<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [AuthController::class, 'register'])
    ->middleware('throttle:otp');
Route::post('auth/register/resend', [AuthController::class, 'resend'])
    ->middleware('throttle:otp');
Route::post('auth/register/confirm', [AuthController::class, 'confirm']);
Route::post('auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:login');
Route::post('auth/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
