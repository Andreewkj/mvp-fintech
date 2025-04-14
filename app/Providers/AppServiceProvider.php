<?php

namespace App\Providers;

use App\Domain\Adapters\NuBankAdapter;
use App\Domain\Adapters\UltraNotifyAdapter;
use App\Domain\Interfaces\Adapters\BankAdapterInterface;
use App\Domain\Interfaces\Adapters\NotifyAdapterInterface;
use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Domain\Interfaces\RequestValidateInterface;
use App\Domain\Repositories\TransferRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\WalletRepository;
use App\Domain\Requests\CreateLoginRequest;
use App\Domain\Requests\CreateTransferRequest;
use App\Domain\Requests\CreateUserRequest;
use App\Domain\Requests\CreateWalletRequest;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

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
