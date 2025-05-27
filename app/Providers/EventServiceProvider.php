<?php

namespace App\Providers;

use App\Domain\Contracts\EventDispatcherInterface;
use App\Events\TransferWasCompleted;
use App\Infra\LaravelEventDispatcher;
use App\Listeners\NotifyPayee;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public bool $shouldDiscoverEvents = false;

    public function register(): void
    {
        $this->app->bind(EventDispatcherInterface::class, LaravelEventDispatcher::class);
    }

    protected array $listen = [
        TransferWasCompleted::class => [
            NotifyPayee::class,
        ],
    ];
}
