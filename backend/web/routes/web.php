<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\NewsFeedController;
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
    
    // Approve - Approve new volunteers (POST)
    Route::post('/volunteer/{volunteer}/approve', [VolunteerController::class, 'approve'])
        ->name('volunteer.approve');
    
    // Reject - Reject new volunteers (POST)
    Route::post('/volunteer/{volunteer}/reject', [VolunteerController::class, 'reject'])
        ->name('volunteer.reject');
    
    // Destroy - Delete volunteers (DELETE)
    Route::delete('/volunteer/{volunteer}', [VolunteerController::class, 'destroy'])
        ->name('volunteer.destroy');
});

// Request Resource Routes
Route::middleware(['auth'])->group(function () {
    // Index - Show all requests (GET)
    Route::get('/request', [RequestController::class, 'index'])
        ->name('request.index');
    
    // Update - Change request status (POST)
    Route::put('/request/{req}/update', [RequestController::class, 'update'])
        ->name('request.update');
    
    // Destroy - Delete request (DELETE)
    Route::delete('/request/{req}', [RequestController::class, 'destroy'])
        ->name('request.destroy');
});

// News Feed Resource Routes
Route::middleware(['auth'])->group(function () {
    // Index - Show all posts (GET)
    Route::get('/news-feed', [NewsFeedController::class, 'index'])
        ->name('news-feed.index');
    
    // Insert - Add new post (POST)
    Route::post('/news-feed/add', [NewsFeedController::class, 'store'])
        ->name('news-feed.store');
    
    // Edit - Edit post (POST)
    Route::get('/news-feed/{post}/edit', [NewsFeedController::class, 'edit'])
        ->name('news-feed.edit');
    
    // Update - Update post (POST)
    Route::post('/news-feed/{post}/update', [NewsFeedController::class, 'update'])
        ->name('news-feed.update');
    
    // Destroy - Delete request (DELETE)
    Route::delete('/news-feed/{post}', [NewsFeedController::class, 'destroy'])
        ->name('news-feed.destroy');
});

