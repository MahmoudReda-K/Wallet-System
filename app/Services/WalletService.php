<?php

namespace App\Services;

use App\Exceptions\SameSenderAndReceiverException;
use Exception;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use App\Exceptions\WalletNotFoundException;
use App\Exceptions\InsufficientBalanceException;


class WalletService
{
    public function __construct(
        public WalletRepository $walletRepository,
        public TransactionRepository $transactionRepository,
        public TransactionService $transactionService
    ){}

    /**
     * @throws Exception
     */
    public function topUp(User $user, array $data)
    :Wallet
    {
        try {
            return DB::transaction(function () use ($user, $data) {
                $wallet = $this->walletRepository->getWalletByUserId($user->id);
                if (!$wallet) {
                    // Handle wallet not found
                    throw new WalletNotFoundException('Wallet not found for user');
                }
                $this->transactionService->depositTransaction($wallet, $data);
                return $this->depositToWallet($wallet, $data['amount']);
            });
        } catch (Exception $e) {
            Log::error('Top-up failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function transfer(User $sender, array $data)
    :Wallet
    {
        try {
            return DB::transaction(function () use ($sender, $data) {
                $amount = $data['amount'];
                $senderWallet = $this->walletRepository->getWalletByUserId($sender->id);
                $receiverWallet = $this->walletRepository->getWalletByUserId($data['receiver']);
                if (!$senderWallet || !$receiverWallet) {
                    // Handle sender or receiver wallet not found
                    throw new WalletNotFoundException('Sender or receiver wallet not found');
                }
                if ($sender->id  == $data['receiver']) {
                    // Handle sender and receiver are same wallet
                    throw new SameSenderAndReceiverException('Sender and receiver can\'t be the same wallet');
                }

                $transactionFee = $this->transactionService->calculateTransactionFee($amount);
                $totalAmount = $amount + $transactionFee;
                if ($senderWallet->balance < $totalAmount) {
                    // Handle insufficient balance
                    throw new InsufficientBalanceException('Insufficient balance');
                }

                $data['transaction_fee'] = $transactionFee;
                $this->transactionService->depositTransaction($receiverWallet, $data);
                $this->transactionService->withdrawTransaction($senderWallet, $data);
                $this->depositToWallet($receiverWallet, $amount);
                return $this->withdrawFromWallet($senderWallet, $totalAmount);
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }

    private function depositToWallet(Wallet $wallet, float $amount)
    : Wallet
    {
        $wallet->balance += $amount;
        $wallet->save();
        return $wallet;
    }

    private function withdrawFromWallet(Wallet $wallet, float $amount)
    : Wallet
    {
        $wallet->balance -= $amount;
        $wallet->save();
        return $wallet;
    }

    public function getCurrentBalance(User $user)
    : float
    {
        return $user->wallet->balance;
    }
}
