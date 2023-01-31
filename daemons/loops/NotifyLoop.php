<?php

namespace app\daemons\loops;

use app\components\ConsoleLogger;
use app\daemons\loops\BaseLoop;
use app\daemons\Message;
use app\models\Notification;
use Interop\Amqp\Impl\AmqpBind;
use Interop\Amqp\Impl\AmqpQueue;
use Interop\Amqp\Impl\AmqpTopic;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;

class NotifyLoop extends BaseLoop
{

    private AMQPChannel $channel;

    public function __construct()
    {
        $queue = 'notifycations';
        $exchange = 'notify_topic';

        $conn = new AMQPStreamConnection(
            'localhost',
            5672,
            'guest',
            'guest'
        );
        $channel = $conn->channel();

        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

        $channel->queue_bind($queue, $exchange);
        $this->channel = $channel;
    }
    public function processed()
    {
        ConsoleLogger::info('processed NOTIFY LOOP');
        $users_ids = $this->clients->getClientsIds();
        $models = Notification::find()->where(['notification.consultant_id' => $users_ids])->andWhere(['status' => Notification::NO_FETCHED_STATUS])->all();
        $modelsArray = $this->changeIndex($models, 'consultant_id');
        $message = new Message();
        $message->setAction(Message::ACTION_NEW_NOTIFICATION);
        foreach ($modelsArray as $user_id => $userNotify) {
            $message->setBody(count($modelsArray[$user_id]));
            $this->clients->sendClientPool($user_id, $message);
        }


        Notification::changeNoFetchedStatusToFetched($models);
        $message = $this->channel->basic_get('notifycations');
        if ($message) {
            $message->ack();
            ConsoleLogger::info($message->body);
        }
    }
}
