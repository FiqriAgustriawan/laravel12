<?php


namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
  use RefreshDatabase;

  public function test_it_can_register_a_new_user()
  {
    $userData = [
      'name' => 'Test User',
      'email' => 'test@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123'
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(201)
      ->assertJsonStructure([
        'message',
        'user' => ['id', 'name', 'email'], // Sesuai dengan AuthController
      ]);

    $this->assertDatabaseHas('users', [
      'email' => 'test@example.com',
      'name' => 'Test User'
    ]);
  }

  public function test_it_validates_user_registration_data()
  {
    $response = $this->postJson('/api/register', [
      'name' => '',
      'email' => 'invalid-email',
      'password' => 'short'
    ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['name', 'email', 'password']);
  }

  public function test_it_can_login_with_correct_credentials()
  {
    $user = User::factory()->create([
      'email' => 'test@example.com',
      'password' => bcrypt('password123')
    ]);

    $response = $this->postJson('/api/login', [
      'email' => 'test@example.com',
      'password' => 'password123'
    ]);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'message',
        'user' => ['id', 'name', 'email'],
        'token'
      ]);
  }

  public function test_it_rejects_invalid_login_credentials()
  {
    $user = User::factory()->create([
      'email' => 'test@example.com',
      'password' => bcrypt('password123')
    ]);

    $response = $this->postJson('/api/login', [
      'email' => 'test@example.com',
      'password' => 'wrong-password'
    ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['email']);
  }

  public function test_it_can_logout()
  {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
      ->postJson('/api/logout');

    $response->assertStatus(200)
      ->assertJson([
        'message' => 'Logged out successfully'
      ]);

    // Check token was deleted
    $this->assertDatabaseCount('personal_access_tokens', 0);
  }

  public function test_it_can_get_user_profile()
  {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
      ->getJson('/api/profile');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => ['id', 'name', 'email'] // UserResource wraps data dalam 'data'
      ]);
  }
}
