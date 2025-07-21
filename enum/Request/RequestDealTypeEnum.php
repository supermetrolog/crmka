<?php

namespace app\enum\Request;

use app\enum\AbstractEnum;

class RequestDealTypeEnum extends AbstractEnum
{
	public const RENT             = 0;
	public const SALE             = 1;
	public const RESPONSE_STORAGE = 2;
	public const SUBLEASE         = 3;

	public static function labels(): array
	{
		return [
			self::RENT             => 'Аренда',
			self::SALE             => 'Продажа',
			self::RESPONSE_STORAGE => 'Ответ-хранение',
			self::SUBLEASE         => 'Субаренда',
		];
	}
}