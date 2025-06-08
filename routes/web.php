<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordTokenViewController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/reset-password', function () {
    return view('emails/reset-password');
});

// Route untuk melihat token reset password (development only)
if (config('app.env') === 'local') {
    Route::get('/password-tokens', [PasswordTokenViewController::class, 'index']);
    Route::get('/password-tokens/{email}', [PasswordTokenViewController::class, 'show']);
}
