<?php

namespace App\Domain\Contracts;

interface EventDispatcherInterface
{
    public function dispatch(mixed $event): void;
}

