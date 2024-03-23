<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\RegistrationService;
use App\Exceptions\RegistrationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationSuccess()
    {
        // Make a request to register endpoint with valid registration data
        $response = $this->postJson('/api/register', [
            'name' => 'Mahmoud Reda',
            'email' => 'm@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['data' => ['user', 'token']])
            ->assertJsonFragment(['message' => 'User registered successfully.']);

        $this->assertDatabaseHas('wallets', ['user_id' => $response->json('data.user.id')]);
    }

    public function testRegistrationWithEmailAlreadyExist()
    {
        $email = 'm@example.com';
        User::factory()->create(['email' => $email]);
        // Make a request to register endpoint with email already exist
        $response = $this->postJson('/api/register', [
            'name' => 'Mahmoud Reda',
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRegistrationException()
    {
        $this->mock(RegistrationService::class, function ($mock) {
            // Simulate a RegistrationException from the service
            $mock->shouldReceive('register')->andThrow(new RegistrationException('Failed to register user.'));
        });

        $response = $this->postJson('/api/register', [
            'name' => 'Mahmoud Reda',
            'email' => 'm@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'message' => 'Failed to register user.',
        ]);
    }
}
