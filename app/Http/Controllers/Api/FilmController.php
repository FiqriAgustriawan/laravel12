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
            'judul' => 'required|string|max:100',
            'slug' => 'nullable|string|unique:films,slug|max:120', // Optional manual slug
            'poster' => 'nullable|image|max:2048',
            'poster_url' => 'nullable|url',
            'harga_tiket' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'tanggal_rilis' => 'nullable|date',
            'durasi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Generate slug jika tidak disediakan
        if (empty($validated['slug'])) {
            $validated['slug'] = Film::generateSlug($validated['judul']);
        }

        // Proses poster (file atau URL)
        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('posters', 'public');
            $posterUrl = Storage::url($path);
        } elseif ($request->filled('poster_url')) {
            $posterUrl = $request->poster_url;
        } else {
            $posterUrl = null;
        }

        // Buat film dengan data yang sudah divalidasi
        $film = Film::create([
            'judul' => $validated['judul'],
            'slug' => $validated['slug'],
            'poster_url' => $posterUrl,
            'harga_tiket' => $validated['harga_tiket'],
            'deskripsi' => $request->deskripsi,
            'tanggal_rilis' => $request->tanggal_rilis,
            'durasi' => $request->durasi,
            'is_active' => $request->is_active ?? true,
        ]);

        return new FilmResource($film);
    }

    /**
     * Memperbarui film yang ada
     */
    public function update(Request $request, Film $film)
    {
        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:100',
            'slug' => 'nullable|string|unique:films,slug,' . $film->id . '|max:120',
            'poster' => 'nullable|image|max:2048',
            'poster_url' => 'nullable|url',
            'harga_tiket' => 'sometimes|required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'tanggal_rilis' => 'nullable|date',
            'durasi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Data untuk update
        $filmData = [
            'judul' => $request->judul ?? $film->judul,
            'harga_tiket' => $request->harga_tiket ?? $film->harga_tiket,
            'deskripsi' => $request->deskripsi ?? $film->deskripsi,
            'tanggal_rilis' => $request->tanggal_rilis ?? $film->tanggal_rilis,
            'durasi' => $request->durasi ?? $film->durasi,
            'is_active' => $request->is_active ?? $film->is_active,
        ];

        // Update slug jika judul berubah atau slug baru disediakan
        if ($request->filled('judul') && $request->judul !== $film->judul) {
            $filmData['slug'] = $request->filled('slug') ? 
                $validated['slug'] : 
                Film::generateSlug($request->judul);
        } elseif ($request->filled('slug')) {
            $filmData['slug'] = $validated['slug'];
        }
        
        // Proses poster (file atau URL)
        if ($request->hasFile('poster')) {
            if ($film->poster_url && Str::startsWith($film->poster_url, '/storage/posters/')) {
                $oldPath = Str::replaceFirst('/storage', 'public', $film->poster_url);
                Storage::delete($oldPath);
            }
            
            $path = $request->file('poster')->store('posters', 'public');
            $filmData['poster_url'] = Storage::url($path);
        } elseif ($request->filled('poster_url')) {
            $filmData['poster_url'] = $request->poster_url;
        }

        $film->update($filmData);

        return new FilmResource($film);
    }

    /**
     * Menghapus film
     */
    public function destroy(Film $film)
    {
        if ($film->poster_url && Str::startsWith($film->poster_url, '/storage/posters/')) {
            $oldPath = Str::replaceFirst('/storage', 'public', $film->poster_url);
            Storage::delete($oldPath);
        }
        
        $film->delete();
        return response()->json(['message' => 'Film berhasil dihapus']);
    }
}