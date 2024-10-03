<?php

declare(strict_types=1);

namespace app\helpers;

use DateInterval;

class DateIntervalHelper
{
	/**
	 * Returns a DateInterval object representing the specified number of days.
	 *
	 * @param int $days The number of days
	 *
	 * @return DateInterval
	 */
	public static function days(int $days): DateInterval
	{
		return new DateInterval("P{$days}D");
	}

	/**
	 * Returns a DateInterval object representing the specified number of hours.
	 *
	 * @param int $hours The number of hours
	 *
	 * @return DateInterval
	 */
	public static function hours(int $hours): DateInterval
	{
		return new DateInterval("PT{$hours}H");
	}
}