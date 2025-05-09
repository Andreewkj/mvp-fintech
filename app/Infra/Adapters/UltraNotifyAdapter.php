<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

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
    public function notifyByEmail(array $data): void
    {
        try {
            $response = $this->client->post($this->url, [
                $data['email']
            ]);
        } catch (GuzzleException $e) {
            Log::error("Failed to send email to user id: {$data['user_id']}, email: {$data['email']}, error: {$e->getMessage()}");
            throw new Exception('Error sending email to user: ' . $data['email']);
        }
    }

    /**
     * @throws Exception
     */
    public function notifyBySms(array $data): void
    {
        try {
            $response = $this->client->post($this->url, [
                $data['phone']
            ]);
        } catch (GuzzleException $e) {
            Log::error("Failed to send SMS to user id: {$data['user_id']}, phone: {$data['phone']}, error: {$e->getMessage()}");
            throw new Exception('Error sending message to user phone: ' . $data['phone']);
        }
    }
}
