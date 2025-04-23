<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Auth::routes([
    'register' => false, // Disable public registration
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
