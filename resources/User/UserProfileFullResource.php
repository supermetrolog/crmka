<?php

declare(strict_types=1);

namespace app\resources\User;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\UserProfile;

class UserProfileFullResource extends JsonResource
{
	private UserProfile $resource;

	public function __construct(UserProfile $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			UserProfileResource::make($this->resource)->toArray(),
			[
				'emails' => UserProfileEmailResource::collection($this->resource->emails),
				'phones' => UserProfilePhoneResource::collection($this->resource->phones)
			]
		);
	}
}