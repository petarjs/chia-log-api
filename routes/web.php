<?php

use App\Http\Controllers\LogController;
use App\Http\Controllers\PlotController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [LogController::class, 'dash'])->name('dashboard');
    
    Route::get('/logs', [LogController::class, 'index'])->name('logs');
    Route::get('/plots', [PlotController::class, 'index'])->name('plots');
    Route::get('/plots/{id}', [PlotController::class, 'details'])->name('plotDetails');
});

require __DIR__.'/auth.php';
