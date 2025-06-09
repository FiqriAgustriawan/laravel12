<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

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
    public function show($slug)
    {
        try {
            $film = Film::where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if (!$film) {
                return response()->json([
                    'message' => 'Film tidak ditemukan atau tidak aktif',
                    'error' => 'Film with slug "' . $slug . '" not found'
                ], 404);
            }

            return new FilmResource($film);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data film',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Method untuk admin - menggunakan ID
     */
    public function showById($id)
    {
        try {
            $film = Film::findOrFail($id);
            return new FilmResource($film);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Film tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Menyimpan film baru =
     */
    public function store(Request $request)
    {
        try {
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
                'durasi' => isset($validated['durasi']) ? (int)$validated['durasi'] : null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Handle poster upload
            if ($request->hasFile('poster')) {
                $film->uploadPoster($request->file('poster'));
            }

            // Return dengan status 201
            return response()->json([
                'data' => new FilmResource($film)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat film',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui film yang ada
     */
    public function update(Request $request, $id)
    {
        try {
            $film = Film::findOrFail($id);

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
            $updateData = [];
            if (isset($validated['judul'])) $updateData['judul'] = $validated['judul'];
            if (isset($validated['slug'])) $updateData['slug'] = $validated['slug'];
            if (isset($validated['harga_tiket'])) $updateData['harga_tiket'] = $validated['harga_tiket'];
            if ($request->has('deskripsi')) $updateData['deskripsi'] = $request->deskripsi;
            if ($request->has('tanggal_rilis')) $updateData['tanggal_rilis'] = $request->tanggal_rilis;
            if (isset($validated['durasi'])) $updateData['durasi'] = (int)$validated['durasi'];
            if (isset($validated['is_active'])) $updateData['is_active'] = $validated['is_active'];

            $film->update($updateData);

            // Handle poster upload
            if ($request->hasFile('poster')) {
                $film->uploadPoster($request->file('poster'));
            }

            return response()->json([
                'data' => new FilmResource($film->fresh())
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Film tidak ditemukan'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengupdate film',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus film
     */
    public function destroy($id)
    {
        try {
            $film = Film::findOrFail($id);
            $film->deletePoster();
            $film->delete();

            return response()->json([
                'success' => true,
                'message' => 'Film berhasil dihapus'
            ], 200);
        } catch (ModelNotFoundException $e) {
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
