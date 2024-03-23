<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function createUser(array $userData): User
    {
        return User::create($userData);
    }
}
