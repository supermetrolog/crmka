<?php

namespace app\daemons\loops\notification;

use app\components\NotificationsQueueService;
use app\daemons\loops\BaseLoop;
use app\daemons\Message;
use Exception;

class NotifyLoop extends BaseLoop
{
	private NotificationsQueueService $notifyQueue;

	public function __construct(NotificationsQueueService $notifyQueue)
	{
		parent::__construct();

		$this->notifyQueue = $notifyQueue;
	}

	/**
	 * @throws Exception
	 */
	public function processed(): void
	{
		while ($message = $this->notifyQueue->get()) {
			$body = $message->getBody();

			$webMessage = new Message();
			$webMessage->setAction($body->action ?? Message::ACTION_NEW_NOTIFICATION);

			$webMessage->setBody($body->payload ?? '');
			$webMessage->setTime($body->ts ?? time());

			if ($this->clients->hasUser($body->consultant_id)) {
				$this->clients->sendToUser($body->consultant_id, $webMessage);
			}

			$message->getNativeMessage()->ack();
		}
	}
}
