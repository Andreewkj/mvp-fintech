<?php

namespace App\Console\Commands;

use App\Infra\Messaging\Consumers\NotifyConsumer;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RunNotifyConsumer extends Command
{
    protected $signature = 'consumer:notify';
    protected $description = 'Inicia o consumer da fila notify_queue';
    protected NotifyConsumer $notifyConsumer;

    public function __construct(NotifyConsumer $notifyConsumer)
    {
        parent::__construct();
        $this->notifyConsumer = $notifyConsumer; // Armazena a instância do NotifyConsumer
    }


    public function handle()
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'localhost'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest'),
            env('RABBITMQ_VHOST', '/')
        );

        $channel = $connection->channel();

        $channel->exchange_declare('transfer_notifications', 'fanout', false, true, false);
        $channel->queue_declare('notify_payee', false, true, false, false);
        $channel->queue_bind('notify_payee', 'transfer_notifications');

        $channel->basic_consume(
            'notify_payee',
            '',
            false,
            false,
            false,
            false,
            [$this->notifyConsumer, 'consume']
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
