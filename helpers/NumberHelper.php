<?php

declare(strict_types=1);

namespace app\helpers;

class NumberHelper
{
	/**
	 * @param mixed $mbNumber
	 *
	 * @return bool
	 */
	public static function isNumber($mbNumber): bool
	{
		return is_numeric($mbNumber);
	}

	/** @param float|int $number */
	public static function round($number, $precision = 0): float
	{
		return round($number, $precision);
	}

	/**
	 * @param float|int $value
	 * @param float|int $total
	 */
	public static function calculatePercentage($value, $total, int $decimals = 0): float
	{
		if ($value === 0 || $total === 0) {
			return 0;
		}

		return self::round(($value / $total) * 100, $decimals);
	}
}