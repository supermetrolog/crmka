<?php

namespace app\daemons\loops\notification;

use app\daemons\Message;
use app\daemons\loops\BaseLoop;
use app\components\ConsoleLogger;
use app\components\NotificationsQueueService;

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
        $webMessage = new Message();
        $webMessage->setAction(Message::ACTION_NEW_NOTIFICATION);
        while ($message = $this->notifyQueue->get()) {
            $notif = $message->getBody();
            ConsoleLogger::info("new notification for consultant with ID: " . $notif->consultant_id);

            $webMessage->setBody(1);
            if ($this->clients->isExistByUserID($notif->consultant_id)) {
                $this->clients->sendClientPool($notif->consultant_id, $webMessage);
            }
            $message->getNativeMessage()->ack();
        }
    }
}
