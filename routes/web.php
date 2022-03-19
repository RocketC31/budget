<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexController;
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
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

require __DIR__ . '/auth.php';

require __DIR__ . '/backup-web.php';
