<?php

declare(strict_types=1);

namespace app\helpers;

use JsonException;

class JSONHelper
{
	/**
	 * @param mixed $data
	 *
	 * @throws JsonException
	 */
	public static function encode($data): string
	{
		return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
	}

	/**
	 * @return mixed
	 * @throws JsonException
	 */
	public static function decode(string $json)
	{
		return json_decode($json, null, 512, JSON_THROW_ON_ERROR);
	}
}