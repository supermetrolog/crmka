<?php

namespace app\enum\Company;

use app\enum\AbstractEnum;

class CompanyStatusReasonEnum extends AbstractEnum
{
	public const SUSPENDED   = 'suspended';
	public const BLOCKED     = 'blocked';
	public const DESTROYED   = 'destroyed';
	public const INCORRECT   = 'incorrect';
	public const OTHER       = 'other';
	public const NO_CONTACTS = 'no-contacts';

	public static function labels(): array
	{
		return [
			self::SUSPENDED   => 'Временно приостановлено',
			self::BLOCKED     => 'Заблокировано модератором',
			self::OTHER       => 'Иное',
			self::DESTROYED   => 'Компания ликвидирована',
			self::INCORRECT   => 'Идентификация невозможна',
			self::NO_CONTACTS => 'Нет активных контактов',
		];
	}
}
