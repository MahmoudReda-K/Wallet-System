<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletCheckBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function testCheckBalanceSuccess()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100]);

        // Make a request to balance endpoint
        $response = $this->actingAs($user)
            ->getJson('/api/wallet/balance');

        $response->assertStatus(Response::HTTP_OK);
        $responseData = $response->json();
        $this->assertEquals(100, $responseData['data']['balance']);
    }
}
