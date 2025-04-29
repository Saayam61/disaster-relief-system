<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContributionController;
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

// Contributions Resource Routes
Route::middleware(['auth'])->group(function () {
    // Index - Show all contributions (GET)
    Route::get('/contribution', [ContributionController::class, 'index'])
        ->name('contribution.index');
    
    // Store - Save new contribution (POST)
    Route::post('/contribution', [ContributionController::class, 'store'])
        ->name('contribution.store');

    // Store - Save new received contribution (POST)
    Route::post('/contributionR', [ContributionController::class, 'storeR'])
        ->name('contribution.storeR');
    
    // Edit - Show edit form (GET)
    Route::get('/contribution/{contribution}/edit', [ContributionController::class, 'edit'])
        ->name('contribution.edit');
    
    // Update - Update existing contribution (PUT/PATCH)
    Route::put('/contribution/{contribution}', [ContributionController::class, 'update'])
        ->name('contribution.update');
    
    // Destroy - Delete contribution (DELETE)
    Route::delete('/contribution/{contribution}', [ContributionController::class, 'destroy'])
        ->name('contribution.destroy');
});

