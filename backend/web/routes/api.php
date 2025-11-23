<?php

use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\NewsFeedController;
use App\Http\Controllers\API\ContributionController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\VContributionController;
use App\Http\Controllers\API\CContributionController;
use App\Http\Controllers\API\OContributionController;
use App\Http\Controllers\API\ApplyController;
use App\Http\Controllers\API\ChatController;
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
    Route::get('/search/chat', [HomeController::class, 'searchChat'])->name('search.chats');

});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/messages/chat/{receiverId}', [ChatController::class, 'chat'])->name('chats');
    Route::post('/messages/send', [ChatController::class, 'send'])->name('sends');
    Route::get('/messages/ui/{receiverId}', [ChatController::class, 'ui'])->name('uis');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/updateUser', [HomeController::class, 'update']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/volunteer', [HomeController::class, 'fetchCurrentVolunteer']);
    Route::post('/updateVolunteer', [HomeController::class, 'updateVolunteer']);
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
    Route::post('/applyCenter/{userId}', [ApplyController::class, 'indexCenter']);
    Route::post('/applyOrg/{userId}', [ApplyController::class, 'indexOrg']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/contributions/user/', [ContributionController::class, 'indexUser']);
    Route::get('/contributions/vol/{userId}', [VContributionController::class, 'index']);
    Route::get('/contributions/center/{userId}', [CContributionController::class, 'index']);
    Route::get('/contributions/org/{userId}', [OContributionController::class, 'index']);
    Route::delete('/contributions/{contribution}', [ContributionController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/requests/user/', [RequestController::class, 'index']);
    Route::post('/add_requests/user/', [RequestController::class, 'store']);
    Route::put('/requests/user/{id}', [RequestController::class, 'update']);
    Route::delete('/requests/{request}', [RequestController::class, 'destroy']);
});
