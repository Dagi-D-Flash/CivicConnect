<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// Public routes - Homepage with issues map
Route::get('/', [IssueController::class, 'index'])->name('home');
Route::get('/issues', [IssueController::class, 'issuesBoard'])->name('issues.index');
Route::get('/issues/{issue}', [IssueController::class, 'show'])->name('issues.show');
Route::get('/report', [IssueController::class, 'create'])->name('issues.create');
Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');

// Search routes
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/search/popular', [SearchController::class, 'popular'])->name('search.popular');

// Voting routes (public - allows anonymous voting by IP)
Route::post('/issues/{issue}/vote', [VoteController::class, 'toggle'])->name('issues.vote');
Route::get('/issues/{issue}/voters', [VoteController::class, 'voters'])->name('issues.voters');
Route::get('/issues/{issue}/has-voted', [VoteController::class, 'hasVoted'])->name('issues.hasVoted');

// Comment routes (public viewing, auth required for posting)
Route::get('/issues/{issue}/comments', [CommentController::class, 'index'])->name('comments.index');
Route::post('/issues/{issue}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');
Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update')->middleware('auth');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth');

// User Dashboard routes (requires authentication)
Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/my-issues', [DashboardController::class, 'myIssues'])->name('myIssues');
    Route::get('/my-votes', [DashboardController::class, 'myVotes'])->name('myVotes');
    Route::get('/my-comments', [DashboardController::class, 'myComments'])->name('myComments');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
});

// Breeze Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/edit', [ProfileController::class, 'update'])->name('profile.update.breeze');
    Route::delete('/profile/edit', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API routes for external integrations
Route::prefix('api')->group(function () {
    Route::get('/issues', [IssueController::class, 'apiIndex']);
    Route::get('/issues/{issue}', [IssueController::class, 'apiShow']);
    Route::get('/categories', [IssueController::class, 'apiCategories']);
    
    // Search API
    Route::get('/search', [SearchController::class, 'index']);
    
    // Voting API
    Route::post('/issues/{issue}/vote', [VoteController::class, 'toggle']);
    Route::get('/issues/{issue}/votes', [VoteController::class, 'voters']);
    
    // Comments API
    Route::get('/issues/{issue}/comments', [CommentController::class, 'index']);
    Route::post('/issues/{issue}/comments', [CommentController::class, 'store'])->middleware('auth');
});

require __DIR__.'/auth.php';
