<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletToUpTest extends TestCase
{
    use RefreshDatabase;

    private string $apiTopUp = '/api/wallet/top-up';

    public function testTopUpSuccess()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 0]);
        $amount = 50;
        $requestData = ['amount' => $amount, 'description' => 'Top-up test'];
        // Make a request to top-ip endpoint
        $response = $this->actingAs($user)
            ->postJson($this->apiTopUp, $requestData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'message' => 'Top-up successful.',
            'data' => [
                'wallet' => [
                    'id' => $wallet->id,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'balance' => $wallet->balance + $amount,
                ],
            ],
        ]);
        // Assert balance increased by amount
        $this->assertEquals($amount, $wallet->fresh()->balance);
    }

    public function testTopUpWalletNotFound()
    {
        $user = User::factory()->create();

        // Make a request to top-ip endpoint with invalid data (user has no wallet)
        $requestData = ['amount' => 50, 'description' => 'Top-up test'];
        $response = $this->actingAs($user)
            ->postJson($this->apiTopUp, $requestData);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Wallet not found for user']);
    }
}
