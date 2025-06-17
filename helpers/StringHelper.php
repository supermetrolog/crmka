<?php

declare(strict_types=1);

namespace app\helpers;

class StringHelper
{
	public const SYMBOL_SPACE      = ' ';
	public const SYMBOL_SLASH      = '/';
	public const SYMBOL_COMMA      = ',';
	public const SPACED_COMMA      = ', ';
	public const SYMBOL_EMPTY      = '';
	public const SYMBOL_SPACED_DOT = '. ';

	/**
	 * Checks if a string is empty.
	 *
	 * This method determines if the given string has a length of zero.
	 *
	 * @param string $string The string to check.
	 *
	 * @return bool True if the string is empty, false otherwise.
	 */
	public static function isEmpty(string $string): bool
	{
		return self::length($string) === 0;
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
	public static function isNotEmpty(string $string): bool
	{
		return !self::isEmpty($string);
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
		if (self::length($string) <= $maxLength) {
			return $string;
		}

		return substr($string, 0, $maxLength);
	}

	public static function startWith(string $string, string $prefix): bool
	{
		return strncmp($string, $prefix, self::length($prefix)) === 0;
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

		return substr($string, $pos + self::length($after));
	}

	/**
	 * Returns first symbol in string
	 */
	public static function first(string $string): string
	{
		return mb_substr($string, 0, 1);
	}

	/**
	 * Make a string's first character uppercase
	 */
	public static function ucFirst(string $string): string
	{
		return self::toUpper(self::substr($string, 0, 1)) . self::substr($string, 1);
	}

	/**
	 * Make a string's first character lowercase
	 */
	public static function lcFirst(string $string): string
	{
		return self::toLower(self::substr($string, 0, 1)) . self::substr($string, 1);
	}

	public static function substr(string $string, int $start, ?int $length = null): string
	{
		return mb_substr($string, $start, $length);
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
		$notEmptyStrings = ArrayHelper::filter($strings, static fn($str) => self::isNotEmpty($str));

		return join($separator, $notEmptyStrings);
	}

	public static function length(string $string): int
	{
		return mb_strlen($string);
	}

	public static function isOnlyDigits(string $string): bool
	{
		return ctype_digit($string);
	}

	public static function isOnlyCharacters(string $string): bool
	{
		return preg_match('/^[\pL]+$/u', $string) === 1;
	}

	public static function isOnlyUpper(string $string): bool
	{
		return self::toUpper($string) === $string;
	}

	public static function isAbbreviation(string $string): bool
	{
		return self::length($string) > 1 && self::isOnlyUpper($string) && self::isOnlyCharacters($string);
	}

	public static function extractDigits(string $string): string
	{
		return preg_replace('/[^0-9]/', '', $string);
	}

	/**
	 * @param mixed $mbString
	 *
	 * @return bool
	 */
	public static function isString($mbString): bool
	{
		return is_string($mbString);
	}

	public static function substrCount(string $haystack, string $needle): int
	{
		return mb_substr_count($haystack, $needle);
	}

	public static function explode(string $delimiter, string $string): array
	{
		return explode($delimiter, $string);
	}

	public static function toLower(string $string): string
	{
		return mb_strtolower($string);
	}

	public static function toUpper(string $string): string
	{
		return mb_strtoupper($string);
	}

	/** @return string[] */
	public static function toWords(string $string): array
	{
		return self::explode(self::SYMBOL_SPACE, $string);
	}
}