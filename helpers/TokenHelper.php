<?php

declare(strict_types=1);

namespace app\helpers;

use app\exceptions\InvalidBearerTokenException;

/**
 * Helper for working with tokens
 *
 * @package app\helpers
 */
class TokenHelper
{
	/** Parse Bearer token from Authorization header
	 *
	 * @param string $header Authorization header value
	 *
	 * @return string Bearer token or null if not found
	 * @throws InvalidBearerTokenException
	 */
	public static function parseBearerToken(string $header): string
	{
		if (!StringHelper::startWith($header, 'Bearer ')) {
			throw new InvalidBearerTokenException();
		}

		return StringHelper::after($header, 'Bearer ');
	}
}