<?php

declare(strict_types=1);

namespace App\Events;

use App\Domain\Interfaces\NotifyAdapterInterface;
use App\Models\Transfer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferWasCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Transfer $transfer,
        public NotifyAdapterInterface $notifyAdapter
    )
    {
        //
    }
}
