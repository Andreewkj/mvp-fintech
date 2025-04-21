<?php

namespace App\Domain\Contracts;

interface DispatcherInterface
{
    public function dispatch(mixed $job): void;
}

