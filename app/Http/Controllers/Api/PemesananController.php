<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PemesananResource;
use App\Models\Film;
use App\Models\Pemesanan;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    /**
     * Menampilkan daftar pemesanan
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->isAdmin()) {
            $pemesanans = Pemesanan::with(['user', 'film'])->paginate(10);
        } else {
            $pemesanans = $user->pemesanans()->with('film')->paginate(10);
        }
        
        return PemesananResource::collection($pemesanans);
    }

    /**
     * Menyimpan pemesanan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
            'jumlah_tiket' => 'required|integer|min:1',
        ]);
        
        $user = $request->user();
        $film = Film::findOrFail($validated['film_id']);
        
        // Hitung total harga
        $totalHarga = $film->harga_tiket * $validated['jumlah_tiket'];
        
        $pemesanan = Pemesanan::create([
            'user_id' => $user->id,
            'film_id' => $film->id,
            'jumlah_tiket' => $validated['jumlah_tiket'],
            'total_harga' => $totalHarga,
            'status' => 'pending'
        ]);
        
        return new PemesananResource($pemesanan->load(['user', 'film']));
    }

    /**
     * Menampilkan detail pemesanan
     */
    public function show(Request $request, Pemesanan $pemesanan)
    {
        // Verifikasi hak akses
        if (!$request->user()->isAdmin() && $pemesanan->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return new PemesananResource($pemesanan->load(['user', 'film']));
    }

    /**
     * Memperbarui pemesanan
     */
    public function update(Request $request, Pemesanan $pemesanan)
    {
        // Hanya admin yang bisa mengubah status
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,canceled',
        ]);
        
        $pemesanan->update($validated);
        
        return new PemesananResource($pemesanan->load(['user', 'film']));
    }

    /**
     * Menghapus pemesanan
     */
    public function destroy(Request $request, Pemesanan $pemesanan)
    {
        // Verifikasi hak akses
        if (!$request->user()->isAdmin() && $pemesanan->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $pemesanan->delete();
        return response()->json(['message' => 'Pemesanan berhasil dihapus']);
    }
}