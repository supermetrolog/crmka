<?php

namespace app\enum\Notification;

use app\enum\AbstractEnum;

class UserNotificationTemplateCategoryEnum extends AbstractEnum
{
	public const REQUEST  = 'request';
	public const TASKS    = 'tasks';
	public const MEETING  = 'meeting';
	public const MESSAGE  = 'message';
	public const REMINDER = 'reminder';
	public const SYSTEM   = 'system';
	public const CLIENT   = 'client';
	public const DEAL     = 'deal';

	public static function labels(): array
	{
		return [
			self::REQUEST  => 'Запрос',
			self::TASKS    => 'Задачи',
			self::MEETING  => 'Встреча',
			self::MESSAGE  => 'Сообщение',
			self::REMINDER => 'Напоминание',
			self::SYSTEM   => 'Система',
			self::CLIENT   => 'Клиент',
			self::DEAL     => 'Сделка',
		];
	}
}