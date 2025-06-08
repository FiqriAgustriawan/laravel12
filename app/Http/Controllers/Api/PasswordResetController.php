<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PasswordResetController extends Controller
{
    /**
     * Send reset password link ke email
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;

        // Generate token
        $token = Str::random(60);

        // Hapus token lama jika ada
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Simpan token baru
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Untuk development, simpan plain token ke cache
        if (config('app.env') === 'local') {
            // Simpan plain token selama 24 jam
            Cache::put("plain_token_{$email}", $token, now()->addHours(24));

            Log::info('Password Reset Token Generated', [
                'email' => $email,
                'plain_token' => $token,
                'reset_url' => url('/api/password/reset') . '?token=' . $token . '&email=' . $email
            ]);
        }

        // Kirim email (optional - bisa menggunakan log untuk testing)
        try {
            Mail::to($email)->send(new ResetPasswordMail($token, $email));

            return response()->json([
                'message' => 'Reset password link telah dikirim ke email Anda',
                'token' => $token, // Plain token untuk testing
                'view_tokens_url' => url('/password-tokens') // Link ke halaman visualisasi
            ]);
        } catch (\Exception $e) {
            // Jika email gagal dikirim, berikan token untuk testing
            return response()->json([
                'message' => 'Email service tidak tersedia, gunakan token berikut untuk reset password',
                'token' => $token,
                'reset_url' => url('/api/password/reset') . '?token=' . $token . '&email=' . $email,
                'view_tokens_url' => url('/password-tokens') // Link ke halaman visualisasi
            ]);
        }
    }

    /**
     * Reset password menggunakan token
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari token di database
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return response()->json([
                'message' => 'Token reset password tidak ditemukan'
            ], 404);
        }

        // Verifikasi token
        if (!Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'message' => 'Token reset password tidak valid'
            ], 400);
        }

        // Cek apakah token sudah expired (24 jam)
        if (now()->diffInHours($passwordReset->created_at) > 24) {
            return response()->json([
                'message' => 'Token reset password sudah expired'
            ], 400);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token setelah digunakan
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password berhasil direset'
        ]);
    }

    /**
     * Verifikasi token reset password
     */
    public function verifyToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari token di database
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return response()->json([
                'message' => 'Token tidak ditemukan',
                'valid' => false
            ], 404);
        }

        // Verifikasi token
        if (!Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'message' => 'Token tidak valid',
                'valid' => false
            ], 400);
        }

        // Cek apakah token sudah expired
        if (now()->diffInHours($passwordReset->created_at) > 24) {
            return response()->json([
                'message' => 'Token sudah expired',
                'valid' => false
            ], 400);
        }

        return response()->json([
            'message' => 'Token valid',
            'valid' => true
        ]);
    }
}
