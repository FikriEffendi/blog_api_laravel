<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/register/confirm', [AuthController::class, 'confirm']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
