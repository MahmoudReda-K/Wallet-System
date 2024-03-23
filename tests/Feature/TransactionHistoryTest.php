<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function testTransactionHistorySuccess()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        $transactions = Transaction::factory(5)->create(['wallet_id' => $wallet->id]);

        // Make a request to transaction history endpoint
        $response = $this->actingAs($user)
            ->getJson('/api/transactions');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => [['id', 'amount', 'transaction_fee', 'type', 'description', 'status', 'created_at']]]);
        $responseData = $response->json();
        $this->assertCount(5, $responseData['data']);
    }
}
