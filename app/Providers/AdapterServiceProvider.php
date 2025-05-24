<?php

namespace App\Providers;

use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Contracts\Adapters\NotifyAdapterInterface;
use App\Infra\Adapters\UltraBankAdapter;
use App\Infra\Adapters\UltraNotifyAdapter;
use Illuminate\Support\ServiceProvider;

class AdapterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(NotifyAdapterInterface::class, UltraNotifyAdapter::class);
        $this->app->bind(BankAdapterInterface::class, UltraBankAdapter::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
