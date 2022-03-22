<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RecurringController;
use App\Http\Controllers\SpendingController;
use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('index');*/
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    //Transactions
    Route::name('transactions.')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('index');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('create');
    });

    Route::name('earnings.')->group(function () {
        Route::get('/earnings/{earning}', [EarningController::class, 'show'])->name('show');
        Route::delete('/earnings/{earning}', [EarningController::class, 'destroy'])->name('delete');
        Route::post('/earnings', [EarningController::class, 'store']);
    });

    Route::name('spendings.')->group(function () {
        Route::delete('/spendings/{id}', [SpendingController::class, 'destroy'])->name('delete');
        Route::get('/spendings/{spending}', [SpendingController::class, 'show'])->name('show');
    });

    Route::name('attachments.')->group(function () {
        Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('download');
        Route::delete('/attachments/{id}/delete', [AttachmentController::class, 'delete'])->name('delete');
        Route::post('/attachments', [AttachmentController::class, 'store'])->name('create');
    });

    //TODO: deprecated, maybe we can remove component too
    Route::resource('/recurrings', RecurringController::class)->only([
        'index',
        'create',
        'store',
        'show'
    ]);
});

require __DIR__ . '/auth.php';

require __DIR__ . '/backup-web.php';
