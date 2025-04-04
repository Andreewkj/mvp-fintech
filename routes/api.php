<?php

use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// TODO: implementar autenticação por ultimo
Route::post('/transfer/create', [TransferController::class, 'makeTransfer']);
Route::post('/wallet/create', [WalletController::class, 'createWallet']);

Route::group(['prefix' => '/user'], function () {
    Route::post('/register', [UserController::class, 'store']);
});

Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [UserController::class, 'index']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'transfer'], function () {
        Route::post('/transfer', [UserController::class, 'transfer']);
    });
});
