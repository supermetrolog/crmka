<?php

namespace app\enum\Company;

use app\enum\AbstractEnum;

class CompanyStatusEnum extends AbstractEnum
{
	public const PASSIVE = 0;
	public const ACTIVE  = 1;
	public const DELETED = 2;

	public static function labels(): array
	{
		return [
			self::PASSIVE => 'Пассив',
			self::ACTIVE  => 'Актив',
			self::DELETED => 'Удалена',
		];
	}
}