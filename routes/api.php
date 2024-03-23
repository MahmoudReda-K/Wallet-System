<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\TransactionController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/register', [RegistrationController::class, 'register']);
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wallet/balance', [WalletController::class, 'checkBalance']);
    Route::post('/wallet/top-up', [WalletController::class, 'topUp']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']);
    Route::get('/transactions', [TransactionController::class, 'transactionHistory']);
});
