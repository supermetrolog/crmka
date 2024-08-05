<?php

declare(strict_types=1);

namespace app\components\Notification\Drivers\Web;

use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationChannelDriverInterface;
use app\components\Notification\Interfaces\StoredNotificationInterface;

class WebNotificationChannelDriver implements NotificationChannelDriverInterface
{
	public function send(NotifiableInterface $notifiable, StoredNotificationInterface $notification): void
	{
		// Ничего не надо делать так как user notification уже создался и будем работать с ним фильтруая по каналу
	}
}