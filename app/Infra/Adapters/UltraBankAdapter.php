<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Entities\Transfer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Throwable;

class UltraBankAdapter implements BankAdapterInterface
{
    private Client $client;
    private string $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('NUBANK_API_URL');
    }

    public function authorizeTransfer(Transfer $transfer): bool
    {
        try {
            retry(3, function () {
                $this->client->get($this->url);
            }, 2000);

            Log::info('Transfer id: ' . $transfer->getTransferId() . ' authorized');

            return true;
        } catch (GuzzleException | Throwable $e) {
            Log::critical('Error authorizing transfer id: ' . $transfer->getTransferId() . ', error: ' . $e->getMessage());
            return false;
        }
    }
}
