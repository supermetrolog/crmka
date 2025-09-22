<?php

declare(strict_types=1);

namespace app\resources\Telegram;

use app\dto\Telegram\StatusLinkTelegramDto;
use app\kernel\web\http\resources\JsonResource;

class StatusTelegramLinkResource extends JsonResource
{
	private StatusLinkTelegramDto $resource;

	public function __construct(StatusLinkTelegramDto $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'linked'           => $this->resource->linked,
			'username'         => $this->resource->username,
			'first_name'       => $this->resource->firstName,
			'last_name'        => $this->resource->lastName,
			'is_login_enabled' => $this->resource->isLoginEnabled
		];
	}
}