<?php

declare(strict_types=1);

namespace App\Infra;

use App\Domain\Contracts\DispatcherInterface;

class LaravelDispatcher implements DispatcherInterface
{
    public function dispatch(mixed $job): void
    {
        dispatch($job);
    }
}

