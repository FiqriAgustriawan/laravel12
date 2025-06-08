<?php
// filepath: d:\docs_pelajaran\KK1web\larv_12\lat_1\tests\Feature\AdminAuthorizationTest.php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_admin_can_access_admin_endpoints()
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/films');

        $response->assertStatus(200);
    }

    public function test_it_regular_user_cannot_access_admin_endpoints()
    {
        $user = User::factory()->create(); // role = 'user'
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/films', [
                'judul' => 'Test Film',
                'deskripsi' => 'Test',
                'harga_tiket' => 50000.00
            ]);

        $response->assertStatus(403);
    }

    public function test_it_admin_can_create_films()
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

        $response->assertStatus(201);
    }

    public function test_it_regular_user_cannot_create_films()
    {
        $user = User::factory()->create(); // role = 'user'
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
    }
}