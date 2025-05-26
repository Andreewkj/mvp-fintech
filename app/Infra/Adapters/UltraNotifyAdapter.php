<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

use App\Domain\Contracts\Adapters\NotifyAdapterInterface;
use App\Domain\Contracts\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class UltraNotifyAdapter implements NotifyAdapterInterface
{
    private Client $client;
    private string $url;

    public function __construct(
        private readonly LoggerInterface $logger
    )
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
            $this->client->post($this->url, [
                $data['email']
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error("Failed to send email to user id: {$data['user_id']}, email: {$data['email']}, error: {$e->getMessage()}");
            throw new Exception('Error sending email to user: ' . $data['email']);
        }
    }

    /**
     * @throws Exception
     */
    public function notifyBySms(array $data): void
    {
        try {
            $this->client->post($this->url, [
                $data['phone']
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error("Failed to send SMS to user id: {$data['user_id']}, phone: {$data['phone']}, error: {$e->getMessage()}");
            throw new Exception('Error sending message to user phone: ' . $data['phone']);
        }
    }
}
