<?php

use App\Http\Controllers\LogController;
use App\Http\Controllers\PlotController;
use App\Http\Controllers\StatusController;
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
    Route::get('/dashboard/{machine?}', [LogController::class, 'dash'])->name('dashboard');
    
    Route::get('/logs', [LogController::class, 'index'])->name('logs');
    Route::get('/plots', [PlotController::class, 'index'])->name('plots');
    Route::get('/plots/{id}', [PlotController::class, 'details'])->name('plotDetails');
    Route::get('/status', [StatusController::class, 'index'])->name('status');
    Route::get('/status/{machine}/disks', [StatusController::class, 'disks'])->name('disks');
    Route::get('/status/{machine}/farm', [StatusController::class, 'farm'])->name('farm');
    Route::get('/status/{machine}/sensors', [StatusController::class, 'sensors'])->name('sensors');
});

require __DIR__.'/auth.php';
