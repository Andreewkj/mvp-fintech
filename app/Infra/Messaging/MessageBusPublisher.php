<?php

namespace App\Infra\Messaging;

use App\Domain\Entities\User;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class MessageBusPublisher
{
    protected AMQPChannel $channel;

    public function __construct(
        protected RabbitMQConnectionFactory $connectionFactory
    ) {
        $connection = $this->connectionFactory->getConnection();
        $this->channel = $connection->channel();

        $this->channel->exchange_declare(
            'transfer_notifications',
            'direct',
            false,
            true,
            false
        );
    }

    public function publishMessage(User $user): void
    {
        $smsPayload = json_encode([
            'user_id' => $user->getId(),
            'phone' => $user->getPhone()->getValue(),
            'message' => "Hello {$user->getName()}, Your transfer was completed successfully"
        ]);

        $emailPayload = json_encode([
            'user_id' => $user->getId(),
            'email' => $user->getEmail()->getValue(),
            'message' => "Good news {$user->getName()}, Your transfer was completed successfully"
        ]);

        $this->publish('sms', $smsPayload);
        $this->publish('email', $emailPayload);
    }

    protected function publish(string $routingKey, string $body): void
    {
        $msg = new AMQPMessage($body, [
            'delivery_mode' => 2
        ]);

        $this->channel->basic_publish($msg, 'transfer_notifications', $routingKey);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connectionFactory->close();
    }
}
