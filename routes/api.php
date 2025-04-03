<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
