<?php

declare(strict_types=1);

namespace app\helpers;

use Exception;
use UnexpectedValueException;

class MatchHelper
{
	/**
	 * @return mixed
	 * @throws Exception
	 */
	public static function match(array $haystack, $value)
	{
		$result = \yii\helpers\ArrayHelper::getValue($haystack, $value);

		if (!$result) {
			throw new UnexpectedValueException('Value not match');
		}

		return $result;
	}
}