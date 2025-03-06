<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\ReservationController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    //====================AUTH====================
    Route::prefix('auth')->group(function () {
        Route::post('/reset-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

});

Route::get('/screenings', [ScreeningController::class, 'index']);
Route::get('/screenings/{screening}', [ScreeningController::class, 'show']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/reservations', [ReservationController::class, 'index']);