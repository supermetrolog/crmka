<?php

namespace app\enum\Letter;

use app\enum\AbstractEnum;

class LetterContactAnswerTypeEnum extends AbstractEnum
{
	public const EMAIL = 'email';
	public const CALL  = 'call';
	public const OTHER = 'other';

	public static function labels(): array
	{
		return [
			self::EMAIL => 'Email',
			self::CALL  => 'Звонок',
			self::OTHER => 'Другое',
		];
	}
}