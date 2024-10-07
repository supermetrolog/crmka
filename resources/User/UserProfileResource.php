<?php

declare(strict_types=1);

namespace app\resources\User;

use app\helpers\StringHelper;
use app\kernel\web\http\resources\JsonResource;
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
			'user_id'     => $this->resource->user_id,
			'first_name'  => $this->resource->first_name,
			'middle_name' => $this->resource->middle_name,
			'last_name'   => $this->resource->last_name,
			'caller_id'   => $this->resource->caller_id,
			'avatar'      => $this->resource->avatar,
			'medium_name' => $this->getMediumName(),
			'full_name'   => $this->getFullName(),
			'short_name'  => $this->getShortName()
		];
	}

	public function getFullName(): string
	{
		$fullName = "{$this->resource->middle_name} {$this->resource->first_name}";
		if ($this->resource->last_name) {
			$fullName .= " {$this->resource->last_name}";
		}

		return $fullName;
	}

	public function getShortName(): string
	{
		$firstName = StringHelper::toUpperCase(StringHelper::first($this->resource->first_name)) . '.';
		$lastName  = "";

		if ($this->resource->last_name) {
			$lastName = StringHelper::toUpperCase(StringHelper::first($this->resource->last_name)) . '.';
		}

		$shortName = $this->resource->middle_name . " $firstName $lastName";

		return StringHelper::trim($shortName);
	}

	public function getMediumName(): string
	{
		return StringHelper::trim($this->resource->first_name . " " . $this->resource->middle_name);
	}
}