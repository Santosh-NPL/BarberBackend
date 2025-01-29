<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RegisterUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
//user login
Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->post('/logout', AuthController::class);

//forget password
Route::post('/verify-mobile', [AuthController::class, 'verifyMobile']);
Route::post('/otp-verify', [AuthController::class, 'OtpVerify']);
Route::post('/update-password', [AuthController::class, 'updatePassword']);

// register new company
Route::post('register', [RegisterUserController::class, 'register']);
