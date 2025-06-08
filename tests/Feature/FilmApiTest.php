<?php
// filepath: d:\docs_pelajaran\KK1web\larv_12\lat_1\tests\Feature\FilmApiTest.php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_get_all_films()
    {
        $films = Film::factory()->count(3)->create();

        $response = $this->getJson('/api/films');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'judul', 'slug', 'deskripsi', 'harga_tiket', 'poster_url']
                ]
            ]);
    }

    public function test_it_can_get_film_by_slug()
    {
        $film = Film::factory()->create();

        $response = $this->getJson("/api/films/{$film->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $film->id,
                    'judul' => $film->judul,
                    'slug' => $film->slug,
                ]
            ]);
    }

    public function test_it_returns_404_for_nonexistent_film()
    {
        $response = $this->getJson('/api/films/nonexistent-slug');

        $response->assertStatus(404);
    }

    public function test_it_admin_can_create_film()
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $filmData = [
            'judul' => 'New Test Film',
            'deskripsi' => 'A test film description',
            'harga_tiket' => 50000.00,
            'poster_url' => 'https://example.com/image.jpg',
            'tanggal_rilis' => '2024-12-01',
            'durasi' => '120 menit'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/films', $filmData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'judul', 'slug', 'deskripsi', 'harga_tiket', 'poster_url']
            ])
            ->assertJson([
                'data' => [
                    'judul' => 'New Test Film',
                    'harga_tiket' => 50000.00,
                ]
            ]);

        $this->assertDatabaseHas('films', [
            'judul' => 'New Test Film',
        ]);
    }

    public function test_it_validates_film_data_on_creation()
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/films', [
                'judul' => '',
                'harga_tiket' => 'not-a-number'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['judul', 'harga_tiket']);
    }

    public function test_it_regular_user_cannot_create_films()
    {
        $user = User::factory()->create(); // User biasa
        $token = $user->createToken('test-token')->plainTextToken;

        $filmData = [
            'judul' => 'New Test Film',
            'deskripsi' => 'A test film description',
            'harga_tiket' => 50000.00,
            'poster_url' => 'https://example.com/image.jpg'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/films', $filmData);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('films', [
            'judul' => 'New Test Film',
        ]);
    }

    public function test_it_admin_can_update_film()
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;
        $film = Film::factory()->create();

        $updateData = [
            'judul' => 'Updated Film Title',
            'harga_tiket' => 75000.00
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/films/{$film->slug}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'judul' => 'Updated Film Title',
                    'harga_tiket' => 75000.00
                ]
            ]);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'judul' => 'Updated Film Title',
            'harga_tiket' => 75000.00
        ]);
    }

    public function test_it_admin_can_delete_film()
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;
        $film = Film::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/films/{$film->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Film berhasil dihapus' // Sesuai dengan FilmController
            ]);

        $this->assertDatabaseMissing('films', [
            'id' => $film->id
        ]);
    }
}