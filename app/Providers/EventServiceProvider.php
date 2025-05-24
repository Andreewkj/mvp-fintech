<?php

namespace App\Providers;

use App\Domain\Contracts\EventDispatcherInterface;
use App\Events\TransferWasCompleted;
use App\Infra\LaravelEventDispatcher;
use App\Listeners\NotifyPayee;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EventDispatcherInterface::class, LaravelEventDispatcher::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(
            TransferWasCompleted::class,
            NotifyPayee::class,
        );
    }
}
