<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\Transaction;

class TransactionRepository
{
    public function createTransaction(array $transactionData): Transaction
    {
        return Transaction::create($transactionData);
    }

    public function getTransactionHistory(Wallet $wallet, $perPage = 10)
    {
        return $wallet->transactions()->orderBy('created_at', 'desc')->paginate($perPage);;
    }
}
