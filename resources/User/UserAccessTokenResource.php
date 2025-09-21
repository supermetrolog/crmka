<?php

declare(strict_types=1);

namespace app\resources\User;

use app\kernel\web\http\resources\JsonResource;
use app\models\User\UserAccessToken;

class UserAccessTokenResource extends JsonResource
{
	private UserAccessToken $resource;

	public function __construct(UserAccessToken $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'expires_at' => $this->resource->expires_at,
			'created_at' => $this->resource->created_at,
			'deleted_at' => $this->resource->deleted_at,
			'user_agent' => $this->resource->user_agent,
			'ip'         => $this->resource->ip
		];
	}
}