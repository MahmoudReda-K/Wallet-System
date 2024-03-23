<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Exceptions\RegistrationException;

class RegistrationService
{
    public function __construct(public UserRepository $userRepository, public WalletRepository $walletRepository){}

    /**
     * @throws RegistrationException
     */
    public function register(array $data)
    :User
    {
        try {
            $data['password'] = Hash::make($data['password']);
            DB::transaction(function () use ($data, &$user) {
                $user = $this->userRepository->createUser($data);
                //create user wallet after registration
                $this->walletRepository->createWalletForUser($user->id);
            });
            return $user;
        } catch (Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            throw new RegistrationException('Failed to register user.', $e->getCode(), $e);
        }
    }
}
