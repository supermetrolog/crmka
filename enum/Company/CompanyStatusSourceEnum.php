<?php

namespace app\enum\Company;

use app\enum\AbstractEnum;

class CompanyStatusSourceEnum extends AbstractEnum
{
	public const USER   = 'user';
	public const SYSTEM = 'system';

	public static function labels(): array
	{
		return [
			self::USER   => 'Пользователь',
			self::SYSTEM => 'Система',
		];
	}
}
