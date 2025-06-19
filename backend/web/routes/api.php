<?php

use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\NewsFeedController;
use App\Http\Controllers\API\ContributionController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\RequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/search', [HomeController::class, 'search']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/updateUser', [HomeController::class, 'update']);
});

Route::middleware('auth:sanctum')->get('/notifications', function (Request $request) {
    return response()->json($request->user()->unreadNotifications);
});

Route::middleware('auth:sanctum')->get('/notifications/read/{id}', function (Request $request, $id) {
    $notification = $request->user()->unreadNotifications()->find($id);
    if ($notification) {
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read.']);
    }
    return response()->json(['error' => 'Notification not found.'], 404);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/news-feed', [NewsFeedController::class, 'index']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/contributions/user/', [ContributionController::class, 'indexUser']);
    Route::delete('/contributions/{contribution}', [ContributionController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/requests/user/', [RequestController::class, 'index']);
    Route::post('/add_requests/user/', [RequestController::class, 'store']);
    Route::put('/requests/user/{id}', [RequestController::class, 'update']);
    Route::delete('/requests/{request}', [RequestController::class, 'destroy']);
});
