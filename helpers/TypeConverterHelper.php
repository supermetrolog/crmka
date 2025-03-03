<?php

declare(strict_types=1);

namespace app\helpers;

use yii\base\InvalidArgumentException;

class TypeConverterHelper
{
	public static function toBool($value): bool
	{
		if (is_bool($value)) {
			return $value;
		}

		if (StringHelper::isString($value)) {
			$value = StringHelper::toLower($value);

			if ($value === 'true') {
				return true;
			}

			if ($value === 'false') {
				return false;
			}
		}

		if (NumberHelper::isNumber($value)) {
			return (bool)$value;
		}

		throw new InvalidArgumentException("Value '$value' with type '" . gettype($value) . "' cannot be converted to bool");
	}

	public static function toInt($value): int
	{
		if (is_int($value)) {
			return $value;
		}

		if (NumberHelper::isNumber($value)) {
			return (int)$value;
		}

		throw new InvalidArgumentException("Value '$value' with type '" . gettype($value) . "' cannot be converted to int");
	}

	public static function toString($value): string
	{
		if (is_string($value)) {
			return $value;
		}

		if (NumberHelper::isNumber($value)) {
			return (string)$value;
		}

		throw new InvalidArgumentException("Value '$value' with type '" . gettype($value) . "' cannot be converted to string");
	}


}