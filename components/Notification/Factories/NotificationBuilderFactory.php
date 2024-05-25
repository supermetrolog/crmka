<?php

declare(strict_types=1);

namespace app\components\Notification\Factories;

use app\components\Notification\Builders\NotificationBuilder;

class NotificationBuilderFactory
{
	public function create(): NotificationBuilder
	{
		return new NotificationBuilder();
	}
}