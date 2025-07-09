<?php

namespace app\enum;

class PhoneStatusEnum extends AbstractEnum
{
	public const ACTIVE  = 'active';
	public const PASSIVE = 'passive';

	public static function labels(): array
	{
		return [
			self::ACTIVE  => 'Актив',
			self::PASSIVE => 'Пассив',
		];
	}
}