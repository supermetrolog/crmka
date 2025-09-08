<?php

declare(strict_types=1);

namespace app\components\Notification;

use app\components\Notification\Interfaces\NotificationActionPayloadInterface;

class NotificationActionNavigatePayload implements NotificationActionPayloadInterface
{
	public ?array  $query       = null;
	public string  $routeCode;
	public ?array  $params      = null;
	public ?string $fallbackUrl = null;

	public function __construct(
		string $routeCode,
		?array $params = null,
		?array $query = null,
		?string $fallbackUrl = null
	)
	{
		$this->routeCode   = $routeCode;
		$this->params      = $params;
		$this->query       = $query;
		$this->fallbackUrl = $fallbackUrl;
	}

	public static function toRoute(string $routeCode, ?array $params = null, ?array $query = null, ?string $fallbackUrl = null): self
	{
		return new self($routeCode, $params, $query, $fallbackUrl);
	}

	public static function toUrl(string $url): self
	{
		return new self('raw_url', null, null, $url);
	}

	public function toArray(): array
	{
		return [
			'route_code'   => $this->routeCode,
			'params'       => $this->params,
			'query'        => $this->query,
			'fallback_url' => $this->fallbackUrl,
		];
	}

	public static function fromArray(array $data): self
	{
		return new self(
			(string)($data['route_code'] ?? ''),
			$data['params'] ?? null,
			$data['query'] ?? null,
			$data['fallback_url'] ?? null
		);
	}
}