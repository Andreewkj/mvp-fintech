<?php

namespace App\Infra\Messaging\Consumers;

use App\Application\Services\NotifyUserService;
use PhpAmqpLib\Message\AMQPMessage;

class NotifyConsumer
{
    protected NotifyUserService $notifyUserService;

    public function __construct(NotifyUserService $notifyUserService)
    {
        $this->notifyUserService = $notifyUserService;
    }

    public function consume(AMQPMessage $msg): void
    {
        $data = json_decode($msg->getBody(), true);
        echo "Iniciando notificação para o usuário ID: {$data['user_id']}...\n";
        $this->notifyUserService->execute($data);
        echo "Notificação para o usuário ID: {$data['user_id']} foi enviada com sucesso.\n";
        $msg->ack();
    }
}
