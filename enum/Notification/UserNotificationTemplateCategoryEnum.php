<?php

namespace app\enum\Notification;

use app\enum\AbstractEnum;

class UserNotificationTemplateCategoryEnum extends AbstractEnum
{
	public const REQUEST  = 'request';
	public const COMPANY  = 'company';
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
			self::REQUEST  => 'Запросы',
			self::COMPANY  => 'Компании',
			self::TASKS    => 'Задачи',
			self::MEETING  => 'Встречи',
			self::MESSAGE  => 'Сообщения',
			self::REMINDER => 'Напоминания',
			self::SYSTEM   => 'Система',
			self::CLIENT   => 'Клиенты',
			self::DEAL     => 'Сделки',
		];
	}
}