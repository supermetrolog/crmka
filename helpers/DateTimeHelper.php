<?php

declare(strict_types=1);

namespace app\helpers;

use DateTime;
use DateTimeInterface;
use Exception;

class DateTimeHelper
{
	public static function now(): DateTimeInterface
	{
		return new DateTime();
	}

	public static function nowf(string $format = 'Y-m-d H:i:s'): string
	{
		return (new DateTime())->format($format);
	}

	/**
	 * @throws Exception
	 */
	public static function tryMake(?string $datetime): ?DateTimeInterface
	{
		if (!$datetime) {
			return null;
		}

		return self::make($datetime);
	}

	/**
	 * @throws Exception
	 */
	public static function make(string $datetime): DateTimeInterface
	{
		return new DateTime($datetime);
	}
}