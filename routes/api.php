<?php

use App\Http\Controllers\DailyVolumeController;
use App\Http\Controllers\GasCostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('scope.user')->group(function () {
    Route::get('/protected', function () {
        return response()->json(['message' => 'Access granted']);
    });
    Route::post('daily-volumes/{perPage?}', [DailyVolumeController::class, 'index']);
    Route::get('daily-volumes/view/{id}', [DailyVolumeController::class, 'show']);
    Route::delete('daily-volumes/{id}', [DailyVolumeController::class, 'destroy']);
    Route::post('gas-costs', [GasCostController::class, 'index']);
    Route::get('gas-costs/view/{id}', [GasCostController::class, 'show']);
    Route::delete('gas-costs/delete/{id}', [GasCostController::class, 'destroy']);
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
