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

}