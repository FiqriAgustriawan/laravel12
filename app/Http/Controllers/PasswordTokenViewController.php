<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PasswordTokenViewController extends Controller
{
    /**
     * Tampilkan semua token reset password
     */
    public function index()
    {
        // Hanya izinkan di development
        if (config('app.env') !== 'local') {
            abort(404);
        }

        $tokens = DB::table('password_reset_tokens')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($token) {
                // Ambil plain token dari cache jika ada
                $plainToken = Cache::get("plain_token_{$token->email}");

                return [
                    'email' => $token->email,
                    'token_hash' => $token->token,
                    'plain_token' => $plainToken, // Token asli untuk testing
                    'created_at' => Carbon::parse($token->created_at),
                    'expires_at' => Carbon::parse($token->created_at)->addHours(24),
                    'is_expired' => Carbon::parse($token->created_at)->addHours(24)->isPast(),
                    'time_remaining' => Carbon::parse($token->created_at)->addHours(24)->diffForHumans(),
                ];
            });

        return view('password-tokens.index', compact('tokens'));
    }

    /**
     * Tampilkan token untuk email tertentu
     */
    public function show($email)
    {
        // Hanya izinkan di development
        if (config('app.env') !== 'local') {
            abort(404);
        }

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$tokenData) {
            abort(404, 'Token not found for this email');
        }

        $token = [
            'email' => $tokenData->email,
            'token_hash' => $tokenData->token,
            'created_at' => Carbon::parse($tokenData->created_at),
            'expires_at' => Carbon::parse($tokenData->created_at)->addHours(24),
            'is_expired' => Carbon::parse($tokenData->created_at)->addHours(24)->isPast(),
            'time_remaining' => Carbon::parse($tokenData->created_at)->addHours(24)->diffForHumans(),
        ];

        return view('password-tokens.show', compact('token'));
    }
}
