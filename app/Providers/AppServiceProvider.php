<?php

namespace App\Providers;

use App\Domain\Interfaces\Adapters\BankAdapterInterface;
use App\Domain\Interfaces\Adapters\NotifyAdapterInterface;
use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Domain\Interfaces\RequestValidateInterface;
use App\Http\Requests\CreateLoginRequest;
use App\Http\Requests\CreateTransferRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\CreateWalletRequest;
use App\Infra\Adapters\NuBankAdapter;
use App\Infra\Adapters\UltraNotifyAdapter;
use App\Infra\Repositories\TransferRepository;
use App\Infra\Repositories\UserRepository;
use App\Infra\Repositories\WalletRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TransferRepositoryInterface::class, TransferRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RequestValidateInterface::class,CreateUserRequest::class);
        $this->app->bind(RequestValidateInterface::class,CreateWalletRequest::class);
        $this->app->bind(RequestValidateInterface::class,CreateTransferRequest::class);
        $this->app->bind(RequestValidateInterface::class,CreateLoginRequest::class);
        $this->app->bind(NotifyAdapterInterface::class, UltraNotifyAdapter::class);
        $this->app->bind(BankAdapterInterface::class, NuBankAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
