<?php

declare(strict_types=1);

namespace App\Domain\Adapters;

use App\Domain\Interfaces\NotifyAdapterInterface;
use App\Models\User;
use GuzzleHttp\Client;

class UltraNotifyAdapter implements NotifyAdapterInterface
{
    private Client $client;
    private string $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('ULTRA_NOTIFY_API_URL');
    }

    public function notifyByEmail(User $user): void
    {
        $response = $this->client->post($this->url, [
            $user->email
        ]);

        if ($response->getStatusCode() !== 204) {
            throw new \Exception('Error sending email to user: ' . $user->email);
        }
    }

    public function notifyBySms(User $user): void
    {
        $response = $this->client->post($this->url, [
            $user->phone
        ]);

        if ($response->getStatusCode() !== 204) {
            throw new \Exception('Error sending email to user: ' . $user->email);
        }
    }
}
