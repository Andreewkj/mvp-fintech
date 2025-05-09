<?php

namespace App\Console\Commands;

use App\Infra\Messaging\Consumers\NotifyConsumer;
use App\Infra\Messaging\RabbitMQChannelFactory;
use Illuminate\Console\Command;

class RunNotifyConsumer extends Command
{
    protected $signature = 'consumer:notify';
    protected $description = 'Inicia o consumer da fila notify_queue';

    public function __construct(
        protected NotifyConsumer $notifyConsumer,
        protected RabbitMQChannelFactory $channelFactory
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $channel = $this->channelFactory->makeWithMultipleQueues([
            'notify_email' => ['routing_key' => 'email'],
            'notify_sms'   => ['routing_key' => 'sms'],
        ], 'transfer_notifications');

        $channel->basic_consume('notify_email', '', false, false, false, false, [$this->notifyConsumer, 'consumeEmail']);
        $channel->basic_consume('notify_sms', '', false, false, false, false, [$this->notifyConsumer, 'consumeSms']);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $channel->getConnection()->close();
    }
}
