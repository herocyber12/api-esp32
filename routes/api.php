<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\RelayControlController;

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

Route::post('/sensor-data', [SensorDataController::class, 'store']);
Route::get('/sensor-data/latest', [SensorDataController::class, 'latest']);
Route::post('/relay-control', [RelayControlController::class, 'control']);
Route::get('/relay-control/latest', [RelayControlController::class, 'latest']);

