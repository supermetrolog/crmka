<?php

namespace app\enum\Deal;

use app\enum\AbstractEnum;

class DealStatusEnum extends AbstractEnum
{
	public const ACTIVE  = 0;
	public const DELETED = -1;

	public static function labels(): array
	{
		return [
			self::ACTIVE  => 'Актив',
			self::DELETED => 'Удалена',
		];
	}
}