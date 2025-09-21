<?php

declare(strict_types=1);

namespace app\resources\Telegram;

use app\dto\Telegram\StartLinkTelegramDto;
use app\kernel\web\http\resources\JsonResource;

class StartTelegramLinkResource extends JsonResource
{
	private StartLinkTelegramDto $resource;

	public function __construct(StartLinkTelegramDto $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'deep_link'  => $this->resource->deepLink,
			'code'       => $this->resource->code,
			'expires_at' => $this->resource->expiresAt
		];
	}
}