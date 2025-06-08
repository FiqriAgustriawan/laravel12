<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'slug' => $this->slug,
            'poster_url' => $this->poster_url,
            'harga_tiket' => $this->harga_tiket,
            'deskripsi' => $this->deskripsi,
            'tanggal_rilis' => $this->tanggal_rilis,
            'durasi' => $this->durasi,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // URL untuk mengakses detail film
            'url' => route('api.films.show', $this->slug),
        ];
    }
}
