<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Login, registration and profile routes are registered by Fortify and Jetstream.
Route::redirect('/', '/dashboard');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('blog', [BlogController::class, 'index'])->name('blog.index');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::redirect('profile', '/user/profile')->name('profile');

        Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');

        Route::get('recycle-bin', [RecycleBinController::class, 'index'])->name('recycle-bin');

        Route::get('publications/export', [PublicationController::class, 'export'])->name('publications.export');
        Route::resource('publications', PublicationController::class)->only(['index', 'create', 'store']);
        Route::post('publications/{publication}/comments', [CommentController::class, 'store'])->name('publications.comments.store');

        Route::get('users/hidden', [UserController::class, 'hidden'])->name('users.hidden');
        Route::patch('users/{user}/restore', [UserController::class, 'restore'])->withTrashed()->name('users.restore');
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::patch('roles/{role}/restore', [RoleController::class, 'restore'])->withTrashed()->name('roles.restore');
        Route::resource('roles', RoleController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])->withTrashed()->name('categories.restore');
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    });
});
