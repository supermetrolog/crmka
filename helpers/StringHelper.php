<?php

declare(strict_types=1);

namespace app\helpers;

class StringHelper
{
	public const SYMBOL_SPACE = ' ';

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

	public static function startWith(string $string, string $prefix): bool
	{
		return strncmp($string, $prefix, strlen($prefix)) === 0;
	}

	/**
	 * Returns the portion of string specified by the after parameter.
	 *
	 * @param string $string
	 * @param string $after The portion of string.
	 *
	 * @return string|null The portion of string, or null if the after parameter was not found.
	 */
	public static function after(string $string, string $after): ?string
	{
		$pos = strpos($string, $after);

		if ($pos === false) {
			return null;
		}

		return substr($string, $pos + strlen($after));
	}

	/**
	 * Returns first symbol in string
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function first(string $string): string
	{
		return mb_substr($string, 0, 1);
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function ucFirst(string $string): string
	{
		return ucfirst($string);
	}

	/**
	 * @param string $string
	 * @param string $characters
	 *
	 * @return string
	 */
	public static function trim(string $string, string $characters = " \t\n\r\0\x0B"): string
	{
		return trim($string, $characters);
	}

	public static function join(string $separator = ' ', string ...$strings): string
	{
		$notEmptyStrings = ArrayHelper::filter(ArrayHelper::toArray($strings), fn($str) => self::notEmpty($str));

		return join($separator, $notEmptyStrings);
	}
}