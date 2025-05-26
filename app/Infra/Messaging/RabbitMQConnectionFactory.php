<?php

declare(strict_types=1);

namespace App\Infra\Messaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConnectionFactory
{
    private ?AMQPStreamConnection $connection = null;

    public function getConnection(): AMQPStreamConnection
    {
        if ($this->connection === null || !$this->connection->isConnected()) {
            $this->connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST', 'localhost'),
                env('RABBITMQ_PORT', 5672),
                env('RABBITMQ_USER', 'guest'),
                env('RABBITMQ_PASSWORD', 'guest'),
                env('RABBITMQ_VHOST', '/')
            );
        }

        return $this->connection;
    }

    public function close(): void
    {
        if ($this->connection && $this->connection->isConnected()) {
            $this->connection->close();
        }
    }
}

