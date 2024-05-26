<?php

declare(strict_types=1);

namespace app\components\Notification\Drivers\Web;

use app\components\Notification\AbstractNotifiable;
use app\components\Notification\AbstractNotification;
use app\components\Notification\Interfaces\NotificationChannelDriverInterface;
use yii\base\ErrorException;

class WebNotificationChannelDriver implements NotificationChannelDriverInterface
{

	/**
	 * @throws ErrorException
	 */
	public function send(AbstractNotifiable $notifiable, AbstractNotification $notification): void
	{
		if (!($notifiable instanceof WebNotifiableInterface)) {
			throw new ErrorException('Notifiable not supported web driver');
		}

		$id = $notifiable->getId();

		// TODO: Implement send() method.
	}
}