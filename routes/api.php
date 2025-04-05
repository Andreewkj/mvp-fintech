<?php

declare(strict_types=1);

use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/user'], function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'store']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'wallet'], function () {
        Route::post('/create', [WalletController::class, 'createWallet']);
    });

    Route::group(['prefix' => 'transfer'], function () {
        Route::post('/create', [TransferController::class, 'makeTransfer']);
    });
});
