<?php
// filepath: d:\docs_pelajaran\KK1web\larv_12\lat_1\database\factories\FilmFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Film>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $judul = fake()->sentence(3);
        
        return [
            'judul' => $judul,
            'slug' => Str::slug($judul), // Akan auto-generate di model, tapi bisa set manual juga
            'poster_url' => fake()->imageUrl(400, 600, 'movies'),
            'harga_tiket' => fake()->randomFloat(2, 25000, 100000), // decimal dengan 2 desimal
            'deskripsi' => fake()->paragraph(),
            'tanggal_rilis' => fake()->date(),
            'durasi' => fake()->randomElement(['90 menit', '120 menit', '150 menit', '180 menit']),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the film is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}