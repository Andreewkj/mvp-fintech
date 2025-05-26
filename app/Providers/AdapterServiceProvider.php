<?php

namespace App\Providers;

use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Contracts\Adapters\NotifyAdapterInterface;
use App\Domain\Contracts\LoggerInterface;
use App\Infra\Adapters\LaravelLoggerAdapter;
use App\Infra\Adapters\UltraBankAdapter;
use App\Infra\Adapters\UltraNotifyAdapter;
use Illuminate\Support\ServiceProvider;

class AdapterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotifyAdapterInterface::class, UltraNotifyAdapter::class);
        $this->app->bind(BankAdapterInterface::class, UltraBankAdapter::class);
        $this->app->bind(LoggerInterface::class, LaravelLoggerAdapter::class);
    }

    public function boot(): void
    {
        //
    }
}
