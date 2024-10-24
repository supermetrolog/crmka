<?php

declare(strict_types=1);

namespace app\resources\User;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\UserProfileEmail;

class UserProfileEmailResource extends JsonResource
{
	private UserProfileEmail $resource;

	public function __construct(UserProfileEmail $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'    => $this->resource->id,
			'email' => $this->resource->email
		];
	}
}