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

	/**
	 * @param mixed $value
	 */
	public static function isValid($value): bool
	{
		return ArrayHelper::includes(static::values(), $value);
	}

	public static function labels(): array
	{
		return static::toArray();
	}

	public static function label($value): ?string
	{
		return static::labels()[$value] ?? null;
	}
}
