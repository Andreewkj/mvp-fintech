<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

use App\Domain\Entities\User;
use App\Domain\Interfaces\Adapters\NotifyAdapterInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class UltraNotifyAdapter implements NotifyAdapterInterface
{
    private Client $client;
    private string $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('ULTRA_NOTIFY_API_URL');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function notifyByEmail(User $user): void
    {
        $response = $this->client->post($this->url, [
            $user->getEmail()->getValue()
        ]);

        if ($response->getStatusCode() !== 204) {
            throw new Exception('Error sending email to user: ' . $user->getEmail()->getValue());
        }
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function notifyBySms(User $user): void
    {
        $response = $this->client->post($this->url, [
            $user->getPhone()->getValue()
        ]);

        if ($response->getStatusCode() !== 204) {
            throw new Exception('Error sending message to user phone: ' . $user->getPhone()->getValue());
        }
    }
}
