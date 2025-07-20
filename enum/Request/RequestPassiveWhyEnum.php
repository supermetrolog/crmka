<?php

namespace app\enum\Request;

use app\enum\AbstractEnum;

class RequestPassiveWhyEnum extends AbstractEnum
{
	public const BLOCK        = 0;
	public const ALREADY_RENT = 1;
	public const ALREADY_BUY  = 2;
	public const OUTDATED     = 3;
	public const SUSPEND      = 4;
	public const OTHER        = 5;
	public const SURVEY       = 6;

	public static function labels(): array
	{
		return [
			self::BLOCK        => 'Заблокировано модератором',
			self::ALREADY_RENT => 'Сняли',
			self::ALREADY_BUY  => 'Купили',
			self::OUTDATED     => 'Запрос устарел',
			self::SUSPEND      => 'Отложили поиск',
			self::OTHER        => 'Иное',
			self::SURVEY       => 'По результату опроса',
		];
	}
}