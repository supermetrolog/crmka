<?php

declare(strict_types=1);

namespace app\components\Notification\Drivers\Web;

use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationChannelDriverInterface;
use app\components\Notification\Interfaces\StoredNotificationInterface;
use app\components\Notification\Interfaces\WebsocketPublisherInterface;

class WebNotificationChannelDriver implements NotificationChannelDriverInterface
{
	protected WebsocketPublisherInterface $publisher;

	public function __construct(WebsocketPublisherInterface $publisher)
	{
		$this->publisher = $publisher;
	}

	public function send(NotifiableInterface $notifiable, StoredNotificationInterface $notification): void
	{
		$this->publisher->publishToUser(
			$notifiable->getUserId(),
			[
				'subject'         => $notification->getSubject(),
				'message'         => $notification->getMessage(),
				'notification_id' => $notification->getId(),
				'priority'        => $notification->getPriority()
			]
		);
	}
}