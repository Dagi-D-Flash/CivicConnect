<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/email', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/password/reset', [PasswordResetController::class, 'reset']);
});

// Protected Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    // User Profile
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
    Route::put('/user/password', [UserController::class, 'changePassword']);
    
    // User Dashboard
    Route::get('/user/issues', [UserController::class, 'myIssues']);
    
    // Comments (authenticated)
    Route::post('/issues/{issue}/comments', [App\Http\Controllers\Api\CommentController::class, 'store']);
    Route::put('/comments/{comment}', [App\Http\Controllers\Api\CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [App\Http\Controllers\Api\CommentController::class, 'destroy']);
    
    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    
    // Photos (authenticated)
    Route::post('/issues/{issue}/photos', [App\Http\Controllers\Api\PhotoController::class, 'store']);
    Route::put('/photos/{photo}', [App\Http\Controllers\Api\PhotoController::class, 'update']);
    Route::post('/photos/{photo}/crop', [App\Http\Controllers\Api\PhotoController::class, 'crop']);
    Route::delete('/photos/{photo}', [App\Http\Controllers\Api\PhotoController::class, 'destroy']);
    Route::post('/issues/{issue}/photos/reorder', [App\Http\Controllers\Api\PhotoController::class, 'reorder']);
    
    // Department Portal (authenticated)
    Route::get('/departments/{department}/dashboard', [App\Http\Controllers\Api\DepartmentController::class, 'dashboard']);
    Route::post('/departments/{department}/issues/{issue}/assign', [App\Http\Controllers\Api\DepartmentController::class, 'assignIssue']);
    Route::put('/departments/{department}/issues/{issue}/status', [App\Http\Controllers\Api\DepartmentController::class, 'updateIssueStatus']);
    
    // Export (authenticated)
    Route::get('/export/pdf', [App\Http\Controllers\Api\ExportController::class, 'pdf']);
    Route::get('/export/excel', [App\Http\Controllers\Api\ExportController::class, 'excel']);
    Route::get('/export/csv', [App\Http\Controllers\Api\ExportController::class, 'csv']);
});

// Public Routes
Route::get('/issues/{issue}/comments', [App\Http\Controllers\Api\CommentController::class, 'index']);
Route::post('/issues/{issue}/vote', [App\Http\Controllers\Api\VoteController::class, 'toggle']);
Route::get('/issues/{issue}/has-voted', [App\Http\Controllers\Api\VoteController::class, 'hasVoted']);
Route::get('/issues/{issue}/voters', [App\Http\Controllers\Api\VoteController::class, 'voters']);
Route::get('/issues/{issue}/photos', [App\Http\Controllers\Api\PhotoController::class, 'index']);

// Departments (public)
Route::get('/departments', [App\Http\Controllers\Api\DepartmentController::class, 'index']);
Route::get('/departments/{department}', [App\Http\Controllers\Api\DepartmentController::class, 'show']);

// Analytics (public)
Route::get('/analytics/dashboard', [App\Http\Controllers\Api\AnalyticsController::class, 'dashboard']);
Route::get('/analytics/resolution-time', [App\Http\Controllers\Api\AnalyticsController::class, 'resolutionTime']);

// Map & Clustering (public)
Route::get('/map/clusters', [App\Http\Controllers\Api\MapController::class, 'clusters']);
Route::get('/map/nearby', [App\Http\Controllers\Api\MapController::class, 'nearby']);
Route::get('/map/within-bounds', [App\Http\Controllers\Api\MapController::class, 'withinBounds']);
