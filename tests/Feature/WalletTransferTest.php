<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletTransferTest extends TestCase
{
    use RefreshDatabase;

    private string $apiTransfer = '/api/wallet/transfer';

    public function testTransferSuccess()
    {
        $initialSenderBalance = 100;
        $initialReceiverBalance = 0;
        // Create users and wallets
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $senderWallet = Wallet::factory()->create(['user_id' => $sender->id, 'balance' => $initialSenderBalance]);
        $receiverWallet = Wallet::factory()->create(['user_id' => $receiver->id, 'balance' => $initialReceiverBalance]);
        // Prepare request data
        $amount = 50;
        $requestData = ['amount' => $amount, 'receiver' => $receiver->id];

        // Assuming sender has enough balance
        // Make a request to transfer endpoint
        $response = $this->actingAs($sender)
            ->postJson($this->apiTransfer, $requestData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => ['wallet']]);

        // Assert sender balance decreased by transfer amount + transaction fee
        $this->assertEquals($initialSenderBalance - $amount - $this->getTransactionFee($amount), $senderWallet->fresh()->balance);
        // Assert receiver balance increased by transfer amount
        $this->assertEquals($amount, $receiverWallet->fresh()->balance);
    }

    public function testTransferInsufficientBalance()
    {
        $amount = 50;
        $totalAmount = $amount + $this->getTransactionFee($amount);
        $initialSenderBalance = $totalAmount - 1;
        $initialReceiverBalance = 0;
        // Create users and wallets
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $senderWallet = Wallet::factory()->create(['user_id' => $sender->id, 'balance' => $initialSenderBalance]);
        $receiverWallet = Wallet::factory()->create(['user_id' => $receiver->id, 'balance' => $initialReceiverBalance]);
        // Prepare request data


        $requestData = ['amount' => $amount, 'receiver' => $receiver->id];
        // Make a request to transfer endpoint with insufficient balance
        $response = $this->actingAs($sender)
            ->postJson($this->apiTransfer, $requestData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['message' => 'Insufficient balance']);

        //Assert sender and receiver's balances don't change
        $this->assertEquals($initialSenderBalance , $senderWallet->fresh()->balance);
        $this->assertEquals($initialReceiverBalance, $receiverWallet->fresh()->balance);
    }

    public function testTransferWalletNotFound()
    {
        // Create users
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // Prepare request data
        $requestData = ['amount' => 50, 'receiver' => $receiver->id];
        // Make a request to transfer endpoint with wallet not found
        $response = $this->actingAs($sender)
            ->postJson($this->apiTransfer, $requestData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['message' => 'Sender or receiver wallet not found']);
    }

    public function testSameSenderAndReceiver()
    {
        $amount = 50;
        $initialSenderBalance = 100;
        // Create users and wallets
        $sender = User::factory()->create();
        Wallet::factory()->create(['user_id' => $sender->id, 'balance' => $initialSenderBalance]);
        // Make a request to transfer endpoint with same sender and receiver
        $requestData = ['amount' => $amount, 'receiver' => $sender->id];
        $response = $this->actingAs($sender)
            ->postJson($this->apiTransfer, $requestData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['message' => 'Sender and receiver can\'t be the same wallet']);
    }

    private function getTransactionFee($amount)
    {
        return $this->app->make('App\Services\TransactionService')->calculateTransactionFee($amount);
    }
}
