<?php

declare(strict_types=1);

namespace app\helpers;

class StringHelper
{
	/**
	 * Checks if a string is empty.
	 *
	 * This method determines if the given string has a length of zero.
	 *
	 * @param string $string The string to check.
	 *
	 * @return bool True if the string is empty, false otherwise.
	 */
	public static function empty(string $string): bool
	{
		return strlen($string) === 0;
	}

	/**
	 * Checks if a string is not empty.
	 *
	 * This method determines if the given string has a length greater than zero.
	 *
	 * @param string $string The string to check.
	 *
	 * @return bool True if the string is not empty, false otherwise.
	 */
	public static function notEmpty(string $string): bool
	{
		return !self::empty($string);
	}

	/** Truncates a string to a given maximum length.
	 *
	 * @param string $string
	 * @param int    $maxLength
	 *
	 * @return string The truncated string or the original string if it is shorter than the maximum length.
	 */
	public static function truncate(string $string, int $maxLength): string
	{
		if (strlen($string) <= $maxLength) {
			return $string;
		}

		return substr($string, 0, $maxLength);
	}
}