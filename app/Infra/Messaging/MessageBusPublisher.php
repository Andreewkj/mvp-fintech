<?php

namespace App\Infra\Messaging;

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageBusPublisher
{
    protected AbstractConnection $connection;
    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'localhost'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest'),
            env('RABBITMQ_VHOST', '/')
        );
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare('transfer_notifications', 'fanout', false, true, false);
    }

    public function publishMessage(string $messageContent): void
    {
        $msg = new AMQPMessage($messageContent);
        $this->channel->basic_publish($msg, 'transfer_notifications');

        $this->channel->close();
        $this->connection->close();
    }
}
