<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginSuccess()
    {
        $user = User::factory()->create([
            'email' => 'm@example.com',
            'password' => bcrypt('password'),
        ]);

        // Make a request to login endpoint with valid credentials
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => ['user', 'token']])
            ->assertJsonFragment(['message' => 'User login successfully.']);
        $this->assertAuthenticatedAs($user);
    }

    public function testLoginFailureInvalidCredentials()
    {
        // Make a request to login endpoint with invalid credentials
        $response = $this->postJson('/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'invalid-password',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(['message' => 'Invalid credentials.']);
    }
}
