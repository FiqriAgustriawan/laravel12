<?php
// filepath: d:\docs_pelajaran\KK1web\larv_12\lat_1\database\factories\PemesananFactory.php

namespace Database\Factories;

use App\Models\Film;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pemesanan>
 */
class PemesananFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $film = Film::factory()->create();
        $jumlahTiket = fake()->numberBetween(1, 5);
        
        return [
            'user_id' => User::factory(),
            'film_id' => $film->id,
            'jumlah_tiket' => $jumlahTiket,
            'total_harga' => $film->harga_tiket * $jumlahTiket, // Gunakan 'harga_tiket'
        ];
    }
}