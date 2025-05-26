<?php

namespace App\Providers;

use App\Domain\Contracts\CreateUserRequestValidateInterface;
use App\Domain\Contracts\CreateWalletRequestValidateInterface;
use App\Domain\Contracts\LoginUserRequestValidateInterface;
use App\Http\Requests\CreateTransferRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\CreateWalletRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\ServiceProvider;

class RequestValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CreateUserRequestValidateInterface::class,CreateUserRequest::class);
        $this->app->bind(CreateWalletRequestValidateInterface::class,CreateWalletRequest::class);
        $this->app->bind(CreateUserRequestValidateInterface::class,CreateTransferRequest::class);
        $this->app->bind(LoginUserRequestValidateInterface::class,LoginUserRequest::class);
    }

    public function boot(): void
    {
        //
    }
}
