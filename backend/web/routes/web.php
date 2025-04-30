<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\VolunteerController;
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

    Route::get('/contribution/donation', [ContributionController::class, 'newDonation'])
        ->name('contribution.donation');    

    Route::get('/contribution/receive', [ContributionController::class, 'newReceive'])
        ->name('contribution.receive');
    
    // Store - Save new contribution (POST)
    Route::post('/contribution', [ContributionController::class, 'store'])
        ->name('contribution.store');
    
    // Edit - Show edit donation form (GET)
    Route::get('/contribution/{contribution}/editDonation', [ContributionController::class, 'editDonation'])
        ->name('contribution.editDonation');

    // Edit - Show edit receive form (GET)
    Route::get('/contribution/{contribution}/editReceive', [ContributionController::class, 'editReceive'])
        ->name('contribution.editReceive');
    
    // Update - Update existing contribution (PUT/PATCH)
    Route::put('/contribution/{contribution}', [ContributionController::class, 'update'])
        ->name('contribution.update');
    
    // Destroy - Delete contribution (DELETE)
    Route::delete('/contribution/{contribution}', [ContributionController::class, 'destroy'])
        ->name('contribution.destroy');
});

// Volunteers Resource Routes
Route::middleware(['auth'])->group(function () {
    // Index - Show all volunteers (GET)
    Route::get('/volunteer', [VolunteerController::class, 'index'])
        ->name('volunteer.index');
    
    // Store - Save new contribution (POST)
    Route::post('/volunteer', [VolunteerController::class, 'store'])
        ->name('volunteer.store');
    
    // Destroy - Delete contribution (DELETE)
    Route::delete('/volunteer/{volunteer}', [VolunteerController::class, 'destroy'])
        ->name('volunteer.destroy');
});

