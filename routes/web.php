<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RecurringController;
use App\Http\Controllers\ResendVerificationMailController;
use App\Http\Controllers\SpendingController;
use App\Http\Controllers\TransactionController;
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
        Route::post('/earnings/{id}/restore', [EarningController::class, 'restore']);
        Route::post('/earnings', [EarningController::class, 'store']);
        Route::delete('/earnings/{earning}', [EarningController::class, 'destroy'])->name('delete');
    });

    //Spendings
    Route::name('spendings.')->group(function () {
        Route::get('/spendings/{spending}', [SpendingController::class, 'show'])->name('show');
        Route::get('/spendings/{spending}/edit', [SpendingController::class, 'edit'])->name('edit');
        Route::patch('/spendings/{spending}', [SpendingController::class, 'update'])->name('update');
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
});

require __DIR__ . '/auth.php';

require __DIR__ . '/backup-web.php';
