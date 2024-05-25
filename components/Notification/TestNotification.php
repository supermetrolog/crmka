<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Drivers\Web\WebNotificationInterface;

class TestNotification extends AbstractNotification implements WebNotificationInterface
{


	public function getSubject(): string
	{
		return 'Request';
	}

	public function getContent(): string
	{
		return 'Request assigned to you';
	}
}