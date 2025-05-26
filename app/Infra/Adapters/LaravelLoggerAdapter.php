<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

use App\Domain\Contracts\LoggerInterface;
use Illuminate\Support\Facades\Log;

class LaravelLoggerAdapter implements LoggerInterface
{
    public function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        Log::critical($message, $context);
    }
}
