<?php

declare(strict_types=1);

namespace App\Infra;

use App\Domain\Contracts\TransactionManagerInterface;
use Illuminate\Support\Facades\DB;

class LaravelTransactionManager implements TransactionManagerInterface
{
    public function run(\Closure $callback)
    {
        return DB::transaction($callback);
    }

    public function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollback(): void
    {
        DB::rollBack();
    }
}
