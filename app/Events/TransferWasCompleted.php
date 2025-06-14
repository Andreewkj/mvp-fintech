<?php

namespace App\Events;

use App\Domain\Entities\Transfer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferWasCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Transfer $transfer)
    {}
}
