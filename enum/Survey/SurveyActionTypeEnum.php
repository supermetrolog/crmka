<?php

namespace app\enum\Survey;

use app\enum\AbstractEnum;

class SurveyActionTypeEnum extends AbstractEnum
{
	public const CALL   = 'call';
	public const LETTER = 'letter';
	public const TASK   = 'task';

	public static function labels(): array
	{
		return [
			self::CALL   => 'Звонок',
			self::LETTER => 'Письмо',
			self::TASK   => 'Задача',
		];
	}
}