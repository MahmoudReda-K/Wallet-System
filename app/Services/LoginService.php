<?php

namespace App\Services;

use App\Exceptions\LoginException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Authenticatable;

class LoginService
{
    /**
     * @throws LoginException
     */
    public function login(array $data)
    :Authenticatable
    {
        try {
            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                return Auth::user();
            }
            throw new LoginException('Invalid credentials.');
        } catch (\Exception $e){
            Log::error('Error while logging in: ' . $e->getTraceAsString());
            throw new LoginException('Invalid credentials.', $e->getCode(), $e);
        }
    }
}
