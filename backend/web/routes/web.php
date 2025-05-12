<?php

use App\Http\Controllers\ReliefCenter\HomeController;
use App\Http\Controllers\ReliefCenter\ContributionController;
use App\Http\Controllers\ReliefCenter\VolunteerController;
use App\Http\Controllers\ReliefCenter\RequestController;
use App\Http\Controllers\ReliefCenter\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NewsFeedController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReliefCenterController as AdminReliefCenterController;
use App\Http\Controllers\Admin\VolunteerController as AdminVolunteerController;
use App\Http\Controllers\Admin\ContributionController as AdminContributionController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Auth::routes([
    'register' => false, // Disable public registration
]);


Route::post('/update-location', [LocationController::class, 'updateLocation'])->middleware('auth');


// Relief Center Routes



Route::middleware(['auth', 'role:Relief Center'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

// Search Resource Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::post('/search', [SearchController::class, 'search'])->name('search.perform');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/profile/update-profile', [HomeController::class, 'updateProfile'])->name('profile.updateProfile');
    Route::post('/profile/update-user', [HomeController::class, 'updateUser'])->name('profile.updateUser');
});

// Contributions Resource Routes
Route::middleware(['auth'])->group(function () {

    Route::get('/contribution/donation', [ContributionController::class, 'newDonation'])
        ->name('contribution.donation');    

    Route::get('/contribution/receive', [ContributionController::class, 'newReceive'])
        ->name('contribution.receive');
    
    // Store - Save new contribution (POST)
    Route::post('/contribution', [ContributionController::class, 'store'])
        ->name('contribution.store');
    
        // Index - Show all contributions (GET)
        Route::get('/contribution/{userId}', [ContributionController::class, 'index'])
        ->name('contribution.index');
    
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
});

// Profile Resource Routes
Route::middleware(['auth'])->group(function () {
    // Index - Show all posts (GET)
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/profile/{centerId}', [NewsFeedController::class, 'profile'])
        ->name('center.profile');

    
    // Insert - Add new post (POST)
    Route::post('/profile/add', [ProfileController::class, 'store'])
        ->name('profile.store');
    
    // Edit - Edit post (POST)
    Route::get('/profile/{post}/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    
    // Update - Update post (POST)
    Route::post('/profile/{post}/update', [ProfileController::class, 'update'])
        ->name('profile.update');
    
    // Destroy - Delete request (DELETE)
    Route::delete('/profile/{post}', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});



// Admin Routes



Route::middleware(['auth', 'role:Administrator'])->group(function () {
    Route::get('/home/admin', [AdminHomeController::class, 'index'])->name('admin.home');
});

Route::middleware(['auth', 'role:Administrator'])->group(function () {
    Route::get('/users/admin', [AdminUserController::class, 'index'])->name('admin.users');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

Route::middleware(['auth', 'role:Administrator'])->group(function () {
    Route::get('/rc/admin', [AdminReliefCenterController::class, 'index'])->name('admin.reliefcenters');
    Route::delete('/admin/rc/{rc}', [AdminReliefCenterController::class, 'destroy'])->name('admin.reliefcenters.destroy');
});

Route::middleware(['auth', 'role:Administrator'])->group(function () {
    Route::get('/vol/admin', [AdminVolunteerController::class, 'index'])->name('admin.volunteers');
    Route::put('/admin/vol/{vol}/updateRC', [AdminVolunteerController::class, 'updateRC'])->name('admin.volunteers.updateRC');
    Route::put('/admin/vol/{vol}/updateOrg', [AdminVolunteerController::class, 'updateOrg'])->name('admin.volunteers.updateOrg');
    Route::delete('/admin/vol/{vol}/delete', [AdminVolunteerController::class, 'destroy'])->name('admin.volunteers.destroy');
});

Route::middleware(['auth', 'role:Administrator'])->group(function () {
    Route::get('/contributions/admin', [AdminContributionController::class, 'index'])->name('admin.contributions');
    Route::delete('/admin/contributions/{contribution}', [AdminContributionController::class, 'destroy'])->name('admin.contributions.destroy');
});

Route::middleware(['auth', 'role:Administrator'])->group(function () {
    Route::get('/posts/admin', [AdminPostController::class, 'index'])->name('admin.posts');
    Route::delete('/admin/posts/{post}', [AdminPostController::class, 'destroy'])->name('admin.posts.destroy');
});

Route::middleware(['auth', 'role:Administrator'])->group(function () {
    Route::get('/requests/admin', [AdminRequestController::class, 'index'])->name('admin.requests');
    Route::delete('/admin/requests/{req}', [AdminRequestController::class, 'destroy'])->name('admin.requests.destroy');
});