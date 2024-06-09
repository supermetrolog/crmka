<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\Survey;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class SurveyResource extends JsonResource
{
	private Survey $resource;

	public function __construct(Survey $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'user_id'    => $this->resource->user_id,
			'contact_id' => $this->resource->contact_id,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'user'       => UserShortResource::make($this->resource->user)->toArray(),
		];
	}
}