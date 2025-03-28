<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StatsController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/auth/check', function (Request $request) {
    return response()->json([
        'user' => $request->user()->only('id', 'name', 'email'),
        'valid' => true
    ]);
});

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
        Route::post('/screenings', [ScreeningController::class, 'store']);
        Route::put('/screenings/{screening}', [ScreeningController::class, 'update']);
        Route::delete('/screenings/{screening}', [ScreeningController::class, 'destroy']);
        Route::get('/screenings', [ScreeningController::class, 'indexAdmin']);
    });
    //====================MOVIES ADMIN====================
    Route::prefix('movies')->group(function () {
        Route::post('/', [MovieController::class, 'store']);
    });
    Route::get('/omdb/search', [MovieController::class, 'omdbSearch']);

    //====================ROOMS ADMIN====================
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index']);
        Route::get('/{room}', [RoomController::class, 'show']);
        Route::post('/availability', [RoomController::class, 'getAvailability']);
    });

    // Routes/api.php
    Route::get('/stats/sales', [StatsController::class, 'sales']);
});

Route::get('/movies', [MovieController::class, 'index']);
Route::get('movies/{movie}', [MovieController::class, 'show']);
Route::get('/screenings', [ScreeningController::class, 'indexClient']);
Route::get('/screenings/movies', [ScreeningController::class, 'getScheduledMovies']);
Route::get('/screenings/{screening}', [ScreeningController::class, 'show']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::post('/reservations/access-link', [ReservationController::class, 'generateAccessLink']);
Route::get('/reservations/purchases/{token}', [ReservationController::class, 'getPurchasesByToken']);