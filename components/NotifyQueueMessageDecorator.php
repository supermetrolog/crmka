<?php

declare(strict_types=1);

namespace app\components;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use stdClass;

class NotifyQueueMessageDecorator
{
    private AMQPMessage $message;

    public function __construct(AMQPMessage $message)
    {
        $this->message = $message;
    }

    public function getBody(): stdClass
    {
        $json = json_decode($this->message->getBody());
        if ($json === false || $json === null) {
            throw new Exception("json decode error");
        }
        return $json;
    }

    public function getNativeMessage(): AMQPMessage
    {
        return $this->message;
    }
}
