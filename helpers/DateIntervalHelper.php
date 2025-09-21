<?php

declare(strict_types=1);

namespace app\helpers;

use DateInterval;

class DateIntervalHelper
{
	public static function days(int $days): DateInterval
	{
		return new DateInterval("P{$days}D");
	}

	public static function hours(int $hours): DateInterval
	{
		return new DateInterval("PT{$hours}H");
	}

	public static function minutes(int $minutes): DateInterval
	{
		return new DateInterval("PT{$minutes}M");
	}

	public static function seconds(int $seconds): DateInterval
	{
		return new DateInterval("PT{$seconds}S");
	}
}