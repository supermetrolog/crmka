<?php

declare(strict_types=1);

namespace app\helpers;

class SQLHelper
{
	public static function fromUnixTime($time): string
	{
		return 'FROM_UNIXTIME(' . $time . ')';
	}
}