<?php

namespace App\Infra\Adapters;

use App\Domain\Contracts\Adapters\BankAdapterInterface;
use App\Domain\Contracts\LoggerInterface;
use App\Domain\Entities\Transfer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

class UltraBankAdapter implements BankAdapterInterface
{
    private Client $client;
    private string $url;

    public function __construct(
        private readonly LoggerInterface $logger
    )
    {
        $this->client = new Client();
        $this->url = env('ULTRA_API_URL');
    }

    public function authorizeTransfer(Transfer $transfer): bool
    {
        try {
            retry(3, function () {
                $this->client->get($this->url);
            }, 2000);

            $this->logger->info('Transfer id: ' . $transfer->getTransferId() . ' authorized');
            return true;
        } catch (GuzzleException | Throwable $e) {
            $this->logger->critical('Error authorizing transfer id: ' . $transfer->getTransferId() . ', error: ' . $e->getMessage());
            return false;
        }
    }
}
