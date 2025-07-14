<?php

namespace app\enum\Phone;

use app\enum\AbstractEnum;

class PhoneTypeEnum extends AbstractEnum
{
	public const MOBILE   = 'mobile';
	public const HOME     = 'home';
	public const WORK     = 'work';
	public const WHATSAPP = 'whatsapp';

	public static function labels(): array
	{
		return [
			self::MOBILE   => 'Мобильный',
			self::HOME     => 'Домашний',
			self::WORK     => 'Рабочий',
			self::WHATSAPP => 'WhatsApp',
		];
	}
}