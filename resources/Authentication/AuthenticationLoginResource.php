<?php

declare(strict_types=1);

namespace app\resources\Authentication;

use app\dto\Authentication\AuthenticationResponseDto;
use app\kernel\web\http\resources\JsonResource;
use app\resources\User\UserResource;

class AuthenticationLoginResource extends JsonResource
{
	private AuthenticationResponseDto $resource;

	public function __construct(AuthenticationResponseDto $resource)
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