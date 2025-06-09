<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FilmController;
use App\Http\Controllers\Api\PemesananController;
use App\Http\Controllers\Api\PasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Password Reset Routes
Route::post('/password/forgot', [PasswordResetController::class, 'sendResetLink'])->name('api.password.forgot');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('api.password.reset');
Route::post('/password/verify-token', [PasswordResetController::class, 'verifyToken'])->name('api.password.verify-token');

// Film routes
Route::get('/films', [FilmController::class, 'index'])->name('api.films.index');
Route::get('/films/{film:slug}', [FilmController::class, 'show'])->name('api.films.show');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('api.profile');

    // Booking routes
    Route::apiResource('pemesanan', PemesananController::class)->names([
        'index' => 'api.pemesanan.index',
        'store' => 'api.pemesanan.store',
        'show' => 'api.pemesanan.show',
        'update' => 'api.pemesanan.update',
        'destroy' => 'api.pemesanan.destroy',
    ]);

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/films', [FilmController::class, 'listAll']);
        Route::post('/films', [FilmController::class, 'store']);
        Route::put('/films/{film}', [FilmController::class, 'update']); // Uses ID
        Route::delete('/films/{film}', [FilmController::class, 'destroy']); // Uses ID
    });
});
