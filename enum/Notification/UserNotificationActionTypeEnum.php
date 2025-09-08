<?php

namespace app\enum\Notification;

use app\enum\AbstractEnum;

class UserNotificationActionTypeEnum extends AbstractEnum
{
	public const NAVIGATE = 'navigate';
	public const COMMAND  = 'command';

	public static function labels(): array
	{
		return [
			static::NAVIGATE => 'Переход',
			static::COMMAND  => 'Операция',
		];
	}
}