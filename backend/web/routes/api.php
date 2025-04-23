<?php

use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Registration route
Route::post('/register', [RegisterController::class, 'register']);

// Login route
Route::post('/login', [LoginController::class, 'login']);

// Forgot password route
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);


// Protected route (requires Sanctum authentication)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
