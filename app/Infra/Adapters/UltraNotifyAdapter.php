<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

use App\Domain\Entities\User;
use App\Domain\Contracts\Adapters\NotifyAdapterInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

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
     * @throws Exception
     */
    public function notifyByEmail(User $user): void
    {
        try {
            $response = $this->client->post($this->url, [
                $user->getEmail()->getValue()
            ]);
        } catch (GuzzleException $e) {
            Log::error("Failed to send email to user id: {$user->getId()}, email: {$user->getPhone()->getValue()}, error: {$e->getMessage()}");
            throw new Exception('Error sending email to user: ' . $user->getEmail()->getValue());
        }
    }

    /**
     * @throws Exception
     */
    public function notifyBySms(User $user): void
    {
        try {
            $response = $this->client->post($this->url, [
                $user->getPhone()->getValue()
            ]);
        } catch (GuzzleException $e) {
            Log::error("Failed to send SMS to user id: {$user->getId()}, phone: {$user->getPhone()->getValue()}, error: {$e->getMessage()}");
            throw new Exception('Error sending message to user phone: ' . $user->getPhone()->getValue());
        }
    }
}
