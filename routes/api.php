<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FilmController;
use App\Http\Controllers\Api\PemesananController;
use App\Http\Controllers\Api\PasswordResetController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::post('/password/forgot', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);


Route::get('/films', [FilmController::class, 'index']);
Route::get('/films/{slug}', [FilmController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Booking routes
    Route::apiResource('pemesanan', PemesananController::class);

   
    Route::middleware(['admin'])->group(function () {
        Route::prefix('admin')->group(function () {
            Route::get('/films', [FilmController::class, 'listAll']);
            Route::post('/films', [FilmController::class, 'store']); 
            Route::get('/films/{id}', [FilmController::class, 'showById']);
            Route::put('/films/{id}', [FilmController::class, 'update']);
            Route::delete('/films/{id}', [FilmController::class, 'destroy']);
        });
    });
});
