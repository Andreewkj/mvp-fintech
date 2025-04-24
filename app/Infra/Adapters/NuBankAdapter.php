<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

use App\Domain\Entities\Transfer;
use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Exceptions\TransferAuthorizationException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Throwable;

class NuBankAdapter implements BankAdapterInterface
{
    private Client $client;
    private string $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('NUBANK_API_URL');
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function authorizeTransfer(Transfer $transfer): bool
    {
        try {
            retry(3, function () use ($transfer) {
                $this->client->get($this->url);
            }, 2000);
            return true;
        } catch (GuzzleException $e) {
            Log::critical('Error authorizing transfer id: ' . $transfer->getId() . ', error: ' . $e->getMessage());
            throw new TransferAuthorizationException('Authorization failed for transfer id: ' . $transfer->getId());
        }
    }
}
