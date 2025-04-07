<?php

declare(strict_types=1);

namespace App\Domain\Adapters;

use App\Domain\Interfaces\BankAdapterInterface;
use App\Models\Transfer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class PicPayAdapter implements BankAdapterInterface
{
    private Client $client;
    private string $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('PICPAY_API_URL');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function authorizeTransfer(Transfer $transfer): void
    {
        $response = $this->client->get($this->url);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new Exception('Error authorizing transfer id: ' . $transfer->id);
        }
    }
}
