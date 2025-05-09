<?php

namespace App\Infra\Messaging;

use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQChannelFactory
{
    public function __construct(
        protected RabbitMQConnectionFactory $connectionFactory
    ) {}

    public function make(
        string $queue,
        array $queueOptions,
        string $exchange,
        array $exchangeOptions,
        string $routingKey = ''
    ): AMQPChannel {
        $connection = $this->connectionFactory->getConnection();
        $channel = $connection->channel();

        $channel->exchange_declare(
            $exchange,
            $exchangeOptions['type'] ?? 'direct',
            $exchangeOptions['passive'] ?? false,
            $exchangeOptions['durable'] ?? true,
            $exchangeOptions['auto_delete'] ?? false,
            $exchangeOptions['internal'] ?? false,
            $exchangeOptions['nowait'] ?? false,
            $exchangeOptions['arguments'] ?? [],
            $exchangeOptions['ticket'] ?? null
        );

        $channel->queue_declare(
            $queue,
            $queueOptions['passive'] ?? false,
            $queueOptions['durable'] ?? true,
            $queueOptions['exclusive'] ?? false,
            $queueOptions['auto_delete'] ?? false,
            $queueOptions['nowait'] ?? false,
            $queueOptions['arguments'] ?? [],
            $queueOptions['ticket'] ?? null
        );

        $channel->queue_bind($queue, $exchange, $routingKey);

        return $channel;
    }

    public function makeWithMultipleQueues(
        array $queues,
        string $exchange,
        array $exchangeOptions = []
    ): AMQPChannel {
        $connection = $this->connectionFactory->getConnection();
        $channel = $connection->channel();

        $channel->exchange_declare(
            $exchange,
            $exchangeOptions['type'] ?? 'direct',
            $exchangeOptions['passive'] ?? false,
            $exchangeOptions['durable'] ?? true,
            $exchangeOptions['auto_delete'] ?? false
        );

        foreach ($queues as $queue => $options) {
            $channel->queue_declare(
                $queue,
                $options['passive'] ?? false,
                $options['durable'] ?? true,
                $options['exclusive'] ?? false,
                $options['auto_delete'] ?? false
            );

            $channel->queue_bind($queue, $exchange, $options['routing_key'] ?? '');
        }

        return $channel;
    }
}
