<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\IndexController;
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
        Route::delete('/earnings/{earning}', [EarningController::class, 'destroy'])->name('delete');
    });

    Route::name('spendings.')->group(function () {
        Route::delete('/spendings/{id}', [SpendingController::class, 'destroy'])->name('delete');
    });
});

require __DIR__ . '/auth.php';

require __DIR__ . '/backup-web.php';
