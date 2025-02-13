<?php

declare(strict_types=1);

namespace app\helpers;

use Exception;
use InvalidArgumentException;
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

	public static function hasEvenLength(array $array): bool
	{
		return self::length($array) % 2 === 0;
	}

	public static function hasOddLength(array $array): bool
	{
		return self::length($array) % 2 === 1;
	}

	/**
	 * @param int|string $key
	 */
	public static function column(array $array, $key): array
	{
		return array_column($array, $key);
	}

	public static function unique(array $array): array
	{
		return array_unique($array);
	}

	/**
	 * @param int|string $key
	 */
	public static function uniqueByKey(array $array, $key): array
	{
		return self::unique(self::column($array, $key));
	}

	/**
	 * Accepts sorted array of integers and distributes value to all of them starting from left to right
	 *
	 * @param int[] $array
	 *
	 * @return int[] Distributed array
	 */
	public static function toDistributedValue(array $array, int $value): array
	{
		if ($value < 0) {
			throw new InvalidArgumentException('Value must be positive');
		}

		if ($value === 0) {
			return [...$array];
		}

		$size = self::length($array);

		if ($size === 0) {
			return [];
		}

		/** @var int[] $result */
		$result = [...$array];

		for ($i = 0; $i < $size; $i++) {
			if ($i < ($size - 1)) {
				$gap = $result[$i + 1] - $result[$i];

				if ($gap < 0) {
					throw new InvalidArgumentException('Array is not sorted');
				}

				$needed = ($i + 1) * $gap;
			} else {
				$needed = $value + 1;
			}

			if ($value >= $needed) {
				for ($j = 0; $j < $i + 1; $j++) {
					$result[$j] += $gap;
				}

				$value -= $needed;
			} else {
				$gap      = (int)floor($value / ($i + 1));
				$leftover = $value % ($i + 1);

				for ($j = 0; $j < $i + 1; $j++) {
					$result[$j] += $gap;

					if ($j < $leftover) {
						$result[$j]++;
					}
				}

				break;
			}
		}

		return $result;
	}
}