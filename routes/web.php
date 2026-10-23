<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserController;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Route;

// Login, registration and profile routes are registered by Fortify and Jetstream.
Route::redirect('/', '/dashboard');

Route::middleware('guest')->prefix('auth/{provider}')->whereIn('provider', SocialAccount::PROVIDERS)->name('social.')->group(function () {
    Route::get('redirect', [SocialiteController::class, 'redirect'])->name('redirect');
    Route::get('callback', [SocialiteController::class, 'callback'])->name('callback');
});

// Confirms the email by code when the provider did not share a verified one.
Route::middleware('guest')->prefix('auth/social')->name('social.')->group(function () {
    Route::get('email', [SocialiteController::class, 'emailForm'])->name('email');
    Route::post('email', [SocialiteController::class, 'sendCode'])->middleware('throttle:5,1')->name('email.send');
    Route::get('verify', [SocialiteController::class, 'codeForm'])->name('verify');
    Route::post('verify', [SocialiteController::class, 'verifyCode'])->middleware('throttle:10,1')->name('verify.check');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('blog', [BlogController::class, 'index'])->name('blog.index');

    Route::prefix('admin')->name('admin.')->group(function () {
        // Every role: write publications and comment on them.
        Route::redirect('profile', '/user/profile')->name('profile');
        Route::get('publications/mine', [PublicationController::class, 'mine'])->name('publications.mine');
        Route::resource('publications', PublicationController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::post('publications/{publication}/comments', [CommentController::class, 'store'])->name('publications.comments.store');

        // Administrators and editors: organize content and see how it performs.
        Route::middleware('role:admin,editor')->group(function () {
            Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');
            Route::get('publications/export', [PublicationController::class, 'export'])->name('publications.export');

            Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])->withTrashed()->name('categories.restore');
            Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        // Administrators only: people, permissions and deleted records.
        Route::middleware('role:admin')->group(function () {
            Route::get('recycle-bin', [RecycleBinController::class, 'index'])->name('recycle-bin');

            Route::get('users/hidden', [UserController::class, 'hidden'])->name('users.hidden');
            Route::patch('users/{user}/restore', [UserController::class, 'restore'])->withTrashed()->name('users.restore');
            Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

            Route::patch('roles/{role}/restore', [RoleController::class, 'restore'])->withTrashed()->name('roles.restore');
            Route::resource('roles', RoleController::class)->only(['index', 'store', 'update', 'destroy']);
        });
    });
});
