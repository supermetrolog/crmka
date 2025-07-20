<?php

namespace app\enum\Request;

use app\enum\AbstractEnum;

class RequestStatusEnum extends AbstractEnum
{
	public const ACTIVE  = 1;
	public const PASSIVE = 0;
	public const DONE    = 2;

	/**
	 * @deprecated use RequestStatusEnum::Done
	 */
	public const DEPRECATED_DONE = 5;

	public static function labels(): array
	{
		return [
			self::ACTIVE          => 'Актив',
			self::PASSIVE         => 'Пассив',
			self::DONE            => 'Завершен',
			self::DEPRECATED_DONE => 'Завершен',
		];
	}
}