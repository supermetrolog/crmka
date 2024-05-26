<?php

declare(strict_types=1);

namespace app\components\Notification\Factories;

use app\components\Notification\Notifier;
use app\models\ActiveQuery\NotificationChannelQuery;

class NotifierFactory
{
	private NotificationDriverFactory $notificationDriverFactory;
	private NotificationChannelQuery  $notificationChannelQuery;

	public function __construct(
		NotificationDriverFactory $notificationDriverFactory,
		NotificationChannelQuery $notificationChannelQuery
	)
	{
		$this->notificationDriverFactory = $notificationDriverFactory;
		$this->notificationChannelQuery  = $notificationChannelQuery;
	}

	public function create(): Notifier
	{
		return new Notifier(
			$this->notificationDriverFactory,
			$this->notificationChannelQuery
		);
	}
}