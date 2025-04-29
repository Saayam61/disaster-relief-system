<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Auth::routes([
    'register' => false, // Disable public registration
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::post('/profile/update-profile', [HomeController::class, 'updateProfile'])->name('profile.updateProfile');
    Route::post('/profile/update-user', [HomeController::class, 'updateUser'])->name('profile.updateUser');
});

