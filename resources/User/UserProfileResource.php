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
		return StringHelper::join(
			StringHelper::SYMBOL_SPACE,
			$this->resource->middle_name ?? "",
			$this->resource->first_name,
			$this->resource->last_name ?? ""
		);
	}

	public function getShortName(): string
	{
		$firstNameCharacter = StringHelper::ucFirst(StringHelper::first($this->resource->first_name));
		$lastNameCharacter  = StringHelper::ucFirst(StringHelper::first($this->resource->last_name ?? ""));

		$characters = StringHelper::join(". ", $firstNameCharacter, $lastNameCharacter);

		return StringHelper::join(StringHelper::SYMBOL_SPACE, $this->resource->middle_name ?? "", $characters) . ".";
	}

	public function getMediumName(): string
	{
		return StringHelper::join(
			StringHelper::SYMBOL_SPACE,
			$this->resource->first_name,
			$this->resource->middle_name ?? ""
		);
	}
}