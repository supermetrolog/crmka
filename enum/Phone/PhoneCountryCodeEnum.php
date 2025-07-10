<?php

namespace app\enum\Phone;

use app\enum\AbstractEnum;

class PhoneCountryCodeEnum extends AbstractEnum
{
	public const RU = 'RU';
	public const BY = 'BY';
	public const UA = 'UA';

	public static function labels(): array
	{
		return [
			self::RU => 'Россия',
			self::BY => 'Беларусь',
			self::UA => 'Украина',
		];
	}
}