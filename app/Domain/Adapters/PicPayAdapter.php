<?php

namespace App\Domain\Adapters;

use App\Domain\Interfaces\BankAdapterInterface;
use App\Models\Transfer;
use App\Models\Wallet;
use GuzzleHttp\Client;

class PicPayAdapter implements BankAdapterInterface
{
    private Client $client;
    private string $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('PICPAY_API_URL');
    }
    public function authorizeTransfer(Transfer $transfer) : void
    {
        $this->client->get($this->url);
    }
}
