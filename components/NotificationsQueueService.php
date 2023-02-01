<?php

declare(strict_types=1);

namespace app\components;

use yii\base\Component;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class NotificationsQueueService extends Component
{
    private AMQPChannel $channel;
    private AMQPStreamConnection $connection;

    public string $host;
    public int $port;
    public string $user;
    public string $password;
    public string $queueName;
    public string $exchangeName;


    public function init()
    {
        $this->connection = new AMQPStreamConnection(
            $this->host,
            $this->port,
            $this->user,
            $this->password
        );

        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, true, false, false);
        $this->channel->exchange_declare($this->exchangeName, AMQPExchangeType::DIRECT, false, true, false);
        $this->channel->queue_bind($this->queueName, $this->exchangeName);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function get(): ?NotifyQueueMessageDecorator
    {
        $message = $this->channel->basic_get($this->queueName);
        if (!$message) {
            return null;
        }

        return new NotifyQueueMessageDecorator($message);
    }
    public function publish(AMQPMessage $msg): void
    {
        $msg->setBody(json_encode($msg->getBody()));
        $this->channel->basic_publish($msg, $this->exchangeName);
    }
}
