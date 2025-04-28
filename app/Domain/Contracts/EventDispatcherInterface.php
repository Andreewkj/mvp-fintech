<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

interface EventDispatcherInterface
{
    public function dispatch(mixed $event): void;
}

