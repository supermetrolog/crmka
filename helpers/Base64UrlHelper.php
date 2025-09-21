<?php

declare(strict_types=1);

namespace app\helpers;

class Base64UrlHelper
{
	public static function encode(string $data): string
	{
		$b64 = base64_encode($data);

		return rtrim(strtr($b64, '+/', '-_'), '=');
	}

	public static function decode(string $data): string
	{
		$b64 = strtr($data, '-_', '+/');
		$pad = strlen($b64) % 4;

		if ($pad) {
			$b64 .= str_repeat('=', 4 - $pad);
		}

		return base64_decode($b64, true) ?: '';
	}
}