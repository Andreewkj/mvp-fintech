<?php

namespace App\Providers;

use App\Application\Factories\TransferFactory;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Infra\LaravelTransactionManager;
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
        $this->app->bind(TransactionManagerInterface::class, LaravelTransactionManager::class);

        $this->app->bind(TransferFactory::class);
        $this->app->bind(WalletRepository::class);
        $this->app->bind(UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
