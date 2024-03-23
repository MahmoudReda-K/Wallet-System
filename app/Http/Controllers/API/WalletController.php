<?php

namespace App\Http\Controllers\API;

use App\Exceptions\SameSenderAndReceiverException;
use Exception;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Http\Requests\TopUpRequest;
use App\Http\Resources\WalletResource;
use App\Http\Requests\TransferRequest;
use App\Http\Controllers\BaseController;
use App\Exceptions\WalletNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\InsufficientBalanceException;

class WalletController extends BaseController
{
    public function __construct(public WalletService $walletService){}

    public function checkBalance(Request $request)
    {
        try {
            $user = $request->user();
            $balance = $this->walletService->getCurrentBalance($user);
            return $this->successResponse(['balance' => $balance], Response::HTTP_OK, 'User balance.');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function topUp(TopUpRequest $request)
    {
        try {
            $user = $request->user();
            $wallet = $this->walletService->topUp($user, $request->validated());
            return $this->successResponse(['wallet' => new WalletResource($wallet)],Response::HTTP_OK, 'Top-up successful.');
        } catch (WalletNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            return $this->errorResponse('Top-up failed.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function transfer(TransferRequest $request)
    {
        try {
            $sender = $request->user();
            $wallet = $this->walletService->transfer($sender, $request->validated());
            return $this->successResponse(['wallet' => new WalletResource($wallet)],Response::HTTP_OK, 'Transfer successful.');
        } catch (WalletNotFoundException | InsufficientBalanceException | SameSenderAndReceiverException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            return $this->errorResponse('Transfer failed.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
