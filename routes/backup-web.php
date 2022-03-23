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

    Route::name('imports.')->group(function () {
        Route::get('/imports', [ImportController::class, 'index'])->name('index');
        Route::get('/imports/create', [ImportController::class, 'create'])->name('create');
        Route::post('/imports', [ImportController::class, 'store'])->name('store');
        Route::get('/imports/{import}/prepare', [ImportController::class, 'getPrepare'])->name('prepare');
        Route::post('/imports/{import}/prepare', [ImportController::class, 'postPrepare']);
        Route::get('/imports/{import}/complete', [ImportController::class, 'getComplete'])->name('complete');
        Route::post('/imports/{import}/complete', [ImportController::class, 'postComplete']);
        Route::delete('/imports/{import}', [ImportController::class, 'destroy']);
    });

    Route::name('settings.')->group(function () {
        Route::get('/settings', [SettingsController::class, 'getIndex'])->name('index');
        Route::post('/settings', [SettingsController::class, 'postIndex']);
        Route::get('/settings/profile', [SettingsController::class, 'getProfile'])->name('profile');
        Route::get('/settings/account', [SettingsController::class, 'getAccount'])->name('account');
        Route::get('/settings/preferences', [SettingsController::class, 'getPreferences'])->name('preferences');
        Route::get('/settings/dashboard', [SettingsController::class, 'getDashboard'])->name('dashboard');
        Route::get('/settings/billing', [SettingsController::class, 'getBilling'])->name('billing')->middleware('stripe');
        Route::post('/settings/billing/upgrade', [SettingsController::class, 'postUpgrade'])->name('billing.upgrade')->middleware('stripe');
        Route::post('/settings/billing/cancel', [SettingsController::class, 'postCancel'])->name('billing.cancel')->middleware('stripe');
        Route::get('/settings/spaces', [SettingsController::class, 'getSpaces'])->name('spaces.index');
    });

    Route::name('spaces.')->group(function () {
        Route::get('/spaces/create', [SpaceController::class, 'create'])->name('create');
        Route::post('/spaces', [SpaceController::class, 'store'])->name('store');
        Route::get('/spaces/{space}', [SpaceController::class, 'show'])->name('show');
        Route::get('/spaces/{space}/edit', [SpaceController::class, 'edit'])->name('edit');
        Route::post('/spaces/{space}/update', [SpaceController::class, 'update'])->name('update');
        Route::post('/spaces/{space}/invite', [SpaceController::class, 'invite'])->name('invite');
    });

    Route::name('space_invites.')->group(function () {
        Route::get('/spaces/{space}/invites/{invite}', [SpaceInviteController::class, 'show'])->name('show');
        Route::post('/spaces/{space}/invites/{invite}/accept', [SpaceInviteController::class, 'accept'])->name('accept');
        Route::post('/spaces/{space}/invites/{invite}/deny', [SpaceInviteController::class, 'deny'])->name('deny');
    });

    Route::get('/translations', TranslationsController::class);
});

// Route::get('/logout', [LogoutController::class, 'index'])->name('logout');
