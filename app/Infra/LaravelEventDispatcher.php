<?php

declare(strict_types=1);

namespace App\Infra;

use App\Domain\Contracts\EventDispatcherInterface;
use Illuminate\Support\Facades\Event;

class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function dispatch(mixed $event): void
    {
        Event::dispatch($event);
    }
}

