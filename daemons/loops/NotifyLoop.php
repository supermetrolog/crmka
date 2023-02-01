<?php

namespace app\daemons\loops;

use app\daemons\Message;
use app\models\Notification;
use app\daemons\loops\BaseLoop;
use app\components\ConsoleLogger;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use app\components\NotificationsQueueService;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class NotifyLoop extends BaseLoop
{

    private NotificationsQueueService $notifyQueue;

    public function __construct(NotificationsQueueService $notifyQueue)
    {
        $this->notifyQueue = $notifyQueue;
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
        $message = $this->notifyQueue->get();
        if ($message) {
            $message->ack();
            ConsoleLogger::info($message->body);
        }
    }
}
