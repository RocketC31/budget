<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RecurringController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResendVerificationMailController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\SpaceInviteController;
use App\Http\Controllers\SpendingController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TranslationsController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::group(['middleware' => ['auth']], function () {
    Route::post('/resend-verification-mail', ResendVerificationMailController::class)->name('resend_verification_mail');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    //Transactions
    Route::name('transactions.')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('index');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('create');
    });

    //Earnings
    Route::name('earnings.')->group(function () {
        Route::get('/earnings/{earning}', [EarningController::class, 'show'])->name('show');
        Route::get('/earnings/{earning}/edit', [EarningController::class, 'edit'])->name('edit');
        Route::patch('/earnings/{earning}', [EarningController::class, 'update'])->name('update');
        Route::put('/earnings/{earning}', [EarningController::class, 'update'])->name('update');
        Route::post('/earnings/{id}/restore', [EarningController::class, 'restore']);
        Route::post('/earnings', [EarningController::class, 'store']);
        Route::delete('/earnings/{earning}', [EarningController::class, 'destroy'])->name('delete');
    });

    //Spendings
    Route::name('spendings.')->group(function () {
        Route::get('/spendings/{spending}', [SpendingController::class, 'show'])->name('show');
        Route::get('/spendings/{spending}/edit', [SpendingController::class, 'edit'])->name('edit');
        Route::patch('/spendings/{spending}', [SpendingController::class, 'update'])->name('update');
        Route::put('/spendings/{spending}', [SpendingController::class, 'update'])->name('update');
        Route::post('/spendings/{id}/restore', [SpendingController::class, 'restore']);
        Route::post('/spendings', [SpendingController::class, 'store']);
        Route::delete('/spendings/{id}', [SpendingController::class, 'destroy'])->name('delete');
    });

    //Attachments
    Route::name('attachments.')->group(function () {
        Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('download');
        Route::delete('/attachments/{id}/delete', [AttachmentController::class, 'delete'])->name('delete');
        Route::post('/attachments', [AttachmentController::class, 'store'])->name('create');
    });

    //Budgets
    Route::name('budgets.')->group(function () {
        Route::get('/budgets', [BudgetController::class, 'index'])->name('index');
        Route::get('/budgets/create', [BudgetController::class, 'create'])->name('create');
        Route::post('/budgets', [BudgetController::class, 'store'])->name('store');
    });

    Route::resource('/recurrings', RecurringController::class)->only([
        'index',
        'create',
        'store',
        'show'
    ]);

    // Tags
    Route::name('tags.')->group(function () {
        Route::get('/tags', [TagController::class, 'index'])->name('index');
        Route::get('/tags/create', [TagController::class, 'create'])->name('create');
        Route::get('/tags/{tag}/edit', [TagController::class, 'edit'])->name('edit');
        Route::post('/tags', [TagController::class, 'store'])->name('store');
        Route::patch('/tags/{tag}', [TagController::class, 'update'])->name('update');
        Route::put('/tags/{tag}', [TagController::class, 'update'])->name('update');
        Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('delete');
    });

    //Reports
    Route::name('reports.')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('index');
        Route::get('/reports/{slug}', [ReportController::class, 'show'])->name('show');
    });

    //Activities
    Route::name('activities.')->group(function () {
        Route::get('/activities', [ActivityController::class, 'index'])->name('index');
    });

    //Import
    Route::name('imports.')->group(function () {
        Route::get('/imports', [ImportController::class, 'index'])->name('index');
        Route::get('/imports/create', [ImportController::class, 'create'])->name('create');
        Route::post('/imports', [ImportController::class, 'store'])->name('store');
        Route::get('/imports/{import}/prepare', [ImportController::class, 'getPrepare'])->name('prepare');
        Route::post('/imports/{import}/prepare', [ImportController::class, 'postPrepare'])->name('prepare.store');
        Route::get('/imports/{import}/complete', [ImportController::class, 'getComplete'])->name('complete');
        Route::post('/imports/{import}/complete', [ImportController::class, 'postComplete'])->name('complete.store');
        Route::delete('/imports/{import}', [ImportController::class, 'destroy'])->name('delete');
    });

    Route::name('widgets.')->group(function () {
        Route::post('/widgets', [WidgetController::class, 'store'])->name('store');
        Route::post('/widgets/{widget}/up', [WidgetController::class, 'up'])->name('up');
        Route::post('/widgets/{widget}/down', [WidgetController::class, 'down'])->name('down');
        Route::delete('/widgets/{widget}', [WidgetController::class, 'delete'])->name('delete');
    });

    //Settings
    Route::name('settings.')->group(function () {
        Route::get('/settings', [SettingsController::class, 'getIndex'])->name('index');
        Route::get('/settings/profile', [SettingsController::class, 'getProfile'])->name('profile');
        Route::post('/settings/profile', [SettingsController::class, 'postProfile'])->name('profile');
        Route::get('/settings/account', [SettingsController::class, 'getAccount'])->name('account');
        Route::post('/settings/account', [SettingsController::class, 'postAccount'])->name('account');
        Route::get('/settings/preferences', [SettingsController::class, 'getPreferences'])->name('preferences');
        Route::post('/settings/preferences', [SettingsController::class, 'postPreferences'])->name('preferences');
        Route::get('/settings/dashboard', [SettingsController::class, 'getDashboard'])->name('dashboard');
        Route::get('/settings/spaces', [SettingsController::class, 'getSpaces'])->name('spaces.index');
    });

    //Space
    Route::name('spaces.')->group(function () {
        Route::get('/spaces/create', [SpaceController::class, 'create'])->name('create');
        Route::post('/spaces', [SpaceController::class, 'store'])->name('store');
        Route::get('/spaces/{space}', [SpaceController::class, 'show'])->name('show');
        Route::get('/spaces/{space}/edit', [SpaceController::class, 'edit'])->name('edit');
        Route::patch('/spaces/{space}/update', [SpaceController::class, 'update'])->name('update');
        Route::put('/spaces/{space}/update', [SpaceController::class, 'update'])->name('update');
        Route::post('/spaces/{space}/invite', [SpaceController::class, 'invite'])->name('invite');
    });

    Route::name('space_invites.')->group(function () {
        Route::get('/spaces/{space}/invites/{invite}', [SpaceInviteController::class, 'show'])->name('show');
        Route::post('/spaces/{space}/invites/{invite}/accept', [SpaceInviteController::class, 'accept'])->name('accept');
        Route::post('/spaces/{space}/invites/{invite}/deny', [SpaceInviteController::class, 'deny'])->name('deny');
    });

    Route::get('/translations', TranslationsController::class);
});

require __DIR__ . '/auth.php';
