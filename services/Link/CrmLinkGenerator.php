<?php

declare(strict_types=1);

namespace app\services\Link;

use InvalidArgumentException;

final class CrmLinkGenerator
{
	private string $baseUrl;

	public function __construct(string $baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}

	private const ROUTES = [
		'profile.integrations' => '/profile/integrations',
	];

	public function generate(string $route, array $params = []): string
	{
		if (!isset(self::ROUTES[$route])) {
			throw new InvalidArgumentException("Route '$route' is not defined.");
		}

		$path = self::ROUTES[$route];

		foreach ($params as $key => $value) {
			$path = str_replace("{" . $key . "}", (string)$value, $path);
		}

		return rtrim($this->baseUrl, '/') . $path;
	}
}
