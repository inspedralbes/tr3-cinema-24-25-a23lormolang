<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MovieController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //====================AUTH====================
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/reset-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    //====================SCREENINGS ADMIN====================
    Route::prefix('admin')->group(function () {
        Route::get('/screenings', [ScreeningController::class, 'index']);
        Route::post('/screenings', [ScreeningController::class, 'store']);
        Route::put('/screenings/{screening}', [ScreeningController::class, 'update']);
        Route::delete('/screenings/{screening}', [ScreeningController::class, 'destroy']);
    });
    //====================MOVIES ADMIN====================
    Route::prefix('movies')->group(function () {
        Route::post('/', [MovieController::class, 'store']);
    });
    Route::get('/omdb/search', [MovieController::class, 'omdbSearch']);

});

Route::get('/nextScreens', [ScreeningController::class, 'nextScreens']);
Route::get('/screenings/{screening}', [ScreeningController::class, 'show']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/reservations', [ReservationController::class, 'index']);