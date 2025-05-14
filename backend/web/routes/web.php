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
use App\Http\Controllers\Admin\OrganizationController as AdminOrganizationController;
use App\Http\Controllers\Admin\ContributionController as AdminContributionController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Organization\HomeController as OrgHomeController;
use App\Http\Controllers\Organization\VolunteerController as OrgVolunteerController;
use App\Http\Controllers\Organization\ContributionController as OrgContributionController;
use App\Http\Controllers\VContributionController as VolContributionController;
use App\Http\Controllers\RiverController;
use App\Http\Controllers\FloodAlertController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
// Route::get('/river', [RiverController::class, 'fetchRiversData'])->name('river.fetch');

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

Auth::routes([
    'register' => false, // Disable public registration
]);


Route::post('/update-location', [LocationController::class, 'updateLocation'])->middleware('auth');

// Search Resource Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::post('/search', [SearchController::class, 'search'])->name('search.perform');
});

// News Feed Resource Routes
Route::middleware(['auth'])->group(function () {
    // Index - Show all posts (GET)
    Route::get('/news-feed', [NewsFeedController::class, 'index'])
        ->name('news-feed.index');
});



// Relief Center Routes



Route::middleware(['auth', 'role:Relief Center'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

Route::middleware(['auth', 'role: Relief Center'])->group(function () {
    Route::post('/relief_center/update-center', [HomeController::class, 'updateCenter'])->name('relief_center.updateCenter');
    Route::post('/relief_center/update-user', [HomeController::class, 'updateUser'])->name('relief_center.updateUser');
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
Route::middleware(['auth', 'role:Relief Center'])->group(function () {
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
Route::middleware(['auth', 'role:Relief Center'])->group(function () {
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

// Profile Resource Routes
Route::middleware(['auth', 'role:Relief Center'])->group(function () {
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

Route::middleware(['auth', 'role: Administrator'])->group(function () {
    Route::post('/admin/update-user', [AdminHomeController::class, 'updateUser'])->name('admin.updateUser');
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
    Route::get('/org/admin', [AdminOrganizationController::class, 'index'])->name('admin.organizations');
    Route::put('/admin/org/{org}/updateType', [AdminOrganizationController::class, 'updateType'])->name('admin.organizations.updateType');
    Route::delete('/admin/org/{org}/delete', [AdminOrganizationController::class, 'destroy'])->name('admin.organizations.destroy');
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

Route::middleware(['auth', 'role:Administrator'])->group(function () {
    Route::get('/alerts/admin', [FloodAlertController::class, 'index'])->name('admin.alerts');
    Route::get('/check-flood/admin', [FloodAlertController::class, 'checkFlood'])->name('admin.checkFloods');
    Route::delete('/admin/alerts/{alert}', [FloodAlertController::class, 'destroy'])->name('admin.alerts.destroy');
});



// Organization Routes



Route::middleware(['auth', 'role:Organization'])->group(function () {
    Route::get('/home/organization', [OrgHomeController::class, 'index'])->name('org.home');
});

Route::middleware(['auth', 'role:Organization'])->group(function () {
    Route::post('/org/update-org', [OrgHomeController::class, 'updateOrg'])->name('org.updateOrg');
    Route::post('/org/update-user', [OrgHomeController::class, 'updateUser'])->name('org.updateUser');
});


// Volunteers Resource Routes
Route::middleware(['auth', 'role:Organization'])->group(function () {
    Route::get('/volunteer/org', [OrgVolunteerController::class, 'index'])
        ->name('volunteers.index');
    
    Route::post('/org/volunteer/{volunteer}/approve', [OrgVolunteerController::class, 'approve'])
        ->name('volunteers.approve');
    
    Route::post('/org/volunteer/{volunteer}/reject', [OrgVolunteerController::class, 'reject'])
        ->name('volunteers.reject');
    
    Route::delete('/org/volunteer/{volunteer}', [OrgVolunteerController::class, 'destroy'])
        ->name('volunteers.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/contributions/org/{userId}', [OrgContributionController::class, 'index'])->name('contributions.index');
    Route::delete('/org/contributions/{contribution}', [OrgContributionController::class, 'destroy'])->name('contributions.destroy');
});



// Volunteer Routes



Route::middleware(['auth'])->group(function () {
    Route::get('/contributions/vol/{userId}', [VolContributionController::class, 'index'])->name('contributionv.index');
    Route::delete('/vol/contributions/{contribution}', [VolContributionController::class, 'destroy'])->name('contributionv.destroy');
});
