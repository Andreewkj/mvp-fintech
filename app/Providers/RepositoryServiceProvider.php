<?php

namespace App\Providers;

use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Infra\Repositories\TransferRepository;
use App\Infra\Repositories\UserRepository;
use App\Infra\Repositories\WalletRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TransferRepositoryInterface::class, TransferRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
