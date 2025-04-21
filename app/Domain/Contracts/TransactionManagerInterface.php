<?php

namespace App\Domain\Contracts;

interface TransactionManagerInterface
{
    public function run(\Closure $callback);
}
