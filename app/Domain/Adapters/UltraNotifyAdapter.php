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
        dd('ara ara');
        $this->client->post($this->url, [
            $user->email
        ]);
    }

    public function notifyBySms(User $user): void
    {
        $this->client->post($this->url, [
            $user->phone
        ]);
    }
}
