<?php

namespace app\enum\Notification;

use app\enum\AbstractEnum;

class UserNotificationTemplatePriorityEnum extends AbstractEnum
{
	public const LOW    = 'low';
	public const NORMAL = 'normal';
	public const HIGH   = 'high';
	public const URGENT = 'urgent';

	public static function labels(): array
	{
		return [
			self::LOW    => 'Низкий',
			self::NORMAL => 'Обычный',
			self::HIGH   => 'Важно',
			self::URGENT => 'Срочно',
		];
	}
}