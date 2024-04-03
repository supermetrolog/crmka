<?php

declare(strict_types=1);

namespace app\helpers;

use DateTime;
use DateTimeInterface;

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
}