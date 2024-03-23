<?php

namespace App\Repositories;

use App\Models\Wallet;

class WalletRepository
{
    public function createWalletForUser(int $userId): Wallet
    {
        return Wallet::create(['user_id' => $userId, 'balance' => 0]);
    }

    public function getWalletByUserId(int $userId): ?Wallet
    {
        return Wallet::where('user_id', $userId)->first();
    }
}
