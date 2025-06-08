<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PemesananResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'film' => $this->whenLoaded('film', function () {
                return [
                    'id' => $this->film->id,
                    'judul' => $this->film->judul,
                    'harga_tiket' => $this->film->harga_tiket,
                ];
            }),
            'jumlah_tiket' => $this->jumlah_tiket,
            'total_harga' => $this->total_harga,
            'waktu_pemesanan' => $this->waktu_pemesanan,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
