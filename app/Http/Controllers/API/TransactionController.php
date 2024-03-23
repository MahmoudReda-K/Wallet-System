<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Services\TransactionService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\TransactionResource;

use Symfony\Component\HttpFoundation\Response;

class TransactionController extends BaseController
{
    public function __construct(public TransactionService $transactionService){}

    public function transactionHistory(Request $request)
    {
        try {
            $transactions = $this->transactionService->getTransactionHistory($request->user());
            $formattedTransactions = TransactionResource::collection($transactions);
            return $this->successResponse($formattedTransactions);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
