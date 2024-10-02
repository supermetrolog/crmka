<?php

declare(strict_types=1);

namespace app\resources\Auth;

use app\dto\Auth\AuthResponseDto;
use app\kernel\web\http\resources\JsonResource;
use app\resources\User\UserResource;

class AuthLoginResource extends JsonResource
{
	private AuthResponseDto $resource;

	public function __construct(AuthResponseDto $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'message' => 'Авторизация прошла успешно. Добро пожаловать!',
			'data'    => [
				'user'         => UserResource::tryMakeArray($this->resource->user),
				'access_token' => $this->resource->accessToken,
			]
		];
	}
}