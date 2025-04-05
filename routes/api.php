<?php

use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// TODO: implementar autenticação por ultimo

Route::group(['prefix' => '/user'], function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'store']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/wallet/create', [WalletController::class, 'createWallet']);

    Route::group(['prefix' => 'transfer'], function () {
        Route::post('/create', [TransferController::class, 'makeTransfer']);
    });
});
