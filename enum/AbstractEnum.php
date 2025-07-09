<?php

namespace app\enum;

use app\helpers\ArrayHelper;
use ReflectionClass;

abstract class AbstractEnum
{
	public static function toArray(): array
	{
		return (new ReflectionClass(static::class))->getConstants();
	}

	public static function values(): array
	{
		return ArrayHelper::values(static::toArray());
	}

	public static function keys(): array
	{
		return ArrayHelper::keys(static::toArray());
	}

	public static function isValid(string $value): bool
	{
		return ArrayHelper::includes(static::values(), $value);
	}

	public static function labels(): array
	{
		return static::toArray();
	}

	public static function label(string $value): ?string
	{
		return static::labels()[$value] ?? null;
	}
}
