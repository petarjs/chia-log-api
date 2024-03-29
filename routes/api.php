<?php

use App\Http\Controllers\LogController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth.apikey')->post('/log', [LogController::class, 'store']);
Route::middleware('auth.apikey')->post('/status', [StatusController::class, 'store']);
