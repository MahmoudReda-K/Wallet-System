<?php

namespace App\Http\Controllers\API;

use App\Services\LoginService;
use App\Exceptions\LoginException;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends BaseController
{
    public function __construct(public LoginService $loginService){}

    public function login(LoginRequest $request)
    {
        try {
            $user = $this->loginService->login($request->validated());
            if ($user) {
                $token = $user->createToken('api-token')->plainTextToken;
                return $this->successResponse(['user' => new UserResource($user), 'token' => $token], Response::HTTP_OK, 'User login successfully.');
            }
            return $this->errorResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        } catch (LoginException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            // Catch any other unexpected exceptions
            return $this->errorResponse('An error occurred during login.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
