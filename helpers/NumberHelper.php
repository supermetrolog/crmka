<?php

declare(strict_types=1);

namespace app\helpers;

class NumberHelper
{
	/**
	 * @param mixed $mbNumber
	 *
	 * @return bool
	 */
	public static function isNumber($mbNumber): bool
	{
		return is_numeric($mbNumber);
	}
}