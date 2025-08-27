<?php

namespace app\enum\Letter;

use app\enum\AbstractEnum;

class LetterContactEventTypeEnum extends AbstractEnum
{
	public const OPEN  = 'open';
	public const CLICK = 'click';

	public static function labels(): array
	{
		return [
			self::OPEN  => 'Просмотр',
			self::CLICK => 'Клик',
		];
	}
}