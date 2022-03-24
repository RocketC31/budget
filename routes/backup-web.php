<?php

use App\Http\Controllers\ImportController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\SpaceInviteController;
use App\Http\Controllers\TranslationsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest']], function () {
    Route::get('/reset_password', [ResetPasswordController::class, 'get'])->name('reset_password');
    Route::post('/reset_password', [ResetPasswordController::class, 'post']);
});

Route::group(['middleware' => ['auth']], function () {
    //Route::get('/dashboard', DashboardController::class)->name('dashboard');







    Route::name('space_invites.')->group(function () {
        Route::get('/spaces/{space}/invites/{invite}', [SpaceInviteController::class, 'show'])->name('show');
        Route::post('/spaces/{space}/invites/{invite}/accept', [SpaceInviteController::class, 'accept'])->name('accept');
        Route::post('/spaces/{space}/invites/{invite}/deny', [SpaceInviteController::class, 'deny'])->name('deny');
    });

    Route::get('/translations', TranslationsController::class);
});

// Route::get('/logout', [LogoutController::class, 'index'])->name('logout');
