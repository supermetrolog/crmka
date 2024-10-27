<?php

declare(strict_types=1);

namespace app\resources\User;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\UserProfilePhone;

class UserProfilePhoneResource extends JsonResource
{
	private UserProfilePhone $resource;

	public function __construct(UserProfilePhone $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'    => $this->resource->id,
			'phone' => $this->resource->toFormattedPhone()
		];
	}
}