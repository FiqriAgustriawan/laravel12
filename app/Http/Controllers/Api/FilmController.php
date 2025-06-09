<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilmController extends Controller
{
    /**
     * Menampilkan daftar film yang aktif
     */
    public function index()
    {
        $films = Film::where('is_active', true)->paginate(10);
        return FilmResource::collection($films);
    }

    /**
     * Menampilkan semua film termasuk yang tidak aktif (admin only)
     */
    public function listAll()
    {
        $films = Film::paginate(10);
        return FilmResource::collection($films);
    }

    /**
     * Menampilkan detail film berdasarkan slug
     */
    public function show(Film $film)
    {
        return new FilmResource($film);
    }

    /**
     * Menyimpan film baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:films,slug|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'harga_tiket' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'tanggal_rilis' => 'nullable|date',
            'durasi' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        // Generate slug jika tidak disediakan
        if (empty($validated['slug'])) {
            $validated['slug'] = Film::generateSlug($validated['judul']);
        }

        // Create film first
        $film = Film::create([
            'judul' => $validated['judul'],
            'slug' => $validated['slug'],
            'harga_tiket' => $validated['harga_tiket'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal_rilis' => $validated['tanggal_rilis'] ?? null,
            'durasi' => $validated['durasi'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Handle poster upload
        if ($request->hasFile('poster')) {
            $film->uploadPoster($request->file('poster'));
        }

        return new FilmResource($film);
    }

    /**
     * Memperbarui film yang ada
     */
    public function update(Request $request, Film $film)
    {
        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|string|unique:films,slug,' . $film->id . '|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'harga_tiket' => 'sometimes|required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'tanggal_rilis' => 'nullable|date',
            'durasi' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        // Update film data
        $filmData = array_filter([
            'judul' => $validated['judul'] ?? null,
            'slug' => $validated['slug'] ?? null,
            'harga_tiket' => $validated['harga_tiket'] ?? null,
            'deskripsi' => $request->deskripsi,
            'tanggal_rilis' => $request->tanggal_rilis,
            'durasi' => $validated['durasi'] ?? null,
            'is_active' => $validated['is_active'] ?? null,
        ], function($value) {
            return $value !== null;
        });

        $film->update($filmData);

        // Handle poster upload
        if ($request->hasFile('poster')) {
            $film->uploadPoster($request->file('poster'));
        }

        return new FilmResource($film);
    }

    /**
     * Menghapus film
     */
    public function destroy($id)
    {
        try {
            // Find film by ID
            $film = Film::findOrFail($id);
            
            // Delete poster file using the model method
            $film->deletePoster();
            
            // Delete film record
            $film->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Film berhasil dihapus'
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Film tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus film: ' . $e->getMessage()
            ], 500);
        }
    }
}