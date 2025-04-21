<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

use App\Domain\Entities\Transfer;
use App\Domain\Contracts\Adapters\BankAdapterInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;

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
     * @throws GuzzleException
     * @throws Exception
     */
    public function authorizeTransfer(Transfer $transfer): void
    {
        $response = $this->client->get($this->url);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new Exception('Error authorizing transfer id: ' . $transfer->getId());
        }
    }
}
