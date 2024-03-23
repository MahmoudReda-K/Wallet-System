<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Services\RegistrationService;
use App\Http\Controllers\BaseController;
use App\Exceptions\RegistrationException;
use App\Http\Requests\RegistrationRequest;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends BaseController
{
    public function __construct(public RegistrationService $registrationService){}

    public function register(RegistrationRequest $request)
    {
        try {
            $user = $this->registrationService->register($request->validated());
            $token = $user->createToken('api-token')->plainTextToken;
            return $this->successResponse(['user' => new UserResource($user), 'token' => $token], 201, 'User registered successfully.');
        } catch (RegistrationException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Catch any other unexpected exceptions
            return $this->errorResponse('An error occurred during registration.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
