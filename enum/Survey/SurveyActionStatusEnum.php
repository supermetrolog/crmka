<?php

namespace app\enum\Survey;

use app\enum\AbstractEnum;

class SurveyActionStatusEnum extends AbstractEnum
{
	public const PENDING  = 'pending';
	public const DONE     = 'done';
	public const CANCELED = 'canceled';

	public static function labels(): array
	{
		return [
			self::PENDING  => 'В процессе',
			self::DONE     => 'Выполнено',
			self::CANCELED => 'Отменено',
		];
	}
}