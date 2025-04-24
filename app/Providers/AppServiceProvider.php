<?php

namespace App\Providers;

use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Contracts\Adapters\NotifyAdapterInterface;
use App\Domain\Contracts\DispatcherInterface;
use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Domain\Contracts\RequestValidateInterface;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Http\Requests\CreateLoginRequest;
use App\Http\Requests\CreateTransferRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\CreateWalletRequest;
use App\Infra\Adapters\UltraBankAdapter;
use App\Infra\Adapters\UltraNotifyAdapter;
use App\Infra\LaravelDispatcher;
use App\Infra\LaravelTransactionManager;
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
        $this->app->bind(BankAdapterInterface::class, UltraBankAdapter::class);
        $this->app->bind(DispatcherInterface::class, LaravelDispatcher::class);
        $this->app->bind(TransactionManagerInterface::class, LaravelTransactionManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
