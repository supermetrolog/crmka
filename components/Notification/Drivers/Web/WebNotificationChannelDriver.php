<?php

declare(strict_types=1);

namespace app\components\Notification\Drivers\Web;

use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\NotificationChannelDriverInterface;
use app\components\Notification\Interfaces\NotificationInterface;
use yii\base\ErrorException;

class WebNotificationChannelDriver implements NotificationChannelDriverInterface
{

	/**
	 * @throws ErrorException
	 */
	public function send(NotifiableInterface $notifiable, NotificationInterface $notification): void
	{
		if (!($notifiable instanceof WebNotifiableInterface)) {
			throw new ErrorException('Notifiable not supported web driver');
		}

		$id = $notifiable->getId();

		dump($id, $notifiable instanceof WebNotifiableInterface);

		// TODO: Implement send() method.
	}
}