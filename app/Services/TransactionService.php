<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Constants\TransactionTypes;
use App\Constants\TransactionStatus;

use App\Repositories\TransactionRepository;

class TransactionService
{
    public function __construct(
        public TransactionRepository $transactionRepository,
        protected float $transactionFeeBase = 2.5,
        protected float $transactionFeePercentage = 0.1
    ){}


    public function withdrawTransaction(Wallet $wallet, array $withdrawData)
    :Transaction
    {
        return $this->transactionRepository->createTransaction([
            'wallet_id' => $wallet->id,
            'type' => TransactionTypes::WITHDRAW,
            'amount' => $withdrawData['amount'],
            'transaction_fee' => $withdrawData['transaction_fee'],
            'description' => $withdrawData['description'],
            'status' => TransactionStatus::DONE
        ]);
    }

    public function depositTransaction(Wallet $wallet, array $depositData)
    :Transaction
    {
        return $this->transactionRepository->createTransaction([
            'wallet_id' => $wallet->id,
            'type' => TransactionTypes::DEPOSIT,
            'amount' => $depositData['amount'],
            'description' => $depositData['description'],
            'status' => TransactionStatus::DONE
        ]);
    }

    public function calculateTransactionFee(float $amount): float
    {
        return $amount > 25 ? ($this->transactionFeeBase + ($this->transactionFeePercentage * $amount)) : 0;
    }

    public function getTransactionHistory(User $user, $perPage = 10)
    {
        return $this->transactionRepository->getTransactionHistory($user->wallet, $perPage);
    }
}
