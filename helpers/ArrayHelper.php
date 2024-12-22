<?php

declare(strict_types=1);

namespace app\helpers;

use Exception;
use yii\helpers\ArrayHelper as YiiArrayHelper;

class ArrayHelper
{
	public static function walk(array &$array, callable $callback): void
	{
		array_walk($array, $callback);
	}

	public static function map(array $array, callable $callback): array
	{
		return array_map($callback, $array);
	}

	public static function merge(array ...$array): array
	{
		return array_merge(...$array);
	}

	public static function diff(array ...$array): array
	{
		return array_diff(...$array);
	}

	public static function values(array $array): array
	{
		return array_values($array);
	}

	public static function keys(array $array): array
	{
		return array_keys($array);
	}

	public static function filteredKeys(array $array, $filters): array
	{
		return array_keys($array, $filters);
	}

	public static function empty(array $array): bool
	{
		return empty($array);
	}

	public static function notEmpty(array $array): bool
	{
		return !self::empty($array);
	}

	public static function isArray($array): bool
	{
		return is_array($array);
	}

	public static function intersect(array ...$array): array
	{
		return array_intersect(...$array);
	}

	public static function toArray($array): array
	{
		return self::isArray($array) ? $array : [$array];
	}


	public static function filter($array, callable $fn): array
	{
		return array_filter($array, $fn);
	}

	/**
	 * @param array    $array
	 * @param callable $fn
	 *
	 * @return mixed|null
	 */
	public static function find(array $array, callable $fn)
	{
		foreach ($array as $key => $value) {
			if ($fn($value, $key)) {
				return $value;
			}
		}

		return null;
	}

	public static function diffByCallback(array $firstArray, array $secondArray, callable $fn): array
	{
		return array_udiff($firstArray, $secondArray, $fn);
	}

	public static function length(array $array): int
	{
		return count($array);
	}

	public static function includes(array $array, $needle, bool $strict = true): bool
	{
		return in_array($needle, $array, $strict);
	}

	/**
	 * @throws Exception
	 */
	public static function includesByKey(array $array, $needle, $key): bool
	{
		foreach ($array as $item) {
			if (YiiArrayHelper::getValue($item, $key) === $needle) {
				return true;
			}
		}

		return false;
	}

	/** @param mixed $needle */
	public static function keyExists(array $array, $needle): bool
	{
		return array_key_exists($needle, $array);
	}

	public static function hasEqualsValues(array $array1, array $array2): bool
	{
		if (self::length($array1) !== self::length($array2)) {
			return false;
		}

		return self::empty(self::diff($array1, $array2));
	}
}