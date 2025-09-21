<?php

declare(strict_types=1);

namespace app\resources\User;

use app\kernel\web\http\resources\JsonResource;
use app\models\User\UserProfile;

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
			'user_id'     => $this->resource->user_id,
			'first_name'  => $this->resource->first_name,
			'middle_name' => $this->resource->middle_name,
			'last_name'   => $this->resource->last_name,
			'caller_id'   => $this->resource->caller_id,
			'avatar'      => $this->resource->avatar,
			'medium_name' => $this->resource->mediumName,
			'full_name'   => $this->resource->fullName,
			'short_name'  => $this->resource->shortName,
			'gender'      => $this->resource->gender,
		];
	}
}