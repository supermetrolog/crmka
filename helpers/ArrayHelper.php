<?php

declare(strict_types=1);

namespace app\helpers;

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
}