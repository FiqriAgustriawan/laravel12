<?php
// filepath: d:\docs_pelajaran\KK1web\larv_12\lat_1\tests\Feature\PemesananApiTest.php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemesananApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_get_user_pemesanan()
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();
        $pemesanan = Pemesanan::factory()->count(3)->create([
            'user_id' => $user->id,
            'film_id' => $film->id
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/pemesanan');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_it_can_create_pemesanan()
    {
        $user = User::factory()->create();
        $film = Film::factory()->create(['harga_tiket' => 50000.00]);
        $token = $user->createToken('test-token')->plainTextToken;

        $pemesananData = [
            'film_id' => $film->id,
            'jumlah_tiket' => 2
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/pemesanan', $pemesananData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('pemesanans', [
            'user_id' => $user->id,
            'film_id' => $film->id,
            'jumlah_tiket' => 2,
        ]);
    }

    public function test_it_validates_pemesanan_data()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/pemesanan', [
                'film_id' => 999,
                'jumlah_tiket' => 0
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['film_id', 'jumlah_tiket']);
    }
}