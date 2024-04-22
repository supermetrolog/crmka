<?php

declare(strict_types=1);

namespace app\resources\User;

use app\kernel\web\http\resources\JsonResource;
use app\models\User;
use app\models\UserProfile;

class UserProfileResource extends JsonResource
{
	private UserProfile $resource;

	public function __construct(UserProfile $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'user_id'     => $this->resource->id,
			'first_name'  => $this->resource->id,
			'middle_name' => $this->resource->id,
			'last_name'   => $this->resource->id,
			'caller_id'   => $this->resource->id,
			'avatar'      => $this->resource->id,
		];
	}
}