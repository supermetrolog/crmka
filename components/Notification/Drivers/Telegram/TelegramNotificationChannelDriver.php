<?php

declare(strict_types=1);

namespace app\components\Notification\Drivers\Telegram;

use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationChannelDriverInterface;
use app\components\Notification\Interfaces\StoredNotificationInterface;
use yii\base\ErrorException;

class TelegramNotificationChannelDriver implements NotificationChannelDriverInterface
{
	/**
	 * @throws ErrorException
	 */
	public function send(NotifiableInterface $notifiable, StoredNotificationInterface $notification): void
	{
		if (!($notifiable instanceof TelegramNotifiableInterface)) {
			throw new ErrorException('This notifiable not supported telegram channel');
		}

		throw new ErrorException('Not implemented');
		// TODO: отпарвка в телегу!
	}
}